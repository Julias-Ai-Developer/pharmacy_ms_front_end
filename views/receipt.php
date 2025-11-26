<?php
require_once __DIR__ . '/../config/db.php';

// Get sale ID from URL parameter
$saleId = isset($_GET['sale_id']) ? (int)$_GET['sale_id'] : 0;

// If no sale_id is provided, show list of recent receipts
if ($saleId <= 0) {
    // Fetch recent sales
    $stmt = $mysqli->prepare('SELECT id, payment_method, total, created_at FROM sales ORDER BY created_at DESC LIMIT 50');
    $stmt->execute();
    $result = $stmt->get_result();
    $recentSales = [];
    while ($row = $result->fetch_assoc()) {
        $recentSales[] = $row;
    }
    $stmt->close();
    
    // Show receipts list view
    $showList = true;
    $sale = null;
    $items = [];
} else {
    // Fetch sale details
    $stmtSale = $mysqli->prepare('SELECT id, payment_method, total, created_at FROM sales WHERE id = ? LIMIT 1');
    $stmtSale->bind_param('i', $saleId);
    $stmtSale->execute();
    $resultSale = $stmtSale->get_result();
    $sale = $resultSale->fetch_assoc();
    $stmtSale->close();

    if (!$sale) {
        header('Location: receipt');
        exit;
    }

    // Fetch sale items
    $stmtItems = $mysqli->prepare('SELECT name, price, quantity FROM sale_items WHERE sale_id = ?');
    $stmtItems->bind_param('i', $saleId);
    $stmtItems->execute();
    $resultItems = $stmtItems->get_result();
    $items = [];
    while ($row = $resultItems->fetch_assoc()) {
        $items[] = $row;
    }
    $stmtItems->close();
    
    $showList = false;
    
    // Calculate subtotal
    $subtotal = 0;
    foreach ($items as $item) {
        $subtotal += $item['price'] * $item['quantity'];
    }
}
?>

<?php include __DIR__ . '/../includes/header.php'; ?>

<style>
    .receipt-container {
        max-width: 800px;
        margin: 0 auto;
        background: var(--glass-bg);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid var(--border-color);
        border-radius: 16px;
        padding: 3rem;
        box-shadow: 0 8px 32px var(--shadow);
    }

    .receipt-header {
        text-align: center;
        border-bottom: 2px solid var(--border-color);
        padding-bottom: 2rem;
        margin-bottom: 2rem;
    }

    .receipt-logo {
        width: 80px;
        height: 80px;
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1rem;
        color: white;
        font-size: 40px;
    }

    .receipt-title {
        font-size: 2rem;
        font-weight: 700;
        color: var(--primary);
        margin-bottom: 0.5rem;
    }

    .receipt-subtitle {
        color: var(--text-secondary);
        font-size: 0.95rem;
    }

    .receipt-info {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
        margin-bottom: 2rem;
        padding: 1.5rem;
        background: rgba(10, 126, 186, 0.05);
        border-radius: 12px;
    }

    .info-item {
        display: flex;
        flex-direction: column;
    }

    .info-label {
        font-size: 0.85rem;
        color: var(--text-secondary);
        margin-bottom: 0.25rem;
    }

    .info-value {
        font-weight: 600;
        color: var(--text-primary);
    }

    .receipt-items {
        margin-bottom: 2rem;
    }

    .items-table {
        width: 100%;
        border-collapse: collapse;
    }

    .items-table thead {
        background: rgba(10, 126, 186, 0.1);
    }

    .items-table th {
        padding: 1rem;
        text-align: left;
        font-weight: 600;
        color: var(--text-primary);
        border-bottom: 2px solid var(--border-color);
    }

    .items-table td {
        padding: 1rem;
        color: var(--text-secondary);
        border-bottom: 1px solid var(--border-color);
    }

    .items-table tbody tr:hover {
        background: rgba(10, 126, 186, 0.05);
    }

    .receipt-totals {
        border-top: 2px solid var(--border-color);
        padding-top: 1.5rem;
        margin-top: 1.5rem;
    }

    .total-row {
        display: flex;
        justify-content: space-between;
        padding: 0.75rem 0;
        font-size: 1rem;
    }

    .total-row.grand-total {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--primary);
        border-top: 2px solid var(--border-color);
        margin-top: 1rem;
        padding-top: 1rem;
    }

    .receipt-footer {
        text-align: center;
        margin-top: 3rem;
        padding-top: 2rem;
        border-top: 2px dashed var(--border-color);
    }

    .thank-you {
        font-size: 1.25rem;
        font-weight: 600;
        color: var(--primary);
        margin-bottom: 0.5rem;
    }

    .footer-note {
        color: var(--text-secondary);
        font-size: 0.9rem;
    }

    .action-buttons {
        display: flex;
        gap: 1rem;
        justify-content: center;
        margin-top: 2rem;
    }

    /* Print Styles */
    @media print {
        body {
            background: white;
            padding: 0;
        }

        .header-glass,
        .sidebar,
        .footer-glass,
        .no-print {
            display: none !important;
        }

        .main-content {
            margin: 0 !important;
            padding: 0 !important;
        }

        .receipt-container {
            max-width: 100%;
            box-shadow: none;
            border: none;
            padding: 0;
            margin: 0;
        }

        .action-buttons {
            display: none !important;
        }
    }

    @media (max-width: 768px) {
        .receipt-container {
            padding: 1.5rem;
        }

        .receipt-info {
            grid-template-columns: 1fr;
        }

        .items-table {
            font-size: 0.9rem;
        }

        .items-table th,
        .items-table td {
            padding: 0.75rem 0.5rem;
        }
    }
