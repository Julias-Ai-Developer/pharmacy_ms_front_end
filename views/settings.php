<?php include __DIR__ . '/../includes/header.php'; ?>

<!-- Link CSS -->
<link rel="stylesheet" href="../assets/css/style.css">

<?php include __DIR__ . '/../includes/sidenav.php'; ?>

<!-- ==================== Main Content ==================== -->
<main class="main-content" id="mainContent">

        <div class="module-content fade-in" id="settings">
            <?php include __DIR__ . '/../includes/notifications.php'; ?>
            
            <h2 class="mb-4" style="color: var(--primary); font-weight: 700;">Settings & Preferences</h2>

            <div class="row">
                <!-- General Settings -->
                <div class="col-lg-6 mb-4">
                    <div class="glass-card">
                        <h5 class="mb-4" style="font-weight: 600;"><i class="bi bi-sliders me-2"></i>General Settings
                        </h5>
                        <form method="post" action="<?php echo $basePath; ?>/actions/settings.php">
                            <div class="mb-3">
                                <label class="form-label" style="font-weight: 600;">Business Name</label>
                                <input name="business_name" type="text" class="form-control form-control-glass" value="<?php require_once __DIR__ . '/../config/db.php'; $r=$mysqli->query("SELECT value FROM settings WHERE `key`='business_name'"); $v='HealthPlus Pharmacy'; if($r&&($row=$r->fetch_assoc())){$v=$row['value'];} echo htmlspecialchars($v); ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label" style="font-weight: 600;">Location</label>
                                <input name="location" type="text" class="form-control form-control-glass" value="<?php $r=$mysqli->query("SELECT value FROM settings WHERE `key`='location'"); $v='Kampala Central, Uganda'; if($r&&($row=$r->fetch_assoc())){$v=$row['value'];} echo htmlspecialchars($v); ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label" style="font-weight: 600;">Phone Number</label>
                                <input name="phone" type="tel" class="form-control form-control-glass" value="<?php $r=$mysqli->query("SELECT value FROM settings WHERE `key`='phone'"); $v='+256 700 123456'; if($r&&($row=$r->fetch_assoc())){$v=$row['value'];} echo htmlspecialchars($v); ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label" style="font-weight: 600;">Email</label>
                                <input name="email" type="email" class="form-control form-control-glass" value="<?php $r=$mysqli->query("SELECT value FROM settings WHERE `key`='email'"); $v='info@healthplus.ug'; if($r&&($row=$r->fetch_assoc())){$v=$row['value'];} echo htmlspecialchars($v); ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label" style="font-weight: 600;">Currency</label>
                                <select name="currency" class="form-control form-control-glass">
                                    <?php $r=$mysqli->query("SELECT value FROM settings WHERE `key`='currency'"); $v='UGX - Uganda Shilling'; if($r&&($row=$r->fetch_assoc())){$v=$row['value'];}
                                      $opts=['UGX - Uganda Shilling','USD - US Dollar','EUR - Euro'];
                                      foreach($opts as $o){ echo '<option' . ($o===$v?' selected':'') . '>' . htmlspecialchars($o) . '</option>'; }
                                    ?>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary-custom">
                                <i class="bi bi-save me-2"></i>
                                Save Changes
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Appearance Settings -->
                <div class="col-lg-6 mb-4">
                    <div class="glass-card">
                        <h5 class="mb-4" style="font-weight: 600;"><i class="bi bi-palette me-2"></i>Appearance</h5>
                        <div class="mb-4">
                            <label class="form-label" style="font-weight: 600;">Theme</label>
                            <div class="d-flex gap-3">
                                <div class="flex-fill">
                                    <input type="radio" class="btn-check" name="theme-radio" id="light-radio" checked>
                                    <label class="btn btn-outline-secondary w-100" for="light-radio"
                                        style="border-radius: 12px; padding: 20px;">
                                        <i class="bi bi-sun-fill d-block mb-2" style="font-size: 32px;"></i>
                                        Light
                                    </label>
                                </div>
                                <div class="flex-fill">
                                    <input type="radio" class="btn-check" name="theme-radio" id="dark-radio">
                                    <label class="btn btn-outline-secondary w-100" for="dark-radio"
                                        style="border-radius: 12px; padding: 20px;">
                                        <i class="bi bi-moon-fill d-block mb-2" style="font-size: 32px;"></i>
                                        Dark
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label" style="font-weight: 600;">Language</label>
                            <select class="form-control form-control-glass">
                                <option>English</option>
                                <option>Luganda</option>
                                <option>Swahili</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" style="font-weight: 600;">Date Format</label>
                            <select class="form-control form-control-glass">
                                <option>DD/MM/YYYY</option>
                                <option>MM/DD/YYYY</option>
                                <option>YYYY-MM-DD</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Multi-Tenant Settings -->
            <div class="glass-card mb-4">
                <h5 class="mb-4" style="font-weight: 600;"><i class="bi bi-building me-2"></i>Multi-Tenant Management
                </h5>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label" style="font-weight: 600;">Select Tenant/Branch</label>
                        <select class="form-control form-control-glass">
                            <option>HealthPlus - Kampala Central</option>
                            <option>HealthPlus - Entebbe Branch</option>
                            <option>HealthPlus - Jinja Branch</option>
                        </select>
                    </div>
                    <div class="col-md-6 d-flex align-items-end">
                        <button class="btn btn-primary-custom w-100">
                            <i class="bi bi-arrow-repeat me-2"></i>
                            Switch Tenant
                        </button>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <div class="glass-card">
                            <h6 style="font-weight: 600; color: var(--primary);">Kampala Central</h6>
                            <small class="text-muted d-block mb-2">Main Branch</small>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="badge-custom badge-success">Active</span>
                                <button class="btn btn-sm btn-link">Manage</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="glass-card">
                            <h6 style="font-weight: 600; color: var(--primary);">Entebbe Branch</h6>
                            <small class="text-muted d-block mb-2">Branch Location</small>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="badge-custom badge-success">Active</span>
                                <button class="btn btn-sm btn-link">Manage</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="glass-card">
                            <h6 style="font-weight: 600; color: var(--primary);">Jinja Branch</h6>
                            <small class="text-muted d-block mb-2">Branch Location</small>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="badge-custom badge-success">Active</span>
                                <button class="btn btn-sm btn-link">Manage</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Main JavaScript -->
<script src="<?php echo isset($basePath) ? $basePath : '/pharmacy_ms'; ?>/assets/js/main.js"></script>

<script>
// Theme Switcher
function initThemeSwitcher() {
    const lightRadio = document.getElementById('light-radio');
    const darkRadio = document.getElementById('dark-radio');
    
    // Load saved theme
    const savedTheme = localStorage.getItem('theme') || 'light';
    if (savedTheme === 'dark') {
        darkRadio.checked = true;
        document.documentElement.setAttribute('data-theme', 'dark');
    } else {
        lightRadio.checked = true;
        document.documentElement.setAttribute('data-theme', 'light');
    }
    
    // Theme change handlers
    lightRadio.addEventListener('change', function() {
        if (this.checked) {
            localStorage.setItem('theme', 'light');
            document.documentElement.setAttribute('data-theme', 'light');
        }
    });
    
    darkRadio.addEventListener('change', function() {
        if (this.checked) {
            localStorage.setItem('theme', 'dark');
            document.documentElement.setAttribute('data-theme', 'dark');
        }
    });
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', initThemeSwitcher);
</script>

</body>
</html>
