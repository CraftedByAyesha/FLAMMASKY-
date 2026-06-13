<?php
// ── admin_login.php ───────────────────────────────────────────────────────────
// Admin login for flamma sky dashboard
// Change ADMIN_USER and ADMIN_PASS below to your preferred credentials
// ─────────────────────────────────────────────────────────────────────────────
session_start();

define('ADMIN_USER', 'flammasky');
define('ADMIN_PASS', 'demo123');   // <-- change this to your preferred password

if (!empty($_SESSION['FLAMMA_admin'])) {
    header('Location: admin_dashboard.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $u = trim($_POST['username'] ?? '');
    $p = trim($_POST['password'] ?? '');
    if ($u === ADMIN_USER && $p === ADMIN_PASS) {
        $_SESSION['FLAMMA_admin'] = true;
        header('Location: admin_dashboard.php');
        exit;
    }
    $error = 'Invalid username or password. Please try again.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Flamma Sky – Admin Login</title>
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'Segoe UI', Arial, sans-serif; background: #0f0500; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
    .card { background: #1a0a00; border: 1px solid #3a1a00; border-radius: 6px; padding: 2.5rem 2rem; width: 100%; max-width: 380px; }
    .logo { text-align: center; margin-bottom: 2rem; }
    .logo h1 { color: #c8732a; font-size: 22px; letter-spacing: 3px; font-weight: 700; }
    .logo span { color: #888; font-size: 11px; letter-spacing: 2px; display: block; margin-top: 4px; }
    hr { border: none; border-top: 1px solid #3a1a00; margin: 0 0 1.5rem; }
    .error { background: #3a0a0a; border: 1px solid #7a2020; color: #f09595; padding: 10px 13px; border-radius: 4px; font-size: 13px; margin-bottom: 1rem; }
    label { display: block; font-size: 11px; color: #888; letter-spacing: 1px; margin-bottom: 5px; }
    input { width: 100%; padding: 11px 13px; background: #0f0500; border: 1px solid #3a1a00; border-radius: 4px; color: #fff; font-size: 14px; outline: none; margin-bottom: 1rem; font-family: 'Segoe UI', sans-serif; }
    input:focus { border-color: #c8732a; }
    button { width: 100%; background: #c8732a; color: #fff; border: none; padding: 13px; border-radius: 4px; font-size: 13px; letter-spacing: 1px; cursor: pointer; font-family: 'Segoe UI', sans-serif; }
    button:hover { background: #a85e20; }
    .back { text-align: center; margin-top: 1.5rem; }
    .back a { color: #666; font-size: 12px; text-decoration: none; }
    .back a:hover { color: #c8732a; }
  </style>
</head>
<body>
<div class="card">
  <div class="logo">
    <h1>FLAMMA SKY</h1>
    <span>ADMIN PANEL</span>
  </div>
  <hr/>
  <?php if ($error): ?>
    <div class="error"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>
  <form method="POST">
    <label>USERNAME</label>
    <input type="text" name="username" placeholder="Enter username" autocomplete="off" required/>
    <label>PASSWORD</label>
    <input type="password" name="password" placeholder="Enter password" required/>
    <button type="submit">LOGIN TO DASHBOARD</button>
  </form>
  <div class="back"><a href="index.html">&larr; Back to website</a></div>
</div>
</body>
</html>
