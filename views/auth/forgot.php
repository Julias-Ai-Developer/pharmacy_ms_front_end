
<?php
session_start();
if (isset($_SESSION['user_id'])) { header('Location: ../dashboard.php'); exit; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Forgot Password • Pharmacy MS</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
<div class="d-flex align-items-center justify-content-center" style="min-height:100vh; padding:2rem;">
  <div class="glass-card" style="max-width:420px; width:100%;">
    <h4 class="mb-3" style="color: var(--primary); font-weight:700;">Forgot Password</h4>
    <small class="text-muted d-block mb-3">Enter your email; we’ll create a reset link.</small>
    <form method="post" action="../../actions/auth.php?action=forgot">
      <div class="mb-3">
        <label class="form-label" style="font-weight:600;">Email</label>
        <input name="email" type="email" class="form-control form-control-glass" required>
      </div>
      <button class="btn btn-primary-custom w-100" type="submit">
        <i class="bi bi-envelope me-2"></i>Send Reset Link
      </button>
    </form>
    <div class="text-center mt-3">
      <a href="./login.php" class="text-decoration-none" style="color: var(--primary);">Back to login</a>
    </div>
  </div>
</div>
</body>
</html>