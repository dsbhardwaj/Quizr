<?php
include("admin_check.php");

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php"); exit();
}

include("../connection.php");

$user_count     = mysqli_fetch_assoc(mysqli_query($data, "SELECT COUNT(*) AS total FROM users"))['total'];
$question_count = mysqli_fetch_assoc(mysqli_query($data, "SELECT COUNT(*) AS total FROM questions"))['total'];
$result_count   = mysqli_fetch_assoc(mysqli_query($data, "SELECT COUNT(*) AS total FROM result"))['total'];
$subject_count  = mysqli_fetch_assoc(mysqli_query($data, "SELECT COUNT(*) AS total FROM subjects"))['total'];

// Recent attempts
$recent_q = mysqli_query($data, "
    SELECT r.*, u.name AS user_name, s.name AS subject_name
    FROM result r
    JOIN users u ON r.user_id = u.id
    JOIN subjects s ON r.subject_id = s.id
    ORDER BY r.id DESC LIMIT 8
");
$recent = [];
while ($row = mysqli_fetch_assoc($recent_q)) { $recent[] = $row; }

// Per-subject attempt counts for mini chart
$subj_q = mysqli_query($data, "
    SELECT s.name, COUNT(*) AS attempts,
           ROUND(AVG((r.score * 100.0) / r.total_ques)) AS avg_pct
    FROM result r
    JOIN subjects s ON r.subject_id = s.id
    GROUP BY s.id ORDER BY attempts DESC
");
$subj_stats = [];
while ($row = mysqli_fetch_assoc($subj_q)) { $subj_stats[] = $row; }
$max_attempts = !empty($subj_stats) ? max(array_column($subj_stats, 'attempts')) : 1;

$admin_name = $_SESSION['admin_username'] ?? 'Admin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard — Quizr</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f5f6fa; color: #111110; min-height: 100vh; }

    /* NAVBAR */
    .navbar {
      display: flex; align-items: center; justify-content: space-between;
      padding: 0 24px; height: 56px; background: #fff;
      border-bottom: 0.5px solid rgba(0,0,0,0.08);
      position: sticky; top: 0; z-index: 100;
    }
    .nav-left { display: flex; align-items: center; gap: 10px; }
    .nav-logo { font-size: 15px; font-weight: 600; display: flex; align-items: center; gap: 8px; text-decoration: none; color: #111110; }
    .nav-logo-dot { width: 8px; height: 8px; border-radius: 50%; background: #1D9E75; }
    .admin-chip { background: #FAEEDA; color: #633806; border: 0.5px solid #FAC775; border-radius: 100px; padding: 2px 8px; font-size: 11px; font-weight: 600; }
    .nav-right { display: flex; align-items: center; gap: 8px; }
    .nav-admin { font-size: 13px; color: #6b6b6a; display: flex; align-items: center; gap: 6px; }
    .nav-avatar { width: 28px; height: 28px; border-radius: 50%; background: #E6F1FB; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 600; color: #0C447C; }
    .btn { display: inline-flex; align-items: center; gap: 6px; padding: 7px 14px; border-radius: 8px; font-size: 13px; font-weight: 500; cursor: pointer; font-family: inherit; transition: all 0.12s; text-decoration: none; border: 0.5px solid rgba(0,0,0,0.14); }
    .btn-danger { background: #FCEBEB; color: #791F1F; border-color: #F7C1C1; }
    .btn-danger:hover { background: #F7C1C1; }
    .btn-primary { background: #111110; color: #fff; border-color: #111110; }
    .btn-primary:hover { opacity: 0.85; }
    .btn-ghost { background: transparent; color: #111110; }
    .btn-ghost:hover { background: #f0f0ef; }

    /* LAYOUT */
    .layout { display: grid; grid-template-columns: 200px 1fr; min-height: calc(100vh - 56px); }

    /* SIDEBAR */
    .sidebar { background: #fff; border-right: 0.5px solid rgba(0,0,0,0.08); padding: 20px 12px; display: flex; flex-direction: column; gap: 2px; }
    .sidebar-label { font-size: 10px; font-weight: 600; color: #9b9b9a; text-transform: uppercase; letter-spacing: 0.08em; padding: 8px 10px 4px; }
    .sidebar-item { display: flex; align-items: center; gap: 8px; padding: 8px 10px; border-radius: 8px; font-size: 13px; color: #6b6b6a; text-decoration: none; border: none; background: transparent; font-family: inherit; width: 100%; text-align: left; transition: all 0.12s; cursor: pointer; }
    .sidebar-item:hover { background: #f0f0ef; color: #111110; }
    .sidebar-item.active { background: #f0f0ef; color: #111110; font-weight: 500; }
    .sidebar-item.danger { color: #A32D2D; }
    .sidebar-item.danger:hover { background: #FCEBEB; }

    /* MAIN */
    .main { padding: 28px 28px 56px; overflow: auto; }

    /* PAGE HEADER */
    .page-header { display: flex; align-items: flex-start; justify-content: space-between; flex-wrap: wrap; gap: 12px; margin-bottom: 28px; }
    .page-title { font-size: 20px; font-weight: 600; letter-spacing: -0.02em; }
    .page-sub { font-size: 13px; color: #6b6b6a; margin-top: 3px; }

    /* SECTION LABEL */
    .section-label { font-size: 11px; font-weight: 600; color: #6b6b6a; text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 12px; }

    /* STATS GRID */
    .stats-grid { display: grid; grid-template-columns: repeat(4,1fr); gap: 12px; margin-bottom: 28px; }
    .stat-card { background: #fff; border: 0.5px solid rgba(0,0,0,0.08); border-radius: 12px; padding: 18px 20px; }
    .stat-top { display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px; }
    .stat-icon { width: 36px; height: 36px; border-radius: 9px; display: flex; align-items: center; justify-content: center; font-size: 17px; }
    .stat-value { font-size: 28px; font-weight: 300; letter-spacing: -0.03em; line-height: 1; }
    .stat-label { font-size: 12px; color: #6b6b6a; margin-top: 4px; }

    /* TWO COL */
    .two-col { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 24px; }

    /* CARD */
    .card { background: #fff; border: 0.5px solid rgba(0,0,0,0.08); border-radius: 14px; overflow: hidden; }
    .card-header { display: flex; align-items: center; justify-content: space-between; padding: 14px 18px 12px; border-bottom: 0.5px solid rgba(0,0,0,0.06); }
    .card-title { font-size: 14px; font-weight: 500; }

    /* QUICK LINKS */
    .quick-links { display: flex; flex-direction: column; gap: 2px; padding: 10px; }
    .quick-link { display: flex; align-items: center; gap: 10px; padding: 10px 10px; border-radius: 8px; font-size: 13px; color: #6b6b6a; text-decoration: none; transition: all 0.12s; }
    .quick-link:hover { background: #f0f0ef; color: #111110; }
    .quick-link-icon { width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 15px; flex-shrink: 0; }
    .quick-link-label { font-size: 13px; font-weight: 500; }
    .quick-link-sub { font-size: 11px; color: #9b9b9a; }

    /* BAR CHART */
    .bar-chart { padding: 16px 18px; display: flex; flex-direction: column; gap: 12px; }
    .bar-row { display: flex; align-items: center; gap: 10px; }
    .bar-name { font-size: 12px; color: #6b6b6a; width: 120px; flex-shrink: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .bar-track { flex: 1; height: 6px; background: #f0f0ef; border-radius: 3px; overflow: hidden; }
    .bar-fill { height: 100%; border-radius: 3px; background: #1D9E75; transition: width 0.6s ease; }
    .bar-val { font-size: 12px; font-weight: 500; color: #6b6b6a; width: 30px; text-align: right; flex-shrink: 0; }

    /* RECENT TABLE */
    .full-card { background: #fff; border: 0.5px solid rgba(0,0,0,0.08); border-radius: 14px; overflow: hidden; }
    table { width: 100%; border-collapse: collapse; }
    th { text-align: left; font-size: 11px; font-weight: 600; color: #6b6b6a; text-transform: uppercase; letter-spacing: 0.07em; padding: 10px 18px; border-bottom: 0.5px solid rgba(0,0,0,0.06); }
    td { padding: 12px 18px; font-size: 13px; border-bottom: 0.5px solid rgba(0,0,0,0.05); vertical-align: middle; }
    tr:last-child td { border-bottom: none; }
    tbody tr:hover td { background: #fafaf9; }

    .pct-badge { display: inline-flex; align-items: center; font-size: 12px; font-weight: 600; padding: 3px 10px; border-radius: 100px; }
    .badge-green { background: #EAF3DE; color: #27500A; }
    .badge-blue  { background: #E6F1FB; color: #0C447C; }
    .badge-amber { background: #FAEEDA; color: #633806; }
    .badge-red   { background: #FCEBEB; color: #791F1F; }

    .subject-chip { display: inline-flex; align-items: center; gap: 5px; padding: 3px 9px; border-radius: 6px; font-size: 12px; background: #f0f0ef; color: #6b6b6a; font-weight: 500; }

    .empty-row td { text-align: center; padding: 32px; color: #9b9b9a; }

    @media (max-width: 900px) {
      .stats-grid { grid-template-columns: repeat(2,1fr); }
      .two-col    { grid-template-columns: 1fr; }
    }
    @media (max-width: 640px) {
      .layout { grid-template-columns: 1fr; }
      .sidebar { display: none; }
      .stats-grid { grid-template-columns: 1fr 1fr; }
    }
  </style>
</head>
<body>

<nav class="navbar">
  <div class="nav-left">
    <a class="nav-logo" href="admin_dashboard.php">
      <div class="nav-logo-dot"></div>Quizr
    </a>
    <span class="admin-chip">Admin</span>
  </div>
  <div class="nav-right">
    <div class="nav-admin">
      <div class="nav-avatar"><?php echo strtoupper(substr($admin_name,0,1)); ?></div>
      <?php echo htmlspecialchars($admin_name); ?>
    </div>
    <a class="btn btn-danger" href="admin_logout.php">
      <i class="ti ti-logout" style="font-size:14px;"></i> Logout
    </a>
  </div>
</nav>

<div class="layout">

  <aside class="sidebar">
    <div class="sidebar-label">Overview</div>
    <a class="sidebar-item active" href="admin_dashboard.php">
      <i class="ti ti-layout-dashboard" style="font-size:15px;"></i> Dashboard
    </a>
    <div class="sidebar-label" style="margin-top:8px;">Content</div>
    <a class="sidebar-item" href="add_question.php">
      <i class="ti ti-circle-plus" style="font-size:15px;"></i> Add Question
    </a>
    <a class="sidebar-item" href="view_questions.php">
      <i class="ti ti-list-details" style="font-size:15px;"></i> View Questions
    </a>
    <div class="sidebar-label" style="margin-top:8px;">Management</div>
    <a class="sidebar-item" href="manage_users.php">
      <i class="ti ti-users" style="font-size:15px;"></i> Manage Users
    </a>
    <a class="sidebar-item" href="results.php">
      <i class="ti ti-chart-bar" style="font-size:15px;"></i> View Results
    </a>
    <div class="sidebar-label" style="margin-top:8px;">Account</div>
    <a class="sidebar-item danger" href="admin_logout.php">
      <i class="ti ti-logout" style="font-size:15px;"></i> Logout
    </a>
  </aside>

  <main class="main">

    <div class="page-header">
      <div>
        <div class="page-title">Dashboard</div>
        <div class="page-sub">Welcome back, <?php echo htmlspecialchars($admin_name); ?> — here's what's happening</div>
      </div>
      <a class="btn btn-primary" href="add_question.php">
        <i class="ti ti-circle-plus" style="font-size:14px;"></i> Add Question
      </a>
    </div>

    <!-- Stats -->
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-top">
          <div>
            <div class="stat-value"><?php echo $user_count; ?></div>
            <div class="stat-label">Total users</div>
          </div>
          <div class="stat-icon" style="background:#E6F1FB;">
            <i class="ti ti-users" style="font-size:17px; color:#0C447C;"></i>
          </div>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-top">
          <div>
            <div class="stat-value"><?php echo $question_count; ?></div>
            <div class="stat-label">Total questions</div>
          </div>
          <div class="stat-icon" style="background:#EAF3DE;">
            <i class="ti ti-help-circle" style="font-size:17px; color:#3B6D11;"></i>
          </div>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-top">
          <div>
            <div class="stat-value"><?php echo $result_count; ?></div>
            <div class="stat-label">Quiz attempts</div>
          </div>
          <div class="stat-icon" style="background:#FAEEDA;">
            <i class="ti ti-list-check" style="font-size:17px; color:#854F0B;"></i>
          </div>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-top">
          <div>
            <div class="stat-value"><?php echo $subject_count; ?></div>
            <div class="stat-label">Subjects</div>
          </div>
          <div class="stat-icon" style="background:#F3E8FF;">
            <i class="ti ti-book" style="font-size:17px; color:#6B21A8;"></i>
          </div>
        </div>
      </div>
    </div>

    <!-- Two col: quick actions + subject bar chart -->
    <div class="two-col">

      <div class="card">
        <div class="card-header">
          <div class="card-title">Quick actions</div>
        </div>
        <div class="quick-links">
          <a class="quick-link" href="add_question.php">
            <div class="quick-link-icon" style="background:#EAF3DE;">
              <i class="ti ti-circle-plus" style="color:#3B6D11;"></i>
            </div>
            <div>
              <div class="quick-link-label">Add Question</div>
              <div class="quick-link-sub">Create a new quiz question</div>
            </div>
          </a>
          <a class="quick-link" href="view_questions.php">
            <div class="quick-link-icon" style="background:#E6F1FB;">
              <i class="ti ti-list-details" style="color:#0C447C;"></i>
            </div>
            <div>
              <div class="quick-link-label">View Questions</div>
              <div class="quick-link-sub">Edit or delete questions</div>
            </div>
          </a>
          <a class="quick-link" href="manage_users.php">
            <div class="quick-link-icon" style="background:#FAEEDA;">
              <i class="ti ti-users" style="color:#854F0B;"></i>
            </div>
            <div>
              <div class="quick-link-label">Manage Users</div>
              <div class="quick-link-sub">View and control accounts</div>
            </div>
          </a>
          <a class="quick-link" href="results.php">
            <div class="quick-link-icon" style="background:#F3E8FF;">
              <i class="ti ti-chart-bar" style="color:#6B21A8;"></i>
            </div>
            <div>
              <div class="quick-link-label">View Results</div>
              <div class="quick-link-sub">All quiz attempt results</div>
            </div>
          </a>
        </div>
      </div>

      <div class="card">
        <div class="card-header">
          <div class="card-title">Attempts by subject</div>
        </div>
        <?php if (!empty($subj_stats)): ?>
        <div class="bar-chart">
          <?php foreach ($subj_stats as $ss):
            $width = $max_attempts > 0 ? round(($ss['attempts'] / $max_attempts) * 100) : 0;
          ?>
          <div class="bar-row">
            <div class="bar-name" title="<?php echo htmlspecialchars($ss['name']); ?>">
              <?php echo htmlspecialchars($ss['name']); ?>
            </div>
            <div class="bar-track">
              <div class="bar-fill" style="width:<?php echo $width; ?>%;"></div>
            </div>
            <div class="bar-val"><?php echo $ss['attempts']; ?></div>
          </div>
          <?php endforeach; ?>
        </div>
        <?php else: ?>
          <div style="padding:24px; text-align:center; font-size:13px; color:#9b9b9a;">No attempts yet.</div>
        <?php endif; ?>
      </div>

    </div>

    <!-- Recent attempts -->
    <div class="full-card">
      <div class="card-header">
        <div class="card-title">Recent quiz attempts</div>
        <a class="btn btn-ghost" href="results.php" style="padding:5px 12px; font-size:12px;">
          View all <i class="ti ti-arrow-right" style="font-size:12px;"></i>
        </a>
      </div>
      <table>
        <thead>
          <tr>
            <th>User</th>
            <th>Subject</th>
            <th>Score</th>
            <th>Percentage</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($recent)): ?>
            <?php foreach ($recent as $r):
              $pct = $r['total_ques'] > 0 ? round(($r['score']/$r['total_ques'])*100) : 0;
              if      ($pct >= 80) $cls = 'pct-badge badge-green';
              elseif  ($pct >= 60) $cls = 'pct-badge badge-blue';
              elseif  ($pct >= 40) $cls = 'pct-badge badge-amber';
              else                 $cls = 'pct-badge badge-red';
            ?>
            <tr>
              <td style="font-weight:500;"><?php echo htmlspecialchars($r['user_name']); ?></td>
              <td>
                <span class="subject-chip">
                  <i class="ti ti-book" style="font-size:11px;"></i>
                  <?php echo htmlspecialchars($r['subject_name']); ?>
                </span>
              </td>
              <td><?php echo $r['score']; ?> <span style="color:#9b9b9a;">/ <?php echo $r['total_ques']; ?></span></td>
              <td><span class="<?php echo $cls; ?>"><?php echo $pct; ?>%</span></td>
            </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr class="empty-row">
              <td colspan="4">
                <i class="ti ti-notes-off" style="font-size:20px; display:block; margin-bottom:6px;"></i>
                No quiz attempts yet.
              </td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

  </main>
</div>

</body>
</html>