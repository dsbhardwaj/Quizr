<?php
include("admin_check.php");
?>

<?php

if(!isset($_SESSION['admin_id']))
{
    header("Location: admin_login.php");
    exit();
}

?>
<?php
include("admin_check.php");
?>

<h1>Admin Dashboard</h1>

<hr>

<a href="add_question.php">Add Question</a>
<br><br>

<a href="view_questions.php">View Questions</a>
<br><br>

<a href="manage_users.php">Manage Users</a>
<br><br>

<a href="results.php">View Results</a>
<br><br>

<a href="admin_logout.php">Logout</a>

<hr>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>

</head>
<body>

<h1>Welcome Admin</h1>

<?php

include("../connection.php");

$user_query = mysqli_query($data,
"SELECT COUNT(*) AS total FROM users");

$user_count = mysqli_fetch_assoc($user_query);

$question_query = mysqli_query($data,
"SELECT COUNT(*) AS total FROM questions");

$question_count = mysqli_fetch_assoc($question_query);

$result_query = mysqli_query($data,
"SELECT COUNT(*) AS total FROM result");

$result_count = mysqli_fetch_assoc($result_query);

$subject_query = mysqli_query($data,
"SELECT COUNT(*) AS total FROM subjects");

$subject_count = mysqli_fetch_assoc($subject_query);

?>

<h2>Statistics</h2>

<p>Total Users:
<?php echo $user_count['total']; ?>
</p>

<p>Total Questions:
<?php echo $question_count['total']; ?>
</p>

<p>Total Quiz Attempts:
<?php echo $result_count['total']; ?>
</p>

<p>Total Subjects:
<?php echo $subject_count['total']; ?>
</p>

<p>
    <?php echo $_SESSION['admin_username']; ?>
</p>

<a href="admin_logout.php">Logout</a>

</body>
</html>