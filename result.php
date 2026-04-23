<?php
$score = $_GET['score'];
$total = $_GET['total'];
$subject = $_GET['subject'];

echo "<h2>$subject Result</h2>";
echo "<p>Score: $score / $total</p>";
?>