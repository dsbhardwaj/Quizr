<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$id = $_SESSION['user_id'];
include "connection.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>User Dashboard</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }

    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: #f5f6fa;
      color: #111110;
      min-height: 100vh;
    }

    /* ── NAVBAR ── */
    .navbar {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 0 24px;
      height: 56px;
      background: #ffffff;
      border-bottom: 0.5px solid rgba(0,0,0,0.08);
      position: sticky;
      top: 0;
      z-index: 100;
    }

    .nav-logo {
      font-size: 15px;
      font-weight: 600;
      display: flex;
      align-items: center;
      gap: 8px;
      text-decoration: none;
      color: #111110;
    }

    .nav-logo-dot {
      width: 8px;
      height: 8px;
      border-radius: 50%;
      background: #1D9E75;
    }

    .nav-links {
      display: flex;
      align-items: center;
      gap: 4px;
    }

    .nav-link {
      display: flex;
      align-items: center;
      gap: 6px;
      padding: 6px 12px;
      border-radius: 8px;
      font-size: 13px;
      color: #6b6b6a;
      text-decoration: none;
      transition: all 0.12s;
    }

    .nav-link:hover {
      background: #f0f0ef;
      color: #111110;
    }

    /* ── DROPDOWN ── */
    .dropdown {
      position: relative;
      display: inline-block;
    }

    .dropbtn {
      display: flex;
      align-items: center;
      gap: 6px;
      padding: 6px 12px;
      border-radius: 8px;
      font-size: 13px;
      font-weight: 500;
      color: #111110;
      cursor: pointer;
      border: 0.5px solid rgba(0,0,0,0.14);
      background: #f0f0ef;
      font-family: inherit;
      transition: all 0.12s;
    }

    .dropbtn:hover {
      background: #e5e5e3;
    }

    .dropdown-menu {
      display: none;
      position: absolute;
      right: 0;
      top: calc(100% + 6px);
      background: #ffffff;
      border: 0.5px solid rgba(0,0,0,0.14);
      border-radius: 12px;
      overflow: hidden;
      z-index: 200;
      min-width: 190px;
      box-shadow: 0 4px 16px rgba(0,0,0,0.08);
    }

    .dropdown:hover .dropdown-menu {
      display: block;
    }

    .dropdown-item {
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 10px 14px;
      font-size: 13px;
      color: #6b6b6a;
      text-decoration: none;
      border-bottom: 0.5px solid rgba(0,0,0,0.06);
      transition: all 0.1s;
    }

    .dropdown-item:last-child {
      border-bottom: none;
    }

    .dropdown-item:hover {
      background: #f0f0ef;
      color: #111110;
    }

    /* ── PAGE ── */
    .page {
      max-width: 820px;
      margin: 0 auto;
      padding: 32px 24px 56px;
      display: flex;
      flex-direction: column;
      gap: 28px;
    }

    /* ── WELCOME ── */
    .welcome-row {
      display: flex;
      align-items: center;
      justify-content: space-between;
      flex-wrap: wrap;
      gap: 12px;
    }

    .avatar-block {
      display: flex;
      align-items: center;
      gap: 14px;
    }

    .avatar {
      width: 48px;
      height: 48px;
      border-radius: 50%;
      background: #E1F5EE;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 16px;
      font-weight: 600;
      color: #0F6E56;
      flex-shrink: 0;
    }

    .welcome-name {
      font-size: 20px;
      font-weight: 600;
      letter-spacing: -0.02em;
    }

    .welcome-sub {
      font-size: 13px;
      color: #6b6b6a;
      margin-top: 2px;
    }

    /* ── BUTTONS ── */
    .btn {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: 8px 16px;
      border-radius: 8px;
      font-size: 13px;
      font-weight: 500;
      cursor: pointer;
      font-family: inherit;
      text-decoration: none;
      transition: all 0.12s;
    }

    .btn-ghost {
      border: 0.5px solid rgba(0,0,0,0.14);
      background: transparent;
      color: #111110;
    }

    .btn-ghost:hover {
      background: #f0f0ef;
    }

    .btn-danger {
      border: 0.5px solid #F7C1C1;
      background: #FCEBEB;
      color: #791F1F;
    }

    .btn-danger:hover {
      background: #F7C1C1;
    }

    /* ── SECTION LABEL ── */
    .section-label {
      font-size: 11px;
      font-weight: 600;
      color: #6b6b6a;
      text-transform: uppercase;
      letter-spacing: 0.08em;
      margin-bottom: 12px;
    }

    /* ── STATS GRID ── */
    .stats-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 12px;
    }

    @media (max-width: 560px) {
      .stats-grid { grid-template-columns: 1fr; }
      .perf-grid  { grid-template-columns: 1fr; }
    }

    .stat-card {
      background: #ffffff;
      border: 0.5px solid rgba(0,0,0,0.08);
      border-radius: 12px;
      padding: 18px 20px;
    }

    .stat-label {
      font-size: 12px;
      color: #6b6b6a;
      margin-bottom: 8px;
      display: flex;
      align-items: center;
      gap: 6px;
    }

    .stat-value {
      font-size: 30px;
      font-weight: 300;
      letter-spacing: -0.03em;
      line-height: 1;
    }

    .stat-sub {
      font-size: 12px;
      color: #6b6b6a;
      margin-top: 6px;
    }

    /* ── PERFORMANCE ── */
    .perf-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 12px;
    }

    .perf-card {
      border-radius: 12px;
      padding: 20px;
      border: 0.5px solid transparent;
    }

    .perf-card.strong {
      background: #EAF3DE;
      border-color: #C0DD97;
    }

    .perf-card.weak {
      background: #FCEBEB;
      border-color: #F7C1C1;
    }

    .perf-tag {
      font-size: 11px;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.07em;
      margin-bottom: 10px;
      display: flex;
      align-items: center;
      gap: 5px;
    }

    .perf-tag.strong { color: #3B6D11; }
    .perf-tag.weak   { color: #A32D2D; }

    .perf-subject {
      font-size: 18px;
      font-weight: 600;
      letter-spacing: -0.01em;
    }

    .perf-subject.strong { color: #27500A; }
    .perf-subject.weak   { color: #791F1F; }

    .perf-score {
      font-size: 34px;
      font-weight: 300;
      letter-spacing: -0.03em;
      margin-top: 4px;
    }

    .perf-score.strong { color: #3B6D11; }
    .perf-score.weak   { color: #A32D2D; }

    /* ── ATTEMPTS ── */
    .attempts-list {
      display: flex;
      flex-direction: column;
      gap: 8px;
    }

    .attempt-row {
      background: #ffffff;
      border: 0.5px solid rgba(0,0,0,0.08);
      border-radius: 12px;
      padding: 14px 18px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 16px;
      transition: border-color 0.12s;
    }

    .attempt-row:hover {
      border-color: rgba(0,0,0,0.16);
    }

    .attempt-info { flex: 1; min-width: 0; }

    .attempt-name {
      font-size: 14px;
      font-weight: 500;
    }

    .attempt-meta {
      font-size: 12px;
      color: #6b6b6a;
      margin-top: 2px;
      margin-bottom: 8px;
    }

    .bar-wrap {
      height: 4px;
      background: #f0f0ef;
      border-radius: 2px;
      width: 100%;
      overflow: hidden;
    }

    .bar-fill {
      height: 100%;
      border-radius: 2px;
      background: #1D9E75;
      transition: width 0.6s ease;
    }

    .bar-fill.low { background: #E24B4A; }

    .attempt-badge {
      font-size: 12px;
      font-weight: 600;
      padding: 4px 10px;
      border-radius: 100px;
      white-space: nowrap;
      flex-shrink: 0;
    }

    .badge-blue  { background: #E6F1FB; color: #0C447C; }
    .badge-red   { background: #FCEBEB; color: #791F1F; }

    /* ── EMPTY STATE ── */
    .empty-state {
      background: #ffffff;
      border: 0.5px solid rgba(0,0,0,0.08);
      border-radius: 12px;
      padding: 32px;
      text-align: center;
      color: #6b6b6a;
      font-size: 14px;
    }
  </style>
</head>
<body>

<?php


// ── ANALYTICS ──
$user_id = $_SESSION['user_id'];

$query = "SELECT * FROM result WHERE user_id = $user_id";
$result = mysqli_query($data, $query);

$total_quizzes  = 0;
$total_score    = 0;
$total_questions = 0;

while ($row_r = mysqli_fetch_assoc($result)) {
    $total_quizzes++;
    $total_score     += $row_r['score'];
    $total_questions += $row_r['total_ques'];
}

$accuracy  = ($total_questions > 0) ? ($total_score / $total_questions) * 100 : 0;
$avg_score = ($total_quizzes   > 0) ? ($total_score / $total_quizzes)         : 0;

// ── STRONG SUBJECT ──
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

// ── WEAK SUBJECT ──
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

// ── ATTEMPTS PER SUBJECT ──
$attempt_data = [];
$stmt = $data->prepare("
    SELECT s.name,
           COUNT(*) AS attempts,
           AVG((r.score * 100.0) / r.total_ques) AS avg_pct
    FROM result r
    JOIN subjects s ON r.subject_id = s.id
    WHERE r.user_id = ?
    GROUP BY s.id
    ORDER BY s.name
");
if ($stmt) {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($row_a = $res->fetch_assoc()) {
        $attempt_data[] = $row_a;
    }
}

// ── USER ROW ──
$sql    = "SELECT * FROM users WHERE id = '$id'";
$result = $data->query($sql);
if (!$result) { die("Invalid query!"); }
$row = $result->fetch_assoc();

// ── AVATAR INITIALS ──
$initials = '';
if ($row) {
    $parts = explode(' ', trim($row['name']));
    foreach (array_slice($parts, 0, 2) as $p) {
        $initials .= strtoupper($p[0]);
    }
}
?>

<!-- ══════════════════════════════════════════ NAVBAR ══ -->
<nav class="navbar">
  <a class="nav-logo" href="user.php">
    <div class="nav-logo-dot"></div>
    Quizr
  </a>

  <div class="nav-links">
    <a class="nav-link" href="profile.php?id=<?php echo $row['id']; ?>">
      <i class="ti ti-user" style="font-size:14px;"></i> Profile
    </a>
    <a class="nav-link" href="scoreboard.php?id=<?php echo $id; ?>">
      <i class="ti ti-trophy" style="font-size:14px;"></i> Scoreboard
    </a>

    <div class="dropdown">
      <button class="dropbtn">
        <i class="ti ti-book" style="font-size:14px;"></i>
        Quizzes
        <i class="ti ti-chevron-down" style="font-size:12px;"></i>
      </button>
      <div class="dropdown-menu">
        <a class="dropdown-item" href="quiz.php?subject=Aptitude&id=<?php echo $row['id']; ?>">
          <i class="ti ti-math-function" style="font-size:14px;"></i> Aptitude
        </a>
        <a class="dropdown-item" href="quiz.php?subject=Logical_Reasoning&id=<?php echo $row['id']; ?>">
          <i class="ti ti-brain" style="font-size:14px;"></i> Logical Reasoning
        </a>
        <a class="dropdown-item" href="quiz.php?subject=Data_Structure&id=<?php echo $row['id']; ?>">
          <i class="ti ti-binary-tree" style="font-size:14px;"></i> Data Structure
        </a>
        <a class="dropdown-item" href="quiz.php?subject=Web_Development&id=<?php echo $row['id']; ?>">
          <i class="ti ti-code" style="font-size:14px;"></i> Web Development
        </a>
        <a class="dropdown-item" href="quiz.php?subject=DBMS&id=<?php echo $row['id']; ?>">
          <i class="ti ti-database" style="font-size:14px;"></i> DBMS
        </a>
      </div>
    </div>
  </div>
</nav>

<!-- ══════════════════════════════════════════ PAGE ══ -->
<div class="page">

  <!-- Welcome -->
  <div class="welcome-row">
    <div class="avatar-block">
      <div class="avatar"><?php echo $initials ?: '?'; ?></div>
      <div>
        <div class="welcome-name">Welcome back, <?php echo htmlspecialchars($row['name']); ?></div>
        <div class="welcome-sub">Here's your performance at a glance</div>
      </div>
    </div>
    <a class="btn btn-danger" href="logout.php">
      <i class="ti ti-logout" style="font-size:14px;"></i> Logout
    </a>
  </div>

  <!-- Stats -->
  <div>
    <div class="section-label">Overview</div>
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-label">
          <i class="ti ti-list-check" style="font-size:14px;"></i> Total quizzes
        </div>
        <div class="stat-value"><?php echo $total_quizzes; ?></div>
        <div class="stat-sub">quizzes attempted</div>
      </div>
      <div class="stat-card">
        <div class="stat-label">
          <i class="ti ti-target" style="font-size:14px;"></i> Accuracy
        </div>
        <div class="stat-value">
          <?php echo round($accuracy, 1); ?><span style="font-size:16px;">%</span>
        </div>
        <div class="stat-sub">of answers correct</div>
      </div>
      <div class="stat-card">
        <div class="stat-label">
          <i class="ti ti-chart-bar" style="font-size:14px;"></i> Avg. score
        </div>
        <div class="stat-value"><?php echo round($avg_score, 1); ?></div>
        <div class="stat-sub">points per quiz</div>
      </div>
    </div>
  </div>

  <!-- Performance spotlight -->
  <div>
    <div class="section-label">Performance spotlight</div>
    <div class="perf-grid">
      <div class="perf-card strong">
        <div class="perf-tag strong">
          <i class="ti ti-trophy" style="font-size:13px;"></i> Strong subject
        </div>
        <div class="perf-subject strong">
          <?php echo htmlspecialchars($strong['name'] ?? 'N/A'); ?>
        </div>
        <div class="perf-score strong">
          <?php echo isset($strong['avg_score']) ? round($strong['avg_score']) . '%' : '0%'; ?>
        </div>
      </div>
      <div class="perf-card weak">
        <div class="perf-tag weak">
          <i class="ti ti-alert-triangle" style="font-size:13px;"></i> Needs attention
        </div>
        <div class="perf-subject weak">
          <?php echo htmlspecialchars($weak['name'] ?? 'N/A'); ?>
        </div>
        <div class="perf-score weak">
          <?php echo isset($weak['avg_score']) ? round($weak['avg_score']) . '%' : '0%'; ?>
        </div>
      </div>
    </div>
  </div>

  <!-- Attempts by subject -->
  <div>
    <div class="section-label">Your attempts by subject</div>

    <?php if (!empty($attempt_data)): ?>
      <div class="attempts-list">
        <?php foreach ($attempt_data as $item):
          $pct     = round($item['avg_pct']);
          $isLow   = $pct < 50;
          $barClass = $isLow ? 'bar-fill low' : 'bar-fill';
          $badgeCls = $isLow ? 'attempt-badge badge-red' : 'attempt-badge badge-blue';
        ?>
        <div class="attempt-row">
          <div class="attempt-info">
            <div class="attempt-name"><?php echo htmlspecialchars($item['name']); ?></div>
            <div class="attempt-meta">
              <?php echo $item['attempts']; ?> attempt<?php echo $item['attempts'] != 1 ? 's' : ''; ?>
            </div>
            <div class="bar-wrap">
              <div class="<?php echo $barClass; ?>" style="width:<?php echo $pct; ?>%;"></div>
            </div>
          </div>
          <span class="<?php echo $badgeCls; ?>"><?php echo $pct; ?>%</span>
        </div>
        <?php endforeach; ?>
      </div>

    <?php else: ?>
      <div class="empty-state">
        <i class="ti ti-notes-off" style="font-size:24px; display:block; margin-bottom:8px;"></i>
        No attempts yet — pick a quiz above to get started!
      </div>
    <?php endif; ?>
  </div>

</div><!-- /page -->

</body>
</html>