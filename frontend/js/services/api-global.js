/**
 * API Configuration and Auth Helpers
 * All fetch() calls are in the service functions at the bottom.
 * Controllers should use the api* wrapper functions.
 */

function computeApiBase() {
  // Check for deployment override via meta tag (DigitalOcean App Platform)
  const metaBase = document.querySelector('meta[name="api-base"]');
  if (metaBase && metaBase.content && metaBase.content.trim() !== '') {
    return metaBase.content.trim().replace(/\/+$/, '');
  }

  // Fallback: derive from <base href> for local XAMPP
  const baseEl = document.querySelector('base');
  const baseHref = baseEl ? baseEl.getAttribute('href') : null;

  try {
    const baseUrl = new URL(baseHref || '/', window.location.origin);
    let path = baseUrl.pathname.replace(/\/+$/, '');
    if (path.endsWith('/frontend')) {
      path = path.slice(0, -'/frontend'.length);
    }
    return window.location.origin + path + '/backend';
  } catch (e) {
    return window.location.origin + '/backend';
  }
}

const API_BASE = computeApiBase();

// ========== AUTH HELPERS ==========
function getToken() {
  return localStorage.getItem('token');
}

function getHeaders(auth = false) {
  const headers = { 'Content-Type': 'application/json' };
  if (auth && getToken()) {
    headers['Authorization'] = 'Bearer ' + getToken();
  }
  return headers;
}

function getStoredUser() {
  const u = localStorage.getItem('user');
  try {
    return u ? JSON.parse(u) : null;
  } catch (e) {
    return null;
  }
}

// ========== SERVICE LAYER (ALL FETCH CALLS HERE) ==========

// --- Auth Service ---
async function _authLogin(email, password) {
  const res = await fetch(`${API_BASE}/auth/login`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ email, password })
  });
  return res.json();
}

async function _authRegister(data) {
  const res = await fetch(`${API_BASE}/auth/register`, {
    method: 'POST',
    headers: getHeaders(),
    body: JSON.stringify(data)
  });
  return res.json();
}

async function _authVerify() {
  const res = await fetch(`${API_BASE}/auth/verify`, {
    method: 'POST',
    headers: getHeaders(true)
  });
  return res.json();
}

// --- User Service ---
async function _userGetCurrent() {
  const user = getStoredUser();
  if (!user || !user.id) {
    return { success: false, message: 'No user in localStorage' };
  }
  const res = await fetch(`${API_BASE}/users/${user.id}`, {
    headers: getHeaders(true)
  });
  if (!res.ok && res.status === 401) {
    localStorage.removeItem('token');
    localStorage.removeItem('user');
  }
  return res.json();
}

async function _userGetById(id) {
  const res = await fetch(`${API_BASE}/users/${id}`, {
    headers: getHeaders(true)
  });
  return res.json();
}

async function _userGetAll() {
  const res = await fetch(`${API_BASE}/users`, {
    headers: getHeaders(true)
  });
  return res.json();
}

async function _userDelete(id) {
  const res = await fetch(`${API_BASE}/users/${id}`, {
    method: 'DELETE',
    headers: getHeaders(true)
  });
  return res.json();
}

// --- Project Service ---
async function _projectGetAll(userId) {
  const res = await fetch(`${API_BASE}/users/${userId}/projects`, {
    headers: getHeaders(true)
  });
  return res.json();
}

async function _projectAdd(userId, data) {
  const res = await fetch(`${API_BASE}/users/${userId}/projects`, {
    method: 'POST',
    headers: getHeaders(true),
    body: JSON.stringify(data)
  });
  return res.json();
}

async function _projectDelete(userId, projectId) {
  const res = await fetch(`${API_BASE}/users/${userId}/projects/${projectId}`, {
    method: 'DELETE',
    headers: getHeaders(true)
  });
  return res.json();
}

// --- Skill Service ---
async function _skillGetAll(userId) {
  const res = await fetch(`${API_BASE}/users/${userId}/skills`, {
    headers: getHeaders(true)
  });
  return res.json();
}

async function _skillAdd(userId, data) {
  const res = await fetch(`${API_BASE}/users/${userId}/skills`, {
    method: 'POST',
    headers: getHeaders(true),
    body: JSON.stringify(data)
  });
  return res.json();
}

async function _skillDelete(userId, skillId) {
  const res = await fetch(`${API_BASE}/users/${userId}/skills/${skillId}`, {
    method: 'DELETE',
    headers: getHeaders(true)
  });
  return res.json();
}

// --- Experience Service ---
async function _experienceGetAll(userId) {
  const res = await fetch(`${API_BASE}/users/${userId}/experiences`, {
    headers: getHeaders(true)
  });
  return res.json();
}

// --- Contact Service ---
async function _contactSend(data) {
  const res = await fetch(`${API_BASE}/contact`, {
    method: 'POST',
    headers: getHeaders(),
    body: JSON.stringify(data)
  });
  return res.json();
}

// ========== PUBLIC API (Controllers call these) ==========

// Auth
function apiLogin(email, password) { return _authLogin(email, password); }
function apiRegister(data) { return _authRegister(data); }
function apiVerifyToken() { return _authVerify(); }

// Users
function apiGetCurrentUser() { return _userGetCurrent(); }
function apiGetUser(id) { return _userGetById(id); }
function apiGetAllUsers() { return _userGetAll(); }
function apiDeleteUser(id) { return _userDelete(id); }

// Projects
function apiGetProjects(userId) { return _projectGetAll(userId); }
function apiAddProject(userId, data) { return _projectAdd(userId, data); }
function apiDeleteProject(userId, projectId) { return _projectDelete(userId, projectId); }

// Skills
function apiGetSkills(userId) { return _skillGetAll(userId); }
function apiAddSkill(userId, data) { return _skillAdd(userId, data); }
function apiDeleteSkill(userId, skillId) { return _skillDelete(userId, skillId); }

// Experiences
function apiGetExperiences(userId) { return _experienceGetAll(userId); }

// Contact
function apiSendContact(data) { return _contactSend(data); }
