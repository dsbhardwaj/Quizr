
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>user dashboard</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    body {
      background-color: #121212;
      color: white;
    }

    /* Navbar */
.navbar {
    display: flex;
    justify-content: space-between;  /* spreads items */
    align-items: center;
    padding: 15px 30px;
    background: #1e1e1e;
    color: white;
    height: 73px;
}

.navbar a {
    margin: 0 15px;
    text-decoration: none;
    color: white;
}
    .navbar .logo {
      font-size: 24px;
      color: #00cc99;
      font-weight: bold;
    }



    .navbar a:hover {
      color: #00cc99;
    }

    .navbar form {
      display: inline;
    }

    .navbar button {
      background: none;
      border: none;
      color: white;
      font-size: 16px;
      cursor: pointer;
      margin-left: 10px;
    }

    .navbar button:hover {
      color: #00cc99;
    }

    /* Dropdown */
    .dropdown {
      position: relative;
      display: inline-block;
    }

    .dropbtn {
      background-color: #2a2a2a;
      color: white;
      padding: 10px 16px;
      font-size: 14px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
    }

    .dropdown-content {
      display: none;
      position: absolute;
      background-color: #333;
      min-width: 160px;
      box-shadow: 0px 8px 16px rgba(0,0,0,0.3);
      z-index: 1;
      border-radius: 5px;
    }

    .dropdown-content a {
      color: white;
      padding: 12px 16px;
      text-decoration: none;
      display: block;
      border-bottom: 1px solid #444;
    }

    .dropdown-content a:hover {
      background-color: #00cc99;
      color: black;
    }

    .dropdown:hover .dropdown-content {
      display: block;
    }

    .dropdown:hover .dropbtn {
      background-color: #444;

}

      .analytics-container {
    display: flex;
    gap: 20px;
    margin: 20px 0;
}

.card {
    background: #ffffff;
    padding: 20px;
    border-radius: 12px;
    width: 200px;
    text-align: center;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    transition: 0.3s;
}

.card:hover {
    transform: translateY(-5px);
}

.card h3 {
    margin-bottom: 10px;
    color: #555;
}

.card p {
    font-size: 24px;
    font-weight: bold;
    color: #2c3e50;
}
    

.main-content {
    padding: 1px;
    background: #f5f6fa;
    min-height: 25vh;
        display: flex;
    align-items: center;
    color: black;
    margin-top:300px;
    margin-right:150px;
    margin-left: -900px;
    padding-left: 75px;
    padding-right: 87px;
}


.analytics-container {
    display: flex;
    gap: 20px;
    margin-top: 20px;
    margin-left: 13px;
}


.performance-section {
  display: flex;
  justify-content: center;
  gap: 20px;
  margin-top: 30px;
}

.card {
  padding: 20px;
  border-radius: 12px;
  width: 220px;
  text-align: center;
  font-weight: bold;
  box-shadow: 0 4px 10px rgba(0,0,0,0.2);
  
}

.strong {
  background-color: #d4edda;
  color: #155724;
  margin-top:200px;
}

.weak {
  background-color: #f8d7da;
  color: #721c24;
 margin-top:200px;
}

.card span {
  font-size: 22px;
  display: block;
  margin-top: 10px;
}
  </style>
</head>
<body>

<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$id = $_SESSION['user_id'];
?>
  <?php 
  include "connection.php";
//   if (isset($_GET['id']) && is_numeric($_GET['id']) && (int)$_GET['id'] > 0) {
//     $id = (int) $_GET['id'];
// } 


//**********************ANALYTICS CODE *********************************

$user_id = $_SESSION['user_id'];

$query = "SELECT * FROM result WHERE user_id = $user_id";
$result = mysqli_query($data, $query);

$total_quizzes = 0;
$total_score= 0;
$total_questions = 0;

while ($row = mysqli_fetch_assoc($result)) {
    $total_quizzes++;
    $total_score += $row['score'];
    $total_questions += $row['total_ques'];
}

