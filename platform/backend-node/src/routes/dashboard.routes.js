import { Router } from "express";
import dayjs from "dayjs";
import { Op } from "sequelize";
import { authRequired, withRoles } from "../middlewares/auth.js";
import { Order, OrderItem, RestaurantTable, User } from "../models.js";

const router = Router();

function orderTotal(order) {
  return (order.OrderItems || []).reduce((acc, item) => acc + Number(item.qty) * Number(item.unitPrice), 0);
}

router.get("/sales", authRequired, withRoles("administrador", "gerente", "cajero"), async (req, res) => {
  const from = dayjs(req.query.from || dayjs().startOf("month").format("YYYY-MM-DD")).startOf("day").toDate();
  const to = dayjs(req.query.to || dayjs().endOf("month").format("YYYY-MM-DD")).endOf("day").toDate();

  const current = await Order.findAll({
    where: { createdAt: { [Op.between]: [from, to] }, status: { [Op.not]: "cancelled" } },
    include: [OrderItem, { model: User, as: "waiter", attributes: ["id", "fullName"] }, RestaurantTable]
  });

  const previousDiff = dayjs(to).diff(from, "day") + 1;
  const prevFrom = dayjs(from).subtract(previousDiff, "day").toDate();
  const prevTo = dayjs(from).subtract(1, "day").endOf("day").toDate();

  const previous = await Order.findAll({
    where: { createdAt: { [Op.between]: [prevFrom, prevTo] }, status: { [Op.not]: "cancelled" } },
    include: [OrderItem]
  });

  const currentSales = current.reduce((acc, row) => acc + orderTotal(row), 0);
  const previousSales = previous.reduce((acc, row) => acc + orderTotal(row), 0);
  const growthPct = previousSales > 0 ? ((currentSales - previousSales) / previousSales) * 100 : 100;

  const waiterMap = {};
  current.forEach((row) => {
    const key = row.waiter?.id || "no_waiter";
    if (!waiterMap[key]) {
      waiterMap[key] = {
        waiterId: row.waiter?.id || null,
        waiterName: row.waiter?.fullName || "Sin asignar",
        orders: 0,
        sales: 0,
        commission: 0
      };
    }
    waiterMap[key].orders += 1;
    waiterMap[key].sales += orderTotal(row);
    waiterMap[key].commission = waiterMap[key].sales * 0.05;
  });

  return res.json({
    range: { from, to },
    sales: {
      current: currentSales,
      previous: previousSales,
      growthPct
    },
    kpis: {
      orders: current.length,
      averageTicket: current.length ? currentSales / current.length : 0,
      occupiedTables: new Set(current.map((o) => o.RestaurantTableId).filter(Boolean)).size
    },
    waiters: Object.values(waiterMap)
  });
});

export default router;
