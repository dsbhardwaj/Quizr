<?php
include "connection.php";
?>

<?php
echo "reached here";
?>



 <!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login</title>
  <style>
     * {
      box-sizing: border-box;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    body {
      background-color: #ffffff;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
    }

    form {
      background-color:rgb(88, 86, 86);
      padding: 40px;
      border-radius: 15px;
      box-shadow: 0 0 15px rgba(0, 255, 150, 0.2);
      width: 100%;
      max-width: 400px;
    }

    h1 {
      color: #fff;
      margin-bottom: 25px;
      text-align: center;
    }

    input[type="email"],
    input[type="password"] {
      
      width: 100%;
      padding: 12px 15px;
      margin: 10px 0;
      border: none;
      border-radius: 8px;
      background-color: #2a2a2a;
      color: white;
    }

    input[type="submit"] {
      width: 100%;
      padding: 12px;
      margin-top: 20px;
      border: none;
      border-radius: 8px;
      background-color: #00cc99;
      color: white;
      font-size: 16px;
      font-weight: bold;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }

    input[type="submit"]:hover {
      background-color: #00b386;
    }

    .error {
      color: #ff4d4d;
      text-align: center;
      margin-top: 15px;
    }

    .placeholder {
      color: #aaa;
    }
    .password-container{
      position:relative;
    }
    .password-container input{
      padding-right:25px;
    <!-- }
    
.password-container i {
  position: absolute;
  right: 10px; /* Adjust positioning */
  top: 50%;
  transform: translateY(-50%);
  cursor: pointer;
}
    </style>
</head>
<body>

<form action="login.php" method="POST">
  <h2> Login to Quiz Platform</h2>
  <input type="email" name="email" placeholder="Enter your email" required>
  <input type="password" name="password" placeholder="Enter your password" required>
  <input type="submit" name="submit" value="Login">
</form>

</body>
</html>

<?php
session_start();
include "connection.php";

if (isset($_POST['submit'])) {

  $email = trim($_POST['email']);
  $password = $_POST['password'];

 $sql = "SELECT * FROM users 
WHERE email='$email' AND status=1";
  $result = mysqli_query($data, $sql);

  if (mysqli_num_rows($result) == 0) {
    echo "<script>alert('User not found');</script>";
  } else {

    $row = mysqli_fetch_assoc($result);

    if (password_verify($password, $row['password'])) {

      $_SESSION['user_id'] = $row['id'];
      $_SESSION['role'] = $row['role'];

      if ($row['role'] == "user") {
        header("Location: user.php");
        exit();
      } elseif ($row['role'] == "admin") {
        header("Location: admin.php");
        exit();
      } else {
        echo "Invalid role";
      }

    } else {
      echo "<script>alert('Wrong password');</script>";
    }
  }
}

// echo "Entered: " . $password . "<br>";
// echo "Stored: " . $row['password'] . "<br>";

// if (password_verify($password, $row['password'])) {
//     echo "MATCH";
// } else {
//     echo "NO MATCH";
// }
// exit;
?>