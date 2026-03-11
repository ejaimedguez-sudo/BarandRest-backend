import { AuditLog } from "../models.js";

function maskBody(body) {
  if (!body || typeof body !== "object") return body;
  const masked = { ...body };
  for (const key of Object.keys(masked)) {
    const low = key.toLowerCase();
    if (low.includes("password") || low.includes("token") || low.includes("secret") || low.includes("key")) {
      masked[key] = "***";
    }
  }
  return masked;
}

export function auditTrail() {
  return (req, res, next) => {
    const started = Date.now();

    res.on("finish", async () => {
      try {
        await AuditLog.create({
          requestId: req.requestId || null,
          method: req.method,
          path: req.originalUrl,
          userId: req.user?.id || null,
          role: req.user?.Role?.name || null,
          statusCode: res.statusCode,
          durationMs: Date.now() - started,
          ipAddress: req.ip,
          userAgent: req.get("user-agent") || null,
          payload: JSON.stringify(maskBody(req.body || {}))
        });
      } catch {
        // Do not block API flow when audit insert fails.
      }
    });

    next();
  };
}
