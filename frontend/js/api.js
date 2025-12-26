function computeApiBase() {
  // Deployment override (frontend and backend on different domains)
  const override =
    (typeof window !== 'undefined' && (window.__API_BASE__ || window.API_BASE)) ||
    document.querySelector('meta[name="api-base"]')?.getAttribute('content');

  if (typeof override === 'string' && override.trim() !== '') {
    return override.trim().replace(/\/+$/, '');
  }

  // API_BASE is derived from <base href> (XAMPP subfolder setup)
  const baseEl = document.querySelector('base');
  const baseHref = baseEl ? baseEl.getAttribute('href') : null;

  try {
    const baseUrl = new URL(baseHref || document.baseURI, document.baseURI);
    let path = baseUrl.pathname.replace(/\/+$/, '');
    if (path.endsWith('/frontend')) {
      path = path.slice(0, -'/frontend'.length);
    }
    return window.location.origin + path + '/backend';
  } catch (e) {
    return window.location.origin + '/mojnoviprojekat/web-programming/backend';
  }
}

const API_BASE = computeApiBase();

async function apiFetchJson(path, options = {}) {
  const res = await fetch(`${API_BASE}${path}`, options);

  // Read as text first to avoid JSON parse errors on empty bodies.
  const text = await res.text();
  const contentType = (res.headers.get('content-type') || '').toLowerCase();

  let data = null;
  if (text && contentType.includes('application/json')) {
    try {
      data = JSON.parse(text);
    } catch (e) {
      throw new Error(`Invalid JSON from API (${res.status})`);
    }
  } else if (text && contentType.includes('text/plain')) {
    data = { message: text };
  }

  if (!res.ok) {
    const msg = (data && (data.message || data.error)) ? (data.message || data.error) : (text || `Request failed (${res.status})`);

    // If token expired/invalid, auto-logout and redirect to login.
    if (res.status === 401) {
      localStorage.removeItem('token');
      localStorage.removeItem('user');
      if (window.location.hash !== '#login') {
        window.location.hash = '#login';
      }
    }

    throw new Error(msg);
  }

  // For endpoints that return 204/empty body
  return data ?? {};
}

// JWT token
function getToken() {
  return localStorage.getItem('token');
}

function getHeaders(auth = false) {
  const headers = {
    'Content-Type': 'application/json'
  };
  if (auth && getToken()) {
    headers['Authorization'] = 'Bearer ' + getToken();
  }
  return headers;
}

// Login user
function apiLogin(email, password) {
  return apiFetchJson('/auth/login', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({ email, password })
  });
}

function apiRegister(data) {
  return apiFetchJson('/auth/register', {
    method: 'POST',
    headers: getHeaders(),
    body: JSON.stringify(data)
  });
}

function apiVerifyToken() {
  return apiFetchJson('/auth/verify', {
    method: 'POST',
    headers: getHeaders(true)
  });
}

// Get current authenticated user
function apiGetCurrentUser() {
  const user = localStorage.getItem('user');
  if (!user) {
    return Promise.reject({ success: false, message: 'No user in localStorage' });
  }
  const userData = JSON.parse(user);
  return apiFetchJson(`/users/${userData.id}`, {
    headers: getHeaders(true)
  }).catch((err) => {
    // Align behavior with previous logic: kick to login on 401-ish messages.
    if ((err && String(err.message || '').toLowerCase().includes('missing token')) ||
      (err && String(err.message || '').toLowerCase().includes('invalid or expired')) ||
      (err && String(err.message || '').toLowerCase().includes('unauthorized'))) {
      localStorage.removeItem('token');
      localStorage.removeItem('user');
      window.location.hash = '#login';
    }
    throw err;
  });
}

// Get user by ID
function apiGetUser(id) {
  return apiFetchJson(`/users/${id}`, {
    headers: getHeaders(true)
  });
}

function apiGetAllUsers() {
  return apiFetchJson('/users', {
    headers: getHeaders(true)
  });
}

// ===== PROJECTS =====
function apiGetProjects(userId) {
  return apiFetchJson(`/users/${userId}/projects`, {
    headers: getHeaders(true)
  });
}

function apiAddProject(userId, data) {
  return apiFetchJson(`/users/${userId}/projects`, {
    method: 'POST',
    headers: getHeaders(true),
    body: JSON.stringify(data)
  });
}

function apiDeleteProject(userId, projectId) {
  return apiFetchJson(`/users/${userId}/projects/${projectId}`, {
    method: 'DELETE',
    headers: getHeaders(true)
  });
}

// ===== SKILLS =====
function apiGetSkills(userId) {
  return apiFetchJson(`/users/${userId}/skills`, {
    headers: getHeaders(true)
  });
}

function apiAddSkill(userId, data) {
  return apiFetchJson(`/users/${userId}/skills`, {
    method: 'POST',
    headers: getHeaders(true),
    body: JSON.stringify(data)
  });
}

function apiDeleteSkill(userId, skillId) {
  return apiFetchJson(`/users/${userId}/skills/${skillId}`, {
    method: 'DELETE',
    headers: getHeaders(true)
  });
}

// ===== EXPERIENCES =====
function apiGetExperiences(userId) {
  return apiFetchJson(`/users/${userId}/experiences`, {
    headers: getHeaders(true)
  });
}

// ===== CONTACT =====
function apiSendContact(data) {
  return apiFetchJson('/contact', {
    method: 'POST',
    headers: getHeaders(),
    body: JSON.stringify(data)
  });
}
