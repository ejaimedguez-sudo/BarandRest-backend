export function waiterKpisFromOrders(orders) {
  const map = {};
  for (const order of orders) {
    const key = order.waiter?.id || "no_waiter";
    if (!map[key]) {
      map[key] = {
        waiterId: order.waiter?.id || null,
        waiterName: order.waiter?.fullName || "Sin asignar",
        ordersCount: 0,
        salesTotal: 0,
        commission: 0
      };
    }

    const total = (order.OrderItems || []).reduce(
      (acc, item) => acc + Number(item.qty) * Number(item.unitPrice),
      0
    );

    map[key].ordersCount += 1;
    map[key].salesTotal += total;
    map[key].commission = map[key].salesTotal * 0.05;
  }

  return Object.values(map);
}
