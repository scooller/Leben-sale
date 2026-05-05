import api from '../lib/api';

class ContactSubmissionsService {
  async create(fields, turnstileToken = null, channel = null) {
    const response = await api.post('/contact-submissions', {
      fields,
      ...(channel ? { channel } : {}),
      ...(turnstileToken ? { turnstile_token: turnstileToken } : {}),
    });

    return response.data;
  }
}

export default new ContactSubmissionsService();
