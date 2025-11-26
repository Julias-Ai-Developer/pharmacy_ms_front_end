<?php include __DIR__ . '/../includes/header.php'; ?>

<?php include __DIR__ . '/../includes/sidenav.php'; ?>

<!-- ==================== Main Content ==================== -->
<main class="main-content" id="mainContent">

        <div class="module-content active fade-in" id="dashboard">
            <h2 class="mb-4" style="color: var(--primary); font-weight: 700;">Dashboard Overview</h2>

            <!-- Stats Cards -->
            <div class="row mb-4">
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="stat-card">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div style="position: relative; z-index: 1;">
                                <p class="mb-1 opacity-75">Total Sales</p>
                                <?php require_once __DIR__ . '/../config/db.php';
                                  $res = $mysqli->query("SELECT SUM(total) AS sum_total FROM sales");
                                  $sum = 0; if ($res && ($row = $res->fetch_assoc())) { $sum = (float)$row['sum_total']; }
                                  echo '<h3 class="mb-0" style="font-weight: 700;">UGX ' . number_format($sum, 0) . '</h3>'; ?>
                            </div>
                            <i class="bi bi-currency-dollar icon-lg opacity-50"></i>
                        </div>
                        <div class="d-flex align-items-center gap-2" style="position: relative; z-index: 1;">
                            <span class="badge-custom badge-success">Today</span>
                            <!-- <small class="opacity-75">database</small> -->
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="stat-card stat-card-2">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div style="position: relative; z-index: 1;">
                                <p class="mb-1 opacity-75">Total Products</p>
                                <?php $res = $mysqli->query("SELECT COUNT(*) AS cnt FROM medicines"); $cnt = 0; if ($res && ($row = $res->fetch_assoc())) { $cnt = (int)$row['cnt']; }
                                  echo '<h3 class="mb-0" style="font-weight: 700;">' . number_format($cnt) . '</h3>'; ?>
                            </div>
                            <i class="bi bi-box-seam icon-lg opacity-50"></i>
                        </div>
                        <div class="d-flex align-items-center gap-2" style="position: relative; z-index: 1;">
                            <?php $res = $mysqli->query("SELECT COUNT(*) AS low FROM medicines WHERE stock <= 20"); $low = 0; if ($res && ($row = $res->fetch_assoc())) { $low = (int)$row['low']; }
                              echo '<span class="badge-custom badge-warning">' . $low . ' Low Stock</span>'; ?>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="stat-card stat-card-3">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div style="position: relative; z-index: 1;">
                                <p class="mb-1 opacity-75">Prescriptions</p>
                                <?php $res = $mysqli->query("SELECT COUNT(*) AS cnt FROM prescriptions"); $pcnt = 0; if ($res && ($row = $res->fetch_assoc())) { $pcnt = (int)$row['cnt']; }
                                  echo '<h3 class="mb-0" style="font-weight: 700;">' . number_format($pcnt) . '</h3>'; ?>
                            </div>
                            <i class="bi bi-file-earmark-text icon-lg opacity-50"></i>
                        </div>
                        <div class="d-flex align-items-center gap-2" style="position: relative; z-index: 1;">
                            <span class="badge-custom badge-success">Live</span>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="stat-card stat-card-4">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div style="position: relative; z-index: 1;">
                                <p class="mb-1 opacity-75">Expiring Soon</p>
                                <?php $res = $mysqli->query("SELECT COUNT(*) AS exp FROM medicines WHERE expiry_date <= DATE_ADD(CURDATE(), INTERVAL 60 DAY)"); $exp = 0; if ($res && ($row = $res->fetch_assoc())) { $exp = (int)$row['exp']; }
                                  echo '<h3 class="mb-0" style="font-weight: 700;">' . number_format($exp) . '</h3>'; ?>
                            </div>
                            <i class="bi bi-exclamation-triangle icon-lg opacity-50"></i>
                        </div>
                        <div class="d-flex align-items-center gap-2" style="position: relative; z-index: 1;">
                            <span class="badge-custom badge-danger">Action Needed</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts and Alerts -->
            <div class="row mb-4">
                <div class="col-lg-8 mb-4">
                    <div class="glass-card">
                        <h5 class="mb-4" style="font-weight: 600;">Sales Overview</h5>
                        <div class="chart-placeholder">
                            <div class="text-center">
                                <i class="bi bi-bar-chart-line"
                                    style="font-size: 48px; color: var(--primary); opacity: 0.3;"></i>
                                <p class="text-muted mt-2">Sales Trend Chart</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 mb-4">
                    <div class="glass-card">
                        <h5 class="mb-4" style="font-weight: 600;">Low Stock Alerts</h5>
                        <div class="d-flex flex-column gap-3">
                            <div class="alert-card warning">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <p class="mb-1" style="font-weight: 600;">Paracetamol 500mg</p>
                                        <small class="text-muted">Stock: 15 units</small>
                                    </div>
                                    <i class="bi bi-exclamation-circle" style="color: #f59e0b; font-size: 20px;"></i>
                                </div>
                            </div>
                            <div class="alert-card warning">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <p class="mb-1" style="font-weight: 600;">Amoxicillin 250mg</p>
                                        <small class="text-muted">Stock: 12 units</small>
                                    </div>
                                    <i class="bi bi-exclamation-circle" style="color: #f59e0b; font-size: 20px;"></i>
                                </div>
                            </div>
                            <div class="alert-card danger">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <p class="mb-1" style="font-weight: 600;">Ibuprofen 400mg</p>
                                        <small class="text-muted">Stock: 9 units</small>
                                    </div>
                                    <i class="bi bi-exclamation-triangle-fill"
                                        style="color: #ef4444; font-size: 20px;"></i>
                                </div>
                            </div>
                            <div class="alert-card warning">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <p class="mb-1" style="font-weight: 600;">Vitamin C Tablets</p>
                                        <small class="text-muted">Stock: 6 units</small>
                                    </div>
                                    <i class="bi bi-exclamation-circle" style="color: #f59e0b; font-size: 20px;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top Selling Products Table -->
            <div class="glass-card">
                <h5 class="mb-4" style="font-weight: 600;">Top Selling Products</h5>
                <div class="table-responsive">
                    <table class="table table-glass mb-0">
                        <thead>
                            <tr>
                                <th>Product Name</th>
                                <th>Category</th>
                                <th>Units Sold</th>
                                <th>Revenue</th>
                                <th>Stock Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                              $sql = "SELECT si.name, SUM(si.quantity) AS units, SUM(si.price*si.quantity) AS revenue,
                                      m.category, m.stock
                                      FROM sale_items si
                                      LEFT JOIN medicines m ON m.name = si.name
                                      GROUP BY si.name, m.category, m.stock
                                      ORDER BY units DESC
                                      LIMIT 5";
                              $res = $mysqli->query($sql);
                              if ($res && $res->num_rows > 0) {
                                while ($row = $res->fetch_assoc()) {
                                  $badge = 'badge-success'; $label = 'In Stock';
                                  $stock = isset($row['stock']) ? (int)$row['stock'] : 0;
                                  if ($stock <= 10) { $badge = 'badge-danger'; $label = 'Critical'; }
                                  elseif ($stock <= 20) { $badge = 'badge-warning'; $label = 'Low Stock'; }
                                  echo '<tr>';
                                  echo '<td><strong>' . htmlspecialchars($row['name']) . '</strong></td>';
                                  echo '<td>' . htmlspecialchars($row['category'] ?: '-') . '</td>';
                                  echo '<td>' . number_format((int)$row['units']) . '</td>';
                                  echo '<td>UGX ' . number_format((float)$row['revenue'], 0) . '</td>';
                                  echo '<td><span class="badge-custom ' . $badge . '">' . $label . '</span></td>';
                                  echo '</tr>';
                                }
                              } else {
                                echo '<tr><td colspan="5" class="text-center text-muted">No sales yet</td></tr>';
                              }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>


</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Main JavaScript -->
<script src="<?php echo isset($basePath) ? $basePath : '/pharmacy_ms'; ?>/assets/js/main.js"></script>

</body>
</html>
