import dotenv from "dotenv";

dotenv.config();

export const env = {
  port: Number(process.env.PORT || 4100),
  dbHost: process.env.DB_HOST || "127.0.0.1",
  dbPort: Number(process.env.DB_PORT || 3306),
  dbName: process.env.DB_NAME || "barandrest_platform",
  dbUser: process.env.DB_USER || "root",
  dbPassword: process.env.DB_PASSWORD || "",
  jwtSecret: process.env.JWT_SECRET || "change_this_secret",
  clientBaseUrl: process.env.CLIENT_BASE_URL || "http://localhost:5173"
};
