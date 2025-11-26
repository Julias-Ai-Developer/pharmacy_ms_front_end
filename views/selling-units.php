<?php include __DIR__ . '/../includes/header.php'; ?>

<!-- Link CSS -->
<link rel="stylesheet" href="../assets/css/style.css">

<?php include __DIR__ . '/../includes/sidenav.php'; ?>

<!-- Main Content -->
<main class="main-content" id="mainContent">
    <div class="module-content fade-in" id="selling-units">
        <h2 class="mb-4" style="color: var(--primary); font-weight: 700;">Selling Units Management</h2>

        <?php
        // Display session messages
        if (isset($_SESSION['message'])) {
            $message_type = isset($_SESSION['message_type']) ? $_SESSION['message_type'] : 'info';
            echo '<div class="alert alert-' . $message_type . ' alert-dismissible fade show" role="alert">';
            echo '<strong>' . ($message_type === 'success' ? 'Success!' : 'Error!') . '</strong> ' . htmlspecialchars($_SESSION['message']);
            echo '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
            echo '</div>';
            unset($_SESSION['message']);
            unset($_SESSION['message_type']);
        }
        ?>

        <!-- Add New Unit Button -->
        <div class="mb-4">
            <button class="btn btn-primary-custom" data-bs-toggle="modal" data-bs-target="#addUnitModal">
                <i class="bi bi-plus-circle me-2"></i>
                Add New Selling Unit
            </button>
        </div>

        <!-- Selling Units Table -->
        <div class="glass-card">
            <h5 class="mb-4" style="font-weight: 600;">Available Selling Units</h5>
            <div class="table-responsive">
                <table class="table table-glass">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Unit Name</th>
                            <th>Type</th>
                            <th>Conversion Factor</th>
                            <th>Base Unit</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        require_once __DIR__ . '/../config/db.php';
                        
                        $result = $mysqli->query("SELECT * FROM selling_units ORDER BY unit_type, unit_name");
                        
                        if ($result && $result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $statusBadge = $row['status'] === 'Active' ? 
                                    '<span class="badge badge-custom badge-success">Active</span>' : 
                                    '<span class="badge badge-custom badge-danger">Inactive</span>';
                                
                                $isBaseUnit = $row['is_base_unit'] ? 
                                    '<i class="bi bi-check-circle-fill text-success"></i> Yes' : 
                                    '<i class="bi bi-x-circle text-muted"></i> No';
                                
                                $conversionDisplay = $row['conversion_factor'] ? 
                                    number_format($row['conversion_factor'], 2) : 
                                    '-';
                                
                                echo "<tr>";
                                echo "<td><strong>#{$row['id']}</strong></td>";
                                echo "<td><strong>{$row['unit_name']}</strong></td>";
                                echo "<td><span class='badge badge-custom badge-info'>{$row['unit_type']}</span></td>";
                                echo "<td>{$conversionDisplay}</td>";
                                echo "<td>{$isBaseUnit}</td>";
                                echo "<td>{$statusBadge}</td>";
                                echo "<td>";
                                echo "<button class='btn btn-sm btn-outline-primary me-1' onclick='editUnit(" . json_encode($row) . ")' style='border-radius: 8px;'>";
                                echo "<i class='bi bi-pencil'></i>";
                                echo "</button>";
                                echo "<button class='btn btn-sm btn-outline-danger' onclick='deleteUnit({$row['id']}, \"{$row['unit_name']}\")' style='border-radius: 8px;'>";
                                echo "<i class='bi bi-trash'></i>";
                                echo "</button>";
                                echo "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='7' class='text-center text-muted'>No selling units found</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Medicine-Unit Assignments -->
        <div class="glass-card mt-4">
            <h5 class="mb-4" style="font-weight: 600;">Medicine Unit Assignments</h5>
            <p class="text-muted mb-3">Configure which units are available for each medicine and set their prices.</p>
            
            <div class="table-responsive">
                <table class="table table-glass">
                    <thead>
                        <tr>
                            <th>Medicine</th>
                            <th>Unit</th>
                            <th>Price (UGX)</th>
                            <th>Default</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $result = $mysqli->query("
                            SELECT 
                                mu.id,
                                mu.medicine_id,
                                m.name AS medicine_name,
                                su.unit_name,
                                mu.price,
                                mu.is_default,
                                mu.status
                            FROM medicine_units mu
                            INNER JOIN medicines m ON mu.medicine_id = m.id
                            INNER JOIN selling_units su ON mu.unit_id = su.id
                            ORDER BY m.name, mu.is_default DESC
                        ");
                        
                        if ($result && $result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $defaultBadge = $row['is_default'] ? 
                                    '<span class="badge badge-custom badge-primary">Default</span>' : 
                                    '<span class="badge badge-custom badge-secondary">Optional</span>';
                                
                                $statusBadge = $row['status'] === 'Active' ? 
                                    '<span class="badge badge-custom badge-success">Active</span>' : 
                                    '<span class="badge badge-custom badge-danger">Inactive</span>';
                                
                                echo "<tr>";
                                echo "<td><strong>{$row['medicine_name']}</strong></td>";
                                echo "<td><span class='badge badge-custom badge-info'>{$row['unit_name']}</span></td>";
                                echo "<td><strong>" . number_format($row['price'], 0) . "</strong></td>";
                                echo "<td>{$defaultBadge}</td>";
                                echo "<td>{$statusBadge}</td>";
                                echo "<td>";
                                echo "<button class='btn btn-sm btn-outline-primary me-1' onclick='editMedicineUnit(" . json_encode($row) . ")' style='border-radius: 8px;'>";
                                echo "<i class='bi bi-pencil'></i>";
                                echo "</button>";
                                echo "<button class='btn btn-sm btn-outline-danger' onclick='deleteMedicineUnit({$row['id']})' style='border-radius: 8px;'>";
                                echo "<i class='bi bi-trash'></i>";
                                echo "</button>";
                                echo "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='6' class='text-center text-muted'>No medicine-unit assignments found</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            
            <div class="mt-3">
                <button class="btn btn-primary-custom" data-bs-toggle="modal" data-bs-target="#assignUnitModal">
                    <i class="bi bi-link-45deg me-2"></i>
                    Assign Unit to Medicine
                </button>
            </div>
        </div>
    </div>
</main>

<!-- Add Unit Modal -->
<div class="modal fade" id="addUnitModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background: var(--glass-bg); backdrop-filter: blur(20px); border: 1px solid var(--border-color);">
            <div class="modal-header" style="border-bottom: 1px solid var(--border-color);">
                <h5 class="modal-title">Add New Selling Unit</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addUnitForm" method="post" action="../actions/selling-units.php?action=create">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label" style="font-weight: 600;">Unit Name</label>
                        <input type="text" name="unit_name" class="form-control form-control-glass" required placeholder="e.g., Tablet, Packet, Strip">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="font-weight: 600;">Unit Type</label>
                        <select name="unit_type" class="form-control form-control-glass" required>
                            <option value="solid">Solid (Tablets/Capsules)</option>
                            <option value="liquid">Liquid (ml/Bottles)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="font-weight: 600;">Conversion Factor</label>
                        <input type="number" name="conversion_factor" step="0.01" class="form-control form-control-glass" placeholder="e.g., 10 (for 10 tablets per packet)">
                        <small class="text-muted">How many base units in this unit? Leave empty for base units.</small>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" name="is_base_unit" class="form-check-input" id="isBaseUnit">
                        <label class="form-check-label" for="isBaseUnit">This is a base unit (smallest unit)</label>
                    </div>
                </div>
                <div class="modal-footer" style="border-top: 1px solid var(--border-color);">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" style="border-radius: 12px;">Cancel</button>
                    <button type="submit" class="btn btn-primary-custom">
                        <i class="bi bi-save me-2"></i>Save Unit
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Unit Modal -->
<div class="modal fade" id="editUnitModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background: var(--glass-bg); backdrop-filter: blur(20px); border: 1px solid var(--border-color);">
            <div class="modal-header" style="border-bottom: 1px solid var(--border-color);">
                <h5 class="modal-title">Edit Selling Unit</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editUnitForm" method="post" action="../actions/selling-units.php?action=update">
                <input type="hidden" name="id" id="editUnitId">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label" style="font-weight: 600;">Unit Name</label>
                        <input type="text" name="unit_name" id="editUnitName" class="form-control form-control-glass" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="font-weight: 600;">Unit Type</label>
                        <select name="unit_type" id="editUnitType" class="form-control form-control-glass" required>
                            <option value="solid">Solid (Tablets/Capsules)</option>
                            <option value="liquid">Liquid (ml/Bottles)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="font-weight: 600;">Conversion Factor</label>
                        <input type="number" name="conversion_factor" id="editConversionFactor" step="0.01" class="form-control form-control-glass">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="font-weight: 600;">Status</label>
                        <select name="status" id="editStatus" class="form-control form-control-glass" required>
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer" style="border-top: 1px solid var(--border-color);">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" style="border-radius: 12px;">Cancel</button>
                    <button type="submit" class="btn btn-primary-custom">
                        <i class="bi bi-save me-2"></i>Update Unit
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Assign Unit to Medicine Modal -->
<div class="modal fade" id="assignUnitModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background: var(--glass-bg); backdrop-filter: blur(20px); border: 1px solid var(--border-color);">
            <div class="modal-header" style="border-bottom: 1px solid var(--border-color);">
                <h5 class="modal-title">Assign Unit to Medicine</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="assignUnitForm" method="post" action="../actions/selling-units.php?action=assign">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label" style="font-weight: 600;">Medicine</label>
                        <select name="medicine_id" class="form-control form-control-glass" required>
                            <option value="">Select Medicine</option>
                            <?php
                            $medicines = $mysqli->query("SELECT id, name FROM medicines ORDER BY name");
                            while ($med = $medicines->fetch_assoc()) {
                                echo "<option value='{$med['id']}'>{$med['name']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="font-weight: 600;">Selling Unit</label>
                        <select name="unit_id" class="form-control form-control-glass" required>
                            <option value="">Select Unit</option>
                            <?php
                            $units = $mysqli->query("SELECT id, unit_name FROM selling_units WHERE status = 'Active' ORDER BY unit_name");
                            while ($unit = $units->fetch_assoc()) {
                                echo "<option value='{$unit['id']}'>{$unit['unit_name']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="font-weight: 600;">Price (UGX)</label>
                        <input type="number" name="price" min="0" class="form-control form-control-glass" required placeholder="e.g., 5000">
                        <small class="text-muted">Enter price without commas (e.g., 1000000 for 1 million UGX)</small>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" name="is_default" class="form-check-input" id="isDefaultUnit">
                        <label class="form-check-label" for="isDefaultUnit">Set as default selling unit</label>
                    </div>
                </div>
                <div class="modal-footer" style="border-top: 1px solid var(--border-color);">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" style="border-radius: 12px;">Cancel</button>
                    <button type="submit" class="btn btn-primary-custom">
                        <i class="bi bi-save me-2"></i>Assign Unit
                    </button>
                </div>
            </form>
</div>
</div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Main JavaScript -->
<script src="<?php echo isset($basePath) ? $basePath : '/pharmacy_ms'; ?>/assets/js/main.js"></script>

<div class="modal fade" id="deleteUnitModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background: var(--glass-bg); backdrop-filter: blur(20px); border: 1px solid var(--border-color);">
            <div class="modal-header" style="border-bottom: 1px solid var(--border-color);">
                <h5 class="modal-title">Delete Selling Unit</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="deleteUnitForm" onsubmit="confirmDeleteUnit(event)">
                <input type="hidden" id="deleteUnitId" name="id">
                <div class="modal-body">
                    <p class="mb-0">Are you sure you want to delete <strong id="deleteUnitName"></strong>?</p>
                </div>
                <div class="modal-footer" style="border-top: 1px solid var(--border-color);">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" style="border-radius: 12px;">Cancel</button>
                    <button type="submit" class="btn btn-danger"><i class="bi bi-trash me-2"></i>Delete</button>
                </div>
            </form>
        </div>
    </div>
    </div>

<div class="modal fade" id="editMedicineUnitModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background: var(--glass-bg); backdrop-filter: blur(20px); border: 1px solid var(--border-color);">
            <div class="modal-header" style="border-bottom: 1px solid var(--border-color);">
                <h5 class="modal-title">Edit Medicine Unit</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editMedicineUnitForm" onsubmit="updateMedicineUnit(event)">
                <input type="hidden" id="editAssignmentId" name="id">
                <input type="hidden" id="editAssignmentMedicineId" name="medicine_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label" style="font-weight: 600;">Medicine</label>
                        <div id="editAssignmentMedicineName" class="form-control form-control-glass" readonly></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="font-weight: 600;">Unit</label>
                        <div id="editAssignmentUnitName" class="form-control form-control-glass" readonly></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="font-weight: 600;">Price (UGX)</label>
                        <input type="number" min="0" id="editAssignmentPrice" name="price" class="form-control form-control-glass" required>
                        <small class="text-muted">Enter price without commas (e.g., 1000000 for 1 million UGX)</small>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" id="editAssignmentDefault" name="is_default" class="form-check-input">
                        <label class="form-check-label" for="editAssignmentDefault">Set as default selling unit</label>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="font-weight: 600;">Status</label>
                        <select id="editAssignmentStatus" name="status" class="form-control form-control-glass" required>
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer" style="border-top: 1px solid var(--border-color);">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" style="border-radius: 12px;">Cancel</button>
                    <button type="submit" class="btn btn-primary-custom"><i class="bi bi-save me-2"></i>Update</button>
                </div>
            </form>
        </div>
    </div>
    </div>

<div class="modal fade" id="deleteMedicineUnitModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background: var(--glass-bg); backdrop-filter: blur(20px); border: 1px solid var(--border-color);">
            <div class="modal-header" style="border-bottom: 1px solid var(--border-color);">
                <h5 class="modal-title">Remove Unit Assignment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="deleteMedicineUnitForm" onsubmit="confirmDeleteMedicineUnit(event)">
                <input type="hidden" id="deleteMedicineUnitId" name="id">
                <div class="modal-body">
                    <p class="mb-0">Are you sure you want to remove <strong id="deleteMedicineUnitText"></strong>?</p>
                </div>
                <div class="modal-footer" style="border-top: 1px solid var(--border-color);">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" style="border-radius: 12px;">Cancel</button>
                    <button type="submit" class="btn btn-danger"><i class="bi bi-trash me-2"></i>Remove</button>
                </div>
            </form>
        </div>
    </div>
    </div>

</body>
</html>
