import { env } from "../config/env.js";

async function mockCharge({ amount, method, reference }) {
  return {
    approved: true,
    provider: "mock",
    externalRef: `MOCK-${reference || Date.now()}`,
    amount,
    method,
    raw: { mode: "mock" }
  };
}

async function stripeLikeCharge({ amount, method, reference }) {
  if (!env.paymentApiUrl || !env.paymentApiKey) {
    throw new Error("Proveedor de pago no configurado: falta PAYMENT_API_URL o PAYMENT_API_KEY");
  }

  const response = await fetch(`${env.paymentApiUrl}/charges`, {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
      Authorization: `Bearer ${env.paymentApiKey}`
    },
    body: JSON.stringify({
      amount,
      currency: env.paymentCurrency,
      method,
      reference
    })
  });

  const data = await response.json().catch(() => ({}));
  if (!response.ok) {
    throw new Error(data.message || `Error proveedor pagos: ${response.status}`);
  }

  return {
    approved: data.status === "approved" || data.approved === true,
    provider: "stripe-like",
    externalRef: data.id || data.reference || reference,
    amount,
    method,
    raw: data
  };
}

export async function processPayment(paymentInput) {
  if (env.paymentProvider === "stripe-like") {
    return stripeLikeCharge(paymentInput);
  }
  return mockCharge(paymentInput);
}
