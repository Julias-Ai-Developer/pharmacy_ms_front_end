<?php include __DIR__ . '/../includes/header.php'; ?>

<!-- Link CSS -->
<link rel="stylesheet" href="../assets/css/style.css">

<?php include __DIR__ . '/../includes/sidenav.php'; ?>

<!-- ==================== Main Content ==================== -->
<main class="main-content" id="mainContent">

        <div class="module-content fade-in" id="clinic">
            <?php include __DIR__ . '/../includes/notifications.php'; ?>
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 style="color: var(--primary); font-weight: 700;">Clinic Services</h2>
                <button class="btn btn-primary-custom" data-bs-toggle="modal" data-bs-target="#addLabRequestModal">
                    <i class="bi bi-plus-circle me-2"></i>
                    New Lab Request
                </button>
            </div>

            <!-- Lab Requests Table -->
            <div class="glass-card">
                <h5 class="mb-4" style="font-weight: 600;"><i class="bi bi-test-tube me-2"></i>Lab Test Requests</h5>
                <div class="table-responsive">
                    <table class="table table-glass mb-0">
                        <thead>
                            <tr>
                                <th>Request ID</th>
                                <th>Patient</th>
                                <th>Test Type</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Notes</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php require_once __DIR__ . '/../config/db.php';
                        $res = $mysqli->query("SELECT id, patient_name, test_type, notes, status, created_at FROM lab_requests ORDER BY created_at DESC");
                        if ($res && $res->num_rows > 0) {
                            while ($row = $res->fetch_assoc()) {
                                $statusClass = $row['status'] === 'Completed' ? 'badge-success' : ($row['status'] === 'In Progress' ? 'badge-info' : 'badge-warning');
                                echo '<tr>';
                                echo '<td><strong>LAB-' . (int)$row['id'] . '</strong></td>';
                                echo '<td>' . htmlspecialchars($row['patient_name']) . '</td>';
                                echo '<td>' . htmlspecialchars($row['test_type']) . '</td>';
                                echo '<td>' . date('M d, Y', strtotime($row['created_at'])) . '</td>';
                                echo '<td><span class="badge-custom ' . $statusClass . '">' . htmlspecialchars($row['status']) . '</span></td>';
                                echo '<td>' . (htmlspecialchars($row['notes']) ?: '-') . '</td>';
                                echo '<td>';
                                echo '<button class="btn btn-sm btn-link" data-bs-toggle="modal" data-bs-target="#editLabRequestModal" data-id="' . (int)$row['id'] . '" data-patient="' . htmlspecialchars($row['patient_name']) . '" data-test="' . htmlspecialchars($row['test_type']) . '" data-notes="' . htmlspecialchars($row['notes']) . '" data-status="' . htmlspecialchars($row['status']) . '"><i class="bi bi-pencil"></i></button>';
                                echo '<button class="btn btn-sm btn-link text-danger" data-bs-toggle="modal" data-bs-target="#deleteLabRequestModal" data-id="' . (int)$row['id'] . '" data-patient="' . htmlspecialchars($row['patient_name']) . '"><i class="bi bi-trash"></i></button>';
                                echo '</td>';
                                echo '</tr>';
                            }
                        } else {
                            echo '<tr><td colspan="7" class="text-center text-muted">No lab requests</td></tr>';
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Add Lab Request Modal -->
            <div class="modal fade" id="addLabRequestModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">New Lab Request</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form method="post" action="<?php echo $basePath; ?>/actions/lab_requests.php?action=create">
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="form-label">Select Patient</label>
                                    <select name="patient_name" class="form-control" required>
                                        <option value="">Choose a patient...</option>
                                        <?php 
                                        $patients = $mysqli->query("SELECT id, patient_code, name FROM patients WHERE status = 'Active' ORDER BY name ASC");
                                        if ($patients) {
                                            while ($p = $patients->fetch_assoc()) {
                                                echo '<option value="' . htmlspecialchars($p['name']) . '">' . htmlspecialchars($p['name']) . ' (' . htmlspecialchars($p['patient_code']) . ')</option>';
                                            }
                                        }
                                        ?>
                                    </select>
                                    <small class="text-muted">Or <a href="<?php echo $basePath; ?>/patients" target="_blank">add a new patient</a></small>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Test Type</label>
                                    <select name="test_type" class="form-control" required>
                                        <option value="">Select test type</option>
                                        <option>Blood Test</option>
                                        <option>Urine Test</option>
                                        <option>X-Ray</option>
                                        <option>CT Scan</option>
                                        <option>MRI</option>
                                        <option>Ultrasound</option>
                                        <option>ECG</option>
                                        <option>Other</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Notes</label>
                                    <textarea name="notes" class="form-control" rows="3" placeholder="Special instructions or observations..."></textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Status</label>
                                    <select name="status" class="form-control">
                                        <option>Pending</option>
                                        <option>In Progress</option>
                                        <option>Completed</option>
                                    </select>
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

            <!-- Edit Lab Request Modal -->
            <div class="modal fade" id="editLabRequestModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Edit Lab Request</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form method="post" action="<?php echo $basePath; ?>/actions/lab_requests.php?action=update">
                            <input type="hidden" name="id" id="edit_lab_id">
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="form-label">Select Patient</label>
                                    <select name="patient_name" id="edit_lab_patient" class="form-control" required>
                                        <option value="">Choose a patient...</option>
                                        <?php 
                                        $patients2 = $mysqli->query("SELECT id, patient_code, name FROM patients WHERE status = 'Active' ORDER BY name ASC");
                                        if ($patients2) {
                                            while ($p2 = $patients2->fetch_assoc()) {
                                                echo '<option value="' . htmlspecialchars($p2['name']) . '">' . htmlspecialchars($p2['name']) . ' (' . htmlspecialchars($p2['patient_code']) . ')</option>';
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Test Type</label>
                                    <select name="test_type" id="edit_lab_test" class="form-control" required>
                                        <option value="">Select test type</option>
                                        <option>Blood Test</option>
                                        <option>Urine Test</option>
                                        <option>X-Ray</option>
                                        <option>CT Scan</option>
                                        <option>MRI</option>
                                        <option>Ultrasound</option>
                                        <option>ECG</option>
                                        <option>Other</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Notes</label>
                                    <textarea name="notes" id="edit_lab_notes" class="form-control" rows="3"></textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Status</label>
                                    <select name="status" id="edit_lab_status" class="form-control">
                                        <option>Pending</option>
                                        <option>In Progress</option>
                                        <option>Completed</option>
                                    </select>
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

            <!-- Delete Lab Request Modal -->
            <div class="modal fade" id="deleteLabRequestModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Confirm Delete</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form method="post" action="<?php echo $basePath; ?>/actions/lab_requests.php?action=delete">
                            <input type="hidden" name="id" id="delete_lab_id">
                            <div class="modal-body">
                                <p>Are you sure you want to delete lab request for <strong id="delete_lab_patient"></strong>?</p>
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
        </div>


</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Main JavaScript -->
<script src="<?php echo isset($basePath) ? $basePath : '/pharmacy_ms'; ?>/assets/js/main.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
  // Edit Lab Request Modal
  const editLabModal = document.getElementById('editLabRequestModal');
  if (editLabModal) {
    editLabModal.addEventListener('show.bs.modal', function (event) {
      const button = event.relatedTarget;
      if (!button) return;
      
      document.getElementById('edit_lab_id').value = button.getAttribute('data-id') || '';
      document.getElementById('edit_lab_patient').value = button.getAttribute('data-patient') || '';
      document.getElementById('edit_lab_test').value = button.getAttribute('data-test') || '';
      document.getElementById('edit_lab_notes').value = button.getAttribute('data-notes') || '';
      document.getElementById('edit_lab_status').value = button.getAttribute('data-status') || 'Pending';
    });
  }

  // Delete Lab Request Modal
  const deleteLabModal = document.getElementById('deleteLabRequestModal');
  if (deleteLabModal) {
    deleteLabModal.addEventListener('show.bs.modal', function (event) {
      const button = event.relatedTarget;
      if (!button) return;
      
      document.getElementById('delete_lab_id').value = button.getAttribute('data-id') || '';
      document.getElementById('delete_lab_patient').textContent = button.getAttribute('data-patient') || '';
    });
  }
});
</script>

</body>
</html>
