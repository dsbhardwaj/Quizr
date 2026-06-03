<?php
include("admin_check.php");
include("../connection.php");

if (!isset($_GET['id'])) { header("Location: view_questions.php"); exit(); }

$id       = (int)$_GET['id'];
$query    = mysqli_query($data, "SELECT * FROM questions WHERE id='$id'");
$question = mysqli_fetch_assoc($query);

if (!$question) { header("Location: view_questions.php"); exit(); }

$subj_res = mysqli_query($data, "SELECT * FROM subjects ORDER BY name ASC");
$subjects = [];
while ($s = mysqli_fetch_assoc($subj_res)) { $subjects[] = $s; }

if (isset($_POST['update_question'])) {
    $subject_id     = $_POST['subject_id'];
    $question_text  = $_POST['question_text'];
    $option_1       = $_POST['option_1'];
    $option_2       = $_POST['option_2'];
    $option_3       = $_POST['option_3'];
    $option_4       = $_POST['option_4'];
    $correct_answer = $_POST['correct_answer'];

    $sql  = "UPDATE questions SET subject_id=?,question_text=?,option_1=?,option_2=?,option_3=?,option_4=?,correct_answer=? WHERE id=?";
    $stmt = mysqli_prepare($data, $sql);
    mysqli_stmt_bind_param($stmt, "isssssii",
        $subject_id, $question_text,
        $option_1, $option_2, $option_3, $option_4,
        $correct_answer, $id);

    if (mysqli_stmt_execute($stmt)) {
        header("Location: view_questions.php?updated=1"); exit();
    }
}

