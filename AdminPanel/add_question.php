<?php
include("admin_check.php");
include("../connection.php");

$subject_query = mysqli_query($data, "SELECT * FROM subjects ORDER BY name ASC");
$subjects = [];
while ($s = mysqli_fetch_assoc($subject_query)) {
    $subjects[] = $s;
}

$success = '';
$error   = '';

if (isset($_POST['add_question'])) {
    $subject_id     = $_POST['subject_id'];
    $question       = $_POST['question_text'];
    $option1        = $_POST['option_1'];
    $option2        = $_POST['option_2'];
    $option3        = $_POST['option_3'];
    $option4        = $_POST['option_4'];
    $correct_answer = $_POST['correct_answer'];

    $sql  = "INSERT INTO questions
             (subject_id, question_text, option_1, option_2, option_3, option_4, correct_answer)
             VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($data, $sql);
    mysqli_stmt_bind_param($stmt, "isssssi",
        $subject_id, $question,
        $option1, $option2, $option3, $option4,
        $correct_answer);

    if (mysqli_stmt_execute($stmt)) {
        $success = "Question added successfully.";
    } else {
        $error = "Failed to add question. Please try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add Question — Quizr Admin</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: #f5f6fa; color: #111110; min-height: 100vh;
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

    .admin-chip {
      background: #FAEEDA; color: #633806;
      border: 0.5px solid #FAC775; border-radius: 100px;
      padding: 2px 8px; font-size: 11px; font-weight: 600;
    }

    .nav-links { display: flex; align-items: center; gap: 4px; }

    .nav-link {
      display: flex; align-items: center; gap: 6px;
      padding: 6px 12px; border-radius: 8px; font-size: 13px;
      color: #6b6b6a; text-decoration: none; transition: all 0.12s;
    }

    .nav-link:hover { background: #f0f0ef; color: #111110; }
    .nav-link.active { background: #f0f0ef; color: #111110; font-weight: 500; }

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
    .btn-primary { background: #111110; color: #fff; border-color: #111110; }
    .btn-primary:hover { opacity: 0.85; }

    /* ── LAYOUT ── */
    .layout { display: grid; grid-template-columns: 200px 1fr; min-height: calc(100vh - 56px); }

    /* ── SIDEBAR ── */
    .sidebar {
      background: #ffffff; border-right: 0.5px solid rgba(0,0,0,0.08);
      padding: 20px 12px; display: flex; flex-direction: column; gap: 2px;
    }

    .sidebar-label {
      font-size: 10px; font-weight: 600; color: #9b9b9a;
      text-transform: uppercase; letter-spacing: 0.08em;
      padding: 8px 10px 4px;
    }

    .sidebar-item {
      display: flex; align-items: center; gap: 8px;
      padding: 8px 10px; border-radius: 8px; font-size: 13px;
      color: #6b6b6a; text-decoration: none; cursor: pointer;
      border: none; background: transparent; font-family: inherit;
      width: 100%; text-align: left; transition: all 0.12s;
    }

    .sidebar-item:hover { background: #f0f0ef; color: #111110; }
    .sidebar-item.active { background: #f0f0ef; color: #111110; font-weight: 500; }

    /* ── MAIN ── */
    .main { padding: 32px; overflow: auto; }

    .page-header { margin-bottom: 28px; }
    .page-title { font-size: 20px; font-weight: 600; letter-spacing: -0.02em; }
    .page-sub   { font-size: 13px; color: #6b6b6a; margin-top: 4px; }

    /* ── ALERTS ── */
    .alert {
      display: flex; align-items: center; gap: 8px;
      border-radius: 10px; padding: 12px 16px;
      font-size: 13px; margin-bottom: 24px;
    }

    .alert-success { background: #EAF3DE; border: 0.5px solid #C0DD97; color: #27500A; }
    .alert-error   { background: #FCEBEB; border: 0.5px solid #F7C1C1; color: #791F1F; }

    /* ── FORM CARD ── */
    .form-card {
      background: #ffffff; border: 0.5px solid rgba(0,0,0,0.08);
      border-radius: 14px; padding: 28px; max-width: 680px;
    }

    .form-section { margin-bottom: 28px; }

    .section-label {
      font-size: 11px; font-weight: 600; color: #6b6b6a;
      text-transform: uppercase; letter-spacing: 0.08em;
      margin-bottom: 14px; padding-bottom: 10px;
      border-bottom: 0.5px solid rgba(0,0,0,0.06);
    }

    .field { margin-bottom: 16px; }

    .field label {
      display: block; font-size: 12px; font-weight: 500;
      color: #6b6b6a; text-transform: uppercase;
      letter-spacing: 0.06em; margin-bottom: 6px;
    }

    .field select,
    .field input[type="text"],
    .field textarea {
      width: 100%;
      padding: 10px 14px;
      border: 0.5px solid rgba(0,0,0,0.14);
      border-radius: 8px;
      font-size: 14px; font-family: inherit;
      background: #fafaf9; color: #111110;
      transition: border-color 0.15s, box-shadow 0.15s;
      outline: none;
      appearance: none;
    }

    .field select:focus,
    .field input:focus,
    .field textarea:focus {
      border-color: #1D9E75;
      box-shadow: 0 0 0 3px rgba(29,158,117,0.12);
      background: #fff;
    }

    .field textarea { resize: vertical; min-height: 90px; line-height: 1.5; }

    .select-wrap { position: relative; }

    .select-wrap::after {
      content: '';
      position: absolute; right: 12px; top: 50%;
      transform: translateY(-50%);
      border: 4px solid transparent;
      border-top-color: #9b9b9a;
      pointer-events: none;
    }

    /* ── OPTIONS GRID ── */
    .options-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }

    .option-field { position: relative; }

    .option-field label {
      display: flex; align-items: center; gap: 6px;
    }

    .option-key {
      width: 20px; height: 20px; border-radius: 5px;
      background: #f0f0ef; display: inline-flex;
      align-items: center; justify-content: center;
      font-size: 10px; font-weight: 700; color: #6b6b6a;
      flex-shrink: 0;
    }

    /* ── CORRECT ANSWER SELECT ── */
    .correct-select-wrap { position: relative; }
    .correct-select-wrap::after {
      content: '';
      position: absolute; right: 12px; top: 50%;
      transform: translateY(-50%);
      border: 4px solid transparent;
      border-top-color: #9b9b9a;
      pointer-events: none;
    }

    .correct-select-wrap select {
      background: #EAF3DE; border-color: #C0DD97; color: #27500A;
      font-weight: 500;
    }

    .correct-select-wrap select:focus {
      border-color: #1D9E75;
      box-shadow: 0 0 0 3px rgba(29,158,117,0.12);
    }

    /* ── FORM FOOTER ── */
    .form-footer {
      display: flex; align-items: center; gap: 10px;
      padding-top: 8px; flex-wrap: wrap;
    }

    .btn-submit {
      display: inline-flex; align-items: center; gap: 6px;
      padding: 11px 24px; border: none; border-radius: 8px;
      background: #111110; color: #fff;
      font-size: 14px; font-weight: 500; font-family: inherit;
      cursor: pointer; transition: opacity 0.15s;
    }

    .btn-submit:hover { opacity: 0.85; }

    @media (max-width: 640px) {
      .layout { grid-template-columns: 1fr; }
      .sidebar { display: none; }
      .options-grid { grid-template-columns: 1fr; }
    }
  </style>
</head>
<body>

<!-- ── NAVBAR ── -->
<nav class="navbar">
  <div style="display:flex; align-items:center; gap:10px;">
    <a class="nav-logo" href="admin_dashboard.php">
      <div class="nav-logo-dot"></div>
      Quizr
    </a>
    <span class="admin-chip">Admin</span>
  </div>
  <div class="nav-links">
    <a class="nav-link" href="admin_dashboard.php">
      <i class="ti ti-layout-dashboard" style="font-size:14px;"></i> Dashboard
    </a>
    <a class="nav-link" href="view_questions.php">
      <i class="ti ti-list" style="font-size:14px;"></i> Questions
    </a>
    <a class="nav-link" href="manage_users.php">
      <i class="ti ti-users" style="font-size:14px;"></i> Users
    </a>
    <a class="btn btn-danger" href="admin_logout.php">
      <i class="ti ti-logout" style="font-size:14px;"></i> Logout
    </a>
  </div>
</nav>

<div class="layout">

  <!-- ── SIDEBAR ── -->
  <aside class="sidebar">
    <div class="sidebar-label">Content</div>
    <a class="sidebar-item active" href="add_question.php">
      <i class="ti ti-circle-plus" style="font-size:15px;"></i> Add Question
    </a>
    <a class="sidebar-item" href="view_questions.php">
      <i class="ti ti-list-details" style="font-size:15px;"></i> View Questions
    </a>
    <div class="sidebar-label" style="margin-top:8px;">Management</div>
    <a class="sidebar-item" href="manage_users.php">
      <i class="ti ti-users" style="font-size:15px;"></i> Manage Users
    </a>
    <a class="sidebar-item" href="admin_dashboard.php">
      <i class="ti ti-chart-bar" style="font-size:15px;"></i> Analytics
    </a>
    <div class="sidebar-label" style="margin-top:8px;">Account</div>
    <a class="sidebar-item" href="admin_logout.php" style="color:#A32D2D;">
      <i class="ti ti-logout" style="font-size:15px;"></i> Logout
    </a>
  </aside>

  <!-- ── MAIN ── -->
  <main class="main">

    <div class="page-header">
      <div class="page-title">Add Question</div>
      <div class="page-sub">Create a new question and assign it to a subject</div>
    </div>

    <?php if ($success): ?>
      <div class="alert alert-success">
        <i class="ti ti-circle-check" style="font-size:16px; flex-shrink:0;"></i>
        <?php echo htmlspecialchars($success); ?>
      </div>
    <?php endif; ?>

    <?php if ($error): ?>
      <div class="alert alert-error">
        <i class="ti ti-alert-circle" style="font-size:16px; flex-shrink:0;"></i>
        <?php echo htmlspecialchars($error); ?>
      </div>
    <?php endif; ?>

    <div class="form-card">
      <form method="POST">

        <!-- Subject -->
        <div class="form-section">
          <div class="section-label">Subject</div>
          <div class="field">
            <label for="subject_id">Select subject</label>
            <div class="select-wrap">
              <select name="subject_id" id="subject_id" required>
                <option value="">— Choose a subject —</option>
                <?php foreach ($subjects as $s): ?>
                  <option value="<?php echo $s['id']; ?>"
                    <?php echo (isset($_POST['subject_id']) && $_POST['subject_id'] == $s['id']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($s['name']); ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
        </div>

        <!-- Question text -->
        <div class="form-section">
          <div class="section-label">Question</div>
          <div class="field">
            <label for="question_text">Question text</label>
            <textarea name="question_text" id="question_text" required
              placeholder="Type the question here…"><?php echo htmlspecialchars($_POST['question_text'] ?? ''); ?></textarea>
          </div>
        </div>

        <!-- Options -->
        <div class="form-section">
          <div class="section-label">Answer options</div>
          <div class="options-grid">
            <?php
            $optKeys = ['A','B','C','D'];
            for ($i = 1; $i <= 4; $i++):
              $val = htmlspecialchars($_POST["option_$i"] ?? '');
            ?>
            <div class="field option-field">
              <label for="option_<?php echo $i; ?>">
                <span class="option-key"><?php echo $optKeys[$i-1]; ?></span>
                Option <?php echo $i; ?>
              </label>
              <input type="text" name="option_<?php echo $i; ?>" id="option_<?php echo $i; ?>"
                placeholder="Enter option <?php echo $i; ?>" value="<?php echo $val; ?>" required>
            </div>
            <?php endfor; ?>
          </div>
        </div>

        <!-- Correct answer -->
        <div class="form-section" style="margin-bottom:0;">
          <div class="section-label">Correct answer</div>
          <div class="field" style="max-width:260px;">
            <label for="correct_answer">Mark the correct option</label>
            <div class="correct-select-wrap">
              <select name="correct_answer" id="correct_answer" required>
                <?php for ($i = 1; $i <= 4; $i++): ?>
                  <option value="<?php echo $i; ?>"
                    <?php echo (isset($_POST['correct_answer']) && $_POST['correct_answer'] == $i) ? 'selected' : ''; ?>>
                    Option <?php echo $i; ?> (<?php echo $optKeys[$i-1]; ?>)
                  </option>
                <?php endfor; ?>
              </select>
            </div>
          </div>
        </div>

        <!-- Footer -->
        <div class="form-footer">
          <button type="submit" name="add_question" class="btn-submit">
            <i class="ti ti-circle-plus" style="font-size:15px;"></i>
            Add Question
          </button>
          <a class="btn btn-ghost" href="view_questions.php">
            <i class="ti ti-list" style="font-size:14px;"></i>
            View all questions
          </a>
        </div>

      </form>
    </div>

  </main>
</div>

</body>
</html>