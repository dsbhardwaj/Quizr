<?php

include("admin_check.php");
include("../connection.php");

if(!isset($_GET['id']))
{
    header("Location: manage_subjects.php");
    exit();
}

$id = $_GET['id'];

$stmt = mysqli_prepare(
    $data,
    "SELECT * FROM subjects WHERE id=?"
);

mysqli_stmt_bind_param(
    $stmt,
    "i",
    $id
);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$subject = mysqli_fetch_assoc($result);

if(!$subject)
{
    header("Location: manage_subjects.php");
    exit();
}

if(isset($_POST['update_subject']))
{
    $name = trim($_POST['name']);

    $stmt = mysqli_prepare(
        $data,
        "UPDATE subjects SET name=? WHERE id=?"
    );

    mysqli_stmt_bind_param(
        $stmt,
        "si",
        $name,
        $id
    );

    if(mysqli_stmt_execute($stmt))
    {
        header("Location: manage_subjects.php");
        exit();
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Subject</title>
</head>
<body>

<h2>Edit Subject</h2>

<form method="POST">

    <input
        type="text"
        name="name"
        value="<?php echo htmlspecialchars($subject['name']); ?>"
        required>

    <button
        type="submit"
        name="update_subject">
        Update Subject
    </button>

</form>

</body>
</html>