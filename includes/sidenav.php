<?php
// Determine base path for links
$basePath = defined('BASE_PATH') ? BASE_PATH : '/pharmacy_ms';
$currentPage = defined('CURRENT_PAGE') ? CURRENT_PAGE : (isset($_GET['url']) ? trim($_GET['url'], '/') : 'dashboard');
if (empty($currentPage)) $currentPage = 'dashboard';
?>

<!-- ==================== Sidebar ==================== -->
<aside class="sidebar" id="sidebar">
    <nav>
        <a href="<?php echo $basePath; ?>/dashboard" class="sidebar-item <?php echo $currentPage === 'dashboard' ? 'active' : ''; ?>">
            <i class="bi bi-house-door-fill"></i>
            <span>Dashboard</span>
        </a>
        <a href="<?php echo $basePath; ?>/pos" class="sidebar-item <?php echo $currentPage === 'pos' ? 'active' : ''; ?>">
            <i class="bi bi-cart-fill"></i>
            <span>POS / Sales</span>
        </a>
        <a href="<?php echo $basePath; ?>/receipt" class="sidebar-item <?php echo $currentPage === 'receipt' ? 'active' : ''; ?>">
            <i class="bi bi-receipt"></i>
            <span>Receipts</span>
        </a>
        <a href="<?php echo $basePath; ?>/inventory" class="sidebar-item <?php echo $currentPage === 'inventory' ? 'active' : ''; ?>">
            <i class="bi bi-box-seam-fill"></i>
            <span>Inventory</span>
        </a>
        <a href="<?php echo $basePath; ?>/selling-units" class="sidebar-item <?php echo $currentPage === 'selling-units' ? 'active' : ''; ?>">
            <i class="bi bi-tags-fill"></i>
            <span>Selling Units</span>
        </a>
        <a href="<?php echo $basePath; ?>/purchases" class="sidebar-item <?php echo $currentPage === 'purchases' ? 'active' : ''; ?>">
            <i class="bi bi-bag-fill"></i>
            <span>Purchases</span>
        </a>
        <a href="<?php echo $basePath; ?>/prescriptions" class="sidebar-item <?php echo $currentPage === 'prescriptions' ? 'active' : ''; ?>">
            <i class="bi bi-file-earmark-medical-fill"></i>
            <span>Prescriptions</span>
        </a>
        <a href="<?php echo $basePath; ?>/patients" class="sidebar-item <?php echo $currentPage === 'patients' ? 'active' : ''; ?>">
            <i class="bi bi-people-fill"></i>
            <span>Patients</span>
        </a>
        <a href="<?php echo $basePath; ?>/clinic" class="sidebar-item <?php echo $currentPage === 'clinic' ? 'active' : ''; ?>">
            <i class="bi bi-hospital-fill"></i>
            <span>Clinic</span>
        </a>
        <a href="<?php echo $basePath; ?>/reports" class="sidebar-item <?php echo $currentPage === 'reports' ? 'active' : ''; ?>">
            <i class="bi bi-graph-up"></i>
            <span>Reports</span>
        </a>
        <a href="<?php echo $basePath; ?>/users" class="sidebar-item <?php echo $currentPage === 'users' ? 'active' : ''; ?>">
            <i class="bi bi-person-badge-fill"></i>
            <span>Users &amp; Roles</span>
        </a>
        <a href="<?php echo $basePath; ?>/settings" class="sidebar-item <?php echo $currentPage === 'settings' ? 'active' : ''; ?>">
            <i class="bi bi-gear-fill"></i>
            <span>Settings</span>
        </a>
    </nav>
</aside>
