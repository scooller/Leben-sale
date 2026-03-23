import api from '../lib/api';

export const paymentsService = {
  /**
   * Obtener lista de pagos del usuario
   */
  async getPayments(params = {}) {
    const response = await api.get('/pagos', { params });
    return response.data;
  },

  /**
   * Obtener un pago por ID
   */
  async getPayment(id) {
    const response = await api.get(`/pagos/${id}`);
    return response.data;
  },

  /**
   * Crear un nuevo pago
   */
  async createPayment(data) {
    const response = await api.post('/pagos', data);
    return response.data;
  },
};
