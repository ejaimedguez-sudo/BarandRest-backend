import dayjs from "dayjs";
import {
  AccountsPayable,
  Category,
  Ingredient,
  InventoryMovement,
  MenuItem,
  Order,
  OrderItem,
  Payment,
  PurchaseOrder,
  Recipe,
  RecipeIngredient,
  RestaurantTable,
  Role,
  Supplier,
  Ticket,
  User
} from "../models.js";
import { sequelize } from "../config/db.js";
import { hashPassword } from "../services/auth.js";

const roles = ["administrador", "mesero", "cajero", "jefe_barra", "jefe_cocina", "gerente"];

const demoUsers = [
  { fullName: "Administrador Demo", email: "admin@barandrest.local", role: "administrador" },
  { fullName: "Gerente Demo", email: "gerente@barandrest.local", role: "gerente" },
  { fullName: "Mesero Demo", email: "mesero@barandrest.local", role: "mesero" },
  { fullName: "Cajero Demo", email: "cajero@barandrest.local", role: "cajero" },
  { fullName: "Jefe Barra Demo", email: "barra@barandrest.local", role: "jefe_barra" },
  { fullName: "Jefe Cocina Demo", email: "cocina@barandrest.local", role: "jefe_cocina" }
];

async function ensureRoleMap() {
  const map = {};
  for (const name of roles) {
    const [row] = await Role.findOrCreate({ where: { name } });
    map[name] = row;
  }
  return map;
}

async function ensureDemoUsers(roleMap) {
  const users = {};
  for (const demo of demoUsers) {
    const [user, created] = await User.findOrCreate({
      where: { email: demo.email },
      defaults: {
        fullName: demo.fullName,
        email: demo.email,
        passwordHash: await hashPassword("Demo12345"),
        RoleId: roleMap[demo.role].id,
        active: true
      }
    });

    if (!created && user.RoleId !== roleMap[demo.role].id) {
      user.RoleId = roleMap[demo.role].id;
      await user.save();
    }
    users[demo.role] = user;
  }
  return users;
}

