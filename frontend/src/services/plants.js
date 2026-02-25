import api from '../lib/api';
import { logError, parseError } from '../utils/errorHandler';

class PlantsService {
  /**
   * Obtener lista de plantas
   */
  async getAll(params = {}) {
    try {
      const response = await api.get('/plants', { params });
      return response.data;
    } catch (error) {
      logError('PlantsService.getAll', error);
      const parsed = parseError(error);
      throw {
        ...parsed,
        context: 'getAll',
        userMessage: parsed.message || 'No se pudieron cargar las plantas.',
      };
    }
  }

  /**
   * Obtener una planta por ID
   */
  async getById(id) {
    try {
      const response = await api.get(`/plants/${id}`);
      return response.data;
    } catch (error) {
      logError('PlantsService.getById', error);
      const parsed = parseError(error);
      throw {
        ...parsed,
        context: 'getById',
        userMessage: parsed.type === 'not_found' 
          ? 'La planta solicitada no existe.' 
          : 'No se pudo cargar la información de la planta.',
      };
    }
  }
}

export default new PlantsService();
