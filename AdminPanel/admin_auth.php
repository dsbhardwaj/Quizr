<?php

session_start();

include("../connection.php");

if($_SERVER["REQUEST_METHOD"] == "POST")
{
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $sql = "SELECT * FROM admin WHERE username = ?";

    $stmt = mysqli_prepare($data, $sql);

    mysqli_stmt_bind_param($stmt, "s", $username);

    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    if(mysqli_num_rows($result) == 1)
    {
        $admin = mysqli_fetch_assoc($result);

        if(password_verify($password, $admin['password']))
        {
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];

            header("Location: admin_dashboard.php");
            exit();
        }
        else
        {
            echo "Invalid Password";
        }
    }
    else
    {
        echo "Admin not found";
    }
}
else
{
    header("Location: admin_login.php");
    exit();
}

?>