async function ensureOperationalData(users) {
  const [mesa1] = await RestaurantTable.findOrCreate({
    where: { tableNumber: "M-01" },
    defaults: { status: "occupied", assignedWaiterId: users.mesero.id }
  });
  const [mesa2] = await RestaurantTable.findOrCreate({
    where: { tableNumber: "M-02" },
    defaults: { status: "available", assignedWaiterId: users.mesero.id }
  });

  mesa1.assignedWaiterId = users.mesero.id;
  mesa1.status = "occupied";
  await mesa1.save();
  mesa2.assignedWaiterId = users.mesero.id;
  await mesa2.save();

  const [catFood] = await Category.findOrCreate({ where: { name: "Cocina", type: "food" } });
  const [catDrink] = await Category.findOrCreate({ where: { name: "Cocteles", type: "drink" } });

  const [burger] = await MenuItem.findOrCreate({
    where: { name: "Hamburguesa Clasica" },
    defaults: {
      description: "Carne de res, queso y vegetales",
      price: 165,
      active: true,
      CategoryId: catFood.id
    }
  });
  const [mojito] = await MenuItem.findOrCreate({
    where: { name: "Mojito de la Casa" },
    defaults: {
      description: "Ron blanco, hierbabuena, limon y soda",
      price: 140,
      active: true,
      CategoryId: catDrink.id
    }
  });

  const [carne] = await Ingredient.findOrCreate({ where: { name: "Carne molida" }, defaults: { unitCost: 0.18, stockQty: 8000, unit: "g" } });
  const [pan] = await Ingredient.findOrCreate({ where: { name: "Pan brioche" }, defaults: { unitCost: 8, stockQty: 120, unit: "u" } });
  const [ron] = await Ingredient.findOrCreate({ where: { name: "Ron blanco" }, defaults: { unitCost: 0.65, stockQty: 5000, unit: "ml" } });
  const [hierbabuena] = await Ingredient.findOrCreate({ where: { name: "Hierbabuena" }, defaults: { unitCost: 0.5, stockQty: 150, unit: "rama" } });

  const [recBurger] = await Recipe.findOrCreate({
    where: { name: "Receta Hamburguesa Clasica", area: "cocina", MenuItemId: burger.id },
    defaults: { instructions: "Sellar carne y montar en pan.", salePrice: 165, MenuItemId: burger.id }
  });
  const [recMojito] = await Recipe.findOrCreate({
    where: { name: "Receta Mojito Casa", area: "barra", MenuItemId: mojito.id },
    defaults: { instructions: "Macerar y mezclar con hielo.", salePrice: 140, MenuItemId: mojito.id }
  });

  await RecipeIngredient.findOrCreate({ where: { RecipeId: recBurger.id, IngredientId: carne.id }, defaults: { qty: 180 } });
  await RecipeIngredient.findOrCreate({ where: { RecipeId: recBurger.id, IngredientId: pan.id }, defaults: { qty: 1 } });
  await RecipeIngredient.findOrCreate({ where: { RecipeId: recMojito.id, IngredientId: ron.id }, defaults: { qty: 60 } });
  await RecipeIngredient.findOrCreate({ where: { RecipeId: recMojito.id, IngredientId: hierbabuena.id }, defaults: { qty: 1 } });

  await InventoryMovement.findOrCreate({
    where: { IngredientId: ron.id, type: "in", qty: 1000, reason: "Carga inicial barra", performedById: users.jefe_barra.id }
  });

  const [supplier] = await Supplier.findOrCreate({
    where: { name: "Distribuidora Centro" },
    defaults: { contact: "Laura Diaz", phone: "555-0101" }
  });

  await PurchaseOrder.findOrCreate({
    where: { quoteRef: "COT-2026-001" },
    defaults: { SupplierId: supplier.id, amount: 5200, status: "ordered" }
  });

  await AccountsPayable.findOrCreate({
    where: { description: "Pago pendiente proveedor bebidas", SupplierId: supplier.id },
    defaults: {
      amount: 5200,
      dueDate: dayjs().add(7, "day").format("YYYY-MM-DD"),
      status: "pending",
      SupplierId: supplier.id
    }
  });

  const [order] = await Order.findOrCreate({
    where: { notes: "Orden demo seed" },
    defaults: {
      source: "waiter",
      status: "served",
      RestaurantTableId: mesa1.id,
      waiterId: users.mesero.id,
      cashierId: users.cajero.id,
      notes: "Orden demo seed"
    }
  });

  await OrderItem.findOrCreate({
    where: { OrderId: order.id, MenuItemId: burger.id },
    defaults: { qty: 1, unitPrice: 165 }
  });
  await OrderItem.findOrCreate({
    where: { OrderId: order.id, MenuItemId: mojito.id },
    defaults: { qty: 2, unitPrice: 140 }
  });

  const [ticket] = await Ticket.findOrCreate({
    where: { folio: "TCK-SEED-0001" },
    defaults: { OrderId: order.id, subtotal: 445, tax: 71.2, total: 516.2 }
  });

  await Payment.findOrCreate({
    where: { TicketId: ticket.id, method: "card", amount: 516.2 },
    defaults: { TicketId: ticket.id, method: "card", amount: 516.2, status: "approved" }
  });

  return { mesa1, mesa2, burger, mojito, order, ticket };
}

async function run() {
  await sequelize.authenticate();
  await sequelize.sync();

  const roleMap = await ensureRoleMap();
  const users = await ensureDemoUsers(roleMap);
  const core = await ensureOperationalData(users);

  console.log("Seed completado:");
  console.log(`- Roles: ${roles.length}`);
  console.log(`- Usuarios demo: ${demoUsers.length} (password: Demo12345)`);
  console.log(`- Mesas demo: ${core.mesa1.tableNumber}, ${core.mesa2.tableNumber}`);
  console.log(`- Menu demo: ${core.burger.name}, ${core.mojito.name}`);
  console.log(`- Orden demo ID: ${core.order.id}, Ticket: ${core.ticket.folio}`);
  await sequelize.close();
}

run().catch((err) => {
  console.error(err);
  process.exit(1);
});
