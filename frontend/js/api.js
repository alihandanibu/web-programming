const API_BASE = 'http://localhost:3000';

// Get JWT token from localStorage
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
  return fetch(`${API_BASE}/auth/login`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({ email, password })
  }).then(res => res.json());
}

function apiRegister(data) {
  return fetch(`${API_BASE}/auth/register`, {
    method: 'POST',
    headers: getHeaders(),
    body: JSON.stringify(data)
  }).then(r => r.json());
}

function apiVerifyToken() {
  return fetch(`${API_BASE}/auth/verify`, {
    method: 'POST',
    headers: getHeaders(true)
  }).then(r => r.json());
}

// Get current authenticated user
function apiGetCurrentUser() {
  const user = localStorage.getItem('user');
  if (!user) {
    return Promise.reject({ success: false, message: 'No user in localStorage' });
  }
  const userData = JSON.parse(user);
  return fetch(`${API_BASE}/users/${userData.id}`, {
    headers: getHeaders(true)
  }).then(r => {
    if (!r.ok) {
      if (r.status === 401) {
        localStorage.removeItem('token');
        localStorage.removeItem('user');
        window.location.hash = '#login';
      }
      throw new Error('Failed to fetch user');
    }
    return r.json();
  });
}

// Get user by ID
function apiGetUser(id) {
  return fetch(`${API_BASE}/users/${id}`, {
    headers: getHeaders(true)
  }).then(r => r.json());
}

function apiGetAllUsers() {
  return fetch(`${API_BASE}/users`, {
    headers: getHeaders(true)
  }).then(r => r.json());
}

// ===== PROJECTS =====
function apiGetProjects(userId) {
  return fetch(`${API_BASE}/users/${userId}/projects`, {
    headers: getHeaders(true)
  }).then(r => r.json());
}

// ===== SKILLS =====
function apiGetSkills(userId) {
  return fetch(`${API_BASE}/users/${userId}/skills`, {
    headers: getHeaders(true)
  }).then(r => r.json());
}

// ===== CONTACT =====
function apiSendContact(data) {
  return fetch(`${API_BASE}/contact`, {
    method: 'POST',
    headers: getHeaders(),
    body: JSON.stringify(data)
  }).then(r => r.json());
}
