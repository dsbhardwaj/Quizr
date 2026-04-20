<?php
include "connection.php";

if (isset($_GET['token'])) {

    $token = $_GET['token'];

    $sql = "SELECT * FROM users WHERE token='$token'";
    $result = mysqli_query($data, $sql);

    if (mysqli_num_rows($result) > 0) {

        $update = "UPDATE users SET status=1, token='' WHERE token='$token'";
        mysqli_query($data, $update);

        echo "<script>
alert('Account verified successfully');
window.location.href='index.php';
</script>";

    } else {
        echo "❌ Invalid or expired token.";
    }
}
?>