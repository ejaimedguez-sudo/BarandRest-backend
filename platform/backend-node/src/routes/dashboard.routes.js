import { Router } from "express";
import dayjs from "dayjs";
import { Op } from "sequelize";
import { authRequired, withRoles } from "../middlewares/auth.js";
import { AuditLog, Order, OrderItem, RestaurantTable, User } from "../models.js";

const router = Router();

function orderTotal(order) {
  return (order.OrderItems || []).reduce((acc, item) => acc + Number(item.qty) * Number(item.unitPrice), 0);
}

function formatBucketKey(date, granularity) {
  const d = dayjs(date);
  if (granularity === "daily") return d.format("YYYY-MM-DD");
  if (granularity === "weekly") return d.startOf("week").format("YYYY-MM-DD");
  if (granularity === "yearly") return d.format("YYYY");
  return d.format("YYYY-MM");
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

router.get("/sales/timeseries", authRequired, withRoles("administrador", "gerente", "cajero"), async (req, res) => {
  const from = dayjs(req.query.from || dayjs().startOf("month").format("YYYY-MM-DD")).startOf("day").toDate();
  const to = dayjs(req.query.to || dayjs().endOf("month").format("YYYY-MM-DD")).endOf("day").toDate();
  const granularity = ["daily", "weekly", "monthly", "yearly"].includes(req.query.granularity)
    ? req.query.granularity
    : "daily";

  const rows = await Order.findAll({
    where: { createdAt: { [Op.between]: [from, to] }, status: { [Op.not]: "cancelled" } },
    include: [OrderItem]
  });

  const buckets = {};
  for (const row of rows) {
    const key = formatBucketKey(row.createdAt, granularity);
    if (!buckets[key]) buckets[key] = { period: key, sales: 0, orders: 0 };
    buckets[key].sales += orderTotal(row);
    buckets[key].orders += 1;
  }

  return res.json({
    range: { from, to, granularity },
    series: Object.values(buckets).sort((a, b) => a.period.localeCompare(b.period))
  });
});

router.get("/waiters/commissions", authRequired, withRoles("administrador", "gerente"), async (req, res) => {
  const from = dayjs(req.query.from || dayjs().startOf("month").format("YYYY-MM-DD")).startOf("day").toDate();
  const to = dayjs(req.query.to || dayjs().endOf("month").format("YYYY-MM-DD")).endOf("day").toDate();
  const commissionPct = Number(req.query.commissionPct || 5);

  const rows = await Order.findAll({
    where: { createdAt: { [Op.between]: [from, to] }, status: { [Op.not]: "cancelled" } },
    include: [OrderItem, { model: User, as: "waiter", attributes: ["id", "fullName"] }]
  });

  const waiterMap = {};
  rows.forEach((row) => {
    const key = row.waiter?.id || "no_waiter";
    if (!waiterMap[key]) {
      waiterMap[key] = {
        waiterId: row.waiter?.id || null,
        waiterName: row.waiter?.fullName || "Sin asignar",
        orders: 0,
        sales: 0,
        commission: 0,
        commissionPct
      };
    }

    waiterMap[key].orders += 1;
    waiterMap[key].sales += orderTotal(row);
    waiterMap[key].commission = waiterMap[key].sales * (commissionPct / 100);
  });

  return res.json({
    range: { from, to },
    commissionPct,
    waiters: Object.values(waiterMap)
  });
});

router.get("/audit/recent", authRequired, withRoles("administrador", "gerente"), async (req, res) => {
  const limit = Math.min(Number(req.query.limit || 100), 500);
  const rows = await AuditLog.findAll({ order: [["createdAt", "DESC"]], limit });
  return res.json(rows);
});

export default router;
