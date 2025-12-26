import { API_BASE, authHeaders } from './api.js';

async function handle(res) {
  const data = await res.json().catch(() => ({}));
  if (!res.ok) throw new Error(data.message || data.error || 'Request failed');
  if (data.success === false) throw new Error(data.message || 'Operation failed');
  return data;
}

export const SkillService = {
  async listByUser(userId) {
    const res = await fetch(`${API_BASE}/users/${userId}/skills`, {
      headers: {
        'Content-Type': 'application/json',
        ...authHeaders()
      }
    });
    const data = await handle(res);
    if (Array.isArray(data)) return data;
    return data.skills || [];
  },

  async create(userId, payload) {
    const res = await fetch(`${API_BASE}/users/${userId}/skills`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        ...authHeaders()
      },
      body: JSON.stringify(payload)
    });
    return handle(res);
  },

  async update(userId, skillId, payload) {
    const res = await fetch(`${API_BASE}/users/${userId}/skills/${skillId}`, {
      method: 'PUT',
      headers: {
        'Content-Type': 'application/json',
        ...authHeaders()
      },
      body: JSON.stringify(payload)
    });
    return handle(res);
  },

  async remove(userId, skillId) {
    const res = await fetch(`${API_BASE}/users/${userId}/skills/${skillId}`, {
      method: 'DELETE',
      headers: {
        'Content-Type': 'application/json',
        ...authHeaders()
      }
    });
    return handle(res);
  }
};
