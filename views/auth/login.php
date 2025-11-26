<?php
session_start();
if (isset($_SESSION['user_id'])) { header('Location: ../dashboard.php'); exit; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login • Pharmacy MS</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="../../assets/css/style.css">
  <style>
    html, body {
        height: 100%;
        margin: 0;
        padding: 0;
        overflow: hidden; /* Prevent scrolling */
        background-color: #f8f9fa;
    }
    
    .diagonal-bg {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 0; /* Changed from -1 to ensure visibility */
        background: linear-gradient(135deg, rgba(10, 126, 186, 0.95) 0%, rgba(6, 182, 168, 0.9) 100%), url('../../assets/img/login_bg.png');
        background-size: cover;
        background-position: center;
        clip-path: polygon(0 100%, 100% 0, 100% 100%); /* Sharper diagonal from bottom-left to top-right */
    }

    .login-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.5);
        border-radius: 24px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        padding: 3rem;
        width: 100%;
        max-width: 450px;
        position: relative;
        z-index: 1;
    }

    .brand-logo {
        width: 80px;
        height: 80px;
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        color: white;
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 32px;
        font-weight: bold;
        margin: 0 auto 1.5rem;
        box-shadow: 0 10px 20px rgba(10, 126, 186, 0.3);
    }

    .form-control-lg {
        border-radius: 12px;
        padding: 0.75rem 1rem;
        font-size: 1rem;
        border: 2px solid #eef2f6;
        background-color: #f8f9fa;
    }

    .form-control-lg:focus {
        border-color: var(--primary);
        background-color: #fff;
        box-shadow: 0 0 0 4px rgba(10, 126, 186, 0.1);
    }

    .btn-login {
        padding: 0.8rem;
        border-radius: 12px;
        font-weight: 600;
        font-size: 1.1rem;
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        border: none;
        transition: all 0.3s ease;
    }

    .btn-login:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(10, 126, 186, 0.3);
    }
    
    /* Floating shapes for extra polish */
    .shape {
        position: absolute;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        opacity: 0.1;
        z-index: -2;
    }
    .shape-1 { width: 300px; height: 300px; top: -100px; left: -100px; }
    .shape-2 { width: 200px; height: 200px; bottom: 10%; right: 10%; }
  </style>
</head>
<body>
  <!-- Diagonal Background -->
  <div class="diagonal-bg"></div>
  
  <!-- Floating Shapes -->
  <div class="shape shape-1"></div>
  
  <div class="d-flex align-items-center justify-content-center" style="min-height:100vh; padding:2rem;">
    <div class="login-card">
      <div class="text-center mb-4">
        <div class="brand-logo">
            <i class="bi bi-capsule-pill"></i>
        </div>
        <h3 style="color: #1e293b; font-weight: 800; margin-bottom: 0.5rem;">Welcome Back</h3>
        <p class="text-muted">Sign in to HealthPlus Pharmacy</p>
      </div>

      <!-- Error Alert -->
      <?php if (isset($_GET['error'])): ?>
      <div class="alert alert-danger d-flex align-items-center gap-2 p-2 mb-4" role="alert" style="border-radius: 12px; font-size: 0.9rem;">
        <i class="bi bi-exclamation-circle-fill"></i>
        <div>
            <?php 
                if ($_GET['error'] == 'invalid_credentials') echo 'Invalid email or password';
                else echo 'An error occurred. Please try again.';
            ?>
        </div>
      </div>
      <?php endif; ?>

      <form method="post" action="../../actions/auth.php?action=login">
        <div class="mb-4">
          <label class="form-label text-muted small fw-bold text-uppercase">Email Address</label>
          <div class="input-group">
            <span class="input-group-text border-0 bg-transparent ps-0 text-primary">
                <i class="bi bi-envelope-fill fs-5"></i>
            </span>
            <input name="email" type="email" class="form-control form-control-lg" placeholder="name@pharmacy.com" required>
          </div>
        </div>
        
        <div class="mb-4">
          <label class="form-label text-muted small fw-bold text-uppercase">Password</label>
          <div class="input-group">
            <span class="input-group-text border-0 bg-transparent ps-0 text-primary">
                <i class="bi bi-lock-fill fs-5"></i>
            </span>
            <input name="password" type="password" class="form-control form-control-lg" placeholder="••••••••" required>
          </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4">
          <div class="form-check">
            <input class="form-check-input" type="checkbox" id="remember">
            <label class="form-check-label text-muted small" for="remember">Remember me</label>
          </div>
          <a href="./forgot.php" class="text-decoration-none small fw-bold" style="color: var(--primary);">Forgot Password?</a>
        </div>

        <button class="btn btn-primary btn-login w-100 text-white" type="submit">
          Sign In
        </button>
      </form>
      
    
    </div>
  </div>
</body>
</html>