import { env } from "../config/env.js";

async function mockStamp({ ticket, fiscalData }) {
  return {
    provider: "mock",
    uuid: `FAKE-${ticket.folio}`,
    pdfUrl: null,
    xmlUrl: null,
    raw: {
      mode: "mock",
      ticketFolio: ticket.folio,
      fiscalData
    }
  };
}

async function apiStamp({ ticket, fiscalData }) {
  if (!env.cfdiApiUrl || !env.cfdiApiKey) {
    throw new Error("Proveedor CFDI no configurado: falta CFDI_API_URL o CFDI_API_KEY");
  }

  const response = await fetch(`${env.cfdiApiUrl}/stamp`, {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
      Authorization: `Bearer ${env.cfdiApiKey}`
    },
    body: JSON.stringify({
      folio: ticket.folio,
      subtotal: ticket.subtotal,
      tax: ticket.tax,
      total: ticket.total,
      ...fiscalData
    })
  });

  const data = await response.json().catch(() => ({}));
  if (!response.ok) {
    throw new Error(data.message || `Error proveedor CFDI: ${response.status}`);
  }

  return {
    provider: "api",
    uuid: data.uuid,
    pdfUrl: data.pdfUrl || null,
    xmlUrl: data.xmlUrl || null,
    raw: data
  };
}

export async function stampInvoice(stampInput) {
  if (env.cfdiProvider === "api") {
    return apiStamp(stampInput);
  }
  return mockStamp(stampInput);
}
