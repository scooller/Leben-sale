import api from '../lib/api';

class ContactSubmissionsService {
  async create(fields, turnstileToken = null) {
    const response = await api.post('/contact-submissions', {
      fields,
      ...(turnstileToken ? { turnstile_token: turnstileToken } : {}),
    });

    return response.data;
  }
}

export default new ContactSubmissionsService();
