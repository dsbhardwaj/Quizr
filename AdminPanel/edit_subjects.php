<?php
include("admin_check.php");
include("../connection.php");

if (!isset($_GET['id'])) { header("Location: manage_subjects.php"); exit(); }

$id   = (int)$_GET['id'];
$stmt = mysqli_prepare($data, "SELECT * FROM subjects WHERE id=?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result  = mysqli_stmt_get_result($stmt);
$subject = mysqli_fetch_assoc($result);

if (!$subject) { header("Location: manage_subjects.php"); exit(); }

// Question count for this subject
$qcount = mysqli_fetch_assoc(mysqli_query($data, "SELECT COUNT(*) AS c FROM questions WHERE subject_id=$id"))['c'];

$error = '';

if (isset($_POST['update_subject'])) {
    $name = trim($_POST['name']);
    if ($name === '') {
        $error = "Subject name cannot be empty.";
    } else {
        $stmt2 = mysqli_prepare($data, "UPDATE subjects SET name=? WHERE id=?");
        mysqli_stmt_bind_param($stmt2, "si", $name, $id);
        if (mysqli_stmt_execute($stmt2)) {
            header("Location: manage_subjects.php?updated=1"); exit();
        } else {
            $error = "Failed to update subject.";
        }
    }
}

$admin_name = $_SESSION['admin_username'] ?? 'Admin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Subject — Quizr Admin</title>
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
    .content-wrap{display:grid;grid-template-columns:360px 1fr;gap:20px;align-items:start;max-width:780px;}
    .form-card{background:#fff;border:.5px solid rgba(0,0,0,.08);border-radius:14px;padding:26px;}
    .form-card-title{font-size:14px;font-weight:500;margin-bottom:18px;padding-bottom:12px;border-bottom:.5px solid rgba(0,0,0,.06);display:flex;align-items:center;gap:8px;}
    .alert-error{display:flex;align-items:center;gap:8px;border-radius:10px;padding:12px 16px;font-size:13px;margin-bottom:18px;background:#FCEBEB;border:.5px solid #F7C1C1;color:#791F1F;}
    .field{margin-bottom:18px;}
    .field label{display:block;font-size:12px;font-weight:500;color:#6b6b6a;text-transform:uppercase;letter-spacing:.06em;margin-bottom:6px;}
    .field input[type="text"]{width:100%;padding:10px 14px;border:.5px solid rgba(0,0,0,.14);border-radius:8px;font-size:14px;font-family:inherit;background:#fafaf9;color:#111110;outline:none;transition:border-color .15s,box-shadow .15s;}
    .field input:focus{border-color:#1D9E75;box-shadow:0 0 0 3px rgba(29,158,117,.12);background:#fff;}
    .form-footer{display:flex;align-items:center;gap:10px;flex-wrap:wrap;}
    .btn-submit{display:inline-flex;align-items:center;gap:6px;padding:11px 24px;border:none;border-radius:8px;background:#111110;color:#fff;font-size:14px;font-weight:500;font-family:inherit;cursor:pointer;transition:opacity .15s;}
    .btn-submit:hover{opacity:.85;}
    /* Info card */
    .info-card{background:#fff;border:.5px solid rgba(0,0,0,.08);border-radius:14px;overflow:hidden;}
    .info-card-header{padding:14px 18px 12px;border-bottom:.5px solid rgba(0,0,0,.06);font-size:14px;font-weight:500;}
    .info-row{display:flex;align-items:center;justify-content:space-between;padding:13px 18px;border-bottom:.5px solid rgba(0,0,0,.05);font-size:13px;}
    .info-row:last-child{border-bottom:none;}
    .info-key{color:#6b6b6a;}
    .info-val{font-weight:500;}
    .q-badge{display:inline-flex;align-items:center;gap:5px;padding:3px 10px;border-radius:100px;font-size:12px;font-weight:500;background:#f0f0ef;color:#6b6b6a;}
    @media(max-width:720px){.content-wrap{grid-template-columns:1fr;}}
    @media(max-width:640px){.layout{grid-template-columns:1fr;}.sidebar{display:none;}}
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
    <a class="sidebar-item" href="view_questions.php"><i class="ti ti-list-details" style="font-size:15px;"></i> View Questions</a>
    <div class="sidebar-label" style="margin-top:8px;">Management</div>
    <a class="sidebar-item" href="manage_users.php"><i class="ti ti-users" style="font-size:15px;"></i> Manage Users</a>
    <a class="sidebar-item active" href="manage_subjects.php"><i class="ti ti-books" style="font-size:15px;"></i> Manage Subjects</a>
    <a class="sidebar-item" href="results.php"><i class="ti ti-chart-bar" style="font-size:15px;"></i> View Results</a>
    <div class="sidebar-label" style="margin-top:8px;">Account</div>
    <a class="sidebar-item danger" href="admin_logout.php"><i class="ti ti-logout" style="font-size:15px;"></i> Logout</a>
  </aside>

  <main class="main">
    <div class="page-header">
      <a class="back-btn" href="manage_subjects.php"><i class="ti ti-arrow-left" style="font-size:14px;"></i> Back</a>
      <div>
        <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
          <div class="page-title">Edit Subject</div>
          <span class="id-badge">ID #<?php echo $id; ?></span>
        </div>
        <div class="page-sub">Rename this subject</div>
      </div>
    </div>

    <div class="content-wrap">

      <!-- Edit form -->
      <div class="form-card">
        <div class="form-card-title">
          <i class="ti ti-pencil" style="font-size:15px;color:#1D9E75;"></i> Rename subject
        </div>

        <?php if ($error): ?>
          <div class="alert-error">
            <i class="ti ti-alert-circle" style="font-size:16px;flex-shrink:0;"></i>
            <?php echo htmlspecialchars($error); ?>
          </div>
        <?php endif; ?>

        <form method="POST">
          <div class="field">
            <label for="name">Subject name</label>
            <input type="text" id="name" name="name"
              value="<?php echo htmlspecialchars($subject['name']); ?>"
              required autocomplete="off">
          </div>
          <div class="form-footer">
            <button type="submit" name="update_subject" class="btn-submit">
              <i class="ti ti-device-floppy" style="font-size:15px;"></i> Save changes
            </button>
            <a class="btn btn-ghost" href="manage_subjects.php">
              <i class="ti ti-x" style="font-size:14px;"></i> Cancel
            </a>
          </div>
        </form>
      </div>

      <!-- Info panel -->
      <div class="info-card">
        <div class="info-card-header">Subject info</div>
        <div class="info-row">
          <span class="info-key">ID</span>
          <span class="info-val">#<?php echo $subject['id']; ?></span>
        </div>
        <div class="info-row">
          <span class="info-key">Current name</span>
          <span class="info-val"><?php echo htmlspecialchars($subject['name']); ?></span>
        </div>
        <div class="info-row">
          <span class="info-key">Questions linked</span>
          <span class="q-badge">
            <i class="ti ti-help-circle" style="font-size:12px;"></i>
            <?php echo $qcount; ?> Q
          </span>
        </div>
        <div class="info-row">
          <span class="info-key">Linked questions</span>
          <a href="view_questions.php?subject=<?php echo $id; ?>" class="btn btn-ghost" style="padding:4px 10px;font-size:12px;">
            <i class="ti ti-list-details" style="font-size:13px;"></i> View all
          </a>
        </div>
      </div>

    </div>
  </main>
</div>

</body>
</html>