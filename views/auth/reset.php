
<?php
session_start();
$token = isset($_GET['token']) ? $_GET['token'] : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Reset Password • Pharmacy MS</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
<div class="d-flex align-items-center justify-content-center" style="min-height:100vh; padding:2rem;">
  <div class="glass-card" style="max-width:420px; width:100%;">
    <h4 class="mb-3" style="color: var(--primary); font-weight:700;">Reset Password</h4>
    <?php if (!$token): ?>
      <div class="alert alert-warning">Invalid or missing token.</div>
      <div class="text-center mt-3"><a href="./forgot.php" class="text-decoration-none" style="color: var(--primary);">Request a new link</a></div>
    <?php else: ?>
      <form method="post" action="../../actions/auth.php?action=reset&token=<?php echo htmlspecialchars($token); ?>">
        <div class="mb-3">
          <label class="form-label" style="font-weight:600;">New Password</label>
          <input name="password" type="password" class="form-control form-control-glass" required minlength="6">
        </div>
        <div class="mb-3">
          <label class="form-label" style="font-weight:600;">Confirm Password</label>
          <input name="password_confirm" type="password" class="form-control form-control-glass" required minlength="6">
        </div>
        <button class="btn btn-primary-custom w-100" type="submit">
          <i class="bi bi-check2-circle me-2"></i>Save New Password
        </button>
      </form>
    <?php endif; ?>
    <div class="text-center mt-3"><a href="./login.php" class="text-decoration-none" style="color: var(--primary);">Back to login</a></div>
  </div>
</div>
</body>
</html>