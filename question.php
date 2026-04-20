<?php
include "connection.php";

$subject = $_GET['subject'] ?? '';
$allowedSubjects = ['maths', 'english', 'physics', 'Logical Reasoning', 'reasoning'];

// Validate subject
if (!in_array($subject, $allowedSubjects)) {
    die("Invalid subject.");
}

// ---------- UPDATE ----------
if (isset($_POST['update'])) {
    $id = (int) $_POST['q_id'];
    $question = $_POST['question'];
    $option_1 = $_POST['option1'];
    $option_2 = $_POST['option2'];
    $option_3 = $_POST['option3'];
    $option_4 = $_POST['option4'];
    $correct_answer = $_POST['correct_answer'];

    $sql = "UPDATE `$subject` SET 
                question = '$question', 
                option_1 = '$option_1', 
                option_2 = '$option_2', 
                option_3 = '$option_3', 
                option_4 = '$option_4', 
                correct_answer = '$correct_answer' 
            WHERE q_id = $id";

    if (mysqli_query($data, $sql)) {
        header("Location: edit.php?subject=$subject");
        exit;
    } else {
        echo "Error updating question: " . mysqli_error($data);
    }
}

// ---------- DELETE ----------
elseif (isset($_POST['delete'])) {
    $id = (int) $_POST['q_id'];

    $sql = "DELETE FROM `$subject` WHERE q_id = $id";

    if (mysqli_query($data, $sql)) {
        header("Location: edit.php?subject=$subject");
        exit;
    } else {
        echo "Error deleting question: " . mysqli_error($data);
    }
}

else {
    echo "No valid action performed.";
}
?>
