import { DataTypes } from "sequelize";
import { sequelize } from "./config/db.js";

export const Role = sequelize.define("Role", {
  name: { type: DataTypes.STRING(60), unique: true, allowNull: false }
});

export const User = sequelize.define("User", {
  fullName: { type: DataTypes.STRING(120), allowNull: false },
  email: { type: DataTypes.STRING(120), unique: true, allowNull: false },
  passwordHash: { type: DataTypes.STRING(255), allowNull: false },
  active: { type: DataTypes.BOOLEAN, allowNull: false, defaultValue: true }
});

export const RestaurantTable = sequelize.define("RestaurantTable", {
  tableNumber: { type: DataTypes.STRING(20), unique: true, allowNull: false },
  status: {
    type: DataTypes.ENUM("available", "occupied", "reserved", "closed"),
    allowNull: false,
    defaultValue: "available"
  }
});

export const Category = sequelize.define("Category", {
  name: { type: DataTypes.STRING(80), allowNull: false },
  type: { type: DataTypes.ENUM("food", "drink"), allowNull: false }
});

export const MenuItem = sequelize.define("MenuItem", {
  name: { type: DataTypes.STRING(120), allowNull: false },
  description: { type: DataTypes.TEXT, allowNull: true },
  price: { type: DataTypes.DECIMAL(10, 2), allowNull: false },
  active: { type: DataTypes.BOOLEAN, allowNull: false, defaultValue: true }
});

export const Recipe = sequelize.define("Recipe", {
  name: { type: DataTypes.STRING(120), allowNull: false },
  area: { type: DataTypes.ENUM("barra", "cocina"), allowNull: false },
  instructions: { type: DataTypes.TEXT, allowNull: true },
  salePrice: { type: DataTypes.DECIMAL(10, 2), allowNull: false, defaultValue: 0 }
});

export const Ingredient = sequelize.define("Ingredient", {
  name: { type: DataTypes.STRING(120), allowNull: false },
  unitCost: { type: DataTypes.DECIMAL(10, 4), allowNull: false, defaultValue: 0 },
  stockQty: { type: DataTypes.DECIMAL(12, 3), allowNull: false, defaultValue: 0 },
  unit: { type: DataTypes.STRING(20), allowNull: false, defaultValue: "u" }
});

export const RecipeIngredient = sequelize.define("RecipeIngredient", {
  qty: { type: DataTypes.DECIMAL(12, 3), allowNull: false }
});

export const Order = sequelize.define("Order", {
  source: { type: DataTypes.ENUM("qr", "waiter", "cashier"), allowNull: false },
  status: {
    type: DataTypes.ENUM("open", "sent_kitchen", "sent_bar", "served", "paid", "cancelled"),
    allowNull: false,
    defaultValue: "open"
  },
  notes: { type: DataTypes.TEXT, allowNull: true }
});

export const OrderItem = sequelize.define("OrderItem", {
  qty: { type: DataTypes.DECIMAL(10, 2), allowNull: false },
  unitPrice: { type: DataTypes.DECIMAL(10, 2), allowNull: false }
});

export const Supplier = sequelize.define("Supplier", {
  name: { type: DataTypes.STRING(120), allowNull: false },
  contact: { type: DataTypes.STRING(120), allowNull: true },
  phone: { type: DataTypes.STRING(40), allowNull: true }
});

export const PurchaseOrder = sequelize.define("PurchaseOrder", {
  quoteRef: { type: DataTypes.STRING(80), allowNull: true },
  amount: { type: DataTypes.DECIMAL(12, 2), allowNull: false, defaultValue: 0 },
  status: { type: DataTypes.ENUM("draft", "ordered", "received", "cancelled"), defaultValue: "draft" }
});

export const AccountsPayable = sequelize.define("AccountsPayable", {
  description: { type: DataTypes.STRING(180), allowNull: false },
  amount: { type: DataTypes.DECIMAL(12, 2), allowNull: false },
  dueDate: { type: DataTypes.DATEONLY, allowNull: false },
  status: { type: DataTypes.ENUM("pending", "paid", "overdue"), defaultValue: "pending" }
});

export const Ticket = sequelize.define("Ticket", {
  folio: { type: DataTypes.STRING(40), unique: true, allowNull: false },
  subtotal: { type: DataTypes.DECIMAL(12, 2), allowNull: false },
  tax: { type: DataTypes.DECIMAL(12, 2), allowNull: false },
  total: { type: DataTypes.DECIMAL(12, 2), allowNull: false }
});

export const Invoice = sequelize.define("Invoice", {
  uuidProvider: { type: DataTypes.STRING(80), allowNull: true },
  fiscalName: { type: DataTypes.STRING(120), allowNull: false },
  rfc: { type: DataTypes.STRING(20), allowNull: false },
  email: { type: DataTypes.STRING(120), allowNull: true }
});

export const Payment = sequelize.define("Payment", {
  method: { type: DataTypes.ENUM("cash", "card", "transfer", "mixed"), allowNull: false },
  amount: { type: DataTypes.DECIMAL(12, 2), allowNull: false },
  externalRef: { type: DataTypes.STRING(120), allowNull: true },
  status: { type: DataTypes.ENUM("pending", "approved", "declined"), defaultValue: "pending" }
});

export const InventoryMovement = sequelize.define("InventoryMovement", {
  type: { type: DataTypes.ENUM("in", "out", "adjustment"), allowNull: false },
  qty: { type: DataTypes.DECIMAL(12, 3), allowNull: false },
  reason: { type: DataTypes.STRING(180), allowNull: true }
});

Role.hasMany(User);
User.belongsTo(Role);

User.hasMany(RestaurantTable, { as: "assignedTables", foreignKey: "assignedWaiterId" });
RestaurantTable.belongsTo(User, { as: "assignedWaiter", foreignKey: "assignedWaiterId" });

Category.hasMany(MenuItem);
MenuItem.belongsTo(Category);

MenuItem.hasOne(Recipe);
Recipe.belongsTo(MenuItem);

Recipe.belongsToMany(Ingredient, { through: RecipeIngredient });
Ingredient.belongsToMany(Recipe, { through: RecipeIngredient });

RestaurantTable.hasMany(Order);
Order.belongsTo(RestaurantTable);
User.hasMany(Order, { as: "waiterOrders", foreignKey: "waiterId" });
Order.belongsTo(User, { as: "waiter", foreignKey: "waiterId" });
User.hasMany(Order, { as: "cashierOrders", foreignKey: "cashierId" });
Order.belongsTo(User, { as: "cashier", foreignKey: "cashierId" });

Order.hasMany(OrderItem);
OrderItem.belongsTo(Order);
MenuItem.hasMany(OrderItem);
OrderItem.belongsTo(MenuItem);

Supplier.hasMany(PurchaseOrder);
PurchaseOrder.belongsTo(Supplier);
Supplier.hasMany(AccountsPayable);
AccountsPayable.belongsTo(Supplier);

Order.hasOne(Ticket);
Ticket.belongsTo(Order);
Ticket.hasOne(Invoice);
Invoice.belongsTo(Ticket);
Ticket.hasMany(Payment);
Payment.belongsTo(Ticket);

Ingredient.hasMany(InventoryMovement);
InventoryMovement.belongsTo(Ingredient);
User.hasMany(InventoryMovement, { as: "inventoryMovements", foreignKey: "performedById" });
InventoryMovement.belongsTo(User, { as: "performedBy", foreignKey: "performedById" });

export async function syncDatabase() {
  await sequelize.sync();
}
