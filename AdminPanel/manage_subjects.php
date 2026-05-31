<?php
include("admin_check.php");
include("../connection.php");

$success = '';
$error   = '';

// ADD
if (isset($_POST['add_subject'])) {
    $name = trim(mysqli_real_escape_string($data, $_POST['name']));
    if ($name === '') {
        $error = "Subject name cannot be empty.";
    } else {
        $check = mysqli_query($data, "SELECT id FROM subjects WHERE name = '$name'");
        if (mysqli_num_rows($check) > 0) {
            $error = "A subject named \"$name\" already exists.";
        } else {
            $stmt = mysqli_prepare($data, "INSERT INTO subjects(name) VALUES(?)");
            mysqli_stmt_bind_param($stmt, "s", $name);
            mysqli_stmt_execute($stmt)
                ? $success = "Subject \"$name\" added successfully."
                : $error   = "Failed to add subject.";
        }
    }
}

// DELETE
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id     = (int)$_GET['delete'];
    $qcheck = mysqli_fetch_assoc(mysqli_query($data, "SELECT COUNT(*) AS c FROM questions WHERE subject_id = $id"));
    if ($qcheck['c'] > 0) {
        $error = "Cannot delete: {$qcheck['c']} question(s) are linked to this subject. Remove them first.";
    } else {
        $stmt = mysqli_prepare($data, "DELETE FROM subjects WHERE id=?");
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        header("Location: manage_subjects.php?deleted=1"); exit();
    }
}

if (isset($_GET['deleted'])) $success = "Subject deleted successfully.";

