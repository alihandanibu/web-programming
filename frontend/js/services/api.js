export function computeApiBase() {
  // Deployment override (e.g. DigitalOcean: frontend and backend on different domains)
  const override =
    (typeof window !== 'undefined' && (window.__ENV && (window.__ENV.API_URL || window.__ENV.VITE_API_URL))) ||
    (typeof window !== 'undefined' && (window.__API_BASE__ || window.API_BASE)) ||
    document.querySelector('meta[name="api-base"]')?.getAttribute('content');

  if (typeof override === 'string' && override.trim() !== '') {
    return override.trim().replace(/\/+$/, '');
  }

  const baseEl = document.querySelector('base');
  const baseHref = baseEl ? baseEl.getAttribute('href') : null;
  const baseUrl = new URL(baseHref || document.baseURI, document.baseURI);
  let path = baseUrl.pathname.replace(/\/+$/, '');
  if (path.endsWith('/frontend')) {
    path = path.slice(0, -'/frontend'.length);
  }
  return window.location.origin + path + '/backend';
}

export const API_BASE = computeApiBase();

export function setAuth(token, user) {
  localStorage.setItem('token', token);
  localStorage.setItem('user', JSON.stringify(user));
}

export function clearAuth() {
  localStorage.removeItem('token');
  localStorage.removeItem('user');
}

export function getStoredUser() {
  const u = localStorage.getItem('user');
  return u ? JSON.parse(u) : null;
}

export function authHeaders() {
  const token = localStorage.getItem('token');
  return token ? { Authorization: `Bearer ${token}` } : {};
}
