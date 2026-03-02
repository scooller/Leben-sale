import { useEffect, useRef, useState } from 'react';
import { authService } from '../services/auth';
import ReservationService from '../services/reservation';

/**
 * Diálogo para seleccionar pasarela de pago y completar datos del comprador
 * Reserva la planta al abrir, libera al cerrar sin compra
 */
function PaymentGatewayDialog({
  open,
  onClose,
  plant,
  gateways,
  loading,
  isAuthenticated,
  onConfirm
}) {
  const dialogRef = useRef(null);
  const [selectedGateway, setSelectedGateway] = useState('');
  const [checkoutName, setCheckoutName] = useState('');
  const [checkoutEmail, setCheckoutEmail] = useState('');
  const [checkoutPhone, setCheckoutPhone] = useState('');
  const [checkoutRut, setCheckoutRut] = useState('');

  // Reservation state
  const [reservationToken, setReservationToken] = useState(null);
  const [reservationLoading, setReservationLoading] = useState(false);
  const [reservationError, setReservationError] = useState(null);
  const [remainingSeconds, setRemainingSeconds] = useState(0);

  // Validación de email
  const isValidEmail = (value) => /\S+@\S+\.\S+/.test(value);

  // Validación de teléfono
  const isValidPhone = (value) => {
    const digits = value.replace(/\D/g, '');
    return digits.length >= 8 && digits.length <= 15;
  };

  // Validación de RUT chileno
  const isValidRut = (value) => {
    const cleaned = value.replace(/[^0-9kK]/g, '').toLowerCase();
    if (cleaned.length < 8) {
      return false;
    }

    const body = cleaned.slice(0, -1);
    const dv = cleaned.slice(-1);

    if (body.length < 7 || body.length > 8) {
      return false;
    }

    let sum = 0;
    let multiplier = 2;

    for (let i = body.length - 1; i >= 0; i -= 1) {
      sum += Number(body[i]) * multiplier;
      multiplier = multiplier === 7 ? 2 : multiplier + 1;
    }

    const remainder = 11 - (sum % 11);
    const expectedDv = remainder === 11 ? '0' : remainder === 10 ? 'k' : `${remainder}`;

    return dv === expectedDv;
  };

  const isEmailValid = checkoutEmail ? isValidEmail(checkoutEmail) : false;
  const isPhoneValid = checkoutPhone ? isValidPhone(checkoutPhone) : false;
  const isRutValid = checkoutRut ? isValidRut(checkoutRut) : false;

  const isCheckoutReady = Boolean(
    isAuthenticated
    && selectedGateway
    && checkoutName
    && checkoutEmail
    && isEmailValid
    && checkoutPhone
    && isPhoneValid
    && checkoutRut
    && isRutValid
    && reservationToken
    && !reservationError
  );

  const reservaExigidaPeso = (
    plant?.proyecto?.valor_reserva_exigido_defecto_peso
    ?? plant?.proyecto?.valorReservaExigidoDefectoPeso
    ?? plant?.valor_reserva_exigido_defecto_peso
    ?? plant?.valorReservaExigidoDefectoPeso
    ?? plant?.reservaExigidaPeso
  );
  const reservaAsNumber = reservaExigidaPeso !== null && reservaExigidaPeso !== undefined
    ? Number(reservaExigidaPeso)
    : null;
  const formattedReserva = Number.isFinite(reservaAsNumber)
    ? `$ ${reservaAsNumber.toLocaleString('es-CL', { maximumFractionDigits: 0 })}`
    : 'Por confirmar';

  const formatCountdown = (seconds) => {
    const mins = Math.floor(seconds / 60);
    const secs = seconds % 60;
    return `${mins}:${String(secs).padStart(2, '0')}`;
  };

  // Sincronizar estado abierto con el diálogo
  useEffect(() => {
    if (dialogRef.current) {
      dialogRef.current.open = open;
    }
  }, [open]);

  // Reserve plant when dialog opens
  useEffect(() => {
    if (!open || !plant || !isAuthenticated) {
      return;
    }

    let cancelled = false;
    const doReserve = async () => {
      setReservationLoading(true);
      setReservationError(null);
      try {
        const reservation = await ReservationService.reserve(plant.id);
        if (!cancelled) {
          setReservationToken(reservation.session_token);
          setRemainingSeconds(reservation.remaining_seconds);
        }
      } catch (err) {
        if (!cancelled) {
          setReservationError(err.userMessage || 'No se pudo reservar esta planta.');
        }
      } finally {
        if (!cancelled) {
          setReservationLoading(false);
        }
      }
    };

    doReserve();
    return () => { cancelled = true; };
  }, [open, plant, isAuthenticated]);

  // Countdown timer
  useEffect(() => {
    if (remainingSeconds <= 0 || !reservationToken) {
      return;
    }

    const interval = setInterval(() => {
      setRemainingSeconds((prev) => {
        if (prev <= 1) {
          clearInterval(interval);
          setReservationError('Tu reserva ha expirado. Cierra este dialogo e intenta nuevamente.');
          setReservationToken(null);
          return 0;
        }
        return prev - 1;
      });
    }, 1000);

    return () => clearInterval(interval);
  }, [reservationToken, remainingSeconds > 0]);

  useEffect(() => {
    const dialog = dialogRef.current;
    if (!dialog) {
      return;
    }

    const handleHide = () => {
      // Release reservation when dialog closes without purchase
      if (reservationToken) {
        ReservationService.release(reservationToken);
        setReservationToken(null);
      }
      setReservationError(null);
      setRemainingSeconds(0);
      onClose();
    };

    dialog.addEventListener('wa-hide', handleHide);

    return () => {
      dialog.removeEventListener('wa-hide', handleHide);
    };
  }, [onClose, reservationToken]);

  // Cargar datos del usuario cuando se abre el diálogo
  useEffect(() => {
    if (open && plant) {
      const currentUser = authService.getCurrentUser();
      setSelectedGateway(gateways.length > 0 ? gateways[0].id : '');
      setCheckoutName(currentUser?.name || '');
      setCheckoutEmail(currentUser?.email || '');
      setCheckoutPhone(currentUser?.phone || '');
      setCheckoutRut(currentUser?.rut || '');
    }
  }, [open, plant, gateways]);

  const handleConfirm = () => {
    if (!isCheckoutReady) {
      return;
    }

    onConfirm({
      plantId: plant?.id,
      gateway: selectedGateway,
      sessionToken: reservationToken,
      userData: {
        name: checkoutName,
        email: checkoutEmail,
        phone: checkoutPhone,
        rut: checkoutRut,
      },
    });
  };

  return (
    <wa-dialog
      ref={dialogRef}
      label="Seleccionar Pasarela de Pago"
      style={{ '--width': '500px' }}
      light-dismiss
    >
      <div className="gateway-selection">
        <div className="checkout-user-fields wa-stack wa-gap-s">
          {/* Reservation status */}
          {reservationLoading && (
            <wa-callout variant="info">
              <wa-spinner slot="icon"></wa-spinner>
              Reservando planta...
            </wa-callout>
          )}

          {reservationError && (
            <wa-callout variant="danger">
              <wa-icon name="circle-exclamation" slot="icon"></wa-icon>
              {reservationError}
            </wa-callout>
          )}

          {reservationToken && remainingSeconds > 0 && (
            <wa-callout variant="warning">
              <wa-icon name="clock" slot="icon"></wa-icon>
              Planta reservada por <strong>{formatCountdown(remainingSeconds)}</strong>
            </wa-callout>
          )}

          {!isAuthenticated && (
            <wa-callout variant="info">
              <wa-icon name="address-card" slot="icon"></wa-icon>
              Rellena todos los campos para continuar al pago.
            </wa-callout>
          )}
          <div className="wa-split wa-align-items-center">
            <strong>Proyecto</strong>
            <span>{plant?.proyecto?.name || plant?.proyectoNombre || 'Sin proyecto'}</span>
          </div>
          <div className="wa-split wa-align-items-center">
            <strong>Planta</strong>
            <span>{plant?.nombre || plant?.name || 'Sin nombre'}</span>
          </div>
          <div className="wa-split wa-align-items-center">
            <strong>Precio Pie</strong>
            <span>{formattedReserva}</span>
          </div>
          <p className="gateway-instructions">Datos del comprador:</p>
          <wa-input
            label="Nombre completo"
            value={checkoutName}
            onChange={(e) => setCheckoutName(e.target.value)}
            required
          ></wa-input>

          <wa-input
            type="email"
            label="Correo electronico"
            value={checkoutEmail}
            onChange={(e) => setCheckoutEmail(e.target.value)}
            required
          ></wa-input>

          <wa-input
            type="tel"
            label="Telefono"
            placeholder="+56 9 1234 5678"
            value={checkoutPhone}
            onChange={(e) => setCheckoutPhone(e.target.value)}
            required
          ></wa-input>

          <wa-input
            label="RUT"
            placeholder="12.345.678-9"
            value={checkoutRut}
            onChange={(e) => setCheckoutRut(e.target.value)}
            required
          ></wa-input>
        </div>

        <p className="gateway-instructions">Selecciona cómo deseas realizar el pago:</p>

        {gateways.length > 0 ? (
          <wa-radio-group
            value={selectedGateway}
            onChange={(e) => setSelectedGateway(e.target.value)}
          >
            {gateways.map((gateway) => (
              <wa-radio key={gateway.id} value={gateway.id}>
                <div className="gateway-option-content">
                  <strong>{gateway.name}</strong>
                  <br />
                  <small>{gateway.description}</small>
                </div>
              </wa-radio>
            ))}
          </wa-radio-group>
        ) : (
          <wa-callout variant="warning">
            No hay pasarelas de pago configuradas
          </wa-callout>
        )}
      </div>

      <wa-button
        slot="footer"
        variant="neutral"
        data-dialog="close"
        disabled={loading}
      >
        Cancelar
      </wa-button>
      <wa-button
        slot="footer"
        variant="brand"
        onClick={handleConfirm}
        disabled={loading || !isCheckoutReady || reservationLoading}
        {...((loading || reservationLoading) && { loading: true })}
      >
        {loading ? 'Procesando...' : 'Continuar al Pago'}
      </wa-button>
    </wa-dialog>
  );
}

export default PaymentGatewayDialog;
