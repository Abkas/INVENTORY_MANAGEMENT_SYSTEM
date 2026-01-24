<!-- Sidebar Navigation Component (HTML/CSS) -->
<link rel="stylesheet" href="/INVENTORY_SYSTEM/BACKEND/css/sidebar.css">
<div class="sidebar-navbar-mobile" id="sidebarNavbarMobile">
  <button class="hamburger" id="sidebarHamburger" aria-label="Open sidebar" onclick="document.querySelector('.sidebar').classList.toggle('sidebar-open')">&#9776;</button>
  <span class="sidebar-navbar-title">Inventory Management</span>
</div>
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
    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
    <a href="/INVENTORY_SYSTEM/BACKEND/reports.php" class="sidebar-link">📈 Reports</a>
    <?php endif; ?>
    <a href="/INVENTORY_SYSTEM/BACKEND/user/logout.php" class="sidebar-link" style="margin-top:auto; color:#ef4444; background: #fef2f2; font-weight:600;">🚪 Logout</a>
  </nav>
</aside>
<script>
const sidebar = document.querySelector('.sidebar');
const hamburger = document.getElementById('sidebarHamburger');
const navbarMobile = document.querySelector('.sidebar-navbar-mobile');

let isSidebarOpen = false; // Track if sidebar is manually opened on mobile

function updateSidebarState() {
  const isMobile = window.innerWidth <= 900;
  console.log('updateSidebarState called, window width:', window.innerWidth, 'isMobile:', isMobile);
  if (isMobile) {
    hamburger.style.display = 'flex';
    navbarMobile.style.display = 'flex';
    if (isSidebarOpen) {
      sidebar.classList.add('sidebar-open');
      sidebar.style.transform = 'translateX(0)';
    } else {
      sidebar.classList.remove('sidebar-open');
      sidebar.style.transform = 'translateX(-100%)';
    }
    console.log('Set to mobile: hamburger shown, sidebar ' + (isSidebarOpen ? 'open' : 'closed'));
  } else {
    sidebar.classList.remove('sidebar-open');
    sidebar.style.transform = '';
    hamburger.style.display = 'none';
    navbarMobile.style.display = 'none';
    isSidebarOpen = false; // Reset on desktop
    console.log('Set to desktop: sidebar visible, hamburger hidden');
  }
}

hamburger.addEventListener('click', function() {
  console.log('Hamburger clicked, window width:', window.innerWidth);
  if (window.innerWidth <= 900) {
    isSidebarOpen = !isSidebarOpen;
    if (isSidebarOpen) {
      sidebar.classList.add('sidebar-open');
      sidebar.style.transform = 'translateX(0)';
      console.log('Sidebar opened');
    } else {
      sidebar.classList.remove('sidebar-open');
      sidebar.style.transform = 'translateX(-100%)';
      console.log('Sidebar closed');
    }
  } else {
    console.log('Hamburger clicked on desktop, no action');
  }
});

window.addEventListener('resize', function() {
  console.log('Resize event, new width:', window.innerWidth);
  updateSidebarState();
});

document.querySelectorAll('.sidebar-link').forEach(link => {
  link.addEventListener('click', function() {
    console.log('Sidebar link clicked, window width:', window.innerWidth);
    if (window.innerWidth <= 900) {
      isSidebarOpen = false;
      sidebar.classList.remove('sidebar-open');
      sidebar.style.transform = 'translateX(-100%)';
      console.log('Sidebar closed after link click');
    }
  });
});

// Initial state
console.log('Initial load');
updateSidebarState();
</script>
