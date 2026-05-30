<?php
include("admin_check.php");
?>

<?php

session_start();

if(!isset($_SESSION['admin_id']))
{
    header("Location: admin_login.php");
    exit();
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
</head>
<body>

<h1>Welcome Admin</h1>

<p>
    <?php echo $_SESSION['admin_username']; ?>
</p>

<a href="admin_logout.php">Logout</a>

</body>
</html>