import { Router } from "express";
import { z } from "zod";
import { Role, User } from "../models.js";
import { hashPassword, signToken, verifyPassword } from "../services/auth.js";

const router = Router();

router.post("/register", async (req, res) => {
  const bodySchema = z.object({
    fullName: z.string().min(3),
    email: z.string().email(),
    password: z.string().min(6),
    role: z.enum(["administrador", "mesero", "cajero", "jefe_barra", "jefe_cocina", "gerente"])
  });

  const parsed = bodySchema.safeParse(req.body);
  if (!parsed.success) {
    return res.status(400).json({ message: "Payload invalido", errors: parsed.error.issues });
  }

  const role = await Role.findOne({ where: { name: parsed.data.role } });
  if (!role) return res.status(400).json({ message: "Rol no existe" });

  const exists = await User.findOne({ where: { email: parsed.data.email } });
  if (exists) return res.status(409).json({ message: "Email ya existe" });

  const user = await User.create({
    fullName: parsed.data.fullName,
    email: parsed.data.email,
    passwordHash: await hashPassword(parsed.data.password),
    RoleId: role.id
  });

  return res.status(201).json({ id: user.id, email: user.email });
});

router.post("/login", async (req, res) => {
  const bodySchema = z.object({ email: z.string().email(), password: z.string().min(1) });
  const parsed = bodySchema.safeParse(req.body);
  if (!parsed.success) return res.status(400).json({ message: "Payload invalido" });

  const user = await User.findOne({ where: { email: parsed.data.email }, include: Role });
  if (!user) return res.status(401).json({ message: "Credenciales invalidas" });

  const ok = await verifyPassword(parsed.data.password, user.passwordHash);
  if (!ok) return res.status(401).json({ message: "Credenciales invalidas" });

  const token = signToken({ userId: user.id, role: user.Role.name });
  return res.json({ token, user: { id: user.id, fullName: user.fullName, role: user.Role.name } });
});

export default router;
