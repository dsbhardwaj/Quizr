
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>hello</title>
  <style>
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
@media (max-width: 600px) 
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
  </style>
</head>
<body>

    
    <tr>
          <table class="table"> 
       <thead>
    <tr>
            <th>ID</th>
         <th> NAME</th>
          <th> EMAIL</th>
        </tr>
    </thead>
    </tr>
<tbody>

<?php
include "connection.php";
if (isset($_GET['id']) && is_numeric($_GET['id']) && (int)$_GET['id'] > 0) {
    $id = (int) $_GET['id'];
} else {
    echo "Invalid or missing ID in URL.";
}
if ($id ) {
   $sql = "SELECT * FROM users WHERE id = '$id'";
 
    $result = $data->query($sql);
    if (!$result) {
    die("Query error: " . $data->error);
} elseif ($result->num_rows === 0) {
    die("No records found for ID = $id");
} else {
   // echo "Query successful!";
}
    while ($row = $result->fetch_assoc()) {
        ?>
        <tr>
            <td> <?php echo $row['id']; ?> </td>
            <td> <?php echo $row['name']; ?> </td>
            <td> <?php echo $row['email']; ?> </td>
        </tr>
        <?php
    }
} else {
    echo "No user selected.";
}
?>
</tbody>


<?php
if (!isset($_GET['id'])) {
    die("User ID missing");
}
$user_id = $_GET['id'];
?>

<div class="page">
  <div class="navbar">
    <div class="logo">User's Profile</div>

    <a href="user.php?id=<?php echo $user_id; ?>">Dashboard</a>
    <a href="scoreboard.php?id=<?php echo $user_id; ?>">Scoreboard</a>
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
  
</body>
</html>