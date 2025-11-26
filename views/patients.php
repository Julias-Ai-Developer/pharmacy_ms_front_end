<?php include __DIR__ . '/../includes/header.php'; ?>

<!-- Link CSS -->
<link rel="stylesheet" href="../assets/css/style.css">

<?php include __DIR__ . '/../includes/sidenav.php'; ?>

<!-- ==================== Main Content ==================== -->
<main class="main-content" id="mainContent">

        <div class="module-content fade-in" id="patients">
            <?php include __DIR__ . '/../includes/notifications.php'; ?>
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 style="color: var(--primary); font-weight: 700;">Patient Management</h2>
                <button class="btn btn-primary-custom" data-bs-toggle="modal" data-bs-target="#patientAddModal">
                    <i class="bi bi-plus-circle me-2"></i>
                    Add New Patient
                </button>
            </div>

            <!-- Search and Filter -->
            <div class="glass-card mb-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <input type="text" id="patientsSearchInput" class="form-control form-control-glass"
                            placeholder="Search patients by name, ID, or phone..."
                            oninput="searchCards(this.value, '.col-md-6.col-lg-4')">
                    </div>
                    <div class="col-md-3">
                        <select id="patientStatusFilter" class="form-control form-control-glass"
                            onchange="filterCards(this.value, '.col-md-6.col-lg-4')">
                            <option>All Patients</option>
                            <option>Active</option>
                            <option>Inactive</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-primary-custom w-100" onclick="document.getElementById('patientsSearchInput').value=''; document.getElementById('patientStatusFilter').value='All Patients'; searchCards('', '.col-md-6.col-lg-4');">
                            <i class="bi bi-arrow-clockwise me-2"></i>
                            Reset
                        </button>
                    </div>
                </div>
            </div>

            <!-- Patients Grid -->
            <div class="row mb-4">
            <?php require_once __DIR__ . '/../config/db.php';
            $res = $mysqli->query("SELECT id, patient_code, name, phone, email, last_visit, status FROM patients ORDER BY name ASC");
            if ($res && $res->num_rows > 0) {
                while ($row = $res->fetch_assoc()) {
                    $initials = '';
                    foreach (explode(' ', $row['name']) as $p) { if ($p) { $initials .= strtoupper($p[0]); if (strlen($initials) >= 2) break; } }
                    echo '<div class="col-md-6 col-lg-4 mb-3">';
                    echo '<div class="glass-card">';
                    echo '<div class="d-flex align-items-center gap-3 mb-3">';
                    echo '<div class="avatar" style="width: 60px; height: 60px; font-size: 20px;">' . $initials . '</div>';
                    echo '<div><h6 style="font-weight: 600; margin-bottom: 4px;">' . htmlspecialchars($row['name']) . '</h6><small class="text-muted">ID: ' . htmlspecialchars($row['patient_code']) . '</small></div>';
                    echo '</div>';
                    echo '<div class="mb-2">';
                    echo '<small class="text-muted d-block"><i class="bi bi-telephone me-2"></i>' . htmlspecialchars($row['phone']) . '</small>';
                    echo '<small class="text-muted d-block"><i class="bi bi-envelope me-2"></i>' . htmlspecialchars($row['email']) . '</small>';
                    $lv = $row['last_visit'] ? date('M d, Y', strtotime($row['last_visit'])) : '-';
                    echo '<small class="text-muted d-block"><i class="bi bi-calendar me-2"></i>Last Visit: ' . $lv . '</small>';
                    echo '</div>';
                    echo '<div class="d-flex gap-2 mt-3">';
                    echo '<button class="btn btn-sm btn-primary-custom" style="border-radius: 8px; flex: 1;" data-bs-toggle="modal" data-bs-target="#patientLabTestsModal" data-patient-id="' . (int)$row['id'] . '" data-patient-name="' . htmlspecialchars($row['name']) . '"><i class="bi bi-clipboard2-pulse me-1"></i> Lab Tests</button>';
                    echo '<button class="btn btn-sm btn-outline-secondary" style="border-radius: 8px;" data-bs-toggle="modal" data-bs-target="#patientEditModal" data-id="' . (int)$row['id'] . '" data-code="' . htmlspecialchars($row['patient_code']) . '" data-name="' . htmlspecialchars($row['name']) . '" data-phone="' . htmlspecialchars($row['phone']) . '" data-email="' . htmlspecialchars($row['email']) . '" data-last_visit="' . htmlspecialchars($row['last_visit']) . '" data-status="' . htmlspecialchars($row['status']) . '"><i class="bi bi-pencil"></i></button>';
                    echo '<form method="post" action="<?php echo $basePath; ?>/actions/patients.php?action=delete" style="display:inline-block;">';
                    echo '<input type="hidden" name="id" value="' . (int)$row['id'] . '">';
                    echo '<button class="btn btn-sm btn-link text-danger" onclick="return confirm(\'Delete this patient?\')"><i class="bi bi-trash"></i></button>';
                    echo '</form>';
                    echo '</div>';
                    echo '</div>';
                    echo '</div>';
                }
            } else {
                echo '<div class="col-12"><p class="text-muted">No patients found</p></div>';
            }
            ?>
            </div>

            <div class="glass-card">
                <h5 class="mb-4" style="font-weight: 600;">Recent Lab Requests</h5>
                <div class="table-responsive">
                    <table class="table table-glass mb-0">
                        <thead>
                            <tr>
                                <th>Patient</th>
                                <th>Test Type</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php $res = $mysqli->query("SELECT id, patient_name, test_type, status, created_at FROM lab_requests ORDER BY created_at DESC LIMIT 10");
                        if ($res && $res->num_rows > 0) {
                            while ($row = $res->fetch_assoc()) {
                                $statusClass = $row['status'] === 'Completed' ? 'badge-success' : 'badge-warning';
                                echo '<tr>';
                                echo '<td>' . htmlspecialchars($row['patient_name']) . '</td>';
                                echo '<td>' . htmlspecialchars($row['test_type']) . '</td>';
                                echo '<td>' . date('M d, Y', strtotime($row['created_at'])) . '</td>';
                                echo '<td><span class="badge-custom ' . $statusClass . '">' . htmlspecialchars($row['status']) . '</span></td>';
                                echo '<td>';
                                echo '<button class="btn btn-sm btn-link text-danger" data-bs-toggle="modal" data-bs-target="#deleteRecentLabModal" data-id="' . (int)$row['id'] . '" data-patient="' . htmlspecialchars($row['patient_name']) . '" data-test="' . htmlspecialchars($row['test_type']) . '"><i class="bi bi-trash"></i></button>';
                                echo '</td>';
                                echo '</tr>';
                            }
                        } else {
                            echo '<tr><td colspan="5" class="text-center text-muted">No lab requests found</td></tr>';
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="modal fade" id="patientAddModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header"><h5 class="modal-title">Add Patient</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div>
                        <form method="post" action="<?php echo $basePath; ?>/actions/patients.php?action=create">
                            <div class="modal-body">
                                <div class="mb-3"><label class="form-label">Patient ID</label><input name="patient_code" type="text" class="form-control" required></div>
                                <div class="mb-3"><label class="form-label">Name</label><input name="name" type="text" class="form-control" required></div>
                                <div class="mb-3"><label class="form-label">Phone</label><input name="phone" type="text" class="form-control" required></div>
                                <div class="mb-3"><label class="form-label">Email</label><input name="email" type="email" class="form-control"></div>
                                <div class="mb-3"><label class="form-label">Last Visit</label><input name="last_visit" type="date" class="form-control"></div>
                                <div class="mb-3"><label class="form-label">Status</label><select name="status" class="form-control"><option>Active</option><option>Inactive</option></select></div>
                            </div>
                            <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-primary">Save</button></div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="patientEditModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header"><h5 class="modal-title">Edit Patient</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div>
                        <form method="post" action="<?php echo $basePath; ?>/actions/patients.php?action=update">
                            <input type="hidden" name="id" id="edit_patient_id">
                            <div class="modal-body">
                                <div class="mb-3"><label class="form-label">Patient ID</label><input name="patient_code" id="edit_patient_code" type="text" class="form-control" required></div>
                                <div class="mb-3"><label class="form-label">Name</label><input name="name" id="edit_patient_name" type="text" class="form-control" required></div>
                                <div class="mb-3"><label class="form-label">Phone</label><input name="phone" id="edit_patient_phone" type="text" class="form-control" required></div>
                                <div class="mb-3"><label class="form-label">Email</label><input name="email" id="edit_patient_email" type="email" class="form-control"></div>
                                <div class="mb-3"><label class="form-label">Last Visit</label><input name="last_visit" id="edit_patient_last_visit" type="date" class="form-control"></div>
                                <div class="mb-3"><label class="form-label">Status</label><select name="status" id="edit_patient_status" class="form-control"><option>Active</option><option>Inactive</option></select></div>
                            </div>
                            <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-primary">Update</button></div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Patient Lab Tests Modal -->
            <div class="modal fade" id="patientLabTestsModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Lab Tests - <span id="lab_patient_name"></span></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <button class="btn btn-primary-custom btn-sm" data-bs-toggle="modal" data-bs-target="#addLabTestModal">
                                    <i class="bi bi-plus-circle me-2"></i>Add Lab Request
                                </button>
                            </div>
                            <div id="patient_lab_tests_content">
                                <p class="text-muted">Loading...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Add Lab Test Modal -->
            <div class="modal fade" id="addLabTestModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Add Lab Request</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form method="post" action="<?php echo $basePath; ?>/actions/lab_requests.php?action=create">
                            <input type="hidden" name="patient_name" id="add_lab_patient_name">
                            <div class="modal-body">
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
                                    <textarea name="notes" class="form-control" rows="3"></textarea>
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

            <!-- Edit Lab Test Modal (from Patient) -->
            <div class="modal fade" id="editPatientLabTestModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Edit Lab Request</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form method="post" action="<?php echo $basePath; ?>/actions/lab_requests.php?action=update">
                            <input type="hidden" name="id" id="edit_patient_lab_id">
                            <input type="hidden" name="patient_name" id="edit_patient_lab_patient_name">
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="form-label">Test Type</label>
                                    <select name="test_type" id="edit_patient_lab_test" class="form-control" required>
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
                                    <textarea name="notes" id="edit_patient_lab_notes" class="form-control" rows="3"></textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Status</label>
                                    <select name="status" id="edit_patient_lab_status" class="form-control">
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

            <!-- Delete Lab Test Modal (from Patient) -->
            <div class="modal fade" id="deletePatientLabTestModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Confirm Delete</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form method="post" action="<?php echo $basePath; ?>/actions/lab_requests.php?action=delete">
                            <input type="hidden" name="id" id="delete_patient_lab_id">
                            <input type="hidden" name="redirect_to" value="patients">
                            <div class="modal-body">
                                <p>Are you sure you want to delete this lab request?</p>
                                <p><strong>Test Type:</strong> <span id="delete_patient_lab_test_type"></span></p>
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

            <!-- Delete Recent Lab Request Modal -->
            <div class="modal fade" id="deleteRecentLabModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Confirm Delete</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form method="post" action="<?php echo $basePath; ?>/actions/lab_requests.php?action=delete">
                            <input type="hidden" name="id" id="delete_recent_lab_id">
                            <input type="hidden" name="redirect_to" value="patients">
                            <div class="modal-body">
                                <p>Are you sure you want to delete this lab request?</p>
                                <p><strong>Patient:</strong> <span id="delete_recent_lab_patient"></span></p>
                                <p><strong>Test Type:</strong> <span id="delete_recent_lab_test"></span></p>
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
  // Delete Recent Lab Request Modal
  const deleteRecentLabModal = document.getElementById('deleteRecentLabModal');
  if (deleteRecentLabModal) {
    deleteRecentLabModal.addEventListener('show.bs.modal', function (event) {
      const button = event.relatedTarget;
      if (!button) return;
      
      document.getElementById('delete_recent_lab_id').value = button.getAttribute('data-id') || '';
      document.getElementById('delete_recent_lab_patient').textContent = button.getAttribute('data-patient') || '';
      document.getElementById('delete_recent_lab_test').textContent = button.getAttribute('data-test') || '';
    });
  }

  // Patient Edit Modal
  const patientEditModal = document.getElementById('patientEditModal');
  if (patientEditModal) {
    patientEditModal.addEventListener('show.bs.modal', function (event) {
      const b = event.relatedTarget; if (!b) return;
      document.getElementById('edit_patient_id').value = b.getAttribute('data-id');
      document.getElementById('edit_patient_code').value = b.getAttribute('data-code');
      document.getElementById('edit_patient_name').value = b.getAttribute('data-name');
      document.getElementById('edit_patient_phone').value = b.getAttribute('data-phone');
      document.getElementById('edit_patient_email').value = b.getAttribute('data-email');
      document.getElementById('edit_patient_last_visit').value = b.getAttribute('data-last_visit');
      document.getElementById('edit_patient_status').value = b.getAttribute('data-status');
    });
  }

  // Patient Lab Tests Modal
  const patientLabTestsModal = document.getElementById('patientLabTestsModal');
  if (patientLabTestsModal) {
    patientLabTestsModal.addEventListener('show.bs.modal', function (event) {
      const button = event.relatedTarget;
      if (!button) return;
      
      const patientName = button.getAttribute('data-patient-name');
      const patientId = button.getAttribute('data-patient-id');
      
      // Set patient name in modal title
      document.getElementById('lab_patient_name').textContent = patientName;
      document.getElementById('add_lab_patient_name').value = patientName;
      
      // Load lab tests for this patient
      const contentDiv = document.getElementById('patient_lab_tests_content');
      contentDiv.innerHTML = '<p class="text-muted">Loading...</p>';
      
      // Fetch lab tests using AJAX
      fetch('<?php echo $basePath; ?>/actions/lab_requests.php?action=get_by_patient&patient_name=' + encodeURIComponent(patientName))
        .then(response => response.json())
        .then(data => {
          if (data && data.length > 0) {
            let html = '<div class="table-responsive"><table class="table table-glass mb-0"><thead><tr><th>Test Type</th><th>Date</th><th>Status</th><th>Notes</th><th>Actions</th></tr></thead><tbody>';
            data.forEach(test => {
              const statusClass = test.status === 'Completed' ? 'badge-success' : (test.status === 'In Progress' ? 'badge-info' : 'badge-warning');
              const date = new Date(test.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
              html += '<tr>';
              html += '<td>' + test.test_type + '</td>';
              html += '<td>' + date + '</td>';
              html += '<td><span class="badge-custom ' + statusClass + '">' + test.status + '</span></td>';
              html += '<td>' + (test.notes || '-') + '</td>';
              html += '<td>';
              html += '<button class="btn btn-sm btn-link" onclick="editPatientLabTest(' + test.id + ', \'' + test.test_type + '\', \'' + (test.notes || '') + '\', \'' + test.status + '\', \'' + patientName + '\')"><i class="bi bi-pencil"></i></button>';
              html += '<button class="btn btn-sm btn-link text-danger" onclick="deletePatientLabTest(' + test.id + ', \'' + test.test_type + '\')"><i class="bi bi-trash"></i></button>';
              html += '</td>';
              html += '</tr>';
            });
            html += '</tbody></table></div>';
            contentDiv.innerHTML = html;
          } else {
            contentDiv.innerHTML = '<p class="text-muted">No lab tests found for this patient.</p>';
          }
        })
        .catch(error => {
          console.error('Error loading lab tests:', error);
          contentDiv.innerHTML = '<p class="text-danger">Error loading lab tests.</p>';
        });
    });
  }
});

