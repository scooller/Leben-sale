import api from '../lib/api';

export const paymentsService = {
  /**
   * Obtener lista de pagos del usuario
   */
  async getPayments(params = {}) {
    const response = await api.get('/payments', { params });
    return response.data;
  },

  /**
   * Obtener un pago por ID
   */
  async getPayment(id) {
    const response = await api.get(`/payments/${id}`);
    return response.data;
  },

  /**
   * Crear un nuevo pago
   */
  async createPayment(data) {
    const response = await api.post('/payments', data);
    return response.data;
  },

  /**
   * Obtener estado publico del pago para la pagina de resultado.
   */
  async getPublicStatus(paymentId, token) {
    const response = await api.get(`/payments/public-status/${paymentId}`, {
      params: { token },
    });

    return response.data;
  },
};
