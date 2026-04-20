 <!-- <?php 
  include "connection.php";
  if (isset($_GET['id']) && is_numeric($_GET['id']) && (int)$_GET['id'] > 0) {
    $id = (int) $_GET['id'];

} 

$sql = "select * from admin where id = '$id' ";
$result = $data -> query($sql);
if(!$result){
    die("invalid query!");
}
if($row=$result->fetch_assoc())
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>admin</title>
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
      background-color: #1f1f1f;
      padding: 15px 30px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.5);
    }

    .navbar .logo {
      font-size: 24px;
      color: #00cc99;
      font-weight: bold;
    }

    .navbar a {
      color: white;
      text-decoration: none;
      margin-right: 20px;
      font-size: 16px;
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

 <?php 
  include "connection.php";
  if (isset($_GET['id']) && is_numeric($_GET['id']) && (int)$_GET['id'] > 0) {
    $id = (int) $_GET['id'];
} 

$sql = "select * from admin where id = '$id' ";
$result = $data -> query($sql);
if(!$result){
    die("invalid query!");
}
if($row=$result->fetch_assoc()) { ?>
  <div class="navbar">
    <div class="logo">Admin Panel</div>
    <div>
     <a href="profile.php?id=<?php echo $row['id'];?>">profile</a>
      <form action="logout.php" method="POST" style="display:inline;">
        <button type="submit" style="background:none; border:none; color:white; cursor:pointer;">Logout</button>
        <div class="dropdown">
      <button class = "dropbtn">EDIT QUESTIONS &#x25BC;</button>
      <div class = "dropdown-content">
        <a href="edit.php?subject=maths&id=<?php echo $row['id'];?>">Maths</a>
        <a href="edit.php?subject=english&id=<?php echo $row['id'];?>">English</a>
        <a href="edit.php?subject=physics&id=<?php echo $row['id'];?>">Physics</a>
       <a href="edit.php?subject=chemistry&id=<?php echo $row['id'];?>">Chemistry</a>
       <a href="edit.php?subject=reasoning&id=<?php echo $row['id'];?>">Reasoning</a>
</div>
      </form>
    </div>
  </div><br><br>
  <?php }
  ?>
</body>
</html> -->

<?php 
  include "connection.php";
  if (isset($_GET['id']) && is_numeric($_GET['id']) && (int)$_GET['id'] > 0) {
    $id = (int) $_GET['id'];
    $sql = "select * from admin where id = '$id' ";
    $result = $data -> query($sql);
    if(!$result){
        die("invalid query!");
    }
    if($row=$result->fetch_assoc()) { 
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Panel</title>
  <style>
    :root {
      --main-bg: #181F2A;
      --container-bg: #212C3A;
      --card-bg: #1A2233;
      --text: #fff;
      --green: #16E2AF;
      --blue: #3793E0;
      --purple: #8D6EDB;
      --orange: #FFB86C;
      --card-border-radius: 16px;
    }
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    body {
      background: radial-gradient(ellipse at top left, #222B3A 70%, #181F2A 100%);
      color: var(--text);
      min-height: 100vh;
      letter-spacing: 0.01em;
    }
    .navbar {
      background: var(--container-bg);
      padding: 20px 40px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      box-shadow: 0 2px 18px rgba(0,0,0,0.22);
      border-bottom: 2px solid #1a223352;
    }
    .logo {
      font-size: 28px;
      color: var(--green);
      font-weight: bold;
      letter-spacing: 1px;
    }
    .navbar-links a,
    .navbar-links button {
      color: var(--text);
      background: none;
      border: none;
      text-decoration: none;
      font-size: 18px;
      margin-right: 22px;
      cursor: pointer;
      transition: color .2s;
    }
    .navbar-links a:hover,
    .navbar-links button:hover {
      color: var(--green);
    }
    .dropdown {
      display: inline-block;
      position: relative;
    }
    .dropbtn {
      background: rgba(52,58,90,0.56);
      color: #fff;
      padding: 10px 18px;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      font-size: 16px;
      margin-right: 12px;
      box-shadow: 0 4px 16px #0002;
      transition: background .2s;
    }
    .dropbtn:hover {
      background: var(--green);
      color: #181f2a;
    }
    .dropdown-content {
      display: none;
      position: absolute;
      right: 0;
      top: 38px;
      background: #232B40;
      min-width: 180px;
      border-radius: 7px;
      box-shadow: 0px 8px 28px rgba(0,0,0,0.25);
      z-index: 2;
      overflow: hidden;
    }
    .dropdown-content a {
      display: block;
      padding: 12px 18px;
      color: #fff;
      text-decoration: none;
      font-size: 15px;
      border-bottom: 1px solid #2d3648;
      background: none;
      transition: background .17s;
    }
    .dropdown-content a:last-child {
      border-bottom: none;
    }
    .dropdown-content a:hover {
      background: var(--green);
      color: #181f2a;
    }
    .dropdown:hover .dropdown-content {
      display: block;
    }

    /* Dashboard Containers */
    .container {
      margin: 38px auto;
      max-width: 1200px;
      padding: 24px;
    }
    .dashboard-grid {
      display: flex;
      gap: 22px;
      margin-bottom: 32px;
      flex-wrap: wrap;
    }
    .card {
      flex: 1 1 220px;
      background: var(--card-bg);
      border-radius: var(--card-border-radius);
      padding: 30px 28px 24px 28px;
      box-shadow: 0 2px 18px #0003;
      margin-bottom: 12px;
      min-width: 260px;
      transition: transform .12s, box-shadow .12s;
      position: relative;
    }
    .card:hover {
      transform: translateY(-5px) scale(1.03);
      box-shadow: 0 10px 34px 2px #0004;
    }
    .card-icon {
      position: absolute;
      right: 18px;
      top: 22px;
      font-size: 32px;
      opacity: 0.82;
    }
    .icon-users { color: var(--blue);}
    .icon-quizzes { color: var(--green);}
    .icon-score { color: var(--purple);}
    .icon-comp { color: var(--orange);}
    .card-title {
      font-size: 18px;
      color: #aaa;
      margin-bottom: 6px;
    }
    .card-value {
      font-size: 32px;
      margin-bottom: 4px;
      font-weight: bold;
    }
    .card-trend {
      font-size: 14px;
      color: #5dfcb1;
      margin-top: 3px;
    }
    .card-trend.negative { color: #ff886a; }
    .card-trend.neutral { color: #ccc; }

    /* Main content area */
    .dashboard-main {
      display: flex;
      gap: 32px;
      flex-wrap: wrap;
    }
    .main-left {
      flex: 2;
      background: var(--container-bg);
      border-radius: var(--card-border-radius);
      padding: 30px 28px;
      box-shadow: 0 2px 18px #0003;
      min-width: 340px;
      margin-bottom: 18px;
    }
    .main-right {
      flex: 1;
      background: var(--container-bg);
      border-radius: var(--card-border-radius);
      padding: 30px 28px;
      box-shadow: 0 2px 18px #0003;
      min-width: 260px;
      margin-bottom: 18px;
    }
    .section-title {
      font-size: 22px;
      font-weight: bolder;
      color: #fff;
      margin-bottom: 18px;
    }
    .activity-list {
      margin-top: 8px;
    }
    .activity-item {
      background: var(--card-bg);
      border-radius: 11px;
      padding: 18px 18px 12px 18px;
      margin-bottom: 14px;
      box-shadow: 0 2px 12px #0002;
      display: flex;
      flex-direction: column;
      font-size: 17px;
    }
    .activity-header {
      font-weight: bold;
      margin-bottom: 7px;
      letter-spacing: 0.01em;
      display: flex;
      align-items: center;
    }
    .dot {
      height: 11px;
      width: 11px;
      background: #47f2b7;
      border-radius: 50%;
      margin-right: 9px;
      margin-top: 1px;
      display: inline-block;
    }
    .activity-details {
      color: #a3c1cf;
      font-size: 15px;
      margin-left: 20px;
    }
    .quick-actions {
      display: flex;
      flex-direction: column;
      gap: 16px;
    }
    .qa-btn {
      border-radius: 8px;
      padding: 15px 22px;
      font-size: 17px;
      font-weight: 600;
      box-shadow: none;
      border: none;
      cursor: pointer;
      text-align: left;
      margin-bottom: 0;
      color: var(--text);
      transition: transform 0.13s, background 0.13s;
    }
    .qa-quiz { background: #1f3c33; color: #34FFBE;}
    .qa-quiz:hover { background: #2fae91; color: #fff;}
    .qa-analytics {background: #1b273c; color: #72a5f8;}
    .qa-analytics:hover {background: #3793E0; color: #fff;}
    .qa-settings {background: #271940; color: #D1B3FF;}
    .qa-settings:hover {background: #8D6EDB; color: #fff;}
    @media (max-width:960px) {
      .dashboard-main {
        flex-direction: column;
      }
      .dashboard-grid {
        flex-direction: column;
      }
    }
    @media (max-width:700px) {
      .container {
        padding: 3vw 2vw;
      }
      .navbar {
        flex-direction: column;
        gap: 18px;
        padding: 10px 6vw;
      }
    }
  </style>
</head>
<body>
  <div class="navbar">
    <div class="logo">Admin Panel</div>
    <div class="navbar-links">
      <a href="profile.php?id=<?php echo $row['id'];?>">Profile</a>
      <div class="dropdown">
        <button class="dropbtn">EDIT QUESTIONS &#x25BC;</button>
        <div class="dropdown-content">
            <a href="edit.php?subject=maths&id=<?php echo $row['id'];?>">Maths</a>
            <a href="edit.php?subject=english&id=<?php echo $row['id'];?>">English</a>
            <a href="edit.php?subject=physics&id=<?php echo $row['id'];?>">Physics</a>
            <a href="edit.php?subject=chemistry&id=<?php echo $row['id'];?>">Chemistry</a>
            <a href="edit.php?subject=reasoning&id=<?php echo $row['id'];?>">Reasoning</a>
        </div>
      </div>
      <form action="logout.php" method="POST" style="display:inline;">
        <button type="submit">Logout</button>
      </form>
    </div>
  </div>
  <div class="container">
    <div class="dashboard-grid">
      <div class="card">
        <div class="card-title">Total Users</div>
        <div class="card-value">2,847</div>
        <div class="card-trend positive">+12% from last month</div>
        <span class="card-icon icon-users">&#128101;</span>
      </div>
      <div class="card">
        <div class="card-title">Active Quizzes</div>
        <div class="card-value">24</div>
        <div class="card-trend positive">+8% from last month</div>
        <span class="card-icon icon-quizzes">&#128218;</span>
      </div>
      <div class="card">
        <div class="card-title">Avg Score</div>
        <div class="card-value">78%</div>
        <div class="card-trend">+5% from last month</div>
        <span class="card-icon icon-score">&#128200;</span>
      </div>
      <div class="card">
        <div class="card-title">Completions</div>
        <div class="card-value">1,429</div>
        <div class="card-trend positive" style="color: #ffa94d;">+18% from last month</div>
        <span class="card-icon icon-comp">&#127941;</span>
      </div>
    </div>
    <div class="dashboard-main">
      <div class="main-left">
        <div class="section-title">Recent Activity</div>
        <div class="activity-list" id="activity-list">
          <div class="activity-item">
            <span class="activity-header"><span class="dot"></span>New user registered</span>
            <span class="activity-details">john.doe@example.com &middot; 2 minutes ago</span>
          </div>
          <div class="activity-item">
            <span class="activity-header"><span class="dot"></span>New user registered</span>
            <span class="activity-details">john.doe@example.com &middot; 2 minutes ago</span>
          </div>
          <div class="activity-item">
            <span class="activity-header"><span class="dot"></span>New user registered</span>
            <span class="activity-details">john.doe@example.com &middot; 2 minutes ago</span>
          </div>
          <div class="activity-item">
            <span class="activity-header"><span class="dot"></span>New user registered</span>
            <span class="activity-details">john.doe@example.com &middot; 2 minutes ago</span>
          </div>
        </div>
      </div>
      <div class="main-right">
        <div class="section-title">Quick Actions</div>
        <div class="quick-actions">
          <button class="qa-btn qa-quiz">&#128218; Create New Quiz</button>
          <button class="qa-btn qa-analytics">&#128202; View Analytics</button>
          <button class="qa-btn qa-settings">&#9881; System Settings</button>
        </div>
      </div>
    </div>
  </div>
  <!-- Script for extra interactivity -->
  <script>
    // Example - interactive animations or fetch data
    // Add more dynamic features as needed
    document.querySelectorAll('.qa-btn').forEach(btn => {
      btn.addEventListener('mousedown', () => {
        btn.style.transform = 'scale(0.97)';
      });
      btn.addEventListener('mouseup', () => {
        btn.style.transform = 'scale(1)';
      });
      btn.addEventListener('mouseleave', () => {
        btn.style.transform = 'scale(1)';
      });
    });
  </script>
</body>
</html>
<?php 
    } // end if row
  } // end if isset id
?>
