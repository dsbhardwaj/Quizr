<?php

include("admin_check.php");
include("../connection.php");

if(!isset($_GET['id']))
{
    header("Location: view_questions.php");
    exit();
}

$id = $_GET['id'];

$query = mysqli_query(
    $data,
    "SELECT * FROM questions WHERE id='$id'"
);

$question = mysqli_fetch_assoc($query);

$subjects = mysqli_query(
    $data,
    "SELECT * FROM subjects"
);

if(isset($_POST['update_question']))
{
    $subject_id = $_POST['subject_id'];
    $question_text = $_POST['question_text'];
    $option_1 = $_POST['option_1'];
    $option_2 = $_POST['option_2'];
    $option_3 = $_POST['option_3'];
    $option_4 = $_POST['option_4'];
    $correct_answer = $_POST['correct_answer'];

    $sql = "UPDATE questions
            SET
            subject_id=?,
            question_text=?,
            option_1=?,
            option_2=?,
            option_3=?,
            option_4=?,
            correct_answer=?
            WHERE id=?";

    $stmt = mysqli_prepare($data, $sql);

    mysqli_stmt_bind_param(
        $stmt,
        "isssssii",
        $subject_id,
        $question_text,
        $option_1,
        $option_2,
        $option_3,
        $option_4,
        $correct_answer,
        $id
    );

    if(mysqli_stmt_execute($stmt))
    {
        header("Location: view_questions.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Question</title>
</head>
<body>

<h2>Edit Question</h2>

<form method="POST">

    <label>Subject</label>
    <br>

    <select name="subject_id" required>

        <?php while($row = mysqli_fetch_assoc($subjects)) { ?>

            <option
                value="<?php echo $row['id']; ?>"
                <?php
                if($row['id'] == $question['subject_id'])
                {
                    echo "selected";
                }
                ?>
            >
                <?php echo $row['name']; ?>
            </option>

        <?php } ?>

    </select>

    <br><br>

    <label>Question</label>
    <br>

    <textarea
        name="question_text"
        required><?php echo $question['question_text']; ?></textarea>

    <br><br>

    <input
        type="text"
        name="option_1"
        value="<?php echo $question['option_1']; ?>"
        required>

    <br><br>

    <input
        type="text"
        name="option_2"
        value="<?php echo $question['option_2']; ?>"
        required>

    <br><br>

    <input
        type="text"
        name="option_3"
        value="<?php echo $question['option_3']; ?>"
        required>

    <br><br>

    <input
        type="text"
        name="option_4"
        value="<?php echo $question['option_4']; ?>"
        required>

    <br><br>

    <label>Correct Answer</label>

    <select name="correct_answer">

        <option value="1"
        <?php if($question['correct_answer']==1) echo "selected"; ?>>
        Option 1
        </option>

        <option value="2"
        <?php if($question['correct_answer']==2) echo "selected"; ?>>
        Option 2
        </option>

        <option value="3"
        <?php if($question['correct_answer']==3) echo "selected"; ?>>
        Option 3
        </option>

        <option value="4"
        <?php if($question['correct_answer']==4) echo "selected"; ?>>
        Option 4
        </option>

    </select>

    <br><br>

    <button type="submit" name="update_question">
        Update Question
    </button>

</form>

</body>
</html>