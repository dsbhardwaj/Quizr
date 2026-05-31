<?php

include("admin_check.php");
include("../connection.php");

$sql = "
SELECT
    r.*,
    u.name AS user_name,
    s.name AS subject_name

FROM result r

LEFT JOIN users u
ON r.user_id = u.id

LEFT JOIN subjects s
ON r.subject_id = s.id

ORDER BY r.id DESC
";

$result = mysqli_query($data, $sql);

?>

<!DOCTYPE html>
<html>
<head>
    <title>Quiz Results</title>
</head>
<body>

<h2>Quiz Results</h2>

<table border="1" cellpadding="10">

<tr>
    <th>ID</th>
    <th>User</th>
    <th>Subject</th>
    <th>Topic</th>
    <th>Score</th>
    <th>Total Questions</th>
    <th>Percentage</th>
    <th>Submitted On</th>
</tr>

<?php while($row = mysqli_fetch_assoc($result)) { ?>

<tr>

    <td><?php echo $row['id']; ?></td>

    <td><?php echo $row['user_name']; ?></td>

    <td><?php echo $row['subject_name']; ?></td>

    <td><?php echo $row['topic']; ?></td>

    <td><?php echo $row['score']; ?></td>

    <td><?php echo $row['total_ques']; ?></td>

    <td>
        <?php
        echo round(
            ($row['score'] / $row['total_ques']) * 100,
            2
        ) . "%";
        ?>
    </td>

    <td><?php echo $row['submitted_on']; ?></td>

</tr>

<?php } ?>

</table>

</body>
</html>