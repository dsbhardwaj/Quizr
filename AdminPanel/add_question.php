<?php

include("admin_check.php");
include("../connection.php");

$subject_query = mysqli_query($data, "SELECT * FROM subjects");

if(isset($_POST['add_question']))
{
    $subject_id = $_POST['subject_id'];
    $question = $_POST['question_text'];
    $option1 = $_POST['option_1'];
    $option2 = $_POST['option_2'];
    $option3 = $_POST['option_3'];
    $option4 = $_POST['option_4'];
    $correct_answer = $_POST['correct_answer'];

    $sql = "INSERT INTO questions
    (subject_id, question_text, option_1, option_2, option_3, option_4, correct_answer)
    VALUES (?, ?, ?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($data, $sql);

    mysqli_stmt_bind_param(
        $stmt,
        "isssssi",
        $subject_id,
        $question,
        $option1,
        $option2,
        $option3,
        $option4,
        $correct_answer
    );

    if(mysqli_stmt_execute($stmt))
    {
        $success = "Question Added Successfully";
    }
    else
    {
        $error = "Failed to Add Question";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Question</title>
</head>
<body>

<h2>Add Question</h2>

<?php
if(isset($success))
{
    echo "<p style='color:green;'>$success</p>";
}

if(isset($error))
{
    echo "<p style='color:red;'>$error</p>";
}
?>

<form method="POST">

    <label>Subject</label>
    <br>

    <select name="subject_id" required>

        <option value="">Select Subject</option>

        <?php
        while($row = mysqli_fetch_assoc($subject_query))
        {
        ?>
            <option value="<?php echo $row['id']; ?>">
                <?php echo $row['name']; ?>
            </option>
        <?php
        }
        ?>

    </select>

    <br><br>

    <label>Question</label>
    <br>
    <textarea name="question_text" required></textarea>

    <br><br>

    <label>Option 1</label>
    <br>
    <input type="text" name="option_1" required>

    <br><br>

    <label>Option 2</label>
    <br>
    <input type="text" name="option_2" required>

    <br><br>

    <label>Option 3</label>
    <br>
    <input type="text" name="option_3" required>

    <br><br>

    <label>Option 4</label>
    <br>
    <input type="text" name="option_4" required>

    <br><br>

    <label>Correct Answer</label>
    <br>

    <select name="correct_answer" required>
        <option value="1">Option 1</option>
        <option value="2">Option 2</option>
        <option value="3">Option 3</option>
        <option value="4">Option 4</option>
    </select>

    <br><br>

    <button type="submit" name="add_question">
        Add Question
    </button>

</form>

</body>
</html>