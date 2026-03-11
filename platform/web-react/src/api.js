export const API_BASE = import.meta.env.VITE_API_BASE || "http://localhost:4100/api";

export async function apiFetch(path, options = {}) {
  const token = localStorage.getItem("barandrest_token");
  const headers = {
    "Content-Type": "application/json",
    ...(options.headers || {})
  };
  if (token) headers.Authorization = `Bearer ${token}`;

  const res = await fetch(`${API_BASE}${path}`, { ...options, headers });
  const data = await res.json().catch(() => ({}));
  if (!res.ok) throw new Error(data.message || "Request failed");
  return data;
}

export const endpoints = {
  login: (payload) => apiFetch("/auth/login", { method: "POST", body: JSON.stringify(payload) }),
  sales: (from, to) => apiFetch(`/dashboard/sales?from=${from}&to=${to}`),
  menuPublic: () => apiFetch("/menu/public"),
  orders: () => apiFetch("/ops/orders")
};
