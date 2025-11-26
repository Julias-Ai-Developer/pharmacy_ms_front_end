<?php include __DIR__ . '/../includes/header.php'; ?>

<?php include __DIR__ . '/../includes/sidenav.php'; ?>

<!-- ==================== Main Content ==================== -->
<main class="main-content" id="mainContent">

    <div class="module-content fade-in" id="inventory">
        <?php
        // Show status notifications
        if (isset($_GET['status'])) {
            $status = $_GET['status'];
            $messages = [
                'created' => ['Success!', 'Medicine added successfully', 'success'],
                'updated' => ['Success!', 'Medicine updated successfully', 'success'],
                'deleted' => ['Success!', 'Medicine deleted successfully', 'success'],
                'error' => ['Error!', 'An error occurred. Please try again.', 'danger'],
                'invalid' => ['Invalid!', 'Invalid data provided', 'warning']
            ];
            
            if (isset($messages[$status])) {
                list($title, $message, $type) = $messages[$status];
                echo "<script>
                    document.addEventListener('DOMContentLoaded', function() {
                        showNotification('$title', '$message', '$type');
                    });
                </script>";
            }
        }
        ?>
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 style="color: var(--primary); font-weight: 700;">Inventory Management</h2>
            <button class="btn btn-primary-custom" data-bs-toggle="modal" data-bs-target="#medicineAddModal">
                <i class="bi bi-plus-circle me-2"></i>
                Add New Medicine
            </button>
        </div>

        <!-- Filters -->
        <div class="glass-card mb-4">
            <div class="row g-3">
                <div class="col-md-4">
                    <input type="text" id="inventorySearchInput" class="form-control form-control-glass" 
                        placeholder="Search medicines..." 
                        oninput="searchTable(this.value, '.table-glass')">
                </div>
                <div class="col-md-3">
                    <select id="categoryFilter" class="form-control form-control-glass" 
                        onchange="filterTableByColumn(this.value, '.table-glass', 1)">
                        <option>All Categories</option>
                        <option>Pain Relief</option>
                        <option>Antibiotics</option>
                        <option>Vitamins</option>
                        <option>Cardiovascular</option>
                        <option>Diabetes</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select id="statusFilter" class="form-control form-control-glass" 
                        onchange="filterTableByColumn(this.value, '.table-glass', 6)">
                        <option>All Status</option>
                        <option>In Stock</option>
                        <option>Low Stock</option>
                        <option>Out of Stock</option>
                        <option>Critical</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-primary-custom w-100" onclick="document.getElementById('inventorySearchInput').value=''; document.getElementById('categoryFilter').value='All Categories'; document.getElementById('statusFilter').value='All Status'; searchTable('', '.table-glass');">
                        <i class="bi bi-arrow-clockwise"></i> Reset
                    </button>
                </div>
            </div>
        </div>

        <!-- Inventory Table -->
        <div class="glass-card">
            <h5 class="mb-4" style="font-weight: 600;">Medicine List</h5>
            <div class="table-responsive">
                <table class="table table-glass mb-0">
                    <thead>
                        <tr>
                            <th>Medicine Name</th>
                            <th>Category</th>
                            <th>Batch No.</th>
                            <th>Stock</th>
                            <th>Price</th>
                            <th>Expiry Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        require_once __DIR__ . '/../config/db.php';

                        $result = $mysqli->query("SELECT id, name, category, batch_no, stock, price, expiry_date FROM medicines ORDER BY name ASC");
                        if ($result && $result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $statusClass = 'badge-success';
                                $statusLabel = 'In Stock';
                                if ((int)$row['stock'] <= 10) {
                                    $statusClass = 'badge-danger';
                                    $statusLabel = 'Critical';
                                } elseif ((int)$row['stock'] <= 20) {
                                    $statusClass = 'badge-warning';
                                    $statusLabel = 'Low Stock';
                                }
                                $priceUgx = 'UGX ' . number_format((float)$row['price'], 0);
                                $stockUnits = (int)$row['stock'] . ' units';
                                $expiry = date('M Y', strtotime($row['expiry_date']));
                                echo '<tr>';
                                echo '<td><strong>' . htmlspecialchars($row['name']) . '</strong></td>';
                                echo '<td>' . htmlspecialchars($row['category']) . '</td>';
                                echo '<td>' . htmlspecialchars($row['batch_no']) . '</td>';
                                echo '<td>' . htmlspecialchars($stockUnits) . '</td>';
                                echo '<td>' . htmlspecialchars($priceUgx) . '</td>';
                                echo '<td>' . htmlspecialchars($expiry) . '</td>';
                                echo '<td><span class="badge-custom ' . $statusClass . '">' . $statusLabel . '</span></td>';
                                echo '<td>';
                                echo '<button class="btn btn-sm btn-link edit-medicine-btn" data-bs-toggle="modal" data-bs-target="#medicineEditModal" data-id="' . (int)$row['id'] . '" data-name="' . htmlspecialchars($row['name']) . '" data-category="' . htmlspecialchars($row['category']) . '" data-batch_no="' . htmlspecialchars($row['batch_no']) . '" data-stock="' . (int)$row['stock'] . '" data-price="' . (float)$row['price'] . '" data-expiry_date="' . htmlspecialchars($row['expiry_date']) . '"><i class="bi bi-pencil"></i></button>';
                                echo '<button class="btn btn-sm btn-link text-danger" data-bs-toggle="modal" data-bs-target="#deleteMedicineModal" data-id="' . (int)$row['id'] . '" data-name="' . htmlspecialchars($row['name'], ENT_QUOTES) . '"><i class="bi bi-trash"></i></button>';
                                echo '</td>';
                                echo '</tr>';
                            }
                        } else {
                            echo '<tr><td colspan="8" class="text-center text-muted">No medicines found</td></tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center mt-4">
                <p class="text-muted mb-0">Inventory loaded from database</p>
                <nav>
                    <ul class="pagination mb-0">
                        <li class="page-item disabled">
                            <a class="page-link" href="#">Previous</a>
                        </li>
                        <li class="page-item active"><a class="page-link" href="#">1</a></li>
                        <li class="page-item"><a class="page-link" href="#">2</a></li>
                        <li class="page-item"><a class="page-link" href="#">3</a></li>
                        <li class="page-item">
                            <a class="page-link" href="#">Next</a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>


</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script src="<?php echo isset($basePath) ? $basePath : '/pharmacy_ms'; ?>/assets/js/main.js"></script>
<script src="<?php echo isset($basePath) ? $basePath : '/pharmacy_ms'; ?>/assets/js/inventory.js"></script>
<script>
    const editModal = document.getElementById('medicineEditModal');
    if (editModal) {
        editModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            if (!button) return;
            document.getElementById('edit_id').value = button.getAttribute('data-id');
            document.getElementById('edit_name').value = button.getAttribute('data-name');
            document.getElementById('edit_category').value = button.getAttribute('data-category');
            document.getElementById('edit_batch_no').value = button.getAttribute('data-batch_no');
            document.getElementById('edit_stock').value = button.getAttribute('data-stock');
            document.getElementById('edit_price').value = button.getAttribute('data-price');
            document.getElementById('edit_expiry_date').value = button.getAttribute('data-expiry_date');
        });
    }
