<?php
include("admin_check.php");
include("../connection.php");

// Filter by subject
$subject_filter = isset($_GET['subject']) ? (int)$_GET['subject'] : 0;

// Search
$search = isset($_GET['q']) ? trim($_GET['q']) : '';

// Build query
$where = "WHERE 1=1";
if ($subject_filter > 0) $where .= " AND q.subject_id = $subject_filter";
if ($search !== '')      $where .= " AND q.question_text LIKE '%" . mysqli_real_escape_string($data, $search) . "%'";

$sql = "SELECT q.*, s.name AS subject_name
        FROM questions q
        JOIN subjects s ON q.subject_id = s.id
        $where
        ORDER BY q.id DESC";

$result  = mysqli_query($data, $sql);
$rows    = [];
while ($row = mysqli_fetch_assoc($result)) { $rows[] = $row; }

// All subjects for filter
$subj_res = mysqli_query($data, "SELECT * FROM subjects ORDER BY name ASC");
$subjects = [];
while ($s = mysqli_fetch_assoc($subj_res)) { $subjects[] = $s; }

$option_labels = [1 => 'A', 2 => 'B', 3 => 'C', 4 => 'D'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>View Questions — Quizr Admin</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f5f6fa; color: #111110; min-height: 100vh; }

    /* NAVBAR */
    .navbar { display: flex; align-items: center; justify-content: space-between; padding: 0 24px; height: 56px; background: #fff; border-bottom: 0.5px solid rgba(0,0,0,0.08); position: sticky; top: 0; z-index: 100; }
    .nav-left { display: flex; align-items: center; gap: 10px; }
    .nav-logo { font-size: 15px; font-weight: 600; display: flex; align-items: center; gap: 8px; text-decoration: none; color: #111110; }
    .nav-logo-dot { width: 8px; height: 8px; border-radius: 50%; background: #1D9E75; }
    .admin-chip { background: #FAEEDA; color: #633806; border: 0.5px solid #FAC775; border-radius: 100px; padding: 2px 8px; font-size: 11px; font-weight: 600; }
    .btn { display: inline-flex; align-items: center; gap: 6px; padding: 7px 14px; border-radius: 8px; font-size: 13px; font-weight: 500; cursor: pointer; font-family: inherit; transition: all 0.12s; text-decoration: none; border: 0.5px solid rgba(0,0,0,0.14); }
    .btn-ghost { background: transparent; color: #111110; }
    .btn-ghost:hover { background: #f0f0ef; }
    .btn-primary { background: #111110; color: #fff; border-color: #111110; }
    .btn-primary:hover { opacity: 0.85; }
    .btn-danger { background: #FCEBEB; color: #791F1F; border-color: #F7C1C1; }
    .btn-danger:hover { background: #F7C1C1; }
    .btn-sm { padding: 5px 10px; font-size: 12px; }

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
    .page-header { display: flex; align-items: flex-start; justify-content: space-between; flex-wrap: wrap; gap: 12px; margin-bottom: 24px; }
    .page-title { font-size: 20px; font-weight: 600; letter-spacing: -0.02em; }
    .page-sub { font-size: 13px; color: #6b6b6a; margin-top: 3px; }

    /* TOOLBAR */
    .toolbar { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; margin-bottom: 16px; }

    .search-wrap { position: relative; flex: 1; min-width: 200px; max-width: 320px; }
    .search-wrap i { position: absolute; left: 10px; top: 50%; transform: translateY(-50%); font-size: 15px; color: #9b9b9a; pointer-events: none; }
    .search-wrap input {
      width: 100%; padding: 8px 12px 8px 32px;
      border: 0.5px solid rgba(0,0,0,0.14); border-radius: 8px;
      font-size: 13px; font-family: inherit; background: #fff; color: #111110;
      outline: none; transition: border-color 0.15s, box-shadow 0.15s;
    }
    .search-wrap input:focus { border-color: #1D9E75; box-shadow: 0 0 0 3px rgba(29,158,117,0.12); }
    .search-wrap input::placeholder { color: #b0b0ae; }

    .select-filter {
      padding: 8px 28px 8px 12px; border: 0.5px solid rgba(0,0,0,0.14);
      border-radius: 8px; font-size: 13px; font-family: inherit;
      background: #fff; color: #111110; outline: none;
      appearance: none; cursor: pointer;
      background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%239b9b9a' stroke-width='2'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
      background-repeat: no-repeat; background-position: right 8px center;
    }

    .result-count { font-size: 13px; color: #6b6b6a; }
    .result-count span { font-weight: 600; color: #111110; }

    /* TABLE CARD */
    .table-card { background: #fff; border: 0.5px solid rgba(0,0,0,0.08); border-radius: 14px; overflow: hidden; }

    table { width: 100%; border-collapse: collapse; }
    th { text-align: left; font-size: 11px; font-weight: 600; color: #6b6b6a; text-transform: uppercase; letter-spacing: 0.07em; padding: 10px 18px; border-bottom: 0.5px solid rgba(0,0,0,0.06); white-space: nowrap; }
    td { padding: 12px 18px; font-size: 13px; border-bottom: 0.5px solid rgba(0,0,0,0.05); vertical-align: top; }
    tr:last-child td { border-bottom: none; }
    tbody tr:hover td { background: #fafaf9; }

    .q-id { font-size: 12px; font-variant-numeric: tabular-nums; color: #9b9b9a; font-weight: 500; }
    .q-text { font-size: 13px; font-weight: 500; line-height: 1.5; max-width: 360px; }
    .q-text-full { color: #6b6b6a; font-size: 12px; margin-top: 2px; }

    .subject-chip { display: inline-flex; align-items: center; gap: 5px; padding: 3px 9px; border-radius: 6px; font-size: 12px; background: #f0f0ef; color: #6b6b6a; font-weight: 500; white-space: nowrap; }

    .correct-key { display: inline-flex; align-items: center; justify-content: center; width: 24px; height: 24px; border-radius: 6px; background: #EAF3DE; color: #27500A; font-size: 12px; font-weight: 700; }

    .action-cell { display: flex; gap: 6px; align-items: center; }

    .empty-state { padding: 48px; text-align: center; color: #9b9b9a; }
    .empty-state i { font-size: 28px; display: block; margin-bottom: 10px; color: #b0b0ae; }

    @media (max-width: 640px) {
      .layout { grid-template-columns: 1fr; }
      .sidebar { display: none; }
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
  <div style="display:flex; gap:8px;">
    <a class="btn btn-primary" href="add_question.php">
      <i class="ti ti-circle-plus" style="font-size:14px;"></i> Add Question
    </a>
    <a class="btn btn-danger" href="admin_logout.php">
      <i class="ti ti-logout" style="font-size:14px;"></i> Logout
    </a>
  </div>
</nav>

<div class="layout">

  <aside class="sidebar">
    <div class="sidebar-label">Overview</div>
    <a class="sidebar-item" href="admin_dashboard.php">
      <i class="ti ti-layout-dashboard" style="font-size:15px;"></i> Dashboard
    </a>
    <div class="sidebar-label" style="margin-top:8px;">Content</div>
    <a class="sidebar-item" href="add_question.php">
      <i class="ti ti-circle-plus" style="font-size:15px;"></i> Add Question
    </a>
    <a class="sidebar-item active" href="view_questions.php">
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
        <div class="page-title">Questions</div>
        <div class="page-sub">Manage all quiz questions across subjects</div>
      </div>
      <a class="btn btn-primary" href="add_question.php">
        <i class="ti ti-circle-plus" style="font-size:14px;"></i> Add Question
      </a>
    </div>

    <!-- Toolbar -->
    <form method="GET" action="view_questions.php">
      <div class="toolbar">
        <div class="search-wrap">
          <i class="ti ti-search"></i>
          <input type="text" name="q" placeholder="Search questions…"
            value="<?php echo htmlspecialchars($search); ?>">
        </div>

        <select name="subject" class="select-filter" onchange="this.form.submit()">
          <option value="0">All subjects</option>
          <?php foreach ($subjects as $s): ?>
            <option value="<?php echo $s['id']; ?>"
              <?php echo $subject_filter === (int)$s['id'] ? 'selected' : ''; ?>>
              <?php echo htmlspecialchars($s['name']); ?>
            </option>
          <?php endforeach; ?>
        </select>

        <?php if ($search || $subject_filter > 0): ?>
          <a class="btn btn-ghost btn-sm" href="view_questions.php">
            <i class="ti ti-x" style="font-size:13px;"></i> Clear
          </a>
        <?php endif; ?>

        <button type="submit" class="btn btn-ghost btn-sm">
          <i class="ti ti-search" style="font-size:13px;"></i> Search
        </button>

        <div class="result-count" style="margin-left:auto;">
          <span><?php echo count($rows); ?></span> question<?php echo count($rows) != 1 ? 's' : ''; ?>
        </div>
      </div>
    </form>

    <!-- Table -->
    <div class="table-card">
      <?php if (!empty($rows)): ?>
      <table>
        <thead>
          <tr>
            <th style="width:50px;">ID</th>
            <th style="width:140px;">Subject</th>
            <th>Question</th>
            <th style="width:80px;">Answer</th>
            <th style="width:110px;">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($rows as $row):
            $correct_label = $option_labels[$row['correct_answer']] ?? $row['correct_answer'];
          ?>
          <tr>
            <td><span class="q-id">#<?php echo $row['id']; ?></span></td>
            <td>
              <span class="subject-chip">
                <i class="ti ti-book" style="font-size:11px;"></i>
                <?php echo htmlspecialchars($row['subject_name']); ?>
              </span>
            </td>
            <td>
              <div class="q-text">
                <?php
                  $text = htmlspecialchars($row['question_text']);
                  echo strlen($text) > 90 ? substr($text, 0, 90) . '…' : $text;
                ?>
              </div>
            </td>
            <td>
              <span class="correct-key"><?php echo $correct_label; ?></span>
            </td>
            <td>
              <div class="action-cell">
                <a class="btn btn-ghost btn-sm" href="edit_question.php?id=<?php echo $row['id']; ?>"
                   title="Edit">
                  <i class="ti ti-pencil" style="font-size:13px;"></i> Edit
                </a>
                <a class="btn btn-danger btn-sm" href="delete_question.php?id=<?php echo $row['id']; ?>"
                   title="Delete"
                   onclick="return confirm('Delete this question? This cannot be undone.');">
                  <i class="ti ti-trash" style="font-size:13px;"></i>
                </a>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>

      <?php else: ?>
        <div class="empty-state">
          <i class="ti ti-help-circle-off"></i>
          <?php if ($search || $subject_filter > 0): ?>
            No questions match your filters.
            <a href="view_questions.php" style="color:#1D9E75; display:block; margin-top:8px; font-size:13px;">Clear filters</a>
          <?php else: ?>
            No questions yet — <a href="add_question.php" style="color:#1D9E75;">add the first one</a>.
          <?php endif; ?>
        </div>
      <?php endif; ?>
    </div>

  </main>
</div>

</body>
</html>