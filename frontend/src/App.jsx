import { useCallback, useContext, useEffect, useMemo, useState } from 'react';
import { SiteConfigProvider, SiteConfigContext } from './contexts/SiteConfigContext';
import MaintenanceMode from './components/MaintenanceMode';
import Home from './pages/Home';
import Contact from './pages/Contact';
import './App.scss';
import './styles/maintenance.scss';

const normalizePathname = (value) => {
  const sanitizedPath = `${value || '/'}`.split('?')[0];
  const normalized = sanitizedPath.replace(/\/+$/, '');

  return normalized === '' ? '/' : normalized;
};

function AppContent() {
  const { config } = useContext(SiteConfigContext) || {};
  const [pathname, setPathname] = useState(() => normalizePathname(window.location.hash.replace(/^#/, '') || '/'));

  useEffect(() => {
    const handlePopState = () => {
      setPathname(normalizePathname(window.location.hash.replace(/^#/, '') || '/'));
    };

    window.addEventListener('hashchange', handlePopState);

    return () => {
      window.removeEventListener('hashchange', handlePopState);
    };
  }, []);

  const navigate = useCallback((nextPath) => {
    const targetPath = normalizePathname(nextPath);

    if (targetPath === pathname) {
      return;
    }

    window.location.hash = targetPath;
    setPathname(targetPath);
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }, [pathname]);

  const currentPath = useMemo(() => normalizePathname(pathname), [pathname]);

  return (
    <div className="app">
      <MaintenanceMode
        maintenanceMode={config?.maintenance_mode}
        maintenanceMessage={config?.maintenance_message}
      />
      <main>
        {currentPath === '/contacto' ? (
          <Contact onNavigate={navigate} currentPath={currentPath} />
        ) : (
          <Home onNavigate={navigate} currentPath={currentPath} />
        )}
      </main>
    </div>
  );
}

function App() {
  return (
    <SiteConfigProvider>
      <AppContent />
    </SiteConfigProvider>
  );
}

export default App;
