<?php
include "connection.php";

if (isset($_GET['token'])) {

    $token = trim($_GET['token']);

    echo "TOKEN FROM URL: " . $token . "<br><br>";

    $sql = "SELECT * FROM users WHERE token='$token'";
    $result = mysqli_query($data, $sql);

    echo "MATCHING ROWS: " . mysqli_num_rows($result) . "<br><br>";

    if (mysqli_num_rows($result) > 0) {

        $update = "UPDATE users SET status=1, token='' WHERE token='$token'";

        if (mysqli_query($data, $update)) {

            echo "UPDATE SUCCESS";

        } else {

            echo mysqli_error($data);

        }

    } else {

        echo "Invalid token";

    }
}
?>