</script>
<div class="modal fade" id="medicineAddModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Medicine</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="post" action="../pharmacy_ms/actions/medicines.php?action=create">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input name="name" type="text" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Category</label>
                        <select name="category" class="form-control" required>
                            <option value="Pain Relief">Pain Relief</option>
                            <option value="Antibiotics">Antibiotics</option>
                            <option value="Vitamins">Vitamins</option>
                            <option value="Cardiovascular">Cardiovascular</option>
                            <option value="Diabetes">Diabetes</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Batch No.</label>
                        <input name="batch_no" type="text" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Stock</label>
                        <input name="stock" type="number" min="0" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Price</label>
                        <input name="price" type="number" step="0.01" min="0" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Expiry Date</label>
                        <input name="expiry_date" type="date" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="medicineEditModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Medicine</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="post" action="../pharmacy_ms/actions/medicines.php?action=update">
                <input type="hidden" name="id" id="edit_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input name="name" id="edit_name" type="text" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Category</label>
                        <select name="category" id="edit_category" class="form-control" required>
                            <option value="Pain Relief">Pain Relief</option>
                            <option value="Antibiotics">Antibiotics</option>
                            <option value="Vitamins">Vitamins</option>
                            <option value="Cardiovascular">Cardiovascular</option>
                            <option value="Diabetes">Diabetes</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Batch No.</label>
                        <input name="batch_no" id="edit_batch_no" type="text" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Stock</label>
                        <input name="stock" id="edit_stock" type="number" min="0" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Price</label>
                        <input name="price" id="edit_price" type="number" step="0.01" min="0" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Expiry Date</label>
                        <input name="expiry_date" id="edit_expiry_date" type="date" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Medicine Confirmation Modal -->
<div class="modal fade" id="deleteMedicineModal" tabindex="-1" aria-labelledby="deleteMedicineModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteMedicineModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="post" action="<?php echo $basePath; ?>/actions/medicines.php?action=delete">
                <input type="hidden" name="id" id="delete_medicine_id">
                <div class="modal-body">
                    <p>Are you sure you want to delete <strong id="delete_medicine_name"></strong>?</p>
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

<script>
// Handle delete modal data population
const deleteMedicineModal = document.getElementById('deleteMedicineModal');
if (deleteMedicineModal) {
    deleteMedicineModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const medicineId = button.getAttribute('data-id');
        const medicineName = button.getAttribute('data-name');
        
        document.getElementById('delete_medicine_id').value = medicineId;
        document.getElementById('delete_medicine_name').textContent = medicineName;
    });
}
</script>

</body>
</html>
