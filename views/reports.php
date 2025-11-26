<?php include __DIR__ . '/../includes/header.php'; ?>

<!-- Chart.js removed -->

<?php include __DIR__ . '/../includes/sidenav.php'; ?>

<!-- ==================== Main Content ==================== -->
<main class="main-content" id="mainContent">

        <?php
        // Initialize filter variables
        $type = isset($_GET['type']) ? $_GET['type'] : 'Expiry Report';
        $from = isset($_GET['from']) ? $_GET['from'] : '';
        $to = isset($_GET['to']) ? $_GET['to'] : '';
        ?>

        <div class="module-content fade-in" id="reports">
            <h2 class="mb-4" style="color: var(--primary); font-weight: 700;">Reports & Analytics</h2>

            <!-- Date Range Filter -->
            <div class="glass-card mb-4">
                <form method="get" action="<?php echo $basePath; ?>/reports">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label" style="font-weight: 600;">From Date</label>
                        <input name="from" type="date" class="form-control form-control-glass" value="<?php echo isset($_GET['from']) ? htmlspecialchars($_GET['from']) : '';?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" style="font-weight: 600;">To Date</label>
                        <input name="to" type="date" class="form-control form-control-glass" value="<?php echo isset($_GET['to']) ? htmlspecialchars($_GET['to']) : date('Y-m-d');?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" style="font-weight: 600;">Report Type</label>
                        <select name="type" class="form-control form-control-glass">
                            <?php $type = isset($_GET['type']) ? $_GET['type'] : 'Expiry Report';
                              $opts = ['Sales Report','Inventory Report','Expiry Report','Supplier Performance'];
                              foreach($opts as $o){ echo '<option' . ($o===$type?' selected':'') . '>' . htmlspecialchars($o) . '</option>'; }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary-custom w-100">
                            <i class="bi bi-file-earmark-bar-graph me-2"></i>
                            Generate Report
                        </button>
                    </div>
                </div>
                </form>
            </div>

            <!-- Charts section removed -->


            <!-- Detailed Reports Table -->
            <div class="glass-card">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 style="font-weight: 600;"><?php echo htmlspecialchars($type); ?></h5>
                    <?php
                    $exportUrl = $basePath . '/actions/generate_pdf_report.php?type=' . urlencode($type);
                    if ($from) $exportUrl .= '&from=' . urlencode($from);
                    if ($to) $exportUrl .= '&to=' . urlencode($to);
                    ?>
                    <a href="<?php echo $exportUrl; ?>" target="_blank" class="btn btn-primary-custom btn-sm">
                        <i class="bi bi-download me-2"></i>
                        Export PDF
                    </a>
                </div>
                
                <div class="table-responsive">
                <?php
                // Display the selected report in table format
                require_once __DIR__ . '/../config/db.php';
                
                // Build table based on report type
                switch($type) {
                    case 'Sales Report':
                        // Sales Report Table
                        ?>
                        <table class="table table-glass mb-0">
                            <thead>
                                <tr>
                                    <th>Sale ID</th>
                                    <th>Date & Time</th>
                                    <th>Amount</th>
                                    <th>Payment Method</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                            $where = '';
                            if ($from && $to) {
                                $fromEsc = $mysqli->real_escape_string($from);
                                $toEsc = $mysqli->real_escape_string($to);
                                $where = "WHERE DATE(created_at) BETWEEN '$fromEsc' AND '$toEsc'";
                            }
                            $res = $mysqli->query("SELECT id, created_at, total, payment_method FROM sales $where ORDER BY created_at DESC LIMIT 100");
                            if ($res && $res->num_rows > 0) {
                                while ($row = $res->fetch_assoc()) {
                                    echo '<tr>';
                                    echo '<td><strong>#' . $row['id'] . '</strong></td>';
                                    echo '<td>' . date('M d, Y h:i A', strtotime($row['created_at'])) . '</td>';
                                    echo '<td><strong>UGX ' . number_format($row['total'], 0) . '</strong></td>';
                                    echo '<td>' . htmlspecialchars($row['payment_method']) . '</td>';
                                    echo '</tr>';
                                }
                            } else {
                                echo '<tr><td colspan="4" class="text-center text-muted">No sales found</td></tr>';
                            }
                            ?>
                            </tbody>
                        </table>
                        <?php
                        break;
                        
                    case 'Inventory Report':
                        // Inventory Report Table
                        ?>
                        <table class="table table-glass mb-0">
                            <thead>
                                <tr>
                                    <th>Product Name</th>
                                    <th>Category</th>
                                    <th>Batch No.</th>
                                    <th>Stock</th>
                                    <th>Price</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                            $res = $mysqli->query("SELECT name, category, batch_no, stock, price FROM medicines ORDER BY name ASC");
                            if ($res && $res->num_rows > 0) {
                                while ($row = $res->fetch_assoc()) {
                                    $stock = (int)$row['stock'];
                                    $statusClass = 'badge-success'; $statusLabel = 'In Stock';
                                    if ($stock == 0) { $statusClass = 'badge-danger'; $statusLabel = 'Out of Stock'; }
                                    elseif ($stock <= 10) { $statusClass = 'badge-danger'; $statusLabel = 'Critical'; }
                                    elseif ($stock <= 20) { $statusClass = 'badge-warning'; $statusLabel = 'Low Stock'; }
                                    
                                    echo '<tr>';
                                    echo '<td><strong>' . htmlspecialchars($row['name']) . '</strong></td>';
                                    echo '<td>' . htmlspecialchars($row['category']) . '</td>';
                                    echo '<td>' . htmlspecialchars($row['batch_no']) . '</td>';
                                    echo '<td>' . $stock . ' units</td>';
                                    echo '<td>UGX ' . number_format($row['price'], 0) . '</td>';
                                    echo '<td><span class="badge-custom ' . $statusClass . '">' . $statusLabel . '</span></td>';
                                    echo '</tr>';
                                }
                            } else {
                                echo '<tr><td colspan="6" class="text-center text-muted">No products found</td></tr>';
                            }
                            ?>
                            </tbody>
                        </table>
                        <?php
                        break;
                        
                    case 'Supplier Performance':
                        // Supplier Performance Report Table
                        ?>
                        <table class="table table-glass mb-0">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Supplier</th>
                                    <th>Order Date</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                            $where = '';
                            if ($from && $to) {
                                $fromEsc = $mysqli->real_escape_string($from);
                                $toEsc = $mysqli->real_escape_string($to);
                                $where = "WHERE DATE(po.order_date) BETWEEN '$fromEsc' AND '$toEsc'";
                            }
                            $query = "SELECT po.id, s.name AS supplier_name, po.order_date, po.amount, po.status 
                                     FROM purchase_orders po 
                                     LEFT JOIN suppliers s ON po.supplier_id = s.id 
                                     $where 
                                     ORDER BY po.order_date DESC LIMIT 50";
                            $res = $mysqli->query($query);
                            if ($res && $res->num_rows > 0) {
                                while ($row = $res->fetch_assoc()) {
                                    $statusClass = 'badge-info';
                                    if ($row['status'] == 'Received') $statusClass = 'badge-success';
                                    elseif ($row['status'] == 'Pending') $statusClass = 'badge-warning';
                                    
                                    echo '<tr>';
                                    echo '<td><strong>#' . $row['id'] . '</strong></td>';
                                    echo '<td>' . htmlspecialchars($row['supplier_name'] ?: 'N/A') . '</td>';
                                    echo '<td>' . date('M d, Y', strtotime($row['order_date'])) . '</td>';
                                    echo '<td><strong>UGX ' . number_format($row['amount'], 0) . '</strong></td>';
                                    echo '<td><span class="badge-custom ' . $statusClass . '">' . htmlspecialchars($row['status']) . '</span></td>';
                                    echo '</tr>';
                                }
                            } else {
                                echo '<tr><td colspan="5" class="text-center text-muted">No orders found</td></tr>';
                            }
                            ?>
                            </tbody>
                        </table>
                        <?php
                        break;
                        
                    case 'Expiry Report':
                    default:
                        // Expiry Report Table (Default)
                        ?>
                        <table class="table table-glass mb-0">
                            <thead>
                                <tr>
                                    <th>Product Name</th>
                                    <th>Batch No.</th>
                                    <th>Stock</th>
                                    <th>Expiry Date</th>
                                    <th>Days Until Expiry</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                            $where = '';
                            if ($from && $to) {
                                $fromEsc = $mysqli->real_escape_string($from);
                                $toEsc = $mysqli->real_escape_string($to);
                                $where = "WHERE expiry_date BETWEEN '$fromEsc' AND '$toEsc'";
                            }
                            $res = $mysqli->query("SELECT name, batch_no, stock, expiry_date FROM medicines $where ORDER BY expiry_date ASC");
                            if ($res && $res->num_rows > 0) {
                                while ($row = $res->fetch_assoc()) {
                                    $expiry = strtotime($row['expiry_date']);
                                    $days = floor(($expiry - time()) / 86400);
                                    $statusClass = 'badge-info'; $statusLabel = 'Monitor';
                                    if ($days <= 60) { $statusClass = 'badge-warning'; $statusLabel = 'Expiring Soon'; }
                                    if ($days <= 30) { $statusClass = 'badge-danger'; $statusLabel = 'Critical'; }
                                    
                                    echo '<tr>';
                                    echo '<td><strong>' . htmlspecialchars($row['name']) . '</strong></td>';
                                    echo '<td>' . htmlspecialchars($row['batch_no']) . '</td>';
                                    echo '<td>' . (int)$row['stock'] . ' units</td>';
                                    echo '<td>' . date('M d, Y', $expiry) . '</td>';
                                    echo '<td>' . max($days, 0) . ' days</td>';
                                    echo '<td><span class="badge-custom ' . $statusClass . '">' . $statusLabel . '</span></td>';
                                    echo '</tr>';
                                }
                            } else {
                                echo '<tr><td colspan="6" class="text-center text-muted">No products found</td></tr>';
                            }
                            ?>
                            </tbody>
                        </table>
                        <?php
                        break;
                }
                ?>
                </div>
            </div>
        </div>


</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Main JavaScript -->
<script src="<?php echo isset($basePath) ? $basePath : '/pharmacy_ms'; ?>/assets/js/main.js"></script>

<!-- Charts removed -->

</body>
</html>
