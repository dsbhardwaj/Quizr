<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Result</title>
    <style>    * {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

body {
  background: #1e1e1e;
  color: white;
  text-align: center;
  padding: 40px 20px;
}

h1 {
  color: #00cc99;
  margin-bottom: 30px;
  font-size: 28px;
}

/* Table Styling */
table {
  border-collapse: collapse;
  margin: 0 auto;
  width: 80%;
  max-width: 800px;
  box-shadow: 0 0 10px rgba(0, 204, 153, 0.2);
}

thead {
  background-color: #00cc99;
  color: black;
}

thead th {
  padding: 14px;
  font-size: 18px;
}

tbody tr {
  background-color: #2d2d2d;
  transition: background-color 0.3s ease;
}

tbody tr:hover {
  background-color: #3e3e3e;
}

td {
  padding: 12px;
  font-size: 16px;
  border-bottom: 1px solid #444;
}

/* Responsive for small screens */
@media (max-width: 600px) {
  table {
    width: 100%;
  }

  thead {
    display: none;
  }

  tbody td {
    display: block;
    text-align: right;
    padding-left: 50%;
    position: relative;
  }

  tbody td::before {
    content: attr(data-label);
    position: absolute;
    left: 15px;
    width: 45%;
    font-weight: bold;
    text-align: left;
    color: #00cc99;
  }

  tbody tr {
    margin-bottom: 15px;
    display: block;
    border: 1px solid #444;
    border-radius: 10px;
    padding: 10px;
  }
}</style>
</head>
<body>

<h1>YOUR RESULT</h1>

<?php
session_start();
include "connection.php";
// sanitize inputs
$user_id = $_SESSION['user_id'];
$subject = mysqli_real_escape_string($data, $_GET['subject'] ?? '');
$answers = $_POST['answers'] ?? [];

// prevent empty submission
if (empty($answers)) {
    die("No answers submitted.");
}

$subject_query = "SELECT id FROM subjects WHERE name = '$subject'";
$res = mysqli_query($data, $subject_query);

if (!$res) {
    die("Subject query failed: " . mysqli_error($data));
}

if (mysqli_num_rows($res) == 0) {
    die("Invalid subject.");
}

$row = mysqli_fetch_assoc($res);
$subject_id = $row['id'];

// get correct answers
$q_ids = implode(",", array_keys($answers));

$query = "SELECT id, correct_answer FROM questions
          WHERE subject_id = $subject_id
          AND id IN ($q_ids)";

$result = mysqli_query($data, $query);

if (!$result) {
    die("Question query failed: " . mysqli_error($data));
}
$correct_answers = [];
while ($row = mysqli_fetch_assoc($result)) {
    $correct_answers[$row['id']] = $row['correct_answer'];
}

// calculate score
$score = 0;
$total = count($answers);

foreach ($answers as $q_id => $user_answer) {
    if (isset($correct_answers[$q_id]) && $user_answer == $correct_answers[$q_id]) {
        $score++;
    }
}


$insert = "INSERT INTO result (user_id, subject_id, score, total_ques)
           VALUES ($user_id, $subject_id, $score, $total)";

           echo "USER ID: $user_id <br>";
echo "SUBJECT ID: $subject_id <br>";
echo "SCORE: $score <br>";
echo "TOTAL: $total <br>";

if(!mysqli_query($data, $insert)){
    die("INSERT FAILED: " . mysqli_error($data));
}

unset($_SESSION['quiz_active']);
unset($_SESSION['start_time']);
unset($_SESSION['duration']);
unset($_SESSION['end_time']);
unset($_SESSION['submitted']);

header("Location: result.php?score=$score&total=$total&subject=$subject");
exit();

// display result
echo "<table border='1' cellpadding='10'>
        <thead>
            <tr>
                <th>User ID</th>
                <th>Subject</th>
                <th>Score</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>$id</td>
                <td>$subject</td>
                <td>$score / $total</td>
            </tr>
        </tbody>
      </table>";
?>
<?php
session_start();

$start = $_SESSION['start_time'];
$duration = $_SESSION['duration'];

$current = time();

if (($current - $start) > $duration) {

    echo "Time expired.";

    // answerally still calculate score
    // or reject submission completely

    exit();
}
?>

</body>
</html>
