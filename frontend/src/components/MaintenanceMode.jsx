import { useEffect, useRef } from 'react';

/**
 * Componente de Modo de Mantenimiento
 * Se muestra como dialog modal de Web Awesome si maintenance_mode está activado
 * El usuario no puede cerrar este dialog mientras esté activo
 */
export default function MaintenanceMode({ maintenanceMode, maintenanceMessage }) {
  const dialogRef = useRef(null);

  useEffect(() => {
    const dialog = dialogRef.current;
    if (!dialog) return;

    const handleHideAttempt = (event) => {
      // Solo permitir el cierre si maintenanceMode es false
      if (maintenanceMode) {
        event.preventDefault();
      }
    };

    // Agregar listener para prevenir cierre del dialog
    dialog.addEventListener('wa-hide', handleHideAttempt);

    // Mostrar u ocultar el dialog según maintenanceMode
    if (maintenanceMode) {
      dialog.open = true;
      document.body.classList.add('mantencion');
    } else {
      dialog.open = false;
      document.body.classList.remove('mantencion');
    }

    return () => {
      dialog.removeEventListener('wa-hide', handleHideAttempt);
      document.body.classList.remove('mantencion');
    };
  }, [maintenanceMode]);

  return (
    <wa-dialog 
        without-header 
        ref={dialogRef} 
        style={{ '--width': '600px' }} 
        class="maintenance-dialog"
    >
        {/* <div slot="label" className="maintenance-dialog-header">
            Estamos en Modo de Mantenimiento
        </div>
        <wa-button class="new-window" slot="header-actions" appearance="plain">
            <wa-icon name="arrow-up-right-from-square" variant="solid" label="Open in new window"></wa-icon>
        </wa-button> */}
        <div className="maintenance-dialog-content">                   
            {maintenanceMessage && (
            <div 
                className="maintenance-message" 
                dangerouslySetInnerHTML={{ __html: maintenanceMessage }}
            />
            )}
        </div>
        {/* <wa-button slot="footer" variant="brand" data-dialog="close">Cerrar</wa-button> */}
    </wa-dialog>
  );
}
