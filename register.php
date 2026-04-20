<?php
include "connection.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Register</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    body {
      background-color: #0d0d0d;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      color: #fff;
    }

    form {
      background: #1e1e1e;
      padding: 40px 30px;
      border-radius: 15px;
      box-shadow: 0 0 20px rgba(255, 255, 255, 0.1);
      width: 100%;
      max-width: 400px;
    }

    h1 {
      margin-bottom: 25px;
      color: #ffffff;
      font-size: 24px;
    }

    input[type="text"],
    input[type="email"],
    input[type="password"] {
      width: 100%;
      padding: 12px 15px;
      margin: 10px 0;
      border: none;
      border-radius: 8px;
      background-color: #333;
      color: white;
      font-size: 14px;
    }

    input[type="submit"] {
      width: 100%;
      padding: 12px;
      background-color: #00cc99;
      color: white;
      font-size: 16px;
      font-weight: bold;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      margin-top: 20px;
      transition: background-color 0.3s ease;
    }

    input[type="submit"]:hover {
      background-color: #00b386;
    }

    ::placeholder {
      color: #aaa;
    }
  </style>
</head>
<body>

  <form action="register.php" method="POST">
    <h1> Hello New User! <br> Register Below</h1>

    <input type="text" name="username" placeholder="Enter your username" required>
    <input type="email" name="email" placeholder="Enter your email" required>
    <input type="password" name="password" placeholder="Enter your password" required>
    <input type="password" name="cpass" placeholder="Retype your password" required>
    
    <input type="submit" name="submit" value="Register">
  </form>

</body>
</html>

<?php
include "connection.php";



use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';






if(isset($_POST['submit'])){
  $username = mysqli_real_escape_string($data, $_POST['username']);
  $email = mysqli_real_escape_string($data, $_POST['email']);
  $password = $_POST['password'];
  $cpassword = $_POST['cpass'];

  // check email exists
  $sql = "SELECT * FROM users WHERE email = '$email'";
  $result = mysqli_query($data, $sql);
  $count_email = mysqli_num_rows($result);

  if ($count_email == 0) {

    if ($password === $cpassword) {

      // 🔥 HASH PASSWORD HERE
      $hashed_password = password_hash($password, PASSWORD_DEFAULT);


       $token = bin2hex(random_bytes(16));




      $sql = "INSERT INTO users (name, email, password , status , token) 
              VALUES ('$username', '$email', '$hashed_password' , 0 , '$token')";

      if (mysqli_query($data, $sql)) {

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->SMTPDebug = 2; // 🔥 debug ON

        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;

        $mail->Username = 'drishtiibhardwaj@gmail.com';
        $mail->Password = 'cnhmsxzckbxxadqn';

        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('drishtiibhardwaj@gmail.com', 'Quiz Platform');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = 'Welcome to Quiz Platform';

        
         $mail->Body = "
    <h2>Welcome, $username 👋</h2>
    <p>Click below to verify your account:</p>
    <a href='http://localhost/FINAL-PROJECT/verify.php?token=$token'>
        Verify Account
    </a>
";
        $mail->send();

        echo "✅ Email sent successfully";
        exit;

    } catch (Exception $e) {
        echo "❌ Email failed: {$mail->ErrorInfo}";
        exit;
    }
}
    }
  }
}

?>