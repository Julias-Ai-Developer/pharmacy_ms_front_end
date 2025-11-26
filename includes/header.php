
<?php
// Start session only if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include config if not already included
if (!defined('BASE_PATH')) {
    require_once __DIR__ . '/../config/config.php';
}

if (!isset($_SESSION['user_id'])) {
    header('Location: ' . BASE_PATH . '/views/auth/login.php');
    exit;
}

// Determine base path for assets
$basePath = BASE_PATH;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pharmacy Management System</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo $basePath; ?>/assets/css/style.css">
    <script>window.BASE_PATH = '<?php echo $basePath; ?>';</script>
    
    <!-- Theme initialization - runs immediately to prevent flash -->
    <script>
        // Apply saved theme immediately before page renders
        (function() {
            const savedTheme = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-theme', savedTheme);
        })();
    </script>
</head>

<body>

<!-- ==================== Header ==================== -->
<header class="header-glass">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center">
            <!-- Left Section -->
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-link text-decoration-none p-0" style="color: var(--text-primary);"
                    onclick="toggleSidebar()">
                    <i class="bi bi-list" style="font-size: 24px;"></i>
                </button>
                <div>
                    <h5 class="mb-0" style="color: var(--primary); font-weight: 700;">HealthPlus Pharmacy</h5>
                    <small class="text-muted d-flex align-items-center gap-1">
                        <i class="bi bi-geo-alt"></i>
                        Kampala Central, Uganda
                    </small>
                </div>
            </div>

            <!-- Right Section -->
            <div class="d-flex align-items-center gap-3">
                <!-- Theme Toggle -->
                <button class="btn btn-link text-decoration-none p-0" style="color: var(--text-primary);"
                    onclick="toggleTheme()">
                    <i class="bi bi-moon-fill" id="themeIcon" style="font-size: 20px;"></i>
                </button>

                

                <!-- Notifications -->
                <button class="btn btn-link text-decoration-none p-0 position-relative"
                    style="color: var(--text-primary);">
                    <i class="bi bi-bell-fill" style="font-size: 20px;"></i>
                    <span class="notification-badge">3</span>
                </button>

                <!-- User Avatar -->
                <div class="position-relative">
                    <div class="avatar" onclick="toggleProfileDropdown()"><?php echo strtoupper(substr($_SESSION['user_name'] ?? 'JD', 0, 2)); ?></div>
                    <div class="dropdown-glass position-absolute end-0 mt-2" id="profileDropdown"
                        style="display: none; min-width: 200px; z-index: 1001;">
                        <div class="dropdown-item-glass">
                            <i class="bi bi-person"></i>
                            <span>My Profile</span>
                        </div>
                        <div class="dropdown-item-glass">
                            <i class="bi bi-gear"></i>
                            <span>Settings</span>
                        </div>
                        <hr class="my-2" style="border-color: var(--border-color);">
                        <a href="<?php echo $basePath; ?>/actions/auth.php?action=logout" class="dropdown-item-glass text-danger" style="text-decoration:none;">
                            <i class="bi bi-box-arrow-right"></i>
                            <span>Logout</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
