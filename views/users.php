<?php include __DIR__ . '/../includes/header.php'; ?>

<!-- Link CSS -->
<link rel="stylesheet" href="../assets/css/style.css">

<?php include __DIR__ . '/../includes/sidenav.php'; ?>

<!-- ==================== Main Content ==================== -->
<main class="main-content" id="mainContent">

        <div class="module-content fade-in" id="users">
            <?php include __DIR__ . '/../includes/notifications.php'; ?>
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 style="color: var(--primary); font-weight: 700;">Users & Roles Management</h2>
                <button class="btn btn-primary-custom" data-bs-toggle="modal" data-bs-target="#userAddModal">
                    <i class="bi bi-plus-circle me-2"></i>
                    Add New User
                </button>
            </div>

            <!-- Users List -->
            <div class="glass-card mb-4">
                <h5 class="mb-4" style="font-weight: 600;">System Users</h5>
                <div class="table-responsive">
                    <table class="table table-glass mb-0">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Last Login</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php require_once __DIR__ . '/../config/db.php';
                        $result = $mysqli->query("SELECT id, name, email, role, status, last_login FROM users ORDER BY name ASC");
                        if ($result && $result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $initials = '';
                                $parts = explode(' ', $row['name']);
                                foreach ($parts as $p) { if ($p !== '') { $initials .= strtoupper($p[0]); if (strlen($initials) >= 2) break; } }
                                echo '<tr>';
                                echo '<td><div class="d-flex align-items-center gap-2"><div class="avatar" style="width: 35px; height: 35px; font-size: 14px;">' . $initials . '</div><strong>' . htmlspecialchars($row['name']) . '</strong></div></td>';
                                echo '<td>' . htmlspecialchars($row['email']) . '</td>';
                                $roleBadge = 'badge-success';
                                if ($row['role'] === 'Pharmacist') $roleBadge = 'badge-info';
                                if ($row['role'] === 'Cashier') $roleBadge = 'badge-warning';
                                echo '<td><span class="badge-custom ' . $roleBadge . '">' . htmlspecialchars($row['role']) . '</span></td>';
                                $statusDot = '<span class="status-dot active"></span>' . htmlspecialchars($row['status']);
                                echo '<td>' . $statusDot . '</td>';
                                $lastLogin = $row['last_login'] ? date('M d, Y h:i A', strtotime($row['last_login'])) : '-';
                                echo '<td>' . $lastLogin . '</td>';
                                echo '<td>';
                                echo '<button class="btn btn-sm btn-link" data-bs-toggle="modal" data-bs-target="#userEditModal" data-id="' . (int)$row['id'] . '" data-name="' . htmlspecialchars($row['name']) . '" data-email="' . htmlspecialchars($row['email']) . '" data-role="' . htmlspecialchars($row['role']) . '" data-status="' . htmlspecialchars($row['status']) . '"><i class="bi bi-pencil"></i></button>';
                                echo '<form method="post" action="<?php echo $basePath; ?>/actions/users.php?action=delete" style="display:inline-block;">';
                                echo '<input type="hidden" name="id" value="' . (int)$row['id'] . '">';
                                echo '<button class="btn btn-sm btn-link text-danger" onclick="return confirm(\'Delete this user?\')"><i class="bi bi-trash"></i></button>';
                                echo '</form>';
                                echo '</td>';
                                echo '</tr>';
                            }
                        } else {
                            echo '<tr><td colspan="6" class="text-center text-muted">No users found</td></tr>';
                        }
                        ?>
                        </tbody>
                    </table>
                </div>

                <div class="modal fade" id="userAddModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Add User</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form method="post" action="<?php echo $basePath; ?>/actions/users.php?action=create">
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label class="form-label">Name</label>
                                        <input name="name" type="text" class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Email</label>
                                        <input name="email" type="email" class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Role</label>
                                        <select name="role" class="form-control" required>
                                            <option>Admin</option>
                                            <option>Pharmacist</option>
                                            <option>Cashier</option>
                                            <option>View Only</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Status</label>
                                        <select name="status" class="form-control" required>
                                            <option>Active</option>
                                            <option>Inactive</option>
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

                <div class="modal fade" id="userEditModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Edit User</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form method="post" action="<?php echo $basePath; ?>/actions/users.php?action=update">
                                <input type="hidden" name="id" id="edit_user_id">
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label class="form-label">Name</label>
                                        <input name="name" id="edit_user_name" type="text" class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Email</label>
                                        <input name="email" id="edit_user_email" type="email" class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Role</label>
                                        <select name="role" id="edit_user_role" class="form-control" required>
                                            <option>Admin</option>
                                            <option>Pharmacist</option>
                                            <option>Cashier</option>
                                            <option>View Only</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Status</label>
                                        <select name="status" id="edit_user_status" class="form-control" required>
                                            <option>Active</option>
                                            <option>Inactive</option>
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
            </div>

            <!-- Roles & Permissions -->
            <div class="glass-card">
                <h5 class="mb-4" style="font-weight: 600;">Roles & Permissions Matrix</h5>
                <div class="table-responsive">
                    <table class="table table-glass mb-0">
                        <thead>
                            <tr>
                                <th>Permission</th>
                                <th>Admin</th>
                                <th>Pharmacist</th>
                                <th>Cashier</th>
                                <th>View Only</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><strong>Dashboard Access</strong></td>
                                <td><i class="bi bi-check-circle-fill text-success"></i></td>
                                <td><i class="bi bi-check-circle-fill text-success"></i></td>
                                <td><i class="bi bi-check-circle-fill text-success"></i></td>
                                <td><i class="bi bi-check-circle-fill text-success"></i></td>
                            </tr>
                            <tr>
                                <td><strong>POS / Sales</strong></td>
                                <td><i class="bi bi-check-circle-fill text-success"></i></td>
                                <td><i class="bi bi-check-circle-fill text-success"></i></td>
                                <td><i class="bi bi-check-circle-fill text-success"></i></td>
                                <td><i class="bi bi-x-circle-fill text-danger"></i></td>
                            </tr>
                            <tr>
                                <td><strong>Inventory Management</strong></td>
                                <td><i class="bi bi-check-circle-fill text-success"></i></td>
                                <td><i class="bi bi-check-circle-fill text-success"></i></td>
                                <td><i class="bi bi-x-circle-fill text-danger"></i></td>
                                <td><i class="bi bi-check-circle-fill text-success"></i></td>
                            </tr>
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
<script>
const userEditModal = document.getElementById('userEditModal');
if (userEditModal) {
  userEditModal.addEventListener('show.bs.modal', function (event) {
    const button = event.relatedTarget;
    if (!button) return;
    document.getElementById('edit_user_id').value = button.getAttribute('data-id');
    document.getElementById('edit_user_name').value = button.getAttribute('data-name');
    document.getElementById('edit_user_email').value = button.getAttribute('data-email');
    document.getElementById('edit_user_role').value = button.getAttribute('data-role');
    document.getElementById('edit_user_status').value = button.getAttribute('data-status');
  });
}
</script>

</body>
</html>
