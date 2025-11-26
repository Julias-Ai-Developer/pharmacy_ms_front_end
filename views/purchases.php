<?php include __DIR__ . '/../includes/header.php'; ?>

<!-- Link CSS -->
<link rel="stylesheet" href="../assets/css/style.css">

<?php include __DIR__ . '/../includes/sidenav.php'; ?>

<!-- ==================== Main Content ==================== -->
<main class="main-content" id="mainContent">

        <div class="module-content fade-in" id="purchases">
            <?php include __DIR__ . '/../includes/notifications.php'; ?>
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 style="color: var(--primary); font-weight: 700;">Purchases & Suppliers</h2>
                <button class="btn btn-primary-custom" data-bs-toggle="modal" data-bs-target="#purchaseOrderModal">
                    <i class="bi bi-plus-circle me-2"></i>
                    New Purchase Order
                </button>
            </div>

            <!-- Stats -->
            <?php 
            require_once __DIR__ . '/../config/db.php';
            
            // Get total purchase orders count
            $totalPurchases = 0;
            $totalResult = $mysqli->query("SELECT COUNT(*) as total FROM purchase_orders");
            if ($totalResult) {
                $totalPurchases = $totalResult->fetch_assoc()['total'];
            }
            
            // Get active suppliers count
            $activeSuppliers = 0;
            $suppliersResult = $mysqli->query("SELECT COUNT(*) as total FROM suppliers WHERE active = 1");
            if ($suppliersResult) {
                $activeSuppliers = $suppliersResult->fetch_assoc()['total'];
            }
            
            // Get pending orders count
            $pendingOrders = 0;
            $pendingResult = $mysqli->query("SELECT COUNT(*) as total FROM purchase_orders WHERE status = 'Pending'");
            if ($pendingResult) {
                $pendingOrders = $pendingResult->fetch_assoc()['total'];
            }
            ?>
            <div class="row mb-4">
                <div class="col-md-4 mb-3">
                    <div class="glass-card">
                        <div class="d-flex align-items-center gap-3">
                            <div class="product-icon">
                                <i class="bi bi-cart-check"></i>
                            </div>
                            <div>
                                <small class="text-muted">Total Purchases</small>
                                <h4 class="mb-0" style="color: var(--primary); font-weight: 700;"><?php echo number_format($totalPurchases); ?></h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="glass-card">
                        <div class="d-flex align-items-center gap-3">
                            <div class="product-icon" style="background: linear-gradient(135deg, #06B6A8, #4ADEDE);">
                                <i class="bi bi-truck"></i>
                            </div>
                            <div>
                                <small class="text-muted">Active Suppliers</small>
                                <h4 class="mb-0" style="color: var(--secondary); font-weight: 700;"><?php echo number_format($activeSuppliers); ?></h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="glass-card">
                        <div class="d-flex align-items-center gap-3">
                            <div class="product-icon" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
                                <i class="bi bi-clock-history"></i>
                            </div>
                            <div>
                                <small class="text-muted">Pending Orders</small>
                                <h4 class="mb-0" style="color: #f59e0b; font-weight: 700;"><?php echo number_format($pendingOrders); ?></h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Purchase Orders Table -->
            <div class="glass-card mb-4">
                <h5 class="mb-4" style="font-weight: 600;">Recent Purchase Orders</h5>
                <div class="table-responsive">
                    <table class="table table-glass mb-0">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Supplier</th>
                                <th>Date</th>
                                <th>Items</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php 
                        require_once __DIR__ . '/../config/db.php';
                        $orders = $mysqli->query("SELECT po.id, po.order_code, po.order_date, po.items, po.amount, po.status, po.supplier_id, s.name AS supplier_name FROM purchase_orders po LEFT JOIN suppliers s ON s.id = po.supplier_id ORDER BY po.order_date DESC");
                        if ($orders && $orders->num_rows > 0) {
                            while ($o = $orders->fetch_assoc()) {
                                $dateStr = $o['order_date'] ? date('M d, Y', strtotime($o['order_date'])) : '-';
                                $badge = 'badge-info'; if ($o['status'] === 'Pending') $badge = 'badge-warning'; if ($o['status'] === 'Received') $badge = 'badge-success';
                                echo '<tr>';
                                echo '<td><strong>' . htmlspecialchars($o['order_code']) . '</strong></td>';
                                echo '<td>' . htmlspecialchars($o['supplier_name'] ?: '-') . '</td>';
                                echo '<td>' . $dateStr . '</td>';
                                echo '<td>' . htmlspecialchars($o['items']) . '</td>';
                                echo '<td>UGX ' . number_format((float)$o['amount'], 0) . '</td>';
                                echo '<td><span class="badge-custom ' . $badge . '">' . htmlspecialchars($o['status']) . '</span></td>';
                                echo '<td>';
                                echo '<button class="btn btn-sm btn-link" data-bs-toggle="modal" data-bs-target="#orderEditModal" data-id="' . (int)$o['id'] . '" data-code="' . htmlspecialchars($o['order_code']) . '" data-supplier="' . htmlspecialchars($o['supplier_name']) . '" data-supplier-id="' . (int)$o['supplier_id'] . '" data-date="' . htmlspecialchars($o['order_date']) . '" data-items="' . htmlspecialchars($o['items']) . '" data-amount="' . htmlspecialchars($o['amount']) . '" data-status="' . htmlspecialchars($o['status']) . '"><i class="bi bi-pencil"></i></button>';
                                echo '<button class="btn btn-sm btn-link text-danger" data-bs-toggle="modal" data-bs-target="#deleteOrderModal" data-id="' . (int)$o['id'] . '" data-code="' . htmlspecialchars($o['order_code']) . '"><i class="bi bi-trash"></i></button>';
                                echo '</td>';
                                echo '</tr>';
                            }
                        } else {
                            echo '<tr><td colspan="7" class="text-center text-muted">No purchase orders found</td></tr>';
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Suppliers List -->
            <div class="glass-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0" style="font-weight: 600;">Suppliers</h5>
                    <button class="btn btn-primary-custom btn-sm" data-bs-toggle="modal" data-bs-target="#supplierAddModal"><i class="bi bi-plus-circle me-2"></i>Add Supplier</button>
                </div>
                <div class="row g-3">
                <?php require_once __DIR__ . '/../config/db.php';
                // Use LEFT JOIN to count actual purchase orders for each supplier
                $result = $mysqli->query("
                    SELECT 
                        s.id, 
                        s.name, 
                        s.phone, 
                        s.email, 
                        s.active,
                        COUNT(po.id) as total_orders
                    FROM suppliers s
                    LEFT JOIN purchase_orders po ON s.id = po.supplier_id
                    GROUP BY s.id, s.name, s.phone, s.email, s.active
                    ORDER BY s.name ASC
                ");
                if ($result && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $dot = '<span class="status-dot ' . ((int)$row['active'] ? 'active' : '') . '"></span>';
                        echo '<div class="col-md-6 col-lg-4">';
                        echo '<div class="glass-card">';
                        echo '<div class="d-flex justify-content-between align-items-start mb-3">';
                        echo '<div>';
                        echo '<h6 style="font-weight: 600;">' . htmlspecialchars($row['name']) . '</h6>';
                        echo '<small class="text-muted d-block"><i class="bi bi-telephone me-1"></i> ' . htmlspecialchars($row['phone']) . '</small>';
                        echo '<small class="text-muted"><i class="bi bi-envelope me-1"></i> ' . htmlspecialchars($row['email']) . '</small>';
                        echo '</div>';
                        echo $dot;
                        echo '</div>';
                        echo '<div class="d-flex justify-content-between">';
                        echo '<p class="text-muted mb-0"><small>Total Orders: ' . (int)$row['total_orders'] . '</small></p>';
                        echo '<div>';
                        echo '<button class="btn btn-sm btn-link" data-bs-toggle="modal" data-bs-target="#supplierEditModal" data-id="' . (int)$row['id'] . '" data-name="' . htmlspecialchars($row['name']) . '" data-phone="' . htmlspecialchars($row['phone']) . '" data-email="' . htmlspecialchars($row['email']) . '" data-active="' . (int)$row['active'] . '"><i class="bi bi-pencil"></i></button>';
                        echo '<button class="btn btn-sm btn-link text-danger" data-bs-toggle="modal" data-bs-target="#deleteSupplierModal" data-id="' . (int)$row['id'] . '" data-name="' . htmlspecialchars($row['name']) . '"><i class="bi bi-trash"></i></button>';
                        echo '</div>';
                        echo '</div>';
                        echo '</div>';
                        echo '</div>';
                    }
                } else {
                    echo '<div class="col-12"><p class="text-muted">No suppliers found</p></div>';
                }
                ?>
                </div>
            </div>

            <div class="modal fade" id="supplierAddModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header"><h5 class="modal-title">Add Supplier</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div>
                        <form method="post" action="<?php echo $basePath; ?>/actions/purchases.php?action=create_supplier">
                            <div class="modal-body">
                                <div class="mb-3"><label class="form-label">Name</label><input name="name" type="text" class="form-control" required></div>
                                <div class="mb-3"><label class="form-label">Phone</label><input name="phone" type="text" class="form-control" required></div>
                                <div class="mb-3"><label class="form-label">Email</label><input name="email" type="email" class="form-control" required></div>
                            </div>
                            <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-primary">Save</button></div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="supplierEditModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header"><h5 class="modal-title">Edit Supplier</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div>
                        <form method="post" action="<?php echo $basePath; ?>/actions/purchases.php?action=update_supplier">
                            <input type="hidden" name="id" id="supplier_edit_id">
                            <div class="modal-body">
                                <div class="mb-3"><label class="form-label">Name</label><input name="name" id="supplier_edit_name" type="text" class="form-control" required></div>
                                <div class="mb-3"><label class="form-label">Phone</label><input name="phone" id="supplier_edit_phone" type="text" class="form-control" required></div>
                                <div class="mb-3"><label class="form-label">Email</label><input name="email" id="supplier_edit_email" type="email" class="form-control" required></div>
                                <div class="mb-3"><label class="form-label">Active</label><select name="active" id="supplier_edit_active" class="form-control"><option value="1">Active</option><option value="0">Inactive</option></select></div>
                            </div>
                            <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-primary">Update</button></div>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="modal fade" id="purchaseOrderModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header"><h5 class="modal-title">New Purchase Order</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div>
                        <form method="post" action="<?php echo $basePath; ?>/actions/purchases.php?action=create_order">
                            <div class="modal-body">
                                <div class="mb-3"><label class="form-label">Order Code</label><input name="order_code" type="text" class="form-control" placeholder="e.g., PO-2025-010" required></div>
                                <div class="mb-3"><label class="form-label">Supplier</label>
                                    <select name="supplier_id" class="form-control" required>
                                        <option value="">Select supplier</option>
                                        <?php $sup = $mysqli->query("SELECT id, name FROM suppliers WHERE active=1 ORDER BY name"); if ($sup) { while ($s = $sup->fetch_assoc()) { echo '<option value="' . (int)$s['id'] . '">' . htmlspecialchars($s['name']) . '</option>'; } } ?>
                                    </select>
                                </div>
                                <div class="mb-3"><label class="form-label">Order Date</label><input name="order_date" type="date" class="form-control" required></div>
                                <div class="mb-3"><label class="form-label">Items</label><textarea name="items" class="form-control" rows="2" placeholder="Comma separated list" required></textarea></div>
                                <div class="mb-3"><label class="form-label">Amount (UGX)</label><input name="amount" type="number" step="0.01" class="form-control" required></div>
                                <div class="mb-3"><label class="form-label">Status</label><select name="status" class="form-control"><option>Pending</option><option>In Transit</option><option>Received</option></select></div>
                            </div>
                            <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-primary">Create</button></div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="orderEditModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header"><h5 class="modal-title">Edit Purchase Order</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div>
                        <form method="post" action="<?php echo $basePath; ?>/actions/purchases.php?action=update_order">
                            <input type="hidden" name="id" id="order_edit_id">
                            <div class="modal-body">
                                <div class="mb-3"><label class="form-label">Order Code</label><input name="order_code" id="order_edit_code" type="text" class="form-control" required></div>
                                <div class="mb-3"><label class="form-label">Supplier</label>
                                    <select name="supplier_id" id="order_edit_supplier_id" class="form-control" required>
                                        <option value="">Select supplier</option>
                                        <?php $sup2 = $mysqli->query("SELECT id, name FROM suppliers WHERE active=1 ORDER BY name"); if ($sup2) { while ($s2 = $sup2->fetch_assoc()) { echo '<option value="' . (int)$s2['id'] . '">' . htmlspecialchars($s2['name']) . '</option>'; } } ?>
                                    </select>
                                </div>
                                <div class="mb-3"><label class="form-label">Order Date</label><input name="order_date" id="order_edit_date" type="date" class="form-control" required></div>
                                <div class="mb-3"><label class="form-label">Items</label><textarea name="items" id="order_edit_items" class="form-control" rows="2" required></textarea></div>
                                <div class="mb-3"><label class="form-label">Amount (UGX)</label><input name="amount" id="order_edit_amount" type="number" step="0.01" class="form-control" required></div>
                                <div class="mb-3"><label class="form-label">Status</label><select name="status" id="order_edit_status" class="form-control"><option>Pending</option><option>In Transit</option><option>Received</option></select></div>
                            </div>
                            <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-primary">Update</button></div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Supplier Confirmation Modal -->
        <div class="modal fade" id="deleteSupplierModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Confirm Delete</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="post" action="<?php echo $basePath; ?>/actions/purchases.php?action=delete_supplier">
                        <input type="hidden" name="id" id="delete_supplier_id">
                        <div class="modal-body">
                            <p>Are you sure you want to delete supplier <strong id="delete_supplier_name"></strong>?</p>
                            <p class="text-danger"><i class="bi bi-exclamation-triangle me-2"></i>This action cannot be undone.</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-danger">Delete</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Delete Order Confirmation Modal -->
        <div class="modal fade" id="deleteOrderModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Confirm Delete</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="post" action="<?php echo $basePath; ?>/actions/purchases.php?action=delete_order">
                        <input type="hidden" name="id" id="delete_order_id">
                        <div class="modal-body">
                            <p>Are you sure you want to delete purchase order <strong id="delete_order_code"></strong>?</p>
                            <p class="text-danger"><i class="bi bi-exclamation-triangle me-2"></i>This action cannot be undone.</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-danger">Delete</button>
                        </div>
                    </form>
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
// Wait for Bootstrap to load
document.addEventListener('DOMContentLoaded', function() {
  // Supplier Edit Modal
  const supplierEditModal = document.getElementById('supplierEditModal');
  if (supplierEditModal) {
    supplierEditModal.addEventListener('show.bs.modal', function (event) {
      const button = event.relatedTarget;
      if (!button) return;
      
      document.getElementById('supplier_edit_id').value = button.getAttribute('data-id') || '';
      document.getElementById('supplier_edit_name').value = button.getAttribute('data-name') || '';
      document.getElementById('supplier_edit_phone').value = button.getAttribute('data-phone') || '';
      document.getElementById('supplier_edit_email').value = button.getAttribute('data-email') || '';
      document.getElementById('supplier_edit_active').value = button.getAttribute('data-active') || '1';
    });
  }

  // Order Edit Modal
  const orderEditModal = document.getElementById('orderEditModal');
  if (orderEditModal) {
    orderEditModal.addEventListener('show.bs.modal', function (event) {
      const button = event.relatedTarget;
      if (!button) return;
      
      document.getElementById('order_edit_id').value = button.getAttribute('data-id') || '';
      document.getElementById('order_edit_code').value = button.getAttribute('data-code') || '';
      document.getElementById('order_edit_supplier_id').value = button.getAttribute('data-supplier-id') || '';
      const rawDate = button.getAttribute('data-date');
      document.getElementById('order_edit_date').value = rawDate ? rawDate.substring(0,10) : '';
      document.getElementById('order_edit_items').value = button.getAttribute('data-items') || '';
      document.getElementById('order_edit_amount').value = button.getAttribute('data-amount') || '';
      document.getElementById('order_edit_status').value = button.getAttribute('data-status') || 'Pending';
    });
  }

  // Delete Supplier Modal
  const deleteSupplierModal = document.getElementById('deleteSupplierModal');
  if (deleteSupplierModal) {
    deleteSupplierModal.addEventListener('show.bs.modal', function (event) {
      const button = event.relatedTarget;
      if (!button) return;
      
      document.getElementById('delete_supplier_id').value = button.getAttribute('data-id') || '';
      document.getElementById('delete_supplier_name').textContent = button.getAttribute('data-name') || '';
    });
  }

  // Delete Order Modal
  const deleteOrderModal = document.getElementById('deleteOrderModal');
  if (deleteOrderModal) {
    deleteOrderModal.addEventListener('show.bs.modal', function (event) {
      const button = event.relatedTarget;
      if (!button) return;
      
      document.getElementById('delete_order_id').value = button.getAttribute('data-id') || '';
      document.getElementById('delete_order_code').textContent = button.getAttribute('data-code') || '';
    });
  }
});
</script>

</body>
</html>
