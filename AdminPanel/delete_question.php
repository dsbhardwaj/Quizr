<?php

include("admin_check.php");
include("../connection.php");

if(isset($_GET['id']))
{
    $id = $_GET['id'];

    $sql = "DELETE FROM questions WHERE id = ?";

    $stmt = mysqli_prepare($data, $sql);

    mysqli_stmt_bind_param($stmt, "i", $id);

    if(mysqli_stmt_execute($stmt))
    {
        header("Location: view_questions.php");
        exit();
    }
    else
    {
        echo "Failed to delete question.";
    }
}
else
{
    header("Location: view_questions.php");
    exit();
}

?>