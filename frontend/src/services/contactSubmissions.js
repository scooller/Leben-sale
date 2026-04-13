import api from '../lib/api';

class ContactSubmissionsService {
  async create(fields) {
    const response = await api.post('/contact-submissions', { fields });

    return response.data;
  }
}

export default new ContactSubmissionsService();