// Accuracy
$accuracy = ($total_questions > 0) ? ($total_score / $total_questions) * 100 : 0;

// Average score
$avg_score = ($total_quizzes > 0) ? ($total_score / $total_quizzes) : 0;





// make sure user_id exists
$user_id = $_SESSION['user_id'] ?? 0;

// STRONG SUBJECT
$strong_query = "
SELECT s.name, 
AVG((r.score * 100.0) / r.total_ques) AS avg_score
FROM result r
JOIN subjects s ON r.subject_id = s.id
WHERE r.user_id = $user_id
GROUP BY s.id
ORDER BY avg_score DESC
LIMIT 1";

$strong_result = mysqli_query($data, $strong_query);
$strong = mysqli_fetch_assoc($strong_result);


// WEAK SUBJECT
$weak_query = "
SELECT s.name, 
AVG((r.score * 100.0) / r.total_ques) AS avg_score
FROM result r
JOIN subjects s ON r.subject_id = s.id
WHERE r.user_id = $user_id
GROUP BY s.id
ORDER BY avg_score ASC
LIMIT 1";

$weak_result = mysqli_query($data, $weak_query);
$weak = mysqli_fetch_assoc($weak_result);
?>



 <?php


$sql = "select * from users where id = '$id' ";
$result = $data -> query($sql);
if(!$result){
    die("invalid query!");
}
if($row=$result->fetch_assoc()) {?>

<div class="page">
  <div class = "navbar">
      <div class="logo">User Dashboard</div>
     <a href="profile.php?id=<?php echo $row['id'];?>">profile</a> 
  <a href="scoreboard.php?id=<?php echo $id; ?>">scoreboard</a>
  <a action="logout.php" method="POST" href="logout.php">logout</a> 
   
   
    <div class="dropdown">
      <button class = "dropbtn">QUIZES  &#x25BC;</button>
      <div class = "dropdown-content">
        <a href="quiz.php?subject=Aptitude&id=<?php echo $row['id']; ?>">Aptitude</a>
<a href="quiz.php?subject=Logical_Reasoning&id=<?php echo $row['id']; ?>">Logical Reasoning</a>
<a href="quiz.php?subject=Data_Structure&id=<?php echo $row['id']; ?>">Data Structure
</a>
<a href="quiz.php?subject=Web_Development&id=<?php echo $row['id']; ?>">Web Development</a>
<a href="quiz.php?subject=DBMS&id=<?php echo $row['id']; ?>">DBMS</a>
</div>
</div>


<div class="main-content">

    <h2>Welcome, <br>  
    <?php echo $row['name']; ?></h2>

    <div class="analytics-container">

        <div class="card">
            <h3>Total Quizzes</h3>
            <p><?php echo $total_quizzes; ?></p>
        </div>

        <div class="card">
            <h3>Accuracy</h3>
            <p><?php echo round($accuracy, 2); ?>%</p>
        </div>

        <div class="card">
            <h3>Average Score</h3>
            <p><?php echo round($avg_score, 2); ?></p>
        </div>

    </div>

</div>

</div>

<div class="performance-section">

  <div class="card strong">
    <h3>Strong Subject 💪</h3>
    <p><?php echo $strong['name'] ?? 'N/A'; ?></p>
    <span>
      <?php 
        echo isset($strong['avg_score']) 
        ? round($strong['avg_score']) . '%' 
        : '0%'; 
      ?>
    </span>
  </div>

  <div class="card weak">
    <h3>Weak Subject ⚠️</h3>
    <p><?php echo $weak['name'] ?? 'N/A'; ?></p>
    <span>
      <?php 
        echo isset($weak['avg_score']) 
        ? round($weak['avg_score']) . '%' 
        : '0%'; 
      ?>
    </span>
  </div>

</div>

<?php } ?>

</body>
</html>