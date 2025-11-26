<?php include __DIR__ . '/../includes/header.php'; ?>

<!-- Link CSS -->
<link rel="stylesheet" href="../assets/css/style.css">

<?php include __DIR__ . '/../includes/sidenav.php'; ?>

<!-- ==================== Main Content ==================== -->
<main class="main-content" id="mainContent">

        <div class="module-content fade-in" id="prescriptions">
            <?php include __DIR__ . '/../includes/notifications.php'; ?>
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 style="color: var(--primary); font-weight: 700;">Prescriptions</h2>
                <button class="btn btn-primary-custom">
                    <i class="bi bi-plus-circle me-2"></i>
                    New Prescription
                </button>
            </div>

            <div class="glass-card mb-4">
                <h5 class="mb-4" style="font-weight: 600;">Add New Prescription</h5>
                <form method="post" action="<?php echo $basePath; ?>/actions/prescriptions.php?action=create">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label" style="font-weight: 600;">Prescription ID</label>
                            <input name="code" type="text" class="form-control form-control-glass" placeholder="e.g., RX-2025-010" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" style="font-weight: 600;">Patient Name</label>
                            <input name="patient_name" type="text" class="form-control form-control-glass" placeholder="Enter patient name" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" style="font-weight: 600;">Doctor Name</label>
                            <input name="doctor_name" type="text" class="form-control form-control-glass" placeholder="Enter doctor name" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label" style="font-weight: 600;">Medications</label>
                            <textarea name="items" class="form-control form-control-glass" rows="3" placeholder="Comma separated list" required></textarea>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" style="font-weight: 600;">Status</label>
                            <select name="status" class="form-control form-control-glass">
                                <option>Pending</option>
                                <option>Dispensed</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary-custom me-2"><i class="bi bi-save me-2"></i>Save Prescription</button>
                            <button type="reset" class="btn btn-outline-secondary" style="border-radius: 12px;">Clear Form</button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="glass-card">
                <h5 class="mb-4" style="font-weight: 600;">Recent Prescriptions</h5>
                <div class="table-responsive">
                    <table class="table table-glass mb-0">
                        <thead>
                            <tr>
                                <th>Prescription ID</th>
                                <th>Patient</th>
                                <th>Doctor</th>
                                <th>Date</th>
                                <th>Medications</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php require_once __DIR__ . '/../config/db.php';
                        $result = $mysqli->query("SELECT id, code, patient_name, doctor_name, items, status, created_at FROM prescriptions ORDER BY created_at DESC");
                        if ($result && $result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $dateStr = date('M d, Y', strtotime($row['created_at']));
                                $statusClass = $row['status'] === 'Dispensed' ? 'badge-success' : 'badge-warning';
                                echo '<tr>';
                                echo '<td><strong>' . htmlspecialchars($row['code']) . '</strong></td>';
                                echo '<td>' . htmlspecialchars($row['patient_name']) . '</td>';
                                echo '<td>' . htmlspecialchars($row['doctor_name']) . '</td>';
                                echo '<td>' . $dateStr . '</td>';
                                echo '<td>' . htmlspecialchars($row['items']) . '</td>';
                                echo '<td><span class="badge-custom ' . $statusClass . '">' . htmlspecialchars($row['status']) . '</span></td>';
                                echo '<td>';
                                echo '<button class="btn btn-sm btn-link" data-bs-toggle="modal" data-bs-target="#rxEditModal" data-id="' . (int)$row['id'] . '" data-code="' . htmlspecialchars($row['code']) . '" data-patient="' . htmlspecialchars($row['patient_name']) . '" data-doctor="' . htmlspecialchars($row['doctor_name']) . '" data-items="' . htmlspecialchars($row['items']) . '" data-status="' . htmlspecialchars($row['status']) . '"><i class="bi bi-pencil"></i></button>';
                                echo '<form method="post" action="' . $basePath . '/actions/prescriptions.php?action=delete" style="display:inline-block;">';
                                echo '<input type="hidden" name="id" value="' . (int)$row['id'] . '">';
                                echo '<button class="btn btn-sm btn-link text-danger" onclick="return confirm(\'Delete this prescription?\')"><i class="bi bi-trash"></i></button>';
                                echo '</form>';
                                echo '</td>';
                                echo '</tr>';
                            }
                        } else {
                            echo '<tr><td colspan="7" class="text-center text-muted">No prescriptions found</td></tr>';
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="modal fade" id="rxEditModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Edit Prescription</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form method="post" action="<?php echo $basePath; ?>/actions/prescriptions.php?action=update">
                            <input type="hidden" name="id" id="edit_rx_id">
                            <div class="modal-body">
                                <div class="mb-3"><label class="form-label">Prescription ID</label><input name="code" id="edit_rx_code" type="text" class="form-control" required></div>
                                <div class="mb-3"><label class="form-label">Patient Name</label><input name="patient_name" id="edit_rx_patient" type="text" class="form-control" required></div>
                                <div class="mb-3"><label class="form-label">Doctor</label><input name="doctor_name" id="edit_rx_doctor" type="text" class="form-control" required></div>
                                <div class="mb-3"><label class="form-label">Medicines</label><input name="items" id="edit_rx_items" type="text" class="form-control" required></div>
                                <div class="mb-3"><label class="form-label">Status</label><select name="status" id="edit_rx_status" class="form-control"><option>Pending</option><option>Dispensed</option></select></div>
                            </div>
                            <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-primary">Update</button></div>
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
const rxEditModal = document.getElementById('rxEditModal');
if (rxEditModal) {
  rxEditModal.addEventListener('show.bs.modal', function (event) {
    const b = event.relatedTarget; if (!b) return;
    document.getElementById('edit_rx_id').value = b.getAttribute('data-id');
    document.getElementById('edit_rx_code').value = b.getAttribute('data-code');
    document.getElementById('edit_rx_patient').value = b.getAttribute('data-patient');
    document.getElementById('edit_rx_doctor').value = b.getAttribute('data-doctor');
    document.getElementById('edit_rx_items').value = b.getAttribute('data-items');
    document.getElementById('edit_rx_status').value = b.getAttribute('data-status');
  });
}
</script>

</body>
</html>
