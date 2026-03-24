import { useState, useRef, useEffect } from 'react';
import gsap from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';

const DEFAULT_PLANT_IMAGE = 'https://sale.ileben.cl/wp-content/uploads/2022/11/portada_bold_terraza.jpg';

/**
 * Componente para el grid de plantas con tarjetas, skeleton, diálogo de detalles y paginación
 */
function PlantsGrid({
  plants,
  loading,
  checkoutLoading,
  onQuickCheckout,
  totalPlants,
  page,
  totalPages,
  onPageChange
}) {
  const [selectedPlant, setSelectedPlant] = useState(null);
  const [zoomImageSrc, setZoomImageSrc] = useState('');
  const dialogRef = useRef(null);
  const zoomDialogRef = useRef(null);
  const gridContainerRef = useRef(null);

  // GSAP ScrollTrigger para animar cards cuando entran al viewport
  useEffect(() => {
    if (loading || plants.length === 0 || !gridContainerRef.current) return;

    gsap.registerPlugin(ScrollTrigger);

    // Esperar a que wa-card esté definido
    customElements.whenDefined('wa-card').then(() => {
      const ctx = gsap.context(() => {
        const cards = gsap.utils.toArray('.plant-card', gridContainerRef.current);

        if (cards.length === 0) return;

        cards.forEach((card, index) => {
          // console.log('Animando card:', card);
          gsap.to(card, {
            opacity: 1,
            scale: 1,
            // delay: index * 0.1,
            ease: 'Power2.in',
            scrollTrigger: {
              trigger: card,
              start: 'top 85%',
              end: 'top 50%',
              toggleActions: 'play none none none',
              markers: true
            }
          });
        });
      }, gridContainerRef);

      return () => ctx.revert();
    });

    return () => {
      ScrollTrigger.getAll().forEach(trigger => trigger.kill());
    };
  }, [plants, loading]);

  const buildPaginationItems = () => {
    if (totalPages <= 7) {
      return Array.from({ length: totalPages }, (_, index) => index + 1);
    }

    const items = [1];
    const left = Math.max(2, page - 1);
    const right = Math.min(totalPages - 1, page + 1);

    if (left > 2) {
      items.push('left-ellipsis');
    }

    for (let current = left; current <= right; current += 1) {
      items.push(current);
    }

    if (right < totalPages - 1) {
      items.push('right-ellipsis');
    }

    items.push(totalPages);

    return items;
  };

  const paginationItems = buildPaginationItems();
  const showingFrom = totalPlants > 0 ? (page - 1) * 12 + 1 : 0;
  const showingTo = Math.min((page - 1) * 12 + plants.length, totalPlants || 0);

  const openPlantDetail = (plant) => {
    setSelectedPlant(plant);
    if (dialogRef.current) {
      dialogRef.current.open = true;
    }
  };

  const handleCheckoutFromDialog = () => {
    if (!selectedPlant) return;
    // Cerrar el diálogopar
    if (dialogRef.current) {
      dialogRef.current.open = false;
    }
    // Llamar al checkout con la planta seleccionada
    onQuickCheckout(selectedPlant);
  };

  const openZoomImage = (imageSrc) => {
    if (!imageSrc) return;

    setZoomImageSrc(imageSrc);

    if (zoomDialogRef.current) {
      zoomDialogRef.current.open = true;
    }
  };

  // Skeleton de carga
  if (loading) {

    return (
      <div className="plants-grid wa-grid">
        {[...Array(6)].map((_, i) => (
          <wa-card key={i} className="skeleton-card" appearance="filled">
            <wa-skeleton slot="media" effect="pulse" style={{ height: '220px' }}></wa-skeleton>

            <div slot="header" className="plant-header-wrapper">
              <div className="wa-stack wa-gap-xs" style={{ width: '100%' }}>
                <wa-skeleton effect="pulse" style={{ height: '18px', width: '65%' }}></wa-skeleton>
                <wa-skeleton effect="pulse" style={{ height: '18px', width: '45%' }}></wa-skeleton>
              </div>
            </div>

            <div slot="header-actions">
              <wa-skeleton effect="pulse" style={{ height: '26px', width: '70px' }}></wa-skeleton>
            </div>

            <div className="plant-body">
              <div className="wa-split wa-align-items-center">
                <wa-skeleton effect="pulse" style={{ height: '16px', width: '35%' }}></wa-skeleton>
                <div className="wa-cluster wa-gap-xs">
                  <wa-skeleton effect="pulse" style={{ height: '26px', width: '70px' }}></wa-skeleton>
                  <wa-skeleton effect="pulse" style={{ height: '26px', width: '70px' }}></wa-skeleton>
                </div>
              </div>
            </div>

            <div slot="footer" className="plant-price-wrapper">
              <div className="wa-stack wa-gap-xs">
                <wa-skeleton effect="pulse" style={{ height: '14px', width: '50%' }}></wa-skeleton>
                <wa-skeleton effect="pulse" style={{ height: '28px', width: '40%' }}></wa-skeleton>
              </div>
            </div>

            <div slot="footer-actions" className="wa-cluster wa-gap-s">
              <wa-button-group label="Skeleton actions">
                <wa-button size="small" disabled>
                  <wa-skeleton effect="pulse" style={{ height: '14px', width: '70px' }}></wa-skeleton>
                </wa-button>
                <wa-button size="small" variant="brand" disabled>
                  <wa-skeleton effect="pulse" style={{ height: '14px', width: '54px' }}></wa-skeleton>
                </wa-button>
              </wa-button-group>
            </div>
          </wa-card>
        ))}
      </div>
    );
  }

  // Estado vacío
  if (plants.length === 0) {
    return (
      <div className="empty-plants">
        {typeof totalPlants === 'number' && (
          <div className="plants-count">Total: {totalPlants} planta{totalPlants === 1 ? '' : 's'}</div>
        )}
        <p><wa-icon name="heart-crack"></wa-icon> No hay plantas disponibles</p>
      </div>
    );
  }

  // Grid de plantas
  return (
    <>
      <div className='wa-stack'>
        {typeof totalPlants === 'number' && (
          <div className="plants-count">Total: {totalPlants} planta{totalPlants === 1 ? '' : 's'}</div>
        )}
        <div className="plants-grid wa-grid" ref={gridContainerRef}>
          {plants.map((plant, index) => (
            <wa-card key={plant.id} className="plant-card" appearance="filled">
                <img
                  slot="media"
                  src={plant.coverImage || plant.cover_image_url || plant.cover_image_media?.url || DEFAULT_PLANT_IMAGE}
                  alt={plant.nombre}
                  onClick={() => openPlantDetail(plant)}
                  className="plant-image"
                />

                <div slot="header" className="plant-header-wrapper">
                  <div className="wa-cluster wa-gap-m wa-align-items-center plant-header wa-heading-l">
                    <span>{plant.proyectoNombre}</span> -
                    <wa-badge appearance="filled-outlined" variant="neutral">Planta {plant.nombre}</wa-badge>
                  </div>
                </div>
                {plant.categoria && (
                    <div slot="header-actions" className="wa-cluster wa-gap-xs">
                      <wa-badge variant="brand">{plant.categoria}</wa-badge>
                      {plant.isReserved && (
                        <wa-badge variant="warning">Reservado</wa-badge>
                      )}
                    </div>
                )}
                <div className="plant-body">
                    <div className="wa-split">
                        <div className="wa-cluster wa-gap-xs wa-align-items-center">
                            <wa-icon name="location-dot" style={{ fontSize: '1em' }}></wa-icon>
                            <span>{plant.proyectoComuna}</span>
                        </div>
                        {/* Tags para información adicional */}
                        <div className="wa-cluster wa-gap-xs plant-tags" style={{ '--spacing': '0' }}>
                            {plant.orientacion && (
                            <wa-card appearance="plain" className="wa-align-self-center wa-align-items-center">
                                <wa-icon name="compass" slot="header"></wa-icon>
                                <span>Orient. {plant.orientacion}</span>
                            </wa-card>
                            )}
                            {plant.piso && (
                            <wa-card appearance="plain" className="wa-align-self-center wa-align-items-center">
                                <wa-icon name="building" slot="header"></wa-icon>
                                <span>Piso {plant.piso}</span>
                            </wa-card>
                            )}
                            {plant.superficie_util && (
                            <wa-card appearance="plain" className="wa-align-self-center wa-align-items-center">
                                <wa-icon name="ruler" slot="header"></wa-icon>
                                <span>Sup. {plant.superficie_util} m²</span>
                            </wa-card>
                            )}
                        </div>
                    </div>
                </div>
                {/* Ubicación destacada */}
                {plant.proyectoComuna && (
                <div slot="footer" className="plant-price-wrapper">
                    {/* Precios destacados en el header */}
                    {(plant.precioBase || plant.precioLista) && (
                        <div className="plant-price-header">
                        {(0 < plant.precioLista) && (plant.precioLista !== plant.precioBase) && (
                            <div className="price-original">
                            <span className="price-label-small">Precio lista: </span>
                            <span className="price wa-font-weight-bold">
                                <s>UF {plant.precioLista.toLocaleString('es-CL', { minimumFractionDigits: 0, maximumFractionDigits: 0 })}</s>
                            </span>
                            </div>
                        )}
                        {(0 < plant.precioBase) && (
                            <div className="price-final">
                            {plant.precioBase < plant.precioLista && (
                                <span className="price-label-discount">Precio sale: </span>
                            )}
                            <span className="price-sale wa-font-weight-bold wa-heading-xl">
                                UF {(plant.precioBase).toLocaleString('es-CL', { minimumFractionDigits: 0, maximumFractionDigits: 0 })}
                            </span>
                            </div>
                        )}
                        </div>
                    )}
                </div>
                )}
                {/* Acciones */}
                <div slot="footer-actions" className="wa-cluster wa-gap-s">
                  <wa-button-group label="Alignment">
                    <wa-button size="small" onClick={() => openPlantDetail(plant)}>
                      <wa-icon name="building-circle-exclamation" slot="start"></wa-icon>
                      Ver Detalles
                    </wa-button>
                    <wa-button
                      size="small"
                      variant="brand"
                      disabled={checkoutLoading || plant.isReserved}
                      {...(checkoutLoading && { loading: true })}
                      onClick={() => onQuickCheckout(plant)}
                    >
                      {plant.isReserved
                        ? 'Reservado'
                        : checkoutLoading
                          ? 'Cargando...'
                          : <><wa-icon name="comments-dollar" slot="start"></wa-icon>Cotizar</>
                      }
                    </wa-button>
                  </wa-button-group>
                </div>
              </wa-card>
          ))}
        </div>
      </div>

      {/* Diálogo - Detalles de Planta */}
      <wa-dialog
        ref={dialogRef}
        style={{ '--width': '720px' }}
        light-dismiss
      >
        {selectedPlant && (
          <>
            <span slot="label"><wa-icon name="building-circle-exclamation"></wa-icon> Planta - {selectedPlant?.nombre || 'Detalle'}</span>
            <div className="wa-grid wa-gap-l" style={{ '--min-column-size': '32ch', padding: '1.25rem 1.5rem 0.75rem' }}>
              <img
                src={selectedPlant.interiorImage || selectedPlant.interior_image_url || selectedPlant.interior_image_media?.url || DEFAULT_PLANT_IMAGE}
                alt={selectedPlant.nombre}
                onClick={() => openZoomImage(selectedPlant.interiorImage || selectedPlant.interior_image_url || selectedPlant.interior_image_media?.url || DEFAULT_PLANT_IMAGE)}
                style={{ width: '100%', height: '100%', maxHeight: '380px', objectFit: 'cover', borderRadius: '0.75rem', cursor: 'zoom-in' }}
              />

              <div className="wa-stack wa-gap-m">
                <div className='wa-grid wa-gap-m' style={{ '--min-column-size': '14rem' }}>
                    {selectedPlant.proyectoNombre && (
                        <div className="wa-split wa-align-items-center">
                        <strong>Proyecto</strong>
                        <span>{selectedPlant.proyectoNombre}</span>
                        </div>
                    )}
                    {selectedPlant.proyectoComuna && (
                        <div className="wa-split wa-align-items-center">
                        <strong>Ubicación</strong>
                        <span>{selectedPlant.proyectoComuna}</span>
                        </div>
                    )}
                    {selectedPlant.proyectoDescripcion && (
                        <div className="wa-stack wa-gap-xs">
                        <strong>Descripción del Proyecto</strong>
                        <span>{selectedPlant.proyectoDescripcion}</span>
                        </div>
                    )}
                    <div className="wa-split wa-align-items-center">
                        <strong>Nombre</strong>
                        <span>{selectedPlant.nombre}</span>
                    </div>
                    {selectedPlant.categoria && (
                        <div className="wa-split wa-align-items-center">
                        <strong>Categoría</strong>
                        <wa-badge variant="brand">{selectedPlant.categoria}</wa-badge>
                        </div>
                    )}
                    {selectedPlant.programa && (
                        <div className="wa-split wa-align-items-center">
                        <strong>Programa</strong>
                        <span>{selectedPlant.programa}</span>
                        </div>
                    )}
                    {selectedPlant.orientacion && (
                        <div className="wa-split wa-align-items-center">
                        <strong>Orientación</strong>
                        <wa-tag variant="primary">{selectedPlant.orientacion}</wa-tag>
                        </div>
                    )}
                    {selectedPlant.piso && (
                        <div className="wa-split wa-align-items-center">
                        <strong>Piso</strong>
                        <wa-tag variant="primary">{selectedPlant.piso}</wa-tag>
                        </div>
                    )}
                </div>
              <wa-divider></wa-divider>
              {(selectedPlant.superficie_total_principal !== null && selectedPlant.superficie_total_principal !== undefined
                || selectedPlant.superficie_interior !== null && selectedPlant.superficie_interior !== undefined
                || selectedPlant.superficie_util !== null && selectedPlant.superficie_util !== undefined
                || selectedPlant.superficie_terraza !== null && selectedPlant.superficie_terraza !== undefined
                || selectedPlant.superficie_vendible !== null && selectedPlant.superficie_vendible !== undefined) && (
                <div className="wa-stack wa-gap-s">
                  <strong>Superficies</strong>
                  <div className="wa-grid wa-gap-s" style={{ '--min-column-size': '12rem' }}>
                    {selectedPlant.superficie_total_principal !== null && selectedPlant.superficie_total_principal !== undefined && (
                      <div className="wa-stack wa-gap-2xs">
                        <div className="wa-cluster wa-gap-xs wa-align-items-center">
                          <wa-icon name="house" style={{ fontSize: '0.9em' }}></wa-icon>
                          <span>Total principal</span>
                        </div>
                        <strong>{selectedPlant.superficie_total_principal} m²</strong>
                      </div>
                    )}
                    {selectedPlant.superficie_interior !== null && selectedPlant.superficie_interior !== undefined && (
                      <div className="wa-stack wa-gap-2xs">
                        <div className="wa-cluster wa-gap-xs wa-align-items-center">
                          <wa-icon name="door-open" style={{ fontSize: '0.9em' }}></wa-icon>
                          <span>Interior</span>
                        </div>
                        <strong>{selectedPlant.superficie_interior} m²</strong>
                      </div>
                    )}
                    {selectedPlant.superficie_util !== null && selectedPlant.superficie_util !== undefined && (
                      <div className="wa-stack wa-gap-2xs">
                        <div className="wa-cluster wa-gap-xs wa-align-items-center">
                          <wa-icon name="ruler" style={{ fontSize: '0.9em' }}></wa-icon>
                          <span>Útil</span>
                        </div>
                        <strong>{selectedPlant.superficie_util} m²</strong>
                      </div>
                    )}
                    {selectedPlant.superficie_terraza !== null && selectedPlant.superficie_terraza !== undefined && (
                      <div className="wa-stack wa-gap-2xs">
                        <div className="wa-cluster wa-gap-xs wa-align-items-center">
                          <wa-icon name="umbrella-beach" style={{ fontSize: '0.9em' }}></wa-icon>
                          <span>Terraza</span>
                        </div>
                        <strong>{selectedPlant.superficie_terraza} m²</strong>
                      </div>
                    )}
                    {selectedPlant.superficie_vendible !== null && selectedPlant.superficie_vendible !== undefined && (
                      <div className="wa-stack wa-gap-2xs">
                        <div className="wa-cluster wa-gap-xs wa-align-items-center">
                          <wa-icon name="layer-group" style={{ fontSize: '0.9em' }}></wa-icon>
                          <span>Vendible</span>
                        </div>
                        <strong>{selectedPlant.superficie_vendible} m²</strong>
                      </div>
                    )}
                  </div>
                </div>
              )}
              {(selectedPlant.precioBase || selectedPlant.precioLista) && (
                <>
                <wa-divider></wa-divider>
                <div className="wa-stack wa-gap-xs">
                  <strong><wa-icon name="dollar-sign"></wa-icon> Precio</strong>
                  <div className="wa-grid wa-gap-2xs">
                    {selectedPlant.precioLista && selectedPlant.precioBase && selectedPlant.precioLista !== selectedPlant.precioBase && (
                      <div className="wa-cluster wa-gap-xs">
                        <span>Precio lista:</span>
                        <span style={{ textDecoration: 'line-through', opacity: 0.7 }}>
                          UF {selectedPlant.precioLista.toLocaleString('es-CL', { minimumFractionDigits: 0, maximumFractionDigits: 0 })}
                        </span>
                      </div>
                    )}
                    <span className="wa-heading-xl wa-font-weight-bold">
                      UF {(selectedPlant.precioBase || selectedPlant.precioLista).toLocaleString('es-CL', { minimumFractionDigits: 0, maximumFractionDigits: 0 })}
                    </span>
                    {selectedPlant.precioBase && selectedPlant.precioLista && selectedPlant.precioBase < selectedPlant.precioLista && (
                      <wa-badge variant="success"><wa-icon name="award"></wa-icon> ¡Con descuento!</wa-badge>
                    )}
                  </div>
                </div>
                </>
              )}
              </div>
            </div>

            <wa-button
              slot="footer"
              variant="neutral"
              data-dialog="close"
            >
              Cerrar
            </wa-button>
            <wa-button
              slot="footer"
              variant="brand"
              disabled={checkoutLoading}
              {...(checkoutLoading && { loading: true })}
              onClick={handleCheckoutFromDialog}
            >
              {checkoutLoading ? 'Cargando...' : <><wa-icon name="hand-holding-dollar"></wa-icon> Cotizar Ahora</>}
            </wa-button>
          </>
        )}
      </wa-dialog>

      <wa-dialog
        ref={zoomDialogRef}
        label={selectedPlant?.nombre ? `Imagen ampliada - ${selectedPlant.nombre}` : 'Imagen ampliada'}
        style={{ '--width': '92vw' }}
        light-dismiss
      >
        {zoomImageSrc && (
          <img
            src={zoomImageSrc}
            alt={selectedPlant?.nombre || 'Imagen de planta ampliada'}
            style={{ width: '100%', maxHeight: '82vh', objectFit: 'contain', borderRadius: '0.75rem' }}
          />
        )}

        <wa-button slot="footer" variant="neutral" data-dialog="close">
          Cerrar
        </wa-button>
      </wa-dialog>

      {/* Paginación */}
      {totalPages > 1 && (
        <div className="wa-stack pagination">
          <wa-divider></wa-divider>
          <div className="wa-split wa-align-items-center">
            <span className="wa-caption-m pagination-info">
              Mostrando {showingFrom} a {showingTo} de {totalPlants || 0} resultados
            </span>
            <wa-button-group orientation="horizontal" label="Paginación">
              <wa-button appearance="outlined" disabled={page === 1} onClick={() => onPageChange(page - 1)}>
                <wa-icon name="chevron-left"></wa-icon>
              </wa-button>

              {paginationItems.map((item, index) => {
                if (typeof item === 'string') {
                  return (
                    <wa-button key={`${item}-${index}`} appearance="outlined" disabled>
                      ...
                    </wa-button>
                  );
                }

                const isActivePage = item === page;

                return (
                  <wa-button
                    key={item}
                    appearance={isActivePage ? 'accent' : 'outlined'}
                    {...(isActivePage ? { variant: 'brand' } : {})}
                    onClick={() => onPageChange(item)}
                  >
                    {item}
                  </wa-button>
                );
              })}

              <wa-button appearance="outlined" disabled={page === totalPages} onClick={() => onPageChange(page + 1)}>
                <wa-icon name="chevron-right"></wa-icon>
              </wa-button>
            </wa-button-group>
          </div>
        </div>
      )}
    </>
  );
}

export default PlantsGrid;
