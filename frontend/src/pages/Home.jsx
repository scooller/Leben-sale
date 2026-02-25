import { useEffect, useRef, useState } from 'react';
import { useSiteConfig } from '../contexts/SiteConfigContext';
import PlantsService from '../services/plants';
import CheckoutService from '../services/checkout';
import { proyectosService } from '../services/proyectos';
import { authService } from '../services/auth';
import ErrorNotification from '../components/ErrorNotification';
import PlantsGrid from '../components/PlantsGrid';
import BannerPromo from '../components/BannerPromo';
import { isRetryableError } from '../utils/errorHandler';
import gsap from 'gsap';
import '../styles/home.scss' with { type: 'css' };

/**
 * Página principal - Catálogo de plantas
 * Usa Web Awesome components de forma nativa con íconos integrados
 */
function Home() {
  const { config, loading: configLoading } = useSiteConfig();
  const [plants, setPlants] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);
  const [checkoutError, setCheckoutError] = useState(null);
  const [page, setPage] = useState(1);
  const [totalPages, setTotalPages] = useState(1);
  const [totalPlants, setTotalPlants] = useState(0);
  const [gateways, setGateways] = useState([]);
  const [checkoutLoading, setCheckoutLoading] = useState(false);
  const [plantForCheckout, setPlantForCheckout] = useState(null);
  const [selectedGateway, setSelectedGateway] = useState('');
  const [checkoutName, setCheckoutName] = useState('');
  const [checkoutEmail, setCheckoutEmail] = useState('');
  const [checkoutPhone, setCheckoutPhone] = useState('');
  const [checkoutRut, setCheckoutRut] = useState('');
  const isAuthenticated = authService.isAuthenticated();
  
  // Estados para filtros
  const [proyectos, setProyectos] = useState([]);
  const [selectedProyecto, setSelectedProyecto] = useState([]);
  const [selectedDormitorios, setSelectedDormitorios] = useState([]);
  const [selectedBanos, setSelectedBanos] = useState([]);
  const [selectedPrecioMin, setSelectedPrecioMin] = useState('');
  const [selectedPrecioMax, setSelectedPrecioMax] = useState('');
  
  // Estados temporales para filtros (antes de aplicar)
  const [tempProyecto, setTempProyecto] = useState([]);
  const [tempDormitorios, setTempDormitorios] = useState([]);
  const [tempBanos, setTempBanos] = useState([]);
  const [tempPrecioMin, setTempPrecioMin] = useState('');
  const [tempPrecioMax, setTempPrecioMax] = useState('');
  
  const gatewayDialogRef = useRef(null);
  const heroRef = useRef(null);

  const isValidEmail = (value) => /\S+@\S+\.\S+/.test(value);

  const isValidPhone = (value) => {
    const digits = value.replace(/\D/g, '');
    return digits.length >= 8 && digits.length <= 15;
  };

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
  );

  const normalizeMultiValue = (value) => {
    if (Array.isArray(value)) {
      return value.filter((item) => item !== null && item !== undefined && `${item}`.trim() !== '');
    }

    if (value === null || value === undefined || `${value}`.trim() === '') {
      return [];
    }

    return [value];
  };

  const getMultiSelectValue = (event) => normalizeMultiValue(event?.target?.value);

  const activeFilterCount = selectedProyecto.length
    + selectedDormitorios.length
    + selectedBanos.length
    + (selectedPrecioMin ? 1 : 0)
    + (selectedPrecioMax ? 1 : 0);

  // Cargar proyectos para el filtro
  useEffect(() => {
    const fetchProyectos = async () => {
      try {
        const data = await proyectosService.getProyectos({ perPage: 100 });
        setProyectos(data.data || []);
      } catch (err) {
      }
    };
    fetchProyectos();
  }, []);

  // Cargar plantas cuando cambian los filtros
  useEffect(() => {
    const loadPlants = async () => {
      try {
        setLoading(true);
        setError(null);
        
        const filters = {
          page,
          perPage: 12,
        };
        
        if (selectedProyecto.length > 0) {
          filters.salesforce_proyecto_id = selectedProyecto;
        }
        
        if (selectedDormitorios.length > 0) {
          filters.programa = selectedDormitorios;
        }
        
        if (selectedBanos.length > 0) {
          filters.programa2 = selectedBanos;
        }
        
        if (selectedPrecioMin) {
          filters.min_precio = selectedPrecioMin;
        }
        
        if (selectedPrecioMax) {
          filters.max_precio = selectedPrecioMax;
        }
        
        const data = await PlantsService.getAll(filters);
        
        const totalCount = data.total ?? data.data?.length ?? 0;
        
        const mappedPlants = (data.data || []).map(plant => ({
          ...plant,
          nombre: plant.name,
          categoria: plant.programa,
          precioBase: Number(plant.precio_base) || 0,
          precioLista: Number(plant.precio_lista) || 0,
          proyectoNombre: plant.proyecto?.name,
          proyectoDescripcion: plant.proyecto?.descripcion,
          proyectoDireccion: plant.proyecto?.direccion,
          proyectoComuna: plant.proyecto?.comuna,
        }));
        
        setPlants(mappedPlants);
        setTotalPages(data.last_page || 1);
        setTotalPlants(totalCount);
      } catch (err) {
        const errorInfo = {
          type: err.type || 'unknown',
          message: err.message || 'Error al cargar las plantas',
          userMessage: err.userMessage || 'No se pudieron cargar las plantas. Por favor, intenta de nuevo.',
          title: 'Error al cargar plantas',
          canRetry: isRetryableError(err),
        };
        setError(errorInfo);
      } finally {        
        setLoading(false);
      }
    };

    loadPlants();
  }, [page, selectedProyecto, selectedDormitorios, selectedBanos, selectedPrecioMin, selectedPrecioMax]);

  // Animaciones del Hero con GSAP
  useEffect(() => {
    if (configLoading || !heroRef.current) return;

    const ctx = gsap.context(() => {
      const tl = gsap.timeline();
      
      // Logo - flipInX (0ms)
      const logo = heroRef.current.querySelector('.hero-logo');
      if (logo) {
        tl.fromTo(logo, {
          y: -90,
          opacity: 0,
        }, {
          y: 0,
          opacity: 1,
          duration: 1.8,
          ease: 'back.out(1.7)',
        }, 0);
      }
      
      // Título y descripción - fadeInDown (500ms)
      tl.fromTo(['.hero-section h1', '.hero-section p'], {
        y: -50,
        opacity: 0,
      }, {
        y: 0,
        opacity: 1,
        duration: 0.8,
        ease: 'power2.out',
        stagger: 0.1
      }, 0.5);
      
      // Header plantas - fadeIn (700ms)
      tl.fromTo('.plants-header', {
        opacity: 0,
      }, {
        opacity: 1,
        duration: 1,
        ease: 'power1.out'
      }, 0.7);
      
      // Filtros - fadeIn (1000ms)
      tl.fromTo('.filters-details', {
        opacity: 0,
      }, {
        opacity: 1,
        duration: 1,
        ease: 'power1.out'
      }, 1);
    }, heroRef);

    return () => ctx.revert();
  }, [configLoading]);

  // Aplicar filtros
  const handleApplyFilters = () => {
    setSelectedProyecto(tempProyecto);
    setSelectedDormitorios(tempDormitorios);
    setSelectedBanos(tempBanos);
    setSelectedPrecioMin(tempPrecioMin);
    setSelectedPrecioMax(tempPrecioMax);
    setPage(1); // Volver a la primera página al aplicar filtros
  };

  // Limpiar filtros
  const handleClearFilters = () => {
    setTempProyecto([]);
    setTempDormitorios([]);
    setTempBanos([]);
    setTempPrecioMin('');
    setTempPrecioMax('');
    setSelectedProyecto([]);
    setSelectedDormitorios([]);
    setSelectedBanos([]);
    setSelectedPrecioMin('');
    setSelectedPrecioMax('');
    setPage(1);
  };

  const closeGatewayDialog = () => {
    if (gatewayDialogRef.current) {
      gatewayDialogRef.current.open = false;
    }
    setPlantForCheckout(null);
  };

  // Cargar pasarelas disponibles
  useEffect(() => {
    const fetchGateways = async () => {
      try {
        const availableGateways = await CheckoutService.getAvailableGateways();
        setGateways(availableGateways);
      } catch (err) {
        // Error no crítico, el usuario puede no ver las pasarelas pero no bloquea la app
        setCheckoutError({
          type: err.type || 'gateway',
          message: err.message || 'Error al cargar pasarelas',
          userMessage: err.userMessage || 'No se pudieron cargar las pasarelas de pago. Intenta recargar la página.',
          title: 'Aviso',
        });
      }
    };
    fetchGateways();
  }, []);

  // Manejar compra directo desde la tarjeta
  const handleQuickCheckout = async (plant) => {
    const currentUser = authService.getCurrentUser();

    setPlantForCheckout(plant);
    setSelectedGateway(gateways.length > 0 ? gateways[0].id : '');
    setCheckoutName(currentUser?.name || '');
    setCheckoutEmail(currentUser?.email || '');
    setCheckoutPhone(currentUser?.phone || '');
    setCheckoutRut(currentUser?.rut || '');
    if (gatewayDialogRef.current) {
      gatewayDialogRef.current.open = true;
    }
  };

  // Confirmar checkout con pasarela seleccionada
  const handleConfirmCheckout = async () => {
    if (!isAuthenticated) {
      setCheckoutError({
        type: 'auth',
        message: 'Usuario no autenticado',
        userMessage: 'Debes iniciar sesion antes de pagar.',
        title: 'Inicio de sesion requerido',
      });
      return;
    }

    if (!plantForCheckout || !selectedGateway) {
      setCheckoutError({
        type: 'validation',
        message: 'Datos incompletos',
        userMessage: 'Por favor selecciona una planta y una pasarela de pago',
        title: 'Error de validación',
      });
      return;
    }

    if (!checkoutName || !checkoutEmail || !checkoutPhone || !checkoutRut) {
      setCheckoutError({
        type: 'validation',
        message: 'Datos de usuario incompletos',
        userMessage: 'Completa tu nombre, email, telefono y RUT antes de pagar',
        title: 'Datos requeridos',
      });
      return;
    }

    if (!isValidEmail(checkoutEmail)) {
      setCheckoutError({
        type: 'validation',
        message: 'Correo electronico invalido',
        userMessage: 'Ingresa un correo electronico valido.',
        title: 'Email invalido',
      });
      return;
    }

    if (!isValidPhone(checkoutPhone)) {
      setCheckoutError({
        type: 'validation',
        message: 'Telefono invalido',
        userMessage: 'Ingresa un telefono valido con al menos 8 digitos.',
        title: 'Telefono invalido',
      });
      return;
    }

    if (!isValidRut(checkoutRut)) {
      setCheckoutError({
        type: 'validation',
        message: 'RUT invalido',
        userMessage: 'Ingresa un RUT valido (ej: 12.345.678-9).',
        title: 'RUT invalido',
      });
      return;
    }

    try {
      setCheckoutLoading(true);
      setCheckoutError(null);
      const response = await CheckoutService.initiate(plantForCheckout.id, 1, selectedGateway, {
        name: checkoutName,
        email: checkoutEmail,
        phone: checkoutPhone,
        rut: checkoutRut,
      });

      const currentUser = authService.getCurrentUser();
      if (currentUser) {
        localStorage.setItem('user', JSON.stringify({
          ...currentUser,
          name: checkoutName,
          email: checkoutEmail,
          phone: checkoutPhone,
          rut: checkoutRut,
        }));
      }
      
      // Cerrar diálogo antes de redirigir
      if (gatewayDialogRef.current) {
        gatewayDialogRef.current.open = false;
      }
      
      // Redirigir a la pasarela
      CheckoutService.redirect(response.redirect_url);
    } catch (err) {
      setCheckoutError({
        type: err.type || 'unknown',
        message: err.message || 'Error en checkout',
        userMessage: err.userMessage || 'Error al iniciar el checkout. Por favor, intenta de nuevo.',
        title: 'Error en el pago',
        details: err.details,
      });
      setCheckoutLoading(false);
    }
  };

  if (configLoading) {
    return (
      <div className="home-container">
        <div className="loading-skeletons wa-stack wa-gap-l">
          <wa-card appearance="filled">
            <div className="wa-stack wa-gap-s" style={{ padding: '1.5rem' }}>
              <wa-skeleton effect="pulse" style={{ height: '28px', width: '35%', margin: '0 auto' }}></wa-skeleton>
              <wa-skeleton effect="pulse" style={{ height: '18px', width: '60%', margin: '0 auto' }}></wa-skeleton>
            </div>
          </wa-card>

          <div className="wa-stack wa-gap-xs">
            <wa-skeleton effect="pulse" style={{ height: '26px', width: '220px' }}></wa-skeleton>
            <wa-skeleton effect="pulse" style={{ height: '16px', width: '320px' }}></wa-skeleton>
          </div>

          <wa-card appearance="outlined">
            <div className="wa-stack wa-gap-m" style={{ padding: '1rem' }}>
              <wa-skeleton effect="pulse" style={{ height: '18px', width: '140px' }}></wa-skeleton>
              <div className="wa-cluster wa-gap-s">
                <wa-skeleton effect="pulse" style={{ height: '42px', width: '220px' }}></wa-skeleton>
                <wa-skeleton effect="pulse" style={{ height: '42px', width: '160px' }}></wa-skeleton>
                <wa-skeleton effect="pulse" style={{ height: '42px', width: '140px' }}></wa-skeleton>
                <wa-skeleton effect="pulse" style={{ height: '42px', width: '150px' }}></wa-skeleton>
                <wa-skeleton effect="pulse" style={{ height: '42px', width: '150px' }}></wa-skeleton>
              </div>
              <div className="wa-cluster wa-gap-s">
                <wa-skeleton effect="pulse" style={{ height: '34px', width: '150px' }}></wa-skeleton>
                <wa-skeleton effect="pulse" style={{ height: '34px', width: '150px' }}></wa-skeleton>
              </div>
            </div>
          </wa-card>

          <div className="plants-grid wa-grid">
            {[...Array(6)].map((_, i) => (
              <wa-card key={i} className="skeleton-card" appearance="filled">
                <wa-skeleton slot="media" effect="pulse" style={{ height: '220px' }}></wa-skeleton>

                <div slot="header" className="wa-stack wa-gap-xs" style={{ width: '100%' }}>
                  <wa-skeleton effect="pulse" style={{ height: '18px', width: '65%' }}></wa-skeleton>
                  <wa-skeleton effect="pulse" style={{ height: '18px', width: '45%' }}></wa-skeleton>
                </div>

                <div slot="header-actions">
                  <wa-skeleton effect="pulse" style={{ height: '24px', width: '70px' }}></wa-skeleton>
                </div>

                <div className="wa-split wa-align-items-center">
                  <wa-skeleton effect="pulse" style={{ height: '16px', width: '35%' }}></wa-skeleton>
                  <div className="wa-cluster wa-gap-xs">
                    <wa-skeleton effect="pulse" style={{ height: '24px', width: '65px' }}></wa-skeleton>
                    <wa-skeleton effect="pulse" style={{ height: '24px', width: '65px' }}></wa-skeleton>
                  </div>
                </div>

                <div slot="footer" className="wa-stack wa-gap-xs">
                  <wa-skeleton effect="pulse" style={{ height: '14px', width: '48%' }}></wa-skeleton>
                  <wa-skeleton effect="pulse" style={{ height: '28px', width: '38%' }}></wa-skeleton>
                </div>

                <div slot="footer-actions">
                  <wa-button-group label="Skeleton actions">
                    <wa-button size="small" disabled>
                      <wa-skeleton effect="pulse" style={{ height: '14px', width: '72px' }}></wa-skeleton>
                    </wa-button>
                    <wa-button size="small" variant="brand" disabled>
                      <wa-skeleton effect="pulse" style={{ height: '14px', width: '56px' }}></wa-skeleton>
                    </wa-button>
                  </wa-button-group>
                </div>
              </wa-card>
            ))}
          </div>
        </div>
      </div>
    );
  }

  if (error) {
    return (
      <div className="home-container">
        <wa-card>
          <div slot="header">
            <h2>{error.title || 'Error'}</h2>
          </div>
          <wa-callout variant="danger">
            <wa-icon slot="icon" name="circle-exclamation" variant="regular" animation="beat"></wa-icon>
            <strong>No se pudieron cargar las plantas</strong>
            <div style={{ marginTop: '8px' }}>
              {error.userMessage || error.message}
            </div>
          </wa-callout>
          <div style={{ marginTop: '16px', display: 'flex', gap: '8px' }}>
            <wa-button onClick={() => loadPlants()} variant="primary">
              <wa-icon slot="start" name="arrow-rotate-right" animation="spin"></wa-icon>
              Reintentar
            </wa-button>
            {error.canRetry && (
              <wa-button onClick={() => window.location.reload()} variant="default">
                Recargar página
              </wa-button>
            )}
          </div>
        </wa-card>
      </div>
    );
  }

  return (
    <div className="home-container" ref={heroRef}>
      {/* Banner Promocional */}
      <BannerPromo banner={config?.banner} />

      {/* Hero Section */}      
      <div className="hero-section">
        {config?.logo && (
          <img src={config.logo} alt={config?.site_name} className="hero-logo" />
        )}
        <h1>{config?.site_name}</h1>
        <p>{config?.site_description}</p>
      </div>

      {/* Header de Plantas */}
      <div className="plants-header">
        <div className="wa-cluster wa-gap-s wa-align-items-center">
          <h2>Nuestras Plantas</h2>
          {activeFilterCount > 0 && (
            <wa-badge variant="brand" pill>
              {activeFilterCount} {activeFilterCount === 1 ? 'filtro' : 'filtros'} activo{activeFilterCount === 1 ? '' : 's'}
            </wa-badge>
          )}
        </div>
        <p>Descubre nuestra colección disponible</p>
      </div>

      {/* Filtros */}
      <wa-details summary="Filtros" className="filters-details wa-mb-m">
          <div className="wa-stack wa-gap-m">
            <div className="wa-cluster wa-gap-m filters-inputs">
              <wa-select
                label="Proyecto"
                placeholder="Todos los proyectos"
                value={tempProyecto}
                onChange={(e) => {
                  const value = getMultiSelectValue(e);
                  setTempProyecto(value);
                }}
                multiple
                clearable
              >
                {proyectos.map((proyecto) => (
                  <wa-option key={proyecto.id} value={proyecto.salesforce_id}>
                    {proyecto.name}
                  </wa-option>
                ))}
              </wa-select>

              <wa-select
                label="Dormitorios"
                placeholder="Todos"
                value={tempDormitorios}
                onChange={(e) => {
                  const value = getMultiSelectValue(e);
                  setTempDormitorios(value);
                }}
                multiple
                clearable
              >
                <wa-option value="ST">Studio</wa-option>
                <wa-option value="1D">1 Dormitorio</wa-option>
                <wa-option value="2D">2 Dormitorios</wa-option>
                <wa-option value="3D">3 Dormitorios</wa-option>
                <wa-option value="4D">4 Dormitorios</wa-option>
              </wa-select>

              <wa-select
                label="Baños"
                placeholder="Todos"
                value={tempBanos}
                onChange={(e) => {
                  const value = getMultiSelectValue(e);
                  setTempBanos(value);
                }}
                multiple
                clearable
              >
                <wa-option value="1B">1 Baño</wa-option>
                <wa-option value="2B">2 Baños</wa-option>
                <wa-option value="3B">3 Baños</wa-option>
              </wa-select>

              <wa-input
                type="number"
                label="Precio Mínimo"
                placeholder="Desde UF"
                value={tempPrecioMin}
                onChange={(e) => {
                  const value = e.target.value || '';
                  setTempPrecioMin(value);
                }}
                clearable
              >
                <wa-icon slot="start" name="dollar-sign"></wa-icon>
              </wa-input>

              <wa-input
                type="number"
                label="Precio Máximo"
                placeholder="Hasta UF"
                value={tempPrecioMax}
                onChange={(e) => {
                  const value = e.target.value || '';
                  setTempPrecioMax(value);
                }}
                clearable
              >
                <wa-icon slot="start" name="dollar-sign"></wa-icon>
              </wa-input>
            </div>

            <div className="wa-cluster wa-gap-s filters-actions">
              <wa-button 
                variant="brand"
                onClick={handleApplyFilters}
              >
                <wa-icon slot="start" name="filter"></wa-icon>
                Aplicar Filtros
              </wa-button>

              {activeFilterCount > 0 && (
                <wa-button 
                  variant="neutral"
                  onClick={handleClearFilters}
                >
                  <wa-icon slot="start" name="xmark"></wa-icon>
                  Limpiar Filtros
                </wa-button>
              )}
            </div>
          </div>
        </wa-details>

      {/* Plantas Grid */}      
      <PlantsGrid 
        plants={plants}
        loading={loading}
        checkoutLoading={checkoutLoading}
        onQuickCheckout={handleQuickCheckout}
        totalPlants={totalPlants}
        page={page}
        totalPages={totalPages}
        onPageChange={setPage}
      />

      {/* Diálogo - Selección de Pasarela de Pago */}
      <wa-dialog
        ref={gatewayDialogRef}
        label="Seleccionar Pasarela de Pago"
        style={{ '--width': '500px' }}
        light-dismiss
      >
        <div className="gateway-selection">        
          <div className="checkout-user-fields wa-stack wa-gap-s">
            {!isAuthenticated && (
              <wa-callout variant="info">
                <wa-icon name="address-card" slot="icon"></wa-icon>
                Rellena todos los campos para continuar al pago.
              </wa-callout>
            )}
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
          disabled={checkoutLoading}
        >
          Cancelar
        </wa-button>
        <wa-button 
          slot="footer"
          variant="brand" 
          onClick={handleConfirmCheckout}
          disabled={checkoutLoading || !isCheckoutReady}
          {...(checkoutLoading && { loading: true })}
        >
          {checkoutLoading ? 'Procesando...' : 'Continuar al Pago'}
        </wa-button>
      </wa-dialog>

      {/* Notificación de errores de checkout */}
      <ErrorNotification 
        error={checkoutError} 
        onClose={() => setCheckoutError(null)}
        duration={6000}
      />
    </div>
  );
}

export default Home;
