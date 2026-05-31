<?php
session_start();

if (isset($_SESSION['admin_id'])) {
    header("Location: admin_dashboard.php");
    exit();
}

$error = '';
if (isset($_GET['error'])) {
    if ($_GET['error'] === 'invalid')       $error = "Invalid username or password.";
    elseif ($_GET['error'] === 'forbidden') $error = "Access denied. Admins only.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Login — Quizr</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: #f5f6fa;
      min-height: 100vh;
      display: flex; flex-direction: column;
      align-items: center; justify-content: center;
      padding: 24px;
    }

    .brand {
      display: flex; align-items: center; gap: 8px;
      font-size: 18px; font-weight: 600; color: #111110;
      text-decoration: none; margin-bottom: 6px;
    }

    .brand-dot { width: 10px; height: 10px; border-radius: 50%; background: #1D9E75; }

    .brand-sub {
      font-size: 12px; color: #9b9b9a; margin-bottom: 28px;
      display: flex; align-items: center; gap: 6px;
    }

    .admin-chip {
      background: #FAEEDA; color: #633806;
      border: 0.5px solid #FAC775;
      border-radius: 100px; padding: 2px 8px;
      font-size: 11px; font-weight: 600;
    }

    .card {
      background: #ffffff;
      border: 0.5px solid rgba(0,0,0,0.10);
      border-radius: 16px;
      padding: 36px 32px;
      width: 100%; max-width: 400px;
    }

    .card-title { font-size: 20px; font-weight: 600; letter-spacing: -0.02em; margin-bottom: 4px; }
    .card-sub   { font-size: 13px; color: #6b6b6a; margin-bottom: 28px; }

    .field { margin-bottom: 16px; }

    .field label {
      display: block; font-size: 12px; font-weight: 500;
      color: #6b6b6a; text-transform: uppercase;
      letter-spacing: 0.06em; margin-bottom: 6px;
    }

    .input-wrap { position: relative; display: flex; align-items: center; }

    .input-wrap i.lead {
      position: absolute; left: 12px;
      font-size: 16px; color: #9b9b9a; pointer-events: none;
    }

    .input-wrap input {
      width: 100%;
      padding: 10px 12px 10px 36px;
      border: 0.5px solid rgba(0,0,0,0.14);
      border-radius: 8px;
      font-size: 14px; font-family: inherit;
      background: #fafaf9; color: #111110;
      transition: border-color 0.15s, box-shadow 0.15s;
      outline: none;
    }

    .input-wrap input:focus {
      border-color: #1D9E75;
      box-shadow: 0 0 0 3px rgba(29,158,117,0.12);
      background: #fff;
    }

    .input-wrap input::placeholder { color: #b0b0ae; }

    .toggle-pw {
      position: absolute; right: 12px;
      background: none; border: none; cursor: pointer;
      color: #9b9b9a; font-size: 16px; padding: 0;
      display: flex; align-items: center; transition: color 0.12s;
    }

    .toggle-pw:hover { color: #111110; }

    .alert {
      display: flex; align-items: center; gap: 8px;
      border-radius: 8px; padding: 10px 14px;
      font-size: 13px; margin-bottom: 20px;
      background: #FCEBEB; border: 0.5px solid #F7C1C1; color: #791F1F;
    }

    .btn-submit {
      width: 100%; padding: 11px; border: none;
      border-radius: 8px; background: #111110; color: #fff;
      font-size: 14px; font-weight: 500; font-family: inherit;
      cursor: pointer; transition: opacity 0.15s; margin-top: 8px;
      display: flex; align-items: center; justify-content: center; gap: 8px;
    }

    .btn-submit:hover { opacity: 0.85; }

    .back-link {
      text-align: center; font-size: 12px;
      color: #9b9b9a; margin-top: 20px;
    }

    .back-link a { color: #1D9E75; text-decoration: none; font-weight: 500; }
    .back-link a:hover { text-decoration: underline; }
  </style>
</head>
<body>

  <a class="brand" href="#">
    <div class="brand-dot"></div>
    Quizr
  </a>
  <div class="brand-sub">
    <span class="admin-chip">Admin</span>
    Restricted access
  </div>

  <div class="card">
    <div class="card-title">Admin sign in</div>
    <div class="card-sub">Enter your credentials to access the dashboard</div>

    <?php if ($error): ?>
      <div class="alert">
        <i class="ti ti-alert-circle" style="font-size:16px; flex-shrink:0;"></i>
        <?php echo htmlspecialchars($error); ?>
      </div>
    <?php endif; ?>

    <form action="admin_auth.php" method="POST">

      <div class="field">
        <label for="username">Username</label>
        <div class="input-wrap">
          <i class="ti ti-user lead"></i>
          <input type="text" id="username" name="username"
            placeholder="Admin username" required autocomplete="username">
        </div>
      </div>

      <div class="field">
        <label for="password">Password</label>
        <div class="input-wrap">
          <i class="ti ti-lock lead"></i>
          <input type="password" id="password" name="password"
            placeholder="Enter your password" required autocomplete="current-password">
          <button type="button" class="toggle-pw" onclick="togglePw()">
            <i class="ti ti-eye" id="pw-icon"></i>
          </button>
        </div>
      </div>

      <button type="submit" class="btn-submit">
        <i class="ti ti-shield-check" style="font-size:15px;"></i>
        Sign in as Admin
      </button>
    </form>

    <div class="back-link">
      Not an admin? <a href="../login.php">Back to user login</a>
    </div>
  </div>

  <script>
    function togglePw() {
      const input = document.getElementById('password');
      const icon  = document.getElementById('pw-icon');
      const hide  = input.type === 'password';
      input.type  = hide ? 'text' : 'password';
      icon.className = hide ? 'ti ti-eye-off' : 'ti ti-eye';
    }
  </script>
</body>
</html>