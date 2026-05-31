<?php

include("admin_check.php");
include("../connection.php");

if(isset($_POST['add_subject']))
{
    $name = trim($_POST['name']);

    if(!empty($name))
    {
        $stmt = mysqli_prepare(
            $data,
            "INSERT INTO subjects(name) VALUES(?)"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "s",
            $name
        );

        mysqli_stmt_execute($stmt);
    }
}

if(isset($_GET['delete']))
{
    $id = $_GET['delete'];

    $stmt = mysqli_prepare(
        $data,
        "DELETE FROM subjects WHERE id=?"
    );

    mysqli_stmt_bind_param(
        $stmt,
        "i",
        $id
    );

    mysqli_stmt_execute($stmt);

    header("Location: manage_subjects.php");
    exit();
}

$subjects = mysqli_query(
    $data,
    "SELECT * FROM subjects ORDER BY id DESC"
);

?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Subjects</title>
</head>
<body>

<h2>Manage Subjects</h2>

<form method="POST">

    <input
        type="text"
        name="name"
        placeholder="Enter Subject Name"
        required>

    <button
        type="submit"
        name="add_subject">
        Add Subject
    </button>

</form>

<br>

<table border="1" cellpadding="10">

<tr>
    <th>ID</th>
    <th>Subject Name</th>
    <th>Action</th>
</tr>

<?php while($row = mysqli_fetch_assoc($subjects)) { ?>

<tr>

    <td><?php echo $row['id']; ?></td>

    <td><?php echo $row['name']; ?></td>

    <td>

        <a href="edit_subject.php?id=<?php echo $row['id']; ?>">
            Edit
        </a>

        |

        <a
        href="manage_subjects.php?delete=<?php echo $row['id']; ?>"
        onclick="return confirm('Delete this subject?')">
            Delete
        </a>

    </td>

</tr>

<?php } ?>

</table>

</body>
</html>