// Fetch with question counts
$subjects = [];
$res = mysqli_query($data, "
    SELECT s.*, COUNT(q.id) AS q_count
    FROM subjects s
    LEFT JOIN questions q ON q.subject_id = s.id
    GROUP BY s.id ORDER BY s.id DESC
");
while ($r = mysqli_fetch_assoc($res)) { $subjects[] = $r; }

$editing_id = isset($_GET['edit']) ? (int)$_GET['edit'] : 0;
$admin_name = $_SESSION['admin_username'] ?? 'Admin';

// INLINE EDIT SAVE
if (isset($_POST['edit_subject'])) {
    $eid  = (int)$_POST['edit_id'];
    $ename = trim(mysqli_real_escape_string($data, $_POST['edit_name']));
    if ($ename === '') { $error = "Name cannot be empty."; }
    else {
        mysqli_query($data, "UPDATE subjects SET name='$ename' WHERE id=$eid")
            ? header("Location: manage_subjects.php?updated=1") && exit()
            : $error = "Update failed.";
        header("Location: manage_subjects.php?updated=1"); exit();
    }
}
if (isset($_GET['updated'])) $success = "Subject updated successfully.";
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Subjects — Quizr Admin</title>
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
    .btn-green{background:#EAF3DE;color:#27500A;border-color:#C0DD97;}.btn-green:hover{background:#C0DD97;}
    .btn-sm{padding:5px 10px;font-size:12px;}
    .layout{display:grid;grid-template-columns:200px 1fr;min-height:calc(100vh - 56px);}
    .sidebar{background:#fff;border-right:.5px solid rgba(0,0,0,.08);padding:20px 12px;display:flex;flex-direction:column;gap:2px;}
    .sidebar-label{font-size:10px;font-weight:600;color:#9b9b9a;text-transform:uppercase;letter-spacing:.08em;padding:8px 10px 4px;}
    .sidebar-item{display:flex;align-items:center;gap:8px;padding:8px 10px;border-radius:8px;font-size:13px;color:#6b6b6a;text-decoration:none;border:none;background:transparent;font-family:inherit;width:100%;text-align:left;transition:all .12s;cursor:pointer;}
    .sidebar-item:hover{background:#f0f0ef;color:#111110;}.sidebar-item.active{background:#f0f0ef;color:#111110;font-weight:500;}
    .sidebar-item.danger{color:#A32D2D;}.sidebar-item.danger:hover{background:#FCEBEB;}
    .main{padding:28px 28px 56px;overflow:auto;}
    .page-header{display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:24px;}
    .page-title{font-size:20px;font-weight:600;letter-spacing:-.02em;}
    .page-sub{font-size:13px;color:#6b6b6a;margin-top:3px;}
    .alert{display:flex;align-items:center;gap:8px;border-radius:10px;padding:12px 16px;font-size:13px;margin-bottom:20px;}
    .alert-success{background:#EAF3DE;border:.5px solid #C0DD97;color:#27500A;}
    .alert-error{background:#FCEBEB;border:.5px solid #F7C1C1;color:#791F1F;}
    .two-col{display:grid;grid-template-columns:320px 1fr;gap:20px;align-items:start;}
    .form-card{background:#fff;border:.5px solid rgba(0,0,0,.08);border-radius:14px;padding:22px;}
    .form-card-title{font-size:14px;font-weight:500;margin-bottom:16px;padding-bottom:12px;border-bottom:.5px solid rgba(0,0,0,.06);display:flex;align-items:center;gap:8px;}
    .field{margin-bottom:14px;}
    .field label{display:block;font-size:12px;font-weight:500;color:#6b6b6a;text-transform:uppercase;letter-spacing:.06em;margin-bottom:6px;}
    .field input[type="text"]{width:100%;padding:9px 12px;border:.5px solid rgba(0,0,0,.14);border-radius:8px;font-size:14px;font-family:inherit;background:#fafaf9;color:#111110;outline:none;transition:border-color .15s,box-shadow .15s;}
    .field input:focus{border-color:#1D9E75;box-shadow:0 0 0 3px rgba(29,158,117,.12);background:#fff;}
    .field input::placeholder{color:#b0b0ae;}
    .btn-submit{display:flex;align-items:center;justify-content:center;gap:6px;padding:10px 20px;border:none;border-radius:8px;background:#111110;color:#fff;font-size:14px;font-weight:500;font-family:inherit;cursor:pointer;transition:opacity .15s;width:100%;}
    .btn-submit:hover{opacity:.85;}
    .table-card{background:#fff;border:.5px solid rgba(0,0,0,.08);border-radius:14px;overflow:hidden;}
    .table-header{display:flex;align-items:center;justify-content:space-between;padding:14px 18px 12px;border-bottom:.5px solid rgba(0,0,0,.06);}
    .table-title{font-size:14px;font-weight:500;}
    .table-count{font-size:12px;color:#6b6b6a;}
    table{width:100%;border-collapse:collapse;}
    th{text-align:left;font-size:11px;font-weight:600;color:#6b6b6a;text-transform:uppercase;letter-spacing:.07em;padding:10px 18px;border-bottom:.5px solid rgba(0,0,0,.06);}
    td{padding:11px 18px;font-size:13px;border-bottom:.5px solid rgba(0,0,0,.05);vertical-align:middle;}
    tr:last-child td{border-bottom:none;}
    tbody tr:hover td{background:#fafaf9;}
    .q-badge{display:inline-flex;align-items:center;gap:5px;padding:3px 10px;border-radius:100px;font-size:12px;font-weight:500;background:#f0f0ef;color:#6b6b6a;}
    .edit-input{width:100%;padding:6px 10px;border:.5px solid #1D9E75;border-radius:6px;font-size:13px;font-family:inherit;background:#fff;color:#111110;outline:none;box-shadow:0 0 0 3px rgba(29,158,117,.10);}
    .action-cell{display:flex;gap:6px;align-items:center;}
    .empty-state{padding:40px;text-align:center;color:#9b9b9a;font-size:14px;}
    .empty-state i{font-size:26px;display:block;margin-bottom:8px;color:#b0b0ae;}
    @media(max-width:860px){.two-col{grid-template-columns:1fr;}}
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
      <div><div class="page-title">Manage Subjects</div><div class="page-sub">Add, rename or remove quiz subjects</div></div>
    </div>
    <?php if ($success): ?><div class="alert alert-success"><i class="ti ti-circle-check" style="font-size:16px;flex-shrink:0;"></i><?php echo htmlspecialchars($success); ?></div><?php endif; ?>
    <?php if ($error):   ?><div class="alert alert-error"><i class="ti ti-alert-circle" style="font-size:16px;flex-shrink:0;"></i><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
    <div class="two-col">
      <div class="form-card">
        <div class="form-card-title"><i class="ti ti-circle-plus" style="font-size:15px;color:#1D9E75;"></i> Add new subject</div>
        <form method="POST">
          <div class="field">
            <label for="name">Subject name</label>
            <input type="text" id="name" name="name" placeholder="e.g. Operating Systems" required autocomplete="off">
          </div>
          <button type="submit" name="add_subject" class="btn-submit"><i class="ti ti-circle-plus" style="font-size:15px;"></i> Add Subject</button>
        </form>
      </div>
      <div class="table-card">
        <div class="table-header">
          <div class="table-title">All subjects</div>
          <div class="table-count"><?php echo count($subjects); ?> subject<?php echo count($subjects)!=1?'s':''; ?></div>
        </div>
        <?php if (!empty($subjects)): ?>
        <table>
          <thead><tr><th style="width:44px;">ID</th><th>Name</th><th style="width:110px;">Questions</th><th style="width:140px;">Actions</th></tr></thead>
          <tbody>
            <?php foreach ($subjects as $s): ?>
            <tr>
              <td style="font-size:12px;color:#9b9b9a;font-weight:500;">#<?php echo $s['id']; ?></td>
              <td>
                <?php if ($editing_id===(int)$s['id']): ?>
                  <form method="POST" style="display:flex;gap:6px;align-items:center;">
                    <input type="hidden" name="edit_id" value="<?php echo $s['id']; ?>">
                    <input type="text" name="edit_name" class="edit-input" value="<?php echo htmlspecialchars($s['name']); ?>" required>
                    <button type="submit" name="edit_subject" class="btn btn-green btn-sm"><i class="ti ti-check" style="font-size:13px;"></i></button>
                    <a class="btn btn-ghost btn-sm" href="manage_subjects.php"><i class="ti ti-x" style="font-size:13px;"></i></a>
                  </form>
                <?php else: ?>
                  <span style="font-weight:500;"><?php echo htmlspecialchars($s['name']); ?></span>
                <?php endif; ?>
              </td>
              <td><span class="q-badge"><i class="ti ti-help-circle" style="font-size:12px;"></i><?php echo $s['q_count']; ?> Q</span></td>
              <td>
                <?php if ($editing_id!==(int)$s['id']): ?>
                <div class="action-cell">
                  <a class="btn btn-ghost btn-sm" href="manage_subjects.php?edit=<?php echo $s['id']; ?>"><i class="ti ti-pencil" style="font-size:13px;"></i> Rename</a>
                  <a class="btn btn-danger btn-sm" href="manage_subjects.php?delete=<?php echo $s['id']; ?>" onclick="return confirm('Delete \'<?php echo addslashes($s['name']); ?>\'?');"><i class="ti ti-trash" style="font-size:13px;"></i></a>
                </div>
                <?php endif; ?>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
        <?php else: ?>
          <div class="empty-state"><i class="ti ti-books"></i>No subjects yet — add the first one.</div>
        <?php endif; ?>
      </div>
    </div>
  </main>
</div>
</body>
</html>