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

export const ExperienceService = {
  async getByUser(userId) {
    const res = await fetch(`${API_BASE}/users/${userId}/experiences`, {
      headers: {
        'Content-Type': 'application/json',
        ...authHeaders()
      }
    });
    return handle(res);
  }
};
