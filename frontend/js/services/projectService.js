import { API_BASE, authHeaders } from "./api.js";

async function handle(res) {
  const data = await res.json().catch(() => ({}));
  if (!res.ok) {
    throw new Error(data.message || data.error || "Request failed");
  }
  if (data.success === false) {
    throw new Error(data.message || "Operation failed");
  }
  return data;
}

export const ProjectService = {
  async listByUser(userId) {
    const res = await fetch(`${API_BASE}/users/${userId}/projects`, {
      headers: {
        'Content-Type': 'application/json',
        ...authHeaders()
      }
    });
    const data = await handle(res);
    if (Array.isArray(data)) return data;
    return data.projects || [];
  },

  async getByUser(userId) {
    return this.listByUser(userId);
  },

  async create(userId, data) {
    return this.add(userId, data);
  },

  async add(userId, data) {
    const res = await fetch(`${API_BASE}/users/${userId}/projects`, {
      method: "POST",
      headers: {
        'Content-Type': 'application/json',
        ...authHeaders()
      },
      body: JSON.stringify(data)
    });
    return handle(res);
  },

  async update(userId, projectId, data) {
    const res = await fetch(`${API_BASE}/users/${userId}/projects/${projectId}`, {
      method: "PUT",
      headers: {
        'Content-Type': 'application/json',
        ...authHeaders()
      },
      body: JSON.stringify(data)
    });
    return handle(res);
  },

  async remove(userId, projectId) {
    const res = await fetch(`${API_BASE}/users/${userId}/projects/${projectId}`, {
      method: "DELETE",
      headers: {
        'Content-Type': 'application/json',
        ...authHeaders()
      }
    });
    return handle(res);
  }
};
