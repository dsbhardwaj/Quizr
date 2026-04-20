



 <!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login | Influx</title>


  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"> 

  <style> 
     * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    body {
      background: linear-gradient(135deg, #0f0f0f, #1a1a1a);
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }

    .login-box {
      background: rgba(30, 30, 30, 0.9);
      padding: 40px;
      border-radius: 16px;
      box-shadow: 0 8px 32px rgba(0, 255, 150, 0.2);
      width: 100%;
      max-width: 380px;
      text-align: center;
      backdrop-filter: blur(8px);
    }

    .login-box h1 {
      color: #00cc99;
      margin-bottom: 25px;
      font-size: 26px;
      letter-spacing: 1px;
    }

    .input-group {
      position: relative;
      margin-bottom: 18px;
    }

    .input-group input {
      width: 100%;
      padding: 12px 40px 12px 15px;
      border: none;
      border-radius: 10px;
      background: #2a2a2a;
      color: #fff;
      font-size: 14px;
      outline: none;
    }

    .input-group i {
      position: absolute;
      right: 12px;
      top: 50%;
      transform: translateY(-50%);
      color: #aaa;
      cursor: pointer;
    }

    .login-btn {
      width: 100%;
      padding: 12px;
      margin-top: 10px;
      border: none;
      border-radius: 10px;
      background: #00cc99;
      color: white;
      font-size: 16px;
      font-weight: bold;
      cursor: pointer;
      transition: 0.3s;
    }

    .login-btn:hover {
      background: #00b386;
    }

    .extra {
      margin-top: 20px;
      font-size: 14px;
      color: #ccc;
    }

    .extra a {
      color: #00cc99;
      text-decoration: none;
      font-weight: bold;
    }

    .extra a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>

  <div class="login-box">
    <h1>Login</h1>
    <form action="login.php" method="POST">

      <div class="input-group">
        <input type="email" name="email" placeholder="Email Address" required>
        <i class="fa-solid fa-envelope"></i>
      </div>

      <div class="input-group">
        <input type="password" id="password" name="password" placeholder="Password" required>
        <i class="fa-solid fa-eye" id="togglePassword"></i>
      </div>

      <button type="submit" class="login-btn" name="submit">Login</button>
    </form>

    <div class="extra">
      <p>Don’t have an account? <a href="register.php">Register</a></p>
    </div>
  </div> 

  <script>
    const togglePassword = document.getElementById('togglePassword');
    const passwordField = document.getElementById('password');

    togglePassword.addEventListener('click', function () {
      const type = passwordField.type === 'password' ? 'text' : 'password'; -->
      <!-- passwordField.type = type;

      this.classList.toggle('fa-eye');
      this.classList.toggle('fa-eye-slash');
    });
  </script>
</body>
</html> 