</style>

<?php include __DIR__ . '/../includes/sidenav.php'; ?>

<main class="main-content" id="mainContent">
    <?php if ($showList): ?>
        <!-- List View: Show recent receipts -->
        <div class="module-content active" id="receipts">
            <h2 class="mb-4" style="color: var(--primary); font-weight: 700;">Sales Receipts</h2>
            
            <div class="glass-card">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 style="font-weight: 600; margin: 0;">Recent Sales</h5>
                    <a href="pos" class="btn btn-primary-custom">
                        <i class="bi bi-plus-circle me-2"></i>
                        New Sale
                    </a>
                </div>
                
                <!-- Search Bar -->
                <div class="mb-4">
                    <input type="text" id="receiptsSearchInput" class="form-control form-control-glass" 
                        placeholder="Search receipts by number, date, or payment method..."
                        oninput="searchTable(this.value, '.table-glass')">
                </div>
                
                <?php if (count($recentSales) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-glass">
                        <thead>
                            <tr>
                                <th>Receipt #</th>
                                <th>Date & Time</th>
                                <th>Payment Method</th>
                                <th style="text-align: right;">Total</th>
                                <th style="text-align: center;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentSales as $s): ?>
                            <tr style="cursor: pointer;" onclick="window.location.href='receipt?sale_id=<?php echo $s['id']; ?>'">
                                <td><strong>#<?php echo str_pad($s['id'], 6, '0', STR_PAD_LEFT); ?></strong></td>
                                <td><?php echo date('M d, Y - h:i A', strtotime($s['created_at'])); ?></td>
                                <td>
                                    <span class="badge badge-custom badge-info">
                                        <?php echo htmlspecialchars($s['payment_method']); ?>
                                    </span>
                                </td>
                                <td style="text-align: right; font-weight: 600; color: var(--primary);">
                                    UGX <?php echo number_format($s['total'], 0); ?>
                                </td>
                                <td style="text-align: center;">
                                    <a href="receipt?sale_id=<?php echo $s['id']; ?>" class="btn btn-sm btn-primary-custom" onclick="event.stopPropagation();">
                                        <i class="bi bi-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="text-center py-5">
                    <i class="bi bi-receipt" style="font-size: 64px; color: var(--text-secondary); opacity: 0.3;"></i>
                    <p class="text-muted mt-3">No sales receipts found</p>
                    <a href="pos" class="btn btn-primary-custom mt-2">
                        <i class="bi bi-cart-fill me-2"></i>
                        Go to POS
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
    <?php else: ?>
        <!-- Individual Receipt View -->
        <div class="container-fluid">
            <!-- Back Button (Mobile/Desktop consistent) -->
            <div class="mb-4">
                <a href="receipt" class="btn btn-outline-secondary no-print" style="border-radius: 12px;">
                    <i class="bi bi-arrow-left me-2"></i> Back to List
                </a>
            </div>

            <div class="receipt-container">
                <!-- Receipt Header -->
                <div class="receipt-header">
                    <div class="receipt-logo">
                        <i class="bi bi-heart-pulse-fill"></i>
                    </div>
                    <h1 class="receipt-title">HealthPlus Pharmacy</h1>
                    <p class="receipt-subtitle">Your Health, Our Priority</p>
                    <p class="receipt-subtitle">Kampala, Uganda | Tel: +256 700 000 000</p>
                </div>

                <!-- Receipt Info -->
                <div class="receipt-info">
                    <div class="info-item">
                        <span class="info-label">Receipt Number</span>
                        <span class="info-value">#<?php echo str_pad($saleId, 6, '0', STR_PAD_LEFT); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Date & Time</span>
                        <span class="info-value"><?php echo date('M d, Y - h:i A', strtotime($sale['created_at'])); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Payment Method</span>
                        <span class="info-value"><?php echo htmlspecialchars($sale['payment_method']); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Served By</span>
                        <span class="info-value">Cashier</span>
                    </div>
                </div>

                <!-- Receipt Items -->
                <div class="receipt-items">
                    <h5 style="font-weight: 600; margin-bottom: 1rem; color: var(--text-primary);">Items Purchased</h5>
                    <table class="items-table">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th style="text-align: center;">Qty</th>
                                <th style="text-align: right;">Price</th>
                                <th style="text-align: right;">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($items as $item): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['name']); ?></td>
                                <td style="text-align: center;"><?php echo $item['quantity']; ?></td>
                                <td style="text-align: right;">UGX <?php echo number_format($item['price'], 0); ?></td>
                                <td style="text-align: right; font-weight: 600;">UGX <?php echo number_format($item['price'] * $item['quantity'], 0); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Receipt Totals -->
                <div class="receipt-totals">
                    <div class="total-row">
                        <span>Subtotal:</span>
                        <strong>UGX <?php echo number_format($subtotal, 0); ?></strong>
                    </div>
                    <div class="total-row">
                        <span>Discount:</span>
                        <strong>UGX <?php echo number_format($subtotal - $sale['total'], 0); ?></strong>
                    </div>
                    <div class="total-row grand-total">
                        <span>TOTAL PAID:</span>
                        <strong>UGX <?php echo number_format($sale['total'], 0); ?></strong>
                    </div>
                </div>

                <!-- Receipt Footer -->
                <div class="receipt-footer">
                    <p class="thank-you">Thank You for Your Purchase!</p>
                    <p class="footer-note">This is a computer-generated receipt.</p>
                    <p class="footer-note">For any queries, please contact us at info@healthplus.ug</p>
                </div>

                <!-- Action Buttons -->
                <div class="action-buttons no-print">
                    <button class="btn btn-primary-custom" onclick="window.print()">
                        <i class="bi bi-printer me-2"></i>
                        Print Receipt
                    </button>
                    <button class="btn btn-outline-secondary" style="border-radius: 12px; padding: 12px 24px;" onclick="window.location.href='pos'">
                        <i class="bi bi-cart me-2"></i>
                        New Sale
                    </button>
                </div>
            </div>
        </div>
    <?php endif; ?>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?php echo isset($basePath) ? $basePath : '/pharmacy_ms'; ?>/assets/js/main.js"></script>
</body>
</html>
