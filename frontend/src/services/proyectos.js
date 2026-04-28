import api from '../lib/api';

export const proyectosService = {
  async getProyecto(id) {
    const response = await api.get(`/proyectos/${id}`);
    return response.data;
  },

  async getProyectos(params = {}) {
    const response = await api.get('/proyectos', { params });
    return response.data;
  },
};
