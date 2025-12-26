import { API_BASE, authHeaders } from "./api.js";

/**
 * Safely parse JSON response and handle errors
 */
async function handle(res) {
  const data = await res.json().catch(() => ({}));
  if (!res.ok) {
    throw new Error(data.message || data.error || `Request failed (${res.status})`);
  }
  if (data.success === false) {
    throw new Error(data.message || "Operation failed");
  }
  return data;
}

export const ContactService = {
  /**
   * Submit contact form (public endpoint)
   * @param {Object} data - { name, email, message, subject? }
   */
  async send(data) {
    const res = await fetch(`${API_BASE}/contact`, {
      method: "POST",
      headers: {
        'Content-Type': 'application/json'
        // No auth needed for public contact form
      },
      body: JSON.stringify(data)
    });
    return handle(res);
  },

  /**
   * List contacts (admin only)
   * @param {number} userId - User ID (for route compatibility, ignored by backend)
   * @param {string|null} status - Optional filter: 'unread', 'read', 'replied'
   */
  async list(userId, status = null) {
    let url = `${API_BASE}/users/${userId}/contacts`;
    if (status) {
      url += `?status=${encodeURIComponent(status)}`;
    }
    const res = await fetch(url, {
      method: "GET",
      headers: {
        'Content-Type': 'application/json',
        ...authHeaders()
      }
    });
    return handle(res);
  },

  /**
   * Delete a contact (admin only)
   * @param {number} userId - User ID (for route compatibility)
   * @param {number} contactId - Contact ID to delete
   */
  async remove(userId, contactId) {
    const res = await fetch(`${API_BASE}/users/${userId}/contacts/${contactId}`, {
      method: "DELETE",
      headers: {
        'Content-Type': 'application/json',
        ...authHeaders()
      }
    });
    return handle(res);
  },

  /**
   * Update contact status (admin only)
   * @param {number} userId - User ID (for route compatibility)
   * @param {number} contactId - Contact ID
   * @param {string} status - New status: 'unread', 'read', 'replied'
   */
  async updateStatus(userId, contactId, status) {
    const res = await fetch(`${API_BASE}/users/${userId}/contacts/${contactId}/status`, {
      method: "PATCH",
      headers: {
        'Content-Type': 'application/json',
        ...authHeaders()
      },
      body: JSON.stringify({ status })
    });
    return handle(res);
  }
};