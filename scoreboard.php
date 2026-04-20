<?php
include "connection.php";

$id = $_GET['id'] ?? '';

$query = "SELECT * FROM result WHERE user_id = $id";
$result = mysqli_query($data, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Scores</title>
    <style>
    * {
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


  /* * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    body {
      background-color: #121212;
      color: white;
    } */

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
}

    </style>
</head>
<body>


<?php
if (!isset($_GET['id'])) {
    die("User ID missing");
}
$user_id = $_GET['id'];
?>

<div class="page">
  <div class="navbar">
    <div class="logo">Your's ScoreCard</div>

    <a href="profile.php?id=<?php echo $user_id; ?>">profile</a>
    <a href="user.php?id=<?php echo $user_id; ?>">dashboard</a>
    <a href="logout.php">Logout</a>

    <div class="dropdown">
      <button class="dropbtn">QUIZZES ▼</button>

      <div class="dropdown-content">
        <a href="quiz.php?subject=Aptitude&id=<?php echo $user_id; ?>">Aptitude</a>
        <a href="quiz.php?subject=Logical_Reasoning&id=<?php echo $user_id; ?>">Logical Reasoning</a>
        <a href="quiz.php?subject=Data_Structure&id=<?php echo $user_id; ?>">Data Structure</a>
        <a href="quiz.php?subject=Web_Development&id=<?php echo $user_id; ?>">Web Development</a>
        <a href="quiz.php?subject=DBMS&id=<?php echo $user_id; ?>">DBMS</a>
      </div>
    </div>
  </div>
</div>




    <h1>Your Scores So Far!</h1>


<table border="1" cellpadding="10">
    <thead>
        <tr>
            
            <th>Subject</th>
            <th>Score</th>
              <th>Date</th>
        </tr>
    </thead>
     <br><br>
    
    <tbody>
        <?php
        while ($row = mysqli_fetch_assoc($result)) {
            $subject = $row['subject_id'];
            $correct = $row['score'];
            $total = $row['total_ques'];
            $scoreDisplay = "$correct / $total";
            $date = $row['submitted_on'];

            echo "<tr>
                  
                    <td>$subject</td>
                    <td>$scoreDisplay</td>
                    <td>$date</td>
                  </tr>";
        }
        ?>
    </tbody>
</table>




</body>
</html>