$admin_name  = $_SESSION['admin_username'] ?? 'Admin';
$opt_keys    = ['A','B','C','D'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Question #<?php echo $id; ?> — Quizr Admin</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
  <style>
    *,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
    body{font-family:'Segoe UI',Tahoma,Geneva,Verdana,sans-serif;background:#f5f6fa;color:#111110;min-height:100vh;}
    .navbar{display:flex;align-items:center;justify-content:space-between;padding:0 24px;height:56px;background:#fff;border-bottom:.5px solid rgba(0,0,0,.08);position:sticky;top:0;z-index:100;}
    .nav-left{display:flex;align-items:center;gap:10px;}
    .nav-logo{font-size:15px;font-weight:600;display:flex;align-items:center;gap:8px;text-decoration:none;color:#111110;}
    .nav-logo-dot{width:8px;height:8px;border-radius:50%;background:#1D9E75;}
    .admin-chip{background:#FAEEDA;color:#633806;border:.5px solid #FAC775;border-radius:100px;padding:2px 8px;font-size:11px;font-weight:600;}
    .nav-right{display:flex;align-items:center;gap:8px;}
    .nav-admin{font-size:13px;color:#6b6b6a;display:flex;align-items:center;gap:6px;}
    .nav-avatar{width:28px;height:28px;border-radius:50%;background:#E6F1FB;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:600;color:#0C447C;}
    .btn{display:inline-flex;align-items:center;gap:6px;padding:7px 14px;border-radius:8px;font-size:13px;font-weight:500;cursor:pointer;font-family:inherit;transition:all .12s;text-decoration:none;border:.5px solid rgba(0,0,0,.14);}
    .btn-ghost{background:transparent;color:#111110;}.btn-ghost:hover{background:#f0f0ef;}
    .btn-primary{background:#111110;color:#fff;border-color:#111110;}.btn-primary:hover{opacity:.85;}
    .btn-danger{background:#FCEBEB;color:#791F1F;border-color:#F7C1C1;}.btn-danger:hover{background:#F7C1C1;}
    .layout{display:grid;grid-template-columns:200px 1fr;min-height:calc(100vh - 56px);}
    .sidebar{background:#fff;border-right:.5px solid rgba(0,0,0,.08);padding:20px 12px;display:flex;flex-direction:column;gap:2px;}
    .sidebar-label{font-size:10px;font-weight:600;color:#9b9b9a;text-transform:uppercase;letter-spacing:.08em;padding:8px 10px 4px;}
    .sidebar-item{display:flex;align-items:center;gap:8px;padding:8px 10px;border-radius:8px;font-size:13px;color:#6b6b6a;text-decoration:none;border:none;background:transparent;font-family:inherit;width:100%;text-align:left;transition:all .12s;cursor:pointer;}
    .sidebar-item:hover{background:#f0f0ef;color:#111110;}.sidebar-item.active{background:#f0f0ef;color:#111110;font-weight:500;}
    .sidebar-item.danger{color:#A32D2D;}.sidebar-item.danger:hover{background:#FCEBEB;}
    .main{padding:28px 28px 56px;overflow:auto;}
    .page-header{display:flex;align-items:center;gap:14px;margin-bottom:28px;flex-wrap:wrap;}
    .back-btn{display:inline-flex;align-items:center;gap:5px;font-size:13px;color:#6b6b6a;text-decoration:none;padding:5px 10px;border-radius:7px;transition:all .12s;}
    .back-btn:hover{background:#f0f0ef;color:#111110;}
    .page-title{font-size:20px;font-weight:600;letter-spacing:-.02em;}
    .page-sub{font-size:13px;color:#6b6b6a;margin-top:3px;}
    .id-badge{font-size:12px;font-weight:600;padding:3px 10px;border-radius:100px;background:#f0f0ef;color:#6b6b6a;border:.5px solid rgba(0,0,0,.10);}
    .form-card{background:#fff;border:.5px solid rgba(0,0,0,.08);border-radius:14px;padding:28px;max-width:680px;}
    .form-section{margin-bottom:28px;}
    .section-label{font-size:11px;font-weight:600;color:#6b6b6a;text-transform:uppercase;letter-spacing:.08em;margin-bottom:14px;padding-bottom:10px;border-bottom:.5px solid rgba(0,0,0,.06);}
    .field{margin-bottom:16px;}
    .field label{display:block;font-size:12px;font-weight:500;color:#6b6b6a;text-transform:uppercase;letter-spacing:.06em;margin-bottom:6px;}
    .field select,.field input[type="text"],.field textarea{width:100%;padding:10px 14px;border:.5px solid rgba(0,0,0,.14);border-radius:8px;font-size:14px;font-family:inherit;background:#fafaf9;color:#111110;outline:none;transition:border-color .15s,box-shadow .15s;appearance:none;}
    .field select:focus,.field input:focus,.field textarea:focus{border-color:#1D9E75;box-shadow:0 0 0 3px rgba(29,158,117,.12);background:#fff;}
    .field textarea{resize:vertical;min-height:90px;line-height:1.5;}
    .select-wrap{position:relative;}.select-wrap::after{content:'';position:absolute;right:12px;top:50%;transform:translateY(-50%);border:4px solid transparent;border-top-color:#9b9b9a;pointer-events:none;}
    .options-grid{display:grid;grid-template-columns:1fr 1fr;gap:12px;}
    .option-key{width:20px;height:20px;border-radius:5px;background:#f0f0ef;display:inline-flex;align-items:center;justify-content:center;font-size:10px;font-weight:700;color:#6b6b6a;flex-shrink:0;}
    .field label.with-key{display:flex;align-items:center;gap:6px;}
    .correct-wrap{position:relative;}.correct-wrap::after{content:'';position:absolute;right:12px;top:50%;transform:translateY(-50%);border:4px solid transparent;border-top-color:#3B6D11;pointer-events:none;}
    .correct-wrap select{background:#EAF3DE;border-color:#C0DD97;color:#27500A;font-weight:500;}
    .correct-wrap select:focus{border-color:#1D9E75;box-shadow:0 0 0 3px rgba(29,158,117,.12);}
    .form-footer{display:flex;align-items:center;gap:10px;padding-top:8px;flex-wrap:wrap;}
    .btn-submit{display:inline-flex;align-items:center;gap:6px;padding:11px 24px;border:none;border-radius:8px;background:#111110;color:#fff;font-size:14px;font-weight:500;font-family:inherit;cursor:pointer;transition:opacity .15s;}
    .btn-submit:hover{opacity:.85;}
    @media(max-width:640px){.layout{grid-template-columns:1fr;}.sidebar{display:none;}.options-grid{grid-template-columns:1fr;}}
  </style>
</head>
<body>

<nav class="navbar">
  <div class="nav-left">
    <a class="nav-logo" href="admin_dashboard.php"><div class="nav-logo-dot"></div>Quizr</a>
    <span class="admin-chip">Admin</span>
  </div>
  <div class="nav-right">
    <div class="nav-admin">
      <div class="nav-avatar"><?php echo strtoupper(substr($admin_name,0,1)); ?></div>
      <?php echo htmlspecialchars($admin_name); ?>
    </div>
    <a class="btn btn-danger" href="admin_logout.php"><i class="ti ti-logout" style="font-size:14px;"></i> Logout</a>
  </div>
</nav>

<div class="layout">
  <aside class="sidebar">
    <div class="sidebar-label">Overview</div>
    <a class="sidebar-item" href="admin_dashboard.php"><i class="ti ti-layout-dashboard" style="font-size:15px;"></i> Dashboard</a>
    <div class="sidebar-label" style="margin-top:8px;">Content</div>
    <a class="sidebar-item" href="add_question.php"><i class="ti ti-circle-plus" style="font-size:15px;"></i> Add Question</a>
    <a class="sidebar-item active" href="view_questions.php"><i class="ti ti-list-details" style="font-size:15px;"></i> View Questions</a>
    <div class="sidebar-label" style="margin-top:8px;">Management</div>
    <a class="sidebar-item" href="manage_users.php"><i class="ti ti-users" style="font-size:15px;"></i> Manage Users</a>
    <a class="sidebar-item" href="manage_subjects.php"><i class="ti ti-books" style="font-size:15px;"></i> Manage Subjects</a>
    <a class="sidebar-item" href="results.php"><i class="ti ti-chart-bar" style="font-size:15px;"></i> View Results</a>
    <div class="sidebar-label" style="margin-top:8px;">Account</div>
    <a class="sidebar-item danger" href="admin_logout.php"><i class="ti ti-logout" style="font-size:15px;"></i> Logout</a>
  </aside>

  <main class="main">
    <div class="page-header">
      <a class="back-btn" href="view_questions.php"><i class="ti ti-arrow-left" style="font-size:14px;"></i> Back</a>
      <div>
        <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
          <div class="page-title">Edit Question</div>
          <span class="id-badge">ID #<?php echo $id; ?></span>
        </div>
        <div class="page-sub">Update the question details below</div>
      </div>
    </div>

    <div class="form-card">
      <form method="POST">

        <!-- Subject -->
        <div class="form-section">
          <div class="section-label">Subject</div>
          <div class="field">
            <label for="subject_id">Select subject</label>
            <div class="select-wrap">
              <select name="subject_id" id="subject_id" required>
                <?php foreach ($subjects as $s): ?>
                  <option value="<?php echo $s['id']; ?>" <?php echo $s['id']==$question['subject_id']?'selected':''; ?>>
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
            <textarea name="question_text" id="question_text" required><?php echo htmlspecialchars($question['question_text']); ?></textarea>
          </div>
        </div>

        <!-- Options -->
        <div class="form-section">
          <div class="section-label">Answer options</div>
          <div class="options-grid">
            <?php for ($i = 1; $i <= 4; $i++): ?>
            <div class="field">
              <label class="with-key" for="option_<?php echo $i; ?>">
                <span class="option-key"><?php echo $opt_keys[$i-1]; ?></span>
                Option <?php echo $i; ?>
              </label>
              <input type="text" name="option_<?php echo $i; ?>" id="option_<?php echo $i; ?>"
                value="<?php echo htmlspecialchars($question["option_$i"]); ?>" required>
            </div>
            <?php endfor; ?>
          </div>
        </div>

        <!-- Correct answer -->
        <div class="form-section" style="margin-bottom:0;">
          <div class="section-label">Correct answer</div>
          <div class="field" style="max-width:260px;">
            <label for="correct_answer">Mark the correct option</label>
            <div class="correct-wrap">
              <select name="correct_answer" id="correct_answer" required>
                <?php for ($i = 1; $i <= 4; $i++): ?>
                  <option value="<?php echo $i; ?>" <?php echo $question['correct_answer']==$i?'selected':''; ?>>
                    Option <?php echo $i; ?> (<?php echo $opt_keys[$i-1]; ?>)
                  </option>
                <?php endfor; ?>
              </select>
            </div>
          </div>
        </div>

        <!-- Footer -->
        <div class="form-footer">
          <button type="submit" name="update_question" class="btn-submit">
            <i class="ti ti-device-floppy" style="font-size:15px;"></i> Save changes
          </button>
          <a class="btn btn-ghost" href="view_questions.php">
            <i class="ti ti-x" style="font-size:14px;"></i> Cancel
          </a>
        </div>

      </form>
    </div>
  </main>
</div>

</body>
</html>