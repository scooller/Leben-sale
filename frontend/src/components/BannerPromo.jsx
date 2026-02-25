/**
 * Componente de Banner Promocional
 * Se muestra antes del hero section si está configurado en el admin
 */
export default function BannerPromo({ banner }) {
  if (!banner?.image) {
    return null;
  }

  const handleBannerClick = (e) => {
    if (banner.link) {
      // Si tiene link externo, abrir en nueva pestaña
      if (banner.link.startsWith('http')) {
        window.open(banner.link, '_blank');
      } else {
        // Si es ruta interna, usar navegación
        window.location.href = banner.link;
      }
    }
  };

  return (
    <div className="banner-promo" role="img" aria-label="Banner promocional">
      <img
        src={banner.image}
        alt="Banner Promocional"
        className="banner-promo-image"
        onClick={handleBannerClick}
        style={{ cursor: banner.link ? 'pointer' : 'default' }}
      />
    </div>
  );
}
