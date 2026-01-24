<link rel="stylesheet" href="<?= $path_prefix ?? '' ?>css/sidebar.css">
<link rel="stylesheet" href="<?= $path_prefix ?? '' ?>css/confirm_modal.css">
<script src="<?= $path_prefix ?? '' ?>js/confirm_modal.js"></script>
<div class="sidebar-navbar-mobile" id="sidebarNavbarMobile">
  <button class="hamburger" id="sidebarHamburger" aria-label="Open sidebar" onclick="document.querySelector('.sidebar').classList.toggle('sidebar-open')">&#9776;</button>
  <span class="sidebar-navbar-title">Inventory Management</span>
</div>
<aside class="sidebar">
  <div class="sidebar-logo">
    <div class="sidebar-logo-icon">
      <span>📦</span>
    </div>
    <div>
      <div class="sidebar-logo-title">Inventory</div>
      <div class="sidebar-logo-desc">Manager</div>
    </div>
  </div>
  <nav class="sidebar-nav">
    <a href="<?= $path_prefix ?? '' ?>index.php" class="sidebar-link">🏠 Dashboard</a>
    <a href="<?= $path_prefix ?? '' ?>products.php" class="sidebar-link">📦 Products</a>
    <a href="<?= $path_prefix ?? '' ?>categories.php" class="sidebar-link">🗂 Categories</a>
    <a href="<?= $path_prefix ?? '' ?>suppliers.php" class="sidebar-link">🚚 Suppliers</a>
    <a href="<?= $path_prefix ?? '' ?>customers.php" class="sidebar-link">👥 Customers</a>
    <a href="<?= $path_prefix ?? '' ?>sales.php" class="sidebar-link">🛒 Sales</a>
    <a href="<?= $path_prefix ?? '' ?>purchases.php" class="sidebar-link">🧾 Purchases</a>
    <a href="<?= $path_prefix ?? '' ?>stock.php" class="sidebar-link">📊 Stock</a>
    <a href="<?= $path_prefix ?? '' ?>warehouses.php" class="sidebar-link">🏢 Warehouses</a>
    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
    <a href="<?= $path_prefix ?? '' ?>staff.php" class="sidebar-link">👥 Manage Staff</a>
    <a href="<?= $path_prefix ?? '' ?>reports.php" class="sidebar-link">📈 Reports</a>
    <?php endif; ?>
    <a href="#" onclick="event.preventDefault(); confirmLogout();" class="sidebar-link" style="margin-top:auto; color:#ef4444; background: #fef2f2; font-weight:600;">🚪 Logout</a>
  </nav>
</aside>
<script>
function confirmLogout() {
    if (typeof showConfirmModal === 'function') {
        showConfirmModal({
            title: 'Logout Confirmation',
            message: 'Are you sure you want to logout? Any unsaved changes will be lost.',
            icon: '🚪',
            iconType: 'warning',
            confirmText: 'Yes, Logout',
            confirmClass: 'logout',
            onConfirm: () => {
                window.location.href = '<?= $path_prefix ?? '' ?>user/logout.php';
            }
        });
    } else {
        if (confirm('Are you sure you want to logout?')) {
            window.location.href = '<?= $path_prefix ?? '' ?>user/logout.php';
        }
    }
}
</script>
<script>
const sidebar = document.querySelector('.sidebar');
const hamburger = document.getElementById('sidebarHamburger');
const navbarMobile = document.querySelector('.sidebar-navbar-mobile');

let isSidebarOpen = false; // Track if sidebar is manually opened on mobile

function updateSidebarState() {
  const isMobile = window.innerWidth <= 900;
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
  } else {
    sidebar.classList.remove('sidebar-open');
    sidebar.style.transform = '';
    hamburger.style.display = 'none';
    navbarMobile.style.display = 'none';
    isSidebarOpen = false; 
  }
}

hamburger.addEventListener('click', function() {
  if (window.innerWidth <= 900) {
    isSidebarOpen = !isSidebarOpen;
    if (isSidebarOpen) {
      sidebar.classList.add('sidebar-open');
      sidebar.style.transform = 'translateX(0)';
    } else {
      sidebar.classList.remove('sidebar-open');
      sidebar.style.transform = 'translateX(-100%)';
    }
  }
});

window.addEventListener('resize', function() {
  updateSidebarState();
});

document.querySelectorAll('.sidebar-link').forEach(link => {
  link.addEventListener('click', function() {
    if (window.innerWidth <= 900) {
      isSidebarOpen = false;
      sidebar.classList.remove('sidebar-open');
      sidebar.style.transform = 'translateX(-100%)';
    }
  });
});

// Initial state
updateSidebarState();
</script>
