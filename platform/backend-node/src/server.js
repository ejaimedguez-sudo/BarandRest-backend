import cors from "cors";
import express from "express";
import { env } from "./config/env.js";
import { sequelize } from "./config/db.js";
import { syncDatabase, Role } from "./models.js";
import authRoutes from "./routes/auth.routes.js";
import menuRoutes from "./routes/menu.routes.js";
import operationsRoutes from "./routes/operations.routes.js";
import recipesRoutes from "./routes/recipes.routes.js";
import inventoryRoutes from "./routes/inventory.routes.js";
import billingRoutes from "./routes/billing.routes.js";
import dashboardRoutes from "./routes/dashboard.routes.js";

const app = express();
app.use(cors());
app.use(express.json());

app.get("/health", (_req, res) => res.json({ status: "ok" }));
app.use("/api/auth", authRoutes);
app.use("/api/menu", menuRoutes);
app.use("/api/ops", operationsRoutes);
app.use("/api/recipes", recipesRoutes);
app.use("/api/inventory", inventoryRoutes);
app.use("/api/billing", billingRoutes);
app.use("/api/dashboard", dashboardRoutes);

app.use((err, _req, res, _next) => {
  console.error(err);
  return res.status(500).json({ message: "Error interno" });
});

const baseRoles = ["administrador", "mesero", "cajero", "jefe_barra", "jefe_cocina", "gerente"];

async function boot() {
  await sequelize.authenticate();
  await syncDatabase();
  for (const name of baseRoles) {
    await Role.findOrCreate({ where: { name } });
  }
  app.listen(env.port, () => {
    console.log(`API lista en http://localhost:${env.port}`);
  });
}

boot().catch((err) => {
  console.error("No se pudo iniciar la API", err);
  process.exit(1);
});
