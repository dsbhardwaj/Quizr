<?php
session_start();
include "connection.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); exit();
}

if (isset($_GET['id']) && is_numeric($_GET['id']) && (int)$_GET['id'] > 0) {
    $id = (int)$_GET['id'];
} else {
    die("Invalid or missing ID.");
}

$user_id = $id;

$sql    = "SELECT * FROM users WHERE id = '$id'";
$result = $data->query($sql);

if (!$result)              { die("Query error: " . $data->error); }
if ($result->num_rows === 0) { die("No user found for ID = $id"); }

$row = $result->fetch_assoc();

// Avatar initials
$initials = '';
$parts = explode(' ', trim($row['name']));
foreach (array_slice($parts, 0, 2) as $p) {
    $initials .= strtoupper($p[0]);
}

// Quiz attempt stats for this user
$stmt = $data->prepare("
    SELECT s.name,
           COUNT(*)                              AS attempts,
           SUM(r.score)                          AS total_score,
           SUM(r.total_ques)                     AS total_ques,
           AVG((r.score * 100.0) / r.total_ques) AS avg_pct
    FROM result r
    JOIN subjects s ON r.subject_id = s.id
    WHERE r.user_id = ?
    GROUP BY s.id
    ORDER BY avg_pct DESC
");

$subject_stats = [];
if ($stmt) {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($sr = $res->fetch_assoc()) {
        $subject_stats[] = $sr;
    }
}

$total_attempts = array_sum(array_column($subject_stats, 'attempts'));
$overall_score  = array_sum(array_column($subject_stats, 'total_score'));
$overall_ques   = array_sum(array_column($subject_stats, 'total_ques'));
$overall_pct    = $overall_ques > 0 ? round(($overall_score / $overall_ques) * 100) : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Profile — <?php echo htmlspecialchars($row['name']); ?> — Quizr</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: #f5f6fa;
      color: #111110;
      min-height: 100vh;
    }

    /* ── NAVBAR ── */
    .navbar {
      display: flex; align-items: center; justify-content: space-between;
      padding: 0 24px; height: 56px;
      background: #ffffff;
      border-bottom: 0.5px solid rgba(0,0,0,0.08);
      position: sticky; top: 0; z-index: 100;
    }

    .nav-logo {
      font-size: 15px; font-weight: 600;
      display: flex; align-items: center; gap: 8px;
      text-decoration: none; color: #111110;
    }

    .nav-logo-dot { width: 8px; height: 8px; border-radius: 50%; background: #1D9E75; }

    .nav-links { display: flex; align-items: center; gap: 4px; }

    .nav-link {
      display: flex; align-items: center; gap: 6px;
      padding: 6px 12px; border-radius: 8px; font-size: 13px;
      color: #6b6b6a; text-decoration: none; transition: all 0.12s;
    }

    .nav-link:hover { background: #f0f0ef; color: #111110; }

    .dropdown { position: relative; display: inline-block; }

    .dropbtn {
      display: flex; align-items: center; gap: 6px;
      padding: 6px 12px; border-radius: 8px;
      font-size: 13px; font-weight: 500; color: #111110;
      cursor: pointer; border: 0.5px solid rgba(0,0,0,0.14);
      background: #f0f0ef; font-family: inherit; transition: all 0.12s;
    }

    .dropbtn:hover { background: #e5e5e3; }

    .dropdown-menu {
      display: none; position: absolute; right: 0; top: calc(100% + 6px);
      background: #ffffff; border: 0.5px solid rgba(0,0,0,0.14);
      border-radius: 12px; overflow: hidden; z-index: 200; min-width: 190px;
      box-shadow: 0 4px 16px rgba(0,0,0,0.08);
    }

    .dropdown:hover .dropdown-menu { display: block; }

    .dropdown-item {
      display: flex; align-items: center; gap: 10px;
      padding: 10px 14px; font-size: 13px; color: #6b6b6a;
      text-decoration: none; border-bottom: 0.5px solid rgba(0,0,0,0.06);
      transition: all 0.1s;
    }

    .dropdown-item:last-child { border-bottom: none; }
    .dropdown-item:hover { background: #f0f0ef; color: #111110; }

    /* ── BUTTONS ── */
    .btn {
      display: inline-flex; align-items: center; gap: 6px;
      padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 500;
      cursor: pointer; font-family: inherit; transition: all 0.12s;
      text-decoration: none; border: 0.5px solid rgba(0,0,0,0.14);
    }

    .btn-ghost { background: transparent; color: #111110; }
    .btn-ghost:hover { background: #f0f0ef; }

    .btn-danger { background: #FCEBEB; color: #791F1F; border-color: #F7C1C1; }
    .btn-danger:hover { background: #F7C1C1; }

    /* ── PAGE ── */
    .page {
      max-width: 720px; margin: 0 auto;
      padding: 32px 24px 64px;
      display: flex; flex-direction: column; gap: 24px;
    }

    /* ── PROFILE CARD ── */
    .profile-card {
      background: #ffffff;
      border: 0.5px solid rgba(0,0,0,0.08);
      border-radius: 16px;
      padding: 28px;
      display: flex; align-items: center; gap: 20px;
      flex-wrap: wrap;
    }

    .avatar {
      width: 72px; height: 72px; border-radius: 50%;
      background: #E1F5EE;
      display: flex; align-items: center; justify-content: center;
      font-size: 24px; font-weight: 600; color: #0F6E56;
      flex-shrink: 0;
    }

    .profile-info { flex: 1; min-width: 0; }

    .profile-name {
      font-size: 22px; font-weight: 600; letter-spacing: -0.02em;
      white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    }

    .profile-meta {
      display: flex; flex-wrap: wrap; gap: 16px; margin-top: 8px;
    }

    .meta-item {
      display: flex; align-items: center; gap: 6px;
      font-size: 13px; color: #6b6b6a;
    }

    .profile-id-badge {
      font-size: 11px; font-weight: 600; padding: 4px 10px;
      border-radius: 100px; background: #f0f0ef; color: #6b6b6a;
      border: 0.5px solid rgba(0,0,0,0.10);
    }

    /* ── SECTION LABEL ── */
    .section-label {
      font-size: 11px; font-weight: 600; color: #6b6b6a;
      text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 12px;
    }

    /* ── OVERVIEW STATS ── */
    .stats-grid {
      display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px;
    }

    .stat-card {
      background: #ffffff; border: 0.5px solid rgba(0,0,0,0.08);
      border-radius: 12px; padding: 16px 18px;
    }

    .stat-label {
      font-size: 12px; color: #6b6b6a; margin-bottom: 6px;
      display: flex; align-items: center; gap: 6px;
    }

    .stat-value { font-size: 26px; font-weight: 300; letter-spacing: -0.03em; }
    .stat-sub   { font-size: 12px; color: #6b6b6a; margin-top: 4px; }

    /* ── SUBJECT TABLE ── */
    .subject-table-wrap {
      background: #ffffff; border: 0.5px solid rgba(0,0,0,0.08);
      border-radius: 14px; overflow: hidden;
    }

    .table-header {
      padding: 14px 18px 12px;
      border-bottom: 0.5px solid rgba(0,0,0,0.06);
      font-size: 14px; font-weight: 500;
    }

    table {
      width: 100%; border-collapse: collapse;
      table-layout: fixed;
    }

    th {
      text-align: left; font-size: 11px; font-weight: 600;
      color: #6b6b6a; text-transform: uppercase; letter-spacing: 0.07em;
      padding: 10px 18px; border-bottom: 0.5px solid rgba(0,0,0,0.06);
    }

    td {
      padding: 13px 18px; font-size: 13px;
      border-bottom: 0.5px solid rgba(0,0,0,0.05);
      vertical-align: middle;
    }

    tr:last-child td { border-bottom: none; }
    tbody tr:hover td { background: #fafaf9; }

    .subject-name { font-weight: 500; font-size: 14px; }

    .bar-wrap { height: 4px; background: #f0f0ef; border-radius: 2px; overflow: hidden; margin-top: 4px; }
    .bar-fill { height: 100%; border-radius: 2px; background: #1D9E75; }
    .bar-fill.low { background: #E24B4A; }

    .pct-badge {
      font-size: 12px; font-weight: 600; padding: 3px 10px;
      border-radius: 100px; white-space: nowrap;
    }

    .badge-green { background: #EAF3DE; color: #27500A; }
    .badge-blue  { background: #E6F1FB; color: #0C447C; }
    .badge-amber { background: #FAEEDA; color: #633806; }
    .badge-red   { background: #FCEBEB; color: #791F1F; }

    /* ── EMPTY STATE ── */
    .empty-state {
      padding: 32px; text-align: center;
      font-size: 14px; color: #6b6b6a;
    }

    @media (max-width: 520px) {
      .stats-grid { grid-template-columns: 1fr 1fr; }
      .profile-card { flex-direction: column; align-items: flex-start; }
    }
  </style>
</head>
<body>

<!-- ── NAVBAR ── -->
<nav class="navbar">
  <a class="nav-logo" href="user.php">
    <div class="nav-logo-dot"></div>
    Quizr
  </a>

  <div class="nav-links">
    <a class="nav-link" href="user.php?id=<?php echo $user_id; ?>">
      <i class="ti ti-home" style="font-size:14px;"></i> Dashboard
    </a>
    <a class="nav-link" href="scoreboard.php?id=<?php echo $user_id; ?>">
      <i class="ti ti-trophy" style="font-size:14px;"></i> Scoreboard
    </a>

    <div class="dropdown">
      <button class="dropbtn">
        <i class="ti ti-book" style="font-size:14px;"></i>
        Quizzes
        <i class="ti ti-chevron-down" style="font-size:12px;"></i>
      </button>
      <div class="dropdown-menu">
        <a class="dropdown-item" href="quiz.php?subject=Aptitude&id=<?php echo $user_id; ?>">
          <i class="ti ti-math-function" style="font-size:14px;"></i> Aptitude
        </a>
        <a class="dropdown-item" href="quiz.php?subject=Logical_Reasoning&id=<?php echo $user_id; ?>">
          <i class="ti ti-brain" style="font-size:14px;"></i> Logical Reasoning
        </a>
        <a class="dropdown-item" href="quiz.php?subject=Data_Structure&id=<?php echo $user_id; ?>">
          <i class="ti ti-binary-tree" style="font-size:14px;"></i> Data Structure
        </a>
        <a class="dropdown-item" href="quiz.php?subject=Web_Development&id=<?php echo $user_id; ?>">
          <i class="ti ti-code" style="font-size:14px;"></i> Web Development
        </a>
        <a class="dropdown-item" href="quiz.php?subject=DBMS&id=<?php echo $user_id; ?>">
          <i class="ti ti-database" style="font-size:14px;"></i> DBMS
        </a>
      </div>
    </div>

    <a class="btn btn-danger" href="logout.php">
      <i class="ti ti-logout" style="font-size:14px;"></i> Logout
    </a>
  </div>
</nav>

<!-- ── PAGE ── -->
<div class="page">

  <!-- Profile card -->
  <div class="profile-card">
    <div class="avatar"><?php echo $initials ?: '?'; ?></div>
    <div class="profile-info">
      <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap; margin-bottom:4px;">
        <div class="profile-name"><?php echo htmlspecialchars($row['name']); ?></div>
        <span class="profile-id-badge">ID #<?php echo $row['id']; ?></span>
      </div>
      <div class="profile-meta">
        <div class="meta-item">
          <i class="ti ti-mail" style="font-size:14px;"></i>
          <?php echo htmlspecialchars($row['email']); ?>
        </div>
        <?php if (!empty($row['role'])): ?>
        <div class="meta-item">
          <i class="ti ti-shield" style="font-size:14px;"></i>
          <?php echo ucfirst(htmlspecialchars($row['role'])); ?>
        </div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- Overview stats -->
  <div>
    <div class="section-label">Overview</div>
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-label">
          <i class="ti ti-list-check" style="font-size:14px;"></i> Total attempts
        </div>
        <div class="stat-value"><?php echo $total_attempts; ?></div>
        <div class="stat-sub">across all subjects</div>
      </div>
      <div class="stat-card">
        <div class="stat-label">
          <i class="ti ti-target" style="font-size:14px;"></i> Overall accuracy
        </div>
        <div class="stat-value">
          <?php echo $overall_pct; ?><span style="font-size:14px;">%</span>
        </div>
        <div class="stat-sub">of all answers correct</div>
      </div>
      <div class="stat-card">
        <div class="stat-label">
          <i class="ti ti-book" style="font-size:14px;"></i> Subjects tried
        </div>
        <div class="stat-value"><?php echo count($subject_stats); ?></div>
        <div class="stat-sub">out of 5 available</div>
      </div>
    </div>
  </div>

  <!-- Subject breakdown -->
  <div>
    <div class="section-label">Subject breakdown</div>
    <div class="subject-table-wrap">
      <div class="table-header">Performance by subject</div>

      <?php if (!empty($subject_stats)): ?>
      <table>
        <thead>
          <tr>
            <th style="width:35%;">Subject</th>
            <th style="width:15%;">Attempts</th>
            <th style="width:20%;">Avg score</th>
            <th style="width:30%;">Progress</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($subject_stats as $s):
            $pct      = round($s['avg_pct']);
            $isLow    = $pct < 50;
            $barClass = $isLow ? 'bar-fill low' : 'bar-fill';

            if      ($pct >= 80) $badgeCls = 'pct-badge badge-green';
            elseif  ($pct >= 60) $badgeCls = 'pct-badge badge-blue';
            elseif  ($pct >= 40) $badgeCls = 'pct-badge badge-amber';
            else                 $badgeCls = 'pct-badge badge-red';
          ?>
          <tr>
            <td>
              <div class="subject-name"><?php echo htmlspecialchars($s['name']); ?></div>
            </td>
            <td style="color:#6b6b6a;"><?php echo $s['attempts']; ?></td>
            <td>
              <span class="<?php echo $badgeCls; ?>"><?php echo $pct; ?>%</span>
            </td>
            <td>
              <div class="bar-wrap" style="width:100%;">
                <div class="<?php echo $barClass; ?>" style="width:<?php echo $pct; ?>%;"></div>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>

      <?php else: ?>
        <div class="empty-state">
          <i class="ti ti-notes-off" style="font-size:24px; display:block; margin-bottom:8px;"></i>
          No quiz attempts yet — pick a subject from the navbar to get started!
        </div>
      <?php endif; ?>
    </div>
  </div>

</div><!-- /page -->

</body>
</html>