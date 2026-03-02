import api from '../lib/api';
import { logError, parseError, ErrorTypes } from '../utils/errorHandler';

class ReservationService {
  /**
   * Reserve a plant. Call when PaymentGatewayDialog opens.
   * @param {number} plantId - The plant ID to reserve
   * @returns {Promise<Object>} reservation data with session_token, expires_at, remaining_seconds
   */
  static async reserve(plantId) {
    try {
      if (!plantId || plantId <= 0) {
        throw {
          type: ErrorTypes.VALIDATION,
          message: 'ID de planta invalido',
          userMessage: 'Error: Planta no valida para reservar.',
        };
      }

      const response = await api.post('/reservations', { plant_id: plantId });
      return response.data.reservation;
    } catch (error) {
      if (error.type && error.userMessage) {
        logError('ReservationService.reserve', error);
        throw error;
      }

      logError('ReservationService.reserve', error);
      const parsed = parseError(error);

      let userMessage = parsed.message;
      if (error.response?.status === 409) {
        userMessage = 'Esta planta ya esta reservada por otro usuario. Intenta con otra.';
      } else if (error.response?.status === 404) {
        userMessage = 'La planta no esta disponible.';
      }

      throw {
        ...parsed,
        context: 'reserve',
        userMessage,
      };
    }
  }

  /**
   * Release a reservation. Call when dialog closes without purchase.
   * @param {string} sessionToken - The reservation session token
   */
  static async release(sessionToken) {
    try {
      if (!sessionToken) {
        return;
      }

      await api.delete(`/reservations/${sessionToken}`);
    } catch (error) {
      // Release errors are non-critical - log but do not block the user
      logError('ReservationService.release', error);
    }
  }

  /**
   * Check if a plant is reserved.
   * @param {number} plantId
   * @returns {Promise<Object>} { reserved: boolean, expires_at?, remaining_seconds? }
   */
  static async checkStatus(plantId) {
    try {
      const response = await api.get(`/reservations/plant/${plantId}`);
      return response.data;
    } catch (error) {
      logError('ReservationService.checkStatus', error);
      return { reserved: false };
    }
  }
}

export default ReservationService;
