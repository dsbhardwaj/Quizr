<?php
include "connection.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

$error   = '';
$success = '';

if (isset($_POST['submit'])) {
  $username  = mysqli_real_escape_string($data, $_POST['username']);
  $email     = mysqli_real_escape_string($data, $_POST['email']);
  $password  = $_POST['password'];
  $cpassword = $_POST['cpass'];

  if ($password !== $cpassword) {
    $error = "Passwords do not match. Please try again.";
  } else {
    $sql    = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($data, $sql);

    if (mysqli_num_rows($result) > 0) {
      $error = "An account with this email already exists.";
    } else {
      $hashed_password = password_hash($password, PASSWORD_DEFAULT);
      $token           = bin2hex(random_bytes(16));

      $sql = "INSERT INTO users (name, email, password, status, token)
              VALUES ('$username', '$email', '$hashed_password', 0, '$token')";

      if (mysqli_query($data, $sql)) {
        $mail = new PHPMailer(true);
        try {
          $mail->isSMTP();

          $mail->SMTPDebug = 2;


          $mail->Host       = 'smtp.gmail.com';
          $mail->SMTPAuth   = true;
          $mail->Username   = 'drishtiibhardwaj@gmail.com';
          $mail->Password   ='YOUR_APP_PASSWORD';
          $mail->SMTPSecure = 'tls';
          $mail->Port       = 587;

          $mail->setFrom('drishtiibhardwaj@gmail.com', 'Quizr');
          $mail->addAddress($email);
          $mail->isHTML(true);
          $mail->Subject = 'Verify your Quizr account';
          $mail->Body    = "
            <div style='font-family:Segoe UI,sans-serif;max-width:480px;margin:0 auto;padding:32px 24px;background:#f5f6fa;border-radius:12px;'>
              <div style='font-size:18px;font-weight:600;margin-bottom:20px;'>
                <span style='display:inline-block;width:8px;height:8px;border-radius:50%;background:#1D9E75;margin-right:8px;'></span>Quizr
              </div>
              <h2 style='font-size:22px;font-weight:600;color:#111110;margin-bottom:8px;'>Welcome, $username 👋</h2>
              <p style='font-size:14px;color:#6b6b6a;margin-bottom:24px;line-height:1.6;'>
                Thanks for signing up! Click the button below to verify your email address and activate your account.
              </p>
              <a href='http://localhost/FINAL-PROJECT/verify.php?token=$token'
                 style='display:inline-block;padding:12px 24px;background:#111110;color:#fff;border-radius:8px;font-size:14px;font-weight:500;text-decoration:none;'>
                Verify my account
              </a>
              <p style='font-size:12px;color:#9b9b9a;margin-top:24px;'>If you didn't create an account, you can safely ignore this email.</p>
            </div>
          ";
          $mail->send();
          $success = "Account created! We've sent a verification email to <strong>$email</strong>. Please check your inbox.";
        } catch (Exception $e) {
          $error = "Account created but verification email failed to send. ({$mail->ErrorInfo})";
        }
      } else {
        $error = "Something went wrong. Please try again.";
      }
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register — Quizr</title>
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

    /* ── BRAND ── */
    .brand {
      display: flex; align-items: center; gap: 8px;
      font-size: 18px; font-weight: 600; color: #111110;
      text-decoration: none; margin-bottom: 28px;
    }

    .brand-dot { width: 10px; height: 10px; border-radius: 50%; background: #1D9E75; }

    /* ── CARD ── */
    .card {
      background: #ffffff;
      border: 0.5px solid rgba(0,0,0,0.10);
      border-radius: 16px;
      padding: 36px 32px;
      width: 100%;
      max-width: 420px;
    }

    .card-title {
      font-size: 20px; font-weight: 600;
      letter-spacing: -0.02em; color: #111110;
      margin-bottom: 4px;
    }

    .card-sub {
      font-size: 13px; color: #6b6b6a;
      margin-bottom: 28px;
    }

    /* ── FIELDS ── */
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

    .input-wrap input.error-input { border-color: #E24B4A; }
    .input-wrap input::placeholder { color: #b0b0ae; }

    .toggle-pw {
      position: absolute; right: 12px;
      background: none; border: none; cursor: pointer;
      color: #9b9b9a; font-size: 16px; padding: 0;
      display: flex; align-items: center; transition: color 0.12s;
    }

    .toggle-pw:hover { color: #111110; }

    /* ── ALERTS ── */
    .alert {
      display: flex; align-items: flex-start; gap: 8px;
      border-radius: 8px; padding: 10px 14px;
      font-size: 13px; margin-bottom: 20px; line-height: 1.5;
    }

    .alert i { font-size: 16px; flex-shrink: 0; margin-top: 1px; }

    .alert-error   { background: #FCEBEB; border: 0.5px solid #F7C1C1; color: #791F1F; }
    .alert-success { background: #EAF3DE; border: 0.5px solid #C0DD97; color: #27500A; }

    /* ── PASSWORD STRENGTH ── */
    .strength-row { margin-top: 8px; display: flex; align-items: center; gap: 8px; }

    .strength-bars { display: flex; gap: 4px; }

    .strength-bar {
      width: 28px; height: 4px; border-radius: 2px;
      background: #f0f0ef; transition: background 0.2s;
    }

    .strength-label { font-size: 11px; color: #9b9b9a; }

    /* ── SUBMIT ── */
    .btn-submit {
      width: 100%; padding: 11px;
      border: none; border-radius: 8px;
      background: #111110; color: #fff;
      font-size: 14px; font-weight: 500;
      font-family: inherit; cursor: pointer;
      transition: opacity 0.15s; margin-top: 8px;
      display: flex; align-items: center; justify-content: center; gap: 8px;
    }

    .btn-submit:hover { opacity: 0.85; }

    /* ── DIVIDER ── */
    .divider {
      display: flex; align-items: center; gap: 12px;
      margin: 20px 0; color: #b0b0ae; font-size: 12px;
    }

    .divider::before, .divider::after {
      content: ''; flex: 1; height: 0.5px; background: rgba(0,0,0,0.10);
    }

    /* ── FOOTER ── */
    .card-footer {
      text-align: center; font-size: 12px; color: #9b9b9a; margin-top: 20px;
    }

    .card-footer a { color: #1D9E75; text-decoration: none; font-weight: 500; }
    .card-footer a:hover { text-decoration: underline; }

    /* ── MATCH INDICATOR ── */
    .match-hint { font-size: 11px; margin-top: 5px; display: none; }
    .match-hint.ok  { color: #1D9E75; display: block; }
    .match-hint.bad { color: #E24B4A; display: block; }
  </style>
</head>
<body>

  <a class="brand" href="login.php">
    <div class="brand-dot"></div>
    Quizr
  </a>

  <div class="card">
    <div class="card-title">Create an account</div>
    <div class="card-sub">Join Quizr and start testing your knowledge</div>

    <?php if ($error): ?>
      <div class="alert alert-error">
        <i class="ti ti-alert-circle"></i>
        <?php echo $error; ?>
      </div>
    <?php endif; ?>

    <?php if ($success): ?>
      <div class="alert alert-success">
        <i class="ti ti-circle-check"></i>
        <?php echo $success; ?>
      </div>
    <?php endif; ?>

    <?php if (!$success): ?>
    <form action="register.php" method="POST" novalidate>

      <!-- Username -->
      <div class="field">
        <label for="username">Username</label>
        <div class="input-wrap">
          <i class="ti ti-user lead"></i>
          <input
            type="text" id="username" name="username"
            placeholder="Your display name"
            value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>"
            required autocomplete="username"
          >
        </div>
      </div>

      <!-- Email -->
      <div class="field">
        <label for="email">Email address</label>
        <div class="input-wrap">
          <i class="ti ti-mail lead"></i>
          <input
            type="email" id="email" name="email"
            placeholder="you@example.com"
            value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
            required autocomplete="email"
          >
        </div>
      </div>

      <!-- Password -->
      <div class="field">
        <label for="password">Password</label>
        <div class="input-wrap">
          <i class="ti ti-lock lead"></i>
          <input
            type="password" id="password" name="password"
            placeholder="Create a strong password"
            required autocomplete="new-password"
            oninput="checkStrength(this.value); checkMatch();"
          >
          <button type="button" class="toggle-pw" onclick="togglePw('password','pw-icon1')">
            <i class="ti ti-eye" id="pw-icon1"></i>
          </button>
        </div>
        <!-- Strength bars -->
        <div class="strength-row">
          <div class="strength-bars">
            <div class="strength-bar" id="bar1"></div>
            <div class="strength-bar" id="bar2"></div>
            <div class="strength-bar" id="bar3"></div>
            <div class="strength-bar" id="bar4"></div>
          </div>
          <span class="strength-label" id="strengthLabel"></span>
        </div>
      </div>

      <!-- Confirm password -->
      <div class="field">
        <label for="cpass">Confirm password</label>
        <div class="input-wrap">
          <i class="ti ti-lock-check lead"></i>
          <input
            type="password" id="cpass" name="cpass"
            placeholder="Retype your password"
            required autocomplete="new-password"
            oninput="checkMatch()"
          >
          <button type="button" class="toggle-pw" onclick="togglePw('cpass','pw-icon2')">
            <i class="ti ti-eye" id="pw-icon2"></i>
          </button>
        </div>
        <div class="match-hint" id="matchHint"></div>
      </div>

      <button type="submit" name="submit" class="btn-submit">
        <i class="ti ti-user-plus" style="font-size:15px;"></i>
        Create account
      </button>
    </form>
    <?php endif; ?>

    <div class="card-footer">
      Already have an account? <a href="login.php">Sign in</a>
    </div>
  </div>

  <script>
    function togglePw(inputId, iconId) {
      const input = document.getElementById(inputId);
      const icon  = document.getElementById(iconId);
      const hide  = input.type === 'password';
      input.type  = hide ? 'text' : 'password';
      icon.className = hide ? 'ti ti-eye-off' : 'ti ti-eye';
    }

    function checkStrength(val) {
      let score = 0;
      if (val.length >= 8)             score++;
      if (/[A-Z]/.test(val))           score++;
      if (/[0-9]/.test(val))           score++;
      if (/[^A-Za-z0-9]/.test(val))   score++;

      const colors = ['#E24B4A','#EF9F27','#3B82F6','#1D9E75'];
      const labels = ['Weak','Fair','Good','Strong'];
      const bars   = ['bar1','bar2','bar3','bar4'];

      bars.forEach((id, i) => {
        document.getElementById(id).style.background =
          i < score ? colors[score - 1] : '#f0f0ef';
      });

      const lbl = document.getElementById('strengthLabel');
      lbl.textContent = val.length ? labels[score - 1] || '' : '';
      lbl.style.color = val.length ? colors[score - 1] : '#9b9b9a';
    }

    function checkMatch() {
      const pw    = document.getElementById('password').value;
      const cp    = document.getElementById('cpass').value;
      const hint  = document.getElementById('matchHint');
      const input = document.getElementById('cpass');
      if (!cp) { hint.className = 'match-hint'; return; }
      if (pw === cp) {
        hint.textContent = '✓ Passwords match';
        hint.className   = 'match-hint ok';
        input.classList.remove('error-input');
      } else {
        hint.textContent = '✗ Passwords do not match';
        hint.className   = 'match-hint bad';
        input.classList.add('error-input');
      }
    }
  </script>

</body>
</html>