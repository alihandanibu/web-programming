import { API_BASE, setAuth, clearAuth, authHeaders } from './api.js';

export const AuthService = {
  async login(email, password) {
    const res = await fetch(`${API_BASE}/auth/login`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ email, password })
    });

    const data = await res.json();
    if (!data.success) throw new Error(data.message);

    setAuth(data.token, data.user);
    return data.user;
  },

  async register(payload) {
    const res = await fetch(`${API_BASE}/auth/register`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload)
    });

    const data = await res.json();
    if (!data.success) throw new Error(data.message);
    return data;
  },

  logout() {
    clearAuth();
  },

  async verify() {
    const res = await fetch(`${API_BASE}/auth/verify`, {
      method: 'POST',
      headers: authHeaders()
    });
    return res.json();
  }
};
