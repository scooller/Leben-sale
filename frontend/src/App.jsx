import { useContext } from 'react';
import { SiteConfigProvider, SiteConfigContext } from './contexts/SiteConfigContext';
import MaintenanceMode from './components/MaintenanceMode';
import Home from './pages/Home';
import './App.scss';
import './styles/maintenance.scss';

function AppContent() {
  const { config } = useContext(SiteConfigContext) || {};

  return (
    <div className="app">
      <MaintenanceMode 
        maintenanceMode={config?.maintenance_mode} 
        maintenanceMessage={config?.maintenance_message}
      />
      <main>
        <Home />
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
