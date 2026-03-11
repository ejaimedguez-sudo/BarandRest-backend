import dotenv from "dotenv";

dotenv.config();

export const env = {
  port: Number(process.env.PORT || 4100),
  dbHost: process.env.DB_HOST || "localhost",
  dbPort: Number(process.env.DB_PORT || 3306),
  dbName: process.env.DB_NAME || "barandrest_platform",
  dbUser: process.env.DB_USER || "root",
  dbPassword: process.env.DB_PASSWORD || "",
  jwtSecret: process.env.JWT_SECRET || "change_this_secret",
  clientBaseUrl: process.env.CLIENT_BASE_URL || "http://localhost:5173",
  corsOrigins: process.env.CORS_ORIGINS || "http://localhost:5173,http://localhost:19006",
  rateLimitWindowMs: Number(process.env.RATE_LIMIT_WINDOW_MS || 60000),
  rateLimitMax: Number(process.env.RATE_LIMIT_MAX || 120),
  paymentProvider: process.env.PAYMENT_PROVIDER || "mock",
  paymentApiUrl: process.env.PAYMENT_API_URL || "",
  paymentApiKey: process.env.PAYMENT_API_KEY || "",
  paymentCurrency: process.env.PAYMENT_CURRENCY || "MXN",
  cfdiProvider: process.env.CFDI_PROVIDER || "mock",
  cfdiApiUrl: process.env.CFDI_API_URL || "",
  cfdiApiKey: process.env.CFDI_API_KEY || ""
};
