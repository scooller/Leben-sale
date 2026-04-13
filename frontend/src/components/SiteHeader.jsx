function SiteHeader({ config, currentPath = '/', onNavigate, onMenuClick }) {
  const goToHome = () => {
    onNavigate?.('/');
  };

  const goToContact = () => {
    onNavigate?.('/contacto');
  };

  const handleMenuAction = () => {
    if (currentPath === '/') {
      onMenuClick?.();
      return;
    }

    onNavigate?.('/');
  };

  return (
    <header className="site-header box-shadow-1 wa-px-xl wa-py-m">
        <div className="wa-split" style={{ width: '100%' }}>
            <div className="site-header-brand">
                {config?.logo ? (
                <img src={config.logo} alt={config?.site_name || 'Logo'} className="site-logo" />
                ) : (
                <span className="site-name">{config?.site_name || 'iLeben'}</span>
                )}
            </div>

            <nav className="site-header-nav" aria-label="Navegación principal">
                <wa-button appearance="plain" onClick={goToHome} className={currentPath === '/' ? 'site-link-active' : ''}>
                Home
                </wa-button>
                <wa-button appearance="plain" onClick={handleMenuAction} className={currentPath === '/#plantas' ? 'site-link-active' : ''}>
                Plantas
                </wa-button>
                <wa-button appearance="plain" onClick={goToContact} className={currentPath === '/contacto' ? 'site-link-active' : ''}>
                Contacto
                </wa-button>
            </nav>
        </div>
    </header>
  );
}

export default SiteHeader;
