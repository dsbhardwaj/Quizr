<?php
session_start();
include "connection.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); exit();
}

$user_id = $_SESSION['user_id'];

// All attempts ordered newest first
$query = "
    SELECT r.*, s.name AS subject_name
    FROM result r
    JOIN subjects s ON r.subject_id = s.id
    WHERE r.user_id = $user_id
    ORDER BY r.id DESC
";
$result = mysqli_query($data, $query);

$rows = [];
while ($row = mysqli_fetch_assoc($result)) {
    $rows[] = $row;
}

// Summary stats
$total_attempts  = count($rows);
$total_score     = array_sum(array_column($rows, 'score'));
$total_questions = array_sum(array_column($rows, 'total_ques'));
$overall_pct     = $total_questions > 0 ? round(($total_score / $total_questions) * 100) : 0;
$best_pct        = 0;
foreach ($rows as $r) {
    $p = $r['total_ques'] > 0 ? round(($r['score'] / $r['total_ques']) * 100) : 0;
    if ($p > $best_pct) $best_pct = $p;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Scoreboard — Quizr</title>
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
    .nav-link.active { background: #f0f0ef; color: #111110; font-weight: 500; }

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
      max-width: 820px; margin: 0 auto;
      padding: 32px 24px 64px;
      display: flex; flex-direction: column; gap: 24px;
    }

    /* ── PAGE HEADER ── */
    .page-header {
      display: flex; align-items: center; justify-content: space-between;
      flex-wrap: wrap; gap: 12px;
    }

    .page-title { font-size: 22px; font-weight: 600; letter-spacing: -0.02em; }
    .page-sub   { font-size: 13px; color: #6b6b6a; margin-top: 3px; }

    /* ── SECTION LABEL ── */
    .section-label {
      font-size: 11px; font-weight: 600; color: #6b6b6a;
      text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 12px;
    }

    /* ── STATS GRID ── */
    .stats-grid { display: grid; grid-template-columns: repeat(3,1fr); gap: 12px; }

    .stat-card {
      background: #ffffff; border: 0.5px solid rgba(0,0,0,0.08);
      border-radius: 12px; padding: 16px 18px;
    }

    .stat-label {
      font-size: 12px; color: #6b6b6a; margin-bottom: 6px;
      display: flex; align-items: center; gap: 6px;
    }

    .stat-value { font-size: 28px; font-weight: 300; letter-spacing: -0.03em; }
    .stat-sub   { font-size: 12px; color: #6b6b6a; margin-top: 4px; }

    /* ── FILTER TABS ── */
    .filter-row {
      display: flex; align-items: center; gap: 6px;
      flex-wrap: wrap;
    }

    .filter-tab {
      padding: 5px 14px; border-radius: 100px; font-size: 12px; font-weight: 500;
      cursor: pointer; border: 0.5px solid rgba(0,0,0,0.12);
      background: transparent; color: #6b6b6a;
      font-family: inherit; transition: all 0.12s;
    }

    .filter-tab:hover { background: #f0f0ef; color: #111110; }
    .filter-tab.active { background: #111110; color: #fff; border-color: #111110; }

    /* ── TABLE WRAP ── */
    .table-wrap {
      background: #ffffff; border: 0.5px solid rgba(0,0,0,0.08);
      border-radius: 14px; overflow: hidden;
    }

    .table-header {
      display: flex; align-items: center; justify-content: space-between;
      padding: 14px 20px 12px;
      border-bottom: 0.5px solid rgba(0,0,0,0.06);
    }

    .table-title { font-size: 14px; font-weight: 500; }
    .table-count { font-size: 12px; color: #6b6b6a; }

    table { width: 100%; border-collapse: collapse; }

    th {
      text-align: left; font-size: 11px; font-weight: 600;
      color: #6b6b6a; text-transform: uppercase; letter-spacing: 0.07em;
      padding: 10px 20px;
      border-bottom: 0.5px solid rgba(0,0,0,0.06);
    }

    td {
      padding: 13px 20px; font-size: 13px;
      border-bottom: 0.5px solid rgba(0,0,0,0.05);
      vertical-align: middle;
    }

    tr:last-child td { border-bottom: none; }
    tbody tr { transition: background 0.1s; }
    tbody tr:hover td { background: #fafaf9; }

    /* ── SUBJECT CHIP ── */
    .subject-chip {
      display: inline-flex; align-items: center; gap: 6px;
      padding: 4px 10px; border-radius: 6px;
      font-size: 12px; font-weight: 500;
      background: #f0f0ef; color: #6b6b6a;
    }

    /* ── SCORE DISPLAY ── */
    .score-val { font-size: 14px; font-weight: 500; }
    .score-total { color: #9b9b9a; font-weight: 400; }

    /* ── PCT BADGE ── */
    .pct-badge {
      display: inline-flex; align-items: center; gap: 4px;
      font-size: 12px; font-weight: 600; padding: 3px 10px;
      border-radius: 100px;
    }

    .badge-green { background: #EAF3DE; color: #27500A; }
    .badge-blue  { background: #E6F1FB; color: #0C447C; }
    .badge-amber { background: #FAEEDA; color: #633806; }
    .badge-red   { background: #FCEBEB; color: #791F1F; }

    /* ── MINI BAR ── */
    .mini-bar-wrap { width: 80px; height: 4px; background: #f0f0ef; border-radius: 2px; overflow: hidden; }
    .mini-bar-fill { height: 100%; border-radius: 2px; }

    /* ── RANK BADGE ── */
    .rank { font-size: 13px; font-weight: 600; color: #9b9b9a; font-variant-numeric: tabular-nums; }
    .rank.gold   { color: #B8860B; }
    .rank.silver { color: #8A8A8A; }
    .rank.bronze { color: #8B5E3C; }

    /* ── EMPTY STATE ── */
    .empty-state {
      padding: 48px; text-align: center;
      color: #6b6b6a; font-size: 14px;
    }

    .empty-state i { font-size: 28px; display: block; margin-bottom: 10px; color: #b0b0ae; }

    @media (max-width: 560px) {
      .stats-grid { grid-template-columns: 1fr 1fr; }
      th.hide-sm, td.hide-sm { display: none; }
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
    <a class="nav-link" href="user.php">
      <i class="ti ti-home" style="font-size:14px;"></i> Dashboard
    </a>
    <a class="nav-link" href="profile.php?id=<?php echo $user_id; ?>">
      <i class="ti ti-user" style="font-size:14px;"></i> Profile
    </a>
    <div class="dropdown">
      <button class="dropbtn">
        <i class="ti ti-book" style="font-size:14px;"></i> Quizzes
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

  <!-- Header -->
  <div class="page-header">
    <div>
      <div class="page-title">My Scoreboard</div>
      <div class="page-sub">A history of all your quiz attempts</div>
    </div>
    <div class="dropdown">
      <button class="dropbtn">
        <i class="ti ti-plus" style="font-size:14px;"></i> Take a quiz
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
  </div>

  <!-- Stats -->
  <div>
    <div class="section-label">Summary</div>
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-label">
          <i class="ti ti-list-check" style="font-size:14px;"></i> Total attempts
        </div>
        <div class="stat-value"><?php echo $total_attempts; ?></div>
        <div class="stat-sub">quizzes taken</div>
      </div>
      <div class="stat-card">
        <div class="stat-label">
          <i class="ti ti-target" style="font-size:14px;"></i> Overall accuracy
        </div>
        <div class="stat-value"><?php echo $overall_pct; ?><span style="font-size:15px;">%</span></div>
        <div class="stat-sub">across all attempts</div>
      </div>
      <div class="stat-card">
        <div class="stat-label">
          <i class="ti ti-trophy" style="font-size:14px;"></i> Best score
        </div>
        <div class="stat-value"><?php echo $best_pct; ?><span style="font-size:15px;">%</span></div>
        <div class="stat-sub">personal best</div>
      </div>
    </div>
  </div>

  <!-- Filter tabs -->
  <div class="filter-row" id="filterRow">
    <button class="filter-tab active" onclick="filterSubject('all', this)">All</button>
    <button class="filter-tab" onclick="filterSubject('Aptitude', this)">Aptitude</button>
    <button class="filter-tab" onclick="filterSubject('Logical_Reasoning', this)">Logical Reasoning</button>
    <button class="filter-tab" onclick="filterSubject('Data_Structure', this)">Data Structure</button>
    <button class="filter-tab" onclick="filterSubject('Web_Development', this)">Web Development</button>
    <button class="filter-tab" onclick="filterSubject('DBMS', this)">DBMS</button>
  </div>

  <!-- Table -->
  <div class="table-wrap">
    <div class="table-header">
      <div class="table-title">Quiz history</div>
      <div class="table-count" id="rowCount">
        <?php echo $total_attempts; ?> attempt<?php echo $total_attempts != 1 ? 's' : ''; ?>
      </div>
    </div>

    <?php if (!empty($rows)): ?>
    <table id="scoreTable">
      <thead>
        <tr>
          <th style="width:48px;">#</th>
          <th>Subject</th>
          <th>Score</th>
          <th>Percentage</th>
          <th class="hide-sm">Progress</th>
          <th class="hide-sm">Grade</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($rows as $i => $row):
          $pct = $row['total_ques'] > 0
            ? round(($row['score'] / $row['total_ques']) * 100) : 0;

          if      ($pct >= 80) { $badgeCls = 'pct-badge badge-green'; $grade = 'Excellent'; $barColor = '#1D9E75'; }
          elseif  ($pct >= 60) { $badgeCls = 'pct-badge badge-blue';  $grade = 'Good';      $barColor = '#3B82F6'; }
          elseif  ($pct >= 40) { $badgeCls = 'pct-badge badge-amber'; $grade = 'Average';   $barColor = '#EF9F27'; }
          else                 { $badgeCls = 'pct-badge badge-red';   $grade = 'Weak';      $barColor = '#E24B4A'; }

          $rankNum = $i + 1;
          $rankCls = $rankNum === 1 ? 'rank gold' : ($rankNum === 2 ? 'rank silver' : ($rankNum === 3 ? 'rank bronze' : 'rank'));

          // slug for JS filter (spaces → underscores)
          $slug = str_replace(' ', '_', $row['subject_name']);
        ?>
        <tr data-subject="<?php echo htmlspecialchars($slug); ?>">
          <td><span class="<?php echo $rankCls; ?>"><?php echo $rankNum; ?></span></td>
          <td>
            <div class="subject-chip">
              <i class="ti ti-book" style="font-size:12px;"></i>
              <?php echo htmlspecialchars($row['subject_name']); ?>
            </div>
          </td>
          <td>
            <span class="score-val">
              <?php echo $row['score']; ?>
              <span class="score-total">/ <?php echo $row['total_ques']; ?></span>
            </span>
          </td>
          <td>
            <span class="<?php echo $badgeCls; ?>">
              <?php echo $pct; ?>%
            </span>
          </td>
          <td class="hide-sm">
            <div class="mini-bar-wrap">
              <div class="mini-bar-fill"
                style="width:<?php echo $pct; ?>%; background:<?php echo $barColor; ?>;"></div>
            </div>
          </td>
          <td class="hide-sm" style="font-size:12px; color:#6b6b6a;"><?php echo $grade; ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

    <?php else: ?>
      <div class="empty-state">
        <i class="ti ti-notes-off"></i>
        No quiz attempts yet — pick a subject above to get started!
      </div>
    <?php endif; ?>
  </div>

</div><!-- /page -->

<script>
  function filterSubject(subject, btn) {
    // Update active tab
    document.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
    btn.classList.add('active');

    const rows = document.querySelectorAll('#scoreTable tbody tr');
    let visible = 0;

    rows.forEach(row => {
      const match = subject === 'all' || row.dataset.subject === subject;
      row.style.display = match ? '' : 'none';
      if (match) visible++;
    });

    document.getElementById('rowCount').textContent =
      visible + ' attempt' + (visible !== 1 ? 's' : '');
  }
</script>

</body>
</html>