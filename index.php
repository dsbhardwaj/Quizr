<?php
session_start();
include "connection.php";

if (isset($_POST['submit'])) {
  $email    = trim($_POST['email']);
  $password = $_POST['password'];

  $sql    = "SELECT * FROM users WHERE email='$email' AND status=1";
  $result = mysqli_query($data, $sql);

  if (mysqli_num_rows($result) == 0) {
    $error = "No account found with that email.";
  } else {
    $row = mysqli_fetch_assoc($result);
    if (password_verify($password, $row['password'])) {
      $_SESSION['user_id'] = $row['id'];
      $_SESSION['role']    = $row['role'];
      if ($row['role'] == "user") {
        header("Location: user.php"); exit();
      } elseif ($row['role'] == "admin") {
        header("Location: admin.php"); exit();
      } else {
        $error = "Invalid role assigned to this account.";
      }
    } else {
      $error = "Incorrect password. Please try again.";
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login — Quizr</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: #f5f6fa;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      padding: 24px;
    }

    .brand {
      display: flex;
      align-items: center;
      gap: 8px;
      font-size: 18px;
      font-weight: 600;
      color: #111110;
      margin-bottom: 28px;
      text-decoration: none;
    }

    .brand-dot {
      width: 10px;
      height: 10px;
      border-radius: 50%;
      background: #1D9E75;
    }

    .card {
      background: #ffffff;
      border: 0.5px solid rgba(0,0,0,0.10);
      border-radius: 16px;
      padding: 36px 32px;
      width: 100%;
      max-width: 400px;
    }

    .card-title {
      font-size: 20px;
      font-weight: 600;
      letter-spacing: -0.02em;
      color: #111110;
      margin-bottom: 4px;
    }

    .card-sub {
      font-size: 13px;
      color: #6b6b6a;
      margin-bottom: 28px;
    }

    .field { margin-bottom: 16px; }

    .field label {
      display: block;
      font-size: 12px;
      font-weight: 500;
      color: #6b6b6a;
      text-transform: uppercase;
      letter-spacing: 0.06em;
      margin-bottom: 6px;
    }

    .input-wrap {
      position: relative;
      display: flex;
      align-items: center;
    }

    .input-wrap i {
      position: absolute;
      left: 12px;
      font-size: 16px;
      color: #9b9b9a;
      pointer-events: none;
    }

    .input-wrap input {
      width: 100%;
      padding: 10px 12px 10px 36px;
      border: 0.5px solid rgba(0,0,0,0.14);
      border-radius: 8px;
      font-size: 14px;
      font-family: inherit;
      background: #fafaf9;
      color: #111110;
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
      position: absolute;
      right: 12px;
      background: none;
      border: none;
      cursor: pointer;
      color: #9b9b9a;
      font-size: 16px;
      padding: 0;
      display: flex;
      align-items: center;
      transition: color 0.12s;
    }

    .toggle-pw:hover { color: #111110; }

    .error-box {
      display: flex;
      align-items: center;
      gap: 8px;
      background: #FCEBEB;
      border: 0.5px solid #F7C1C1;
      border-radius: 8px;
      padding: 10px 14px;
      font-size: 13px;
      color: #791F1F;
      margin-bottom: 20px;
    }

    .btn-submit {
      width: 100%;
      padding: 11px;
      border: none;
      border-radius: 8px;
      background: #111110;
      color: #fff;
      font-size: 14px;
      font-weight: 500;
      font-family: inherit;
      cursor: pointer;
      transition: opacity 0.15s;
      margin-top: 8px;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
    }

    .btn-submit:hover { opacity: 0.85; }

    .card-footer {
      text-align: center;
      font-size: 12px;
      color: #9b9b9a;
      margin-top: 20px;
    }

    .card-footer a { color: #1D9E75; text-decoration: none; font-weight: 500; }
    .card-footer a:hover { text-decoration: underline; }
  </style>
</head>
<body>

  <a class="brand" href="#">
    <div class="brand-dot"></div>
    Quizr
  </a>

  <div class="card">
    <div class="card-title">Welcome back</div>
    <div class="card-sub">Sign in to continue to your dashboard</div>

    <?php if (!empty($error)): ?>
      <div class="error-box">
        <i class="ti ti-alert-circle" style="font-size:16px; flex-shrink:0;"></i>
        <?php echo htmlspecialchars($error); ?>
      </div>
    <?php endif; ?>

    <form action="login.php" method="POST">

      <div class="field">
        <label for="email">Email address</label>
        <div class="input-wrap">
          <i class="ti ti-mail"></i>
          <input
            type="email"
            id="email"
            name="email"
            placeholder="you@example.com"
            value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
            required
            autocomplete="email"
          >
        </div>
      </div>

      <div class="field">
        <label for="password">Password</label>
        <div class="input-wrap">
          <i class="ti ti-lock"></i>
          <input
            type="password"
            id="password"
            name="password"
            placeholder="Enter your password"
            required
            autocomplete="current-password"
          >
          <button type="button" class="toggle-pw" onclick="togglePw()" aria-label="Toggle password visibility">
            <i class="ti ti-eye" id="pw-icon"></i>
          </button>
        </div>
      </div>

      <button type="submit" name="submit" class="btn-submit">
        <i class="ti ti-login" style="font-size:15px;"></i>
        Sign in
      </button>
    </form>

    <div class="card-footer">
      Don't have an account? <a href="register.php">Sign up</a>
    </div>
  </div>

  <script>
    function togglePw() {
      const input  = document.getElementById('password');
      const icon   = document.getElementById('pw-icon');
      const isHidden = input.type === 'password';
      input.type   = isHidden ? 'text' : 'password';
      icon.className = isHidden ? 'ti ti-eye-off' : 'ti ti-eye';
    }
  </script>

</body>
</html>