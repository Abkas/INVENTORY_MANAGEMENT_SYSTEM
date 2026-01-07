<!-- Sidebar Navigation Component (HTML/CSS) -->
<style>
.sidebar {
  width: 220px;
  background: #18181b;
  color: #fff;
  min-height: 100vh;
  position: fixed;
  left: 0;
  top: 0;
  display: flex;
  flex-direction: column;
  z-index: 100;
}
.sidebar-logo {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 24px 20px 16px 20px;
  border-bottom: 1px solid #23232a;
}
.sidebar-logo-icon {
  width: 36px;
  height: 36px;
  background: #27272a;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 20px;
}
.sidebar-logo-title {
  font-weight: bold;
  font-size: 18px;
  margin: 0;
}
.sidebar-logo-desc {
  font-size: 12px;
  color: #aaa;
  margin: 0;
}
.sidebar-nav {
  flex: 1;
  display: flex;
  flex-direction: column;
  padding: 20px 0;
}
.sidebar-link {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px 24px;
  color: #fff;
  text-decoration: none;
  font-size: 15px;
  border-left: 4px solid transparent;
  transition: background 0.2s, border-color 0.2s;
}
.sidebar-link:hover, .sidebar-link-active {
  background: #23232a;
  border-left: 4px solid #6366f1;
  color: #fff;
}
.sidebar-link svg {
  width: 20px;
  height: 20px;
}
@media (max-width: 900px) {
  .sidebar {
    position: static;
    width: 100%;
    min-height: auto;
    flex-direction: row;
    height: 60px;
  }
  .sidebar-logo, .sidebar-nav {
    flex-direction: row;
    align-items: center;
    padding: 0 10px;
    border: none;
  }
  .sidebar-nav {
    flex-direction: row;
    padding: 0;
  }
  .sidebar-link {
    padding: 10px 10px;
    font-size: 14px;
    border-left: none;
    border-bottom: 2px solid transparent;
  }
  .sidebar-link:hover, .sidebar-link-active {
    border-left: none;
    border-bottom: 2px solid #6366f1;
  }
}
</style>
<aside class="sidebar">
  <div class="sidebar-logo">
    <div class="sidebar-logo-icon">
      <!-- You can use an SVG icon here -->
      <span>📦</span>
    </div>
    <div>
      <div class="sidebar-logo-title">Inventory</div>
      <div class="sidebar-logo-desc">Manager</div>
    </div>
  </div>
  <nav class="sidebar-nav">
    <a href="/INVENTORY_SYSTEM/BACKEND/index.php" class="sidebar-link">🏠 Dashboard</a>
    <a href="/INVENTORY_SYSTEM/BACKEND/products.php" class="sidebar-link">📦 Products</a>
    <a href="/INVENTORY_SYSTEM/BACKEND/categories.php" class="sidebar-link">🗂 Categories</a>
    <a href="/INVENTORY_SYSTEM/BACKEND/suppliers.php" class="sidebar-link">🚚 Suppliers</a>
    <a href="/INVENTORY_SYSTEM/BACKEND/customers.php" class="sidebar-link">👥 Customers</a>
    <a href="/INVENTORY_SYSTEM/BACKEND/sales.php" class="sidebar-link">🛒 Sales</a>
    <a href="/INVENTORY_SYSTEM/BACKEND/purchases.php" class="sidebar-link">🧾 Purchases</a>
    <a href="/INVENTORY_SYSTEM/BACKEND/stock.php" class="sidebar-link">📊 Stock</a>
    <a href="/INVENTORY_SYSTEM/BACKEND/warehouses.php" class="sidebar-link">🏢 Warehouses</a>
  </nav>
</aside>