// Global functions for editing and deleting lab tests from patient view
function editPatientLabTest(id, testType, notes, status, patientName) {
  document.getElementById('edit_patient_lab_id').value = id;
  document.getElementById('edit_patient_lab_patient_name').value = patientName;
  document.getElementById('edit_patient_lab_test').value = testType;
  document.getElementById('edit_patient_lab_notes').value = notes;
  document.getElementById('edit_patient_lab_status').value = status;
  
  // Close the lab tests modal and open edit modal
  const labTestsModal = bootstrap.Modal.getInstance(document.getElementById('patientLabTestsModal'));
  if (labTestsModal) labTestsModal.hide();
  
  const editModal = new bootstrap.Modal(document.getElementById('editPatientLabTestModal'));
  editModal.show();
}

function deletePatientLabTest(id, testType) {
  document.getElementById('delete_patient_lab_id').value = id;
  document.getElementById('delete_patient_lab_test_type').textContent = testType;
  
  // Close the lab tests modal and open delete modal
  const labTestsModal = bootstrap.Modal.getInstance(document.getElementById('patientLabTestsModal'));
  if (labTestsModal) labTestsModal.hide();
  
  const deleteModal = new bootstrap.Modal(document.getElementById('deletePatientLabTestModal'));
  deleteModal.show();
}
</script>

</body>
</html>
