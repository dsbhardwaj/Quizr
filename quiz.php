<?php
include "connection.php";

$subject = $_GET['subject'] ?? '';
$allowedSubjects = ['Aptitude', 'Logical Reasoning', 'Data Structure', 'DBMS', 'Web Development'];

$query = "SELECT id FROM subjects WHERE name = '$subject'";
$res = mysqli_query($data, $query);



if (mysqli_num_rows($res) == 0) {
    die("Invalid subject.");
}

$row = mysqli_fetch_assoc($res);
$subject_id = $row['id'];
//echo "Subject from URL: [$subject]";


$sql = "SELECT * FROM questions WHERE subject_id = $subject_id";
$result = mysqli_query($data, $sql);

?>
<!DOCTYPE html>
<html>
<head>
  <title><?php echo strtoupper($subject); ?> QUIZ</title>
</head>
<body>

<h2><?php echo strtoupper($subject); ?> QUIZ</h2>

<form method="POST" action="score.php?id=<?php echo $_GET['id']; ?>&subject=<?php echo $subject; ?>"
<?php
$qno = 1;
while ($row = mysqli_fetch_assoc($result)) {
    echo "<p>Q$qno. {$row['question_text']}</p>";

    echo "<input type='radio' name='answers[{$row['id']}]' value='1'> {$row['option_1']}<br>";
    echo "<input type='radio' name='answers[{$row['id']}]' value='2'> {$row['option_2']}<br>";
    echo "<input type='radio' name='answers[{$row['id']}]' value='3'> {$row['option_3']}<br>";
    echo "<input type='radio' name='answers[{$row['id']}]' value='4'> {$row['option_4']}<br><br>";

    $qno++;
}
?>

<button type="submit">Submit</button>

</form>
</body>
</html>