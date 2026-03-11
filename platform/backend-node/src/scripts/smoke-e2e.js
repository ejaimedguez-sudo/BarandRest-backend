const API_BASE = process.env.API_BASE || "http://localhost:4100";

function assert(condition, message) {
  if (!condition) throw new Error(message);
}

async function request(path, options = {}) {
  const mergedHeaders = {
    "Content-Type": "application/json",
    ...(options.headers || {})
  };

  const response = await fetch(`${API_BASE}${path}`, {
    ...options,
    headers: mergedHeaders
  });

  const data = await response.json().catch(() => ({}));
  if (!response.ok) {
    throw new Error(`${response.status} ${response.statusText} at ${path}: ${JSON.stringify(data)}`);
  }
  return data;
}

async function run() {
  console.log("[1/11] Health check...");
  const health = await request("/health");
  assert(health.status === "ok", "Health check fallo");

  console.log("[2/11] Login admin demo...");
  const login = await request("/api/auth/login", {
    method: "POST",
    body: JSON.stringify({ email: "admin@barandrest.local", password: "Demo12345" })
  });
  assert(login.token, "No se obtuvo token");
  const token = login.token;

  console.log("[3/11] Leer menu publico...");
  const menu = await request("/api/menu/public");
  assert(Array.isArray(menu) && menu.length > 0, "Menu publico vacio");

  console.log("[4/11] Crear orden comensal QR...");
  const guestOrder = await request("/api/ops/orders/guest", {
    method: "POST",
    body: JSON.stringify({
      tableId: 1,
      useWaiter: false,
      notes: "Smoke test order",
      items: [{ menuItemId: menu[0].id, qty: 1, unitPrice: Number(menu[0].price || 0) || 100 }]
    })
  });
  assert(guestOrder.orderId, "No se creo orden guest");

  console.log("[5/11] Tomar orden como mesero/caja via endpoint protegido...");
  const securedOrder = await request("/api/ops/orders", {
    method: "POST",
    headers: { Authorization: `Bearer ${token}` },
    body: JSON.stringify({
      source: "cashier",
      tableId: 1,
      waiterId: 3,
      notes: "Smoke secured order",
      items: [{ menuItemId: menu[0].id, qty: 2, unitPrice: Number(menu[0].price || 0) || 100 }]
    })
  });
  assert(securedOrder.order?.id, "No se creo orden protegida");

  console.log("[6/11] Agregar items adicionales como caja...");
  const addItems = await request(`/api/ops/orders/${securedOrder.order.id}/add-items`, {
    method: "POST",
    headers: { Authorization: `Bearer ${token}` },
    body: JSON.stringify({ items: [{ menuItemId: menu[0].id, qty: 1, unitPrice: Number(menu[0].price || 0) || 100 }] })
  });
  assert(addItems.order?.id, "No se agregaron items adicionales");

  console.log("[7/11] Generar ticket para orden...");
  const ticket = await request(`/api/billing/tickets/${securedOrder.order.id}`, {
    method: "POST",
    headers: { Authorization: `Bearer ${token}` }
  });
  assert(ticket.id, "No se genero ticket");

  console.log("[8/11] Registrar pago y factura...");
  const payment = await request(`/api/billing/payments/${ticket.id}`, {
    method: "POST",
    headers: { Authorization: `Bearer ${token}` },
    body: JSON.stringify({ method: "card", amount: ticket.total, status: "approved" })
  });
  assert(payment.id, "No se registro pago");

  const invoice = await request(`/api/billing/invoices/${ticket.id}`, {
    method: "POST",
    headers: { Authorization: `Bearer ${token}` },
    body: JSON.stringify({
      fiscalName: "Cliente Demo SA de CV",
      rfc: "XAXX010101000",
      email: "cliente.demo@example.com"
    })
  });
  assert(invoice.id, "No se genero factura");

  console.log("[9/11] Consultar dashboard BI...");
  const from = new Date(new Date().getFullYear(), new Date().getMonth(), 1).toISOString().slice(0, 10);
  const to = new Date().toISOString().slice(0, 10);
  const dashboard = await request(`/api/dashboard/sales?from=${from}&to=${to}`, {
    headers: { Authorization: `Bearer ${token}` }
  });
  assert(dashboard.sales && typeof dashboard.sales.current !== "undefined", "Dashboard sin metricas");

  console.log("[10/11] Consultar series temporales y comisiones...");
  const series = await request(`/api/dashboard/sales/timeseries?from=${from}&to=${to}&granularity=daily`, {
    headers: { Authorization: `Bearer ${token}` }
  });
  assert(Array.isArray(series.series), "Series temporales no disponibles");

  const commissions = await request(`/api/dashboard/waiters/commissions?from=${from}&to=${to}&commissionPct=5`, {
    headers: { Authorization: `Bearer ${token}` }
  });
  assert(Array.isArray(commissions.waiters), "Comisiones por mesero no disponibles");

  console.log("[11/11] Consultar auditoria reciente...");
  const audits = await request("/api/dashboard/audit/recent?limit=20", {
    headers: { Authorization: `Bearer ${token}` }
  });
  assert(Array.isArray(audits), "Auditoria no disponible");

  console.log("Smoke E2E OK");
  console.log(JSON.stringify({
    guestOrderId: guestOrder.orderId,
    securedOrderId: securedOrder.order.id,
    ticketId: ticket.id,
    paymentId: payment.id,
    invoiceId: invoice.id,
    salesCurrent: dashboard.sales.current
  }, null, 2));
}

run().catch((err) => {
  if (String(err.message || "").includes("fetch failed")) {
    console.error("Smoke E2E FAIL API no disponible. Inicia primero el backend: npm start");
  } else {
    console.error("Smoke E2E FAIL", err.message);
  }
  process.exit(1);
});
