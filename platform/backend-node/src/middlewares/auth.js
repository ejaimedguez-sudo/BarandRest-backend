import { User, Role } from "../models.js";
import { verifyToken } from "../services/auth.js";

export async function authRequired(req, res, next) {
  const tokenHeader = req.headers.authorization || "";
  const token = tokenHeader.startsWith("Bearer ") ? tokenHeader.slice(7) : null;
  if (!token) {
    return res.status(401).json({ message: "Token requerido" });
  }

  try {
    const decoded = verifyToken(token);
    const user = await User.findByPk(decoded.userId, { include: Role });
    if (!user || !user.active) {
      return res.status(401).json({ message: "Usuario no autorizado" });
    }
    req.user = user;
    return next();
  } catch {
    return res.status(401).json({ message: "Token invalido" });
  }
}

export function withRoles(...roles) {
  return (req, res, next) => {
    const role = req.user?.Role?.name;
    if (!role || !roles.includes(role)) {
      return res.status(403).json({ message: "No tienes permisos para esta accion" });
    }
    return next();
  };
}
