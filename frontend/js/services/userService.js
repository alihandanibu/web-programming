import { API_BASE, authHeaders } from './api.js';

async function handle(res) {
  const data = await res.json().catch(() => ({}));
  if (!res.ok) {
    throw new Error(data.message || data.error || 'Request failed');
  }
  if (data.success === false) {
    throw new Error(data.message || 'Operation failed');
  }
  return data;
}

export const UserService = {
  async list() {
    const res = await fetch(`${API_BASE}/users`, {
      headers: {
        'Content-Type': 'application/json',
        ...authHeaders()
      }
    });
    const data = await handle(res);
    // očekujemo {success:true, users:[...]}
    return data.users || [];
  },

  async get(id) {
    const res = await fetch(`${API_BASE}/users/${id}`, {
      headers: {
        'Content-Type': 'application/json',
        ...authHeaders()
      }
    });
    const data = await handle(res);
    // očekujemo {success:true, user:{...}} ili {success:true, ...}
    return data.user || data;
  },

  async update(id, payload) {
    const res = await fetch(`${API_BASE}/users/${id}`, {
      method: 'PUT',
      headers: {
        'Content-Type': 'application/json',
        ...authHeaders()
      },
      body: JSON.stringify(payload)
    });
    return handle(res);
  },

  async remove(id) {
    const res = await fetch(`${API_BASE}/users/${id}`, {
      method: 'DELETE',
      headers: {
        'Content-Type': 'application/json',
        ...authHeaders()
      }
    });
    return handle(res);
  },

  async updateRole(id, role) {
    const res = await fetch(`${API_BASE}/users/${id}`, {
      method: 'PUT',
      headers: {
        'Content-Type': 'application/json',
        ...authHeaders()
      },
      body: JSON.stringify({ role })
    });
    return handle(res);
  }
};
