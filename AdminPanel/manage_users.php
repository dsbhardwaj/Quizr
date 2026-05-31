<?php
include("admin_check.php");
include("../connection.php");

$success = '';
$error   = '';

// DEACTIVATE
if (isset($_GET['deactivate']) && is_numeric($_GET['deactivate'])) {
    $id   = (int)$_GET['deactivate'];
    $stmt = mysqli_prepare($data, "UPDATE users SET status=0 WHERE id=?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    header("Location: manage_users.php?msg=deactivated"); exit();
}

// ACTIVATE
if (isset($_GET['activate']) && is_numeric($_GET['activate'])) {
    $id   = (int)$_GET['activate'];
    $stmt = mysqli_prepare($data, "UPDATE users SET status=1 WHERE id=?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    header("Location: manage_users.php?msg=activated"); exit();
}

if (isset($_GET['msg'])) {
    if ($_GET['msg'] === 'deactivated') $success = "User deactivated successfully.";
    if ($_GET['msg'] === 'activated')   $success = "User activated successfully.";
}

// Search
$search = isset($_GET['q']) ? trim($_GET['q']) : '';
$where  = '';
if ($search !== '') {
    $s     = mysqli_real_escape_string($data, $search);
    $where = "WHERE name LIKE '%$s%' OR email LIKE '%$s%'";
}

$users = [];
$res   = mysqli_query($data, "SELECT * FROM users $where ORDER BY id DESC");
while ($r = mysqli_fetch_assoc($res)) { $users[] = $r; }

$total       = count($users);
$active_count   = count(array_filter($users, fn($u) => $u['status'] == 1));
$inactive_count = $total - $active_count;

$admin_name = $_SESSION['admin_username'] ?? 'Admin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Users — Quizr Admin</title>
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
    .btn-amber{background:#FAEEDA;color:#633806;border-color:#FAC775;}.btn-amber:hover{background:#FAC775;}
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
    .stats-row{display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-bottom:20px;}
    .stat-card{background:#fff;border:.5px solid rgba(0,0,0,.08);border-radius:12px;padding:16px 18px;display:flex;align-items:center;gap:14px;}
    .stat-icon{width:36px;height:36px;border-radius:9px;display:flex;align-items:center;justify-content:center;font-size:17px;flex-shrink:0;}
    .stat-val{font-size:22px;font-weight:300;letter-spacing:-.02em;line-height:1;}
    .stat-lbl{font-size:12px;color:#6b6b6a;margin-top:3px;}
    .toolbar{display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin-bottom:16px;}
    .search-wrap{position:relative;flex:1;min-width:200px;max-width:320px;}
    .search-wrap i{position:absolute;left:10px;top:50%;transform:translateY(-50%);font-size:15px;color:#9b9b9a;pointer-events:none;}
    .search-wrap input{width:100%;padding:8px 12px 8px 32px;border:.5px solid rgba(0,0,0,.14);border-radius:8px;font-size:13px;font-family:inherit;background:#fff;color:#111110;outline:none;transition:border-color .15s,box-shadow .15s;}
    .search-wrap input:focus{border-color:#1D9E75;box-shadow:0 0 0 3px rgba(29,158,117,.12);}
    .search-wrap input::placeholder{color:#b0b0ae;}
    .filter-tab{padding:5px 14px;border-radius:100px;font-size:12px;font-weight:500;cursor:pointer;border:.5px solid rgba(0,0,0,.12);background:transparent;color:#6b6b6a;font-family:inherit;transition:all .12s;}
    .filter-tab:hover{background:#f0f0ef;color:#111110;}
    .filter-tab.active{background:#111110;color:#fff;border-color:#111110;}
    .result-count{font-size:13px;color:#6b6b6a;margin-left:auto;}
    .result-count span{font-weight:600;color:#111110;}
    .table-card{background:#fff;border:.5px solid rgba(0,0,0,.08);border-radius:14px;overflow:hidden;}
    table{width:100%;border-collapse:collapse;}
    th{text-align:left;font-size:11px;font-weight:600;color:#6b6b6a;text-transform:uppercase;letter-spacing:.07em;padding:10px 18px;border-bottom:.5px solid rgba(0,0,0,.06);}
    td{padding:12px 18px;font-size:13px;border-bottom:.5px solid rgba(0,0,0,.05);vertical-align:middle;}
    tr:last-child td{border-bottom:none;}
    tbody tr:hover td{background:#fafaf9;}
    .user-cell{display:flex;align-items:center;gap:10px;}
    .user-avatar{width:32px;height:32px;border-radius:50%;background:#E1F5EE;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:600;color:#0F6E56;flex-shrink:0;}
    .user-name{font-weight:500;font-size:13px;}
    .user-email{font-size:11px;color:#9b9b9a;margin-top:1px;}
    .role-badge{display:inline-flex;align-items:center;padding:3px 9px;border-radius:100px;font-size:11px;font-weight:600;}
    .role-admin{background:#E6F1FB;color:#0C447C;}
    .role-user{background:#f0f0ef;color:#6b6b6a;}
    .status-badge{display:inline-flex;align-items:center;gap:5px;padding:3px 10px;border-radius:100px;font-size:12px;font-weight:500;}
    .status-active{background:#EAF3DE;color:#27500A;}
    .status-inactive{background:#FCEBEB;color:#791F1F;}
    .status-dot{width:6px;height:6px;border-radius:50%;}
    .dot-green{background:#1D9E75;}
    .dot-red{background:#E24B4A;}
    .action-cell{display:flex;gap:6px;align-items:center;}
    .empty-state{padding:48px;text-align:center;color:#9b9b9a;font-size:14px;}
    .empty-state i{font-size:28px;display:block;margin-bottom:10px;color:#b0b0ae;}
    @media(max-width:900px){.stats-row{grid-template-columns:1fr 1fr;}}
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
    <a class="sidebar-item active" href="manage_users.php"><i class="ti ti-users" style="font-size:15px;"></i> Manage Users</a>
    <a class="sidebar-item" href="manage_subjects.php"><i class="ti ti-books" style="font-size:15px;"></i> Manage Subjects</a>
    <a class="sidebar-item" href="results.php"><i class="ti ti-chart-bar" style="font-size:15px;"></i> View Results</a>
    <div class="sidebar-label" style="margin-top:8px;">Account</div>
    <a class="sidebar-item danger" href="admin_logout.php"><i class="ti ti-logout" style="font-size:15px;"></i> Logout</a>
  </aside>
  <main class="main">
    <div class="page-header">
      <div><div class="page-title">Manage Users</div><div class="page-sub">View and control all registered accounts</div></div>
    </div>
    <?php if ($success): ?><div class="alert alert-success"><i class="ti ti-circle-check" style="font-size:16px;flex-shrink:0;"></i><?php echo htmlspecialchars($success); ?></div><?php endif; ?>
    <?php if ($error):   ?><div class="alert alert-error"><i class="ti ti-alert-circle" style="font-size:16px;flex-shrink:0;"></i><?php echo htmlspecialchars($error); ?></div><?php endif; ?>

    <div class="stats-row">
      <div class="stat-card">
        <div class="stat-icon" style="background:#E6F1FB;"><i class="ti ti-users" style="color:#0C447C;font-size:17px;"></i></div>
        <div><div class="stat-val"><?php echo $total; ?></div><div class="stat-lbl">Total users</div></div>
      </div>
      <div class="stat-card">
        <div class="stat-icon" style="background:#EAF3DE;"><i class="ti ti-user-check" style="color:#27500A;font-size:17px;"></i></div>
        <div><div class="stat-val"><?php echo $active_count; ?></div><div class="stat-lbl">Active</div></div>
      </div>
      <div class="stat-card">
        <div class="stat-icon" style="background:#FCEBEB;"><i class="ti ti-user-off" style="color:#791F1F;font-size:17px;"></i></div>
        <div><div class="stat-val"><?php echo $inactive_count; ?></div><div class="stat-lbl">Inactive</div></div>
      </div>
    </div>

    <form method="GET" action="manage_users.php">
      <div class="toolbar">
        <div class="search-wrap">
          <i class="ti ti-search"></i>
          <input type="text" name="q" placeholder="Search by name or email…" value="<?php echo htmlspecialchars($search); ?>">
        </div>
        <button type="submit" class="btn btn-ghost btn-sm"><i class="ti ti-search" style="font-size:13px;"></i> Search</button>
        <?php if ($search): ?><a class="btn btn-ghost btn-sm" href="manage_users.php"><i class="ti ti-x" style="font-size:13px;"></i> Clear</a><?php endif; ?>
        <div class="result-count"><span id="visibleCount"><?php echo $total; ?></span> user<?php echo $total!=1?'s':''; ?></div>
      </div>
    </form>

    <div class="table-card">
      <?php if (!empty($users)): ?>
      <table id="usersTable">
        <thead>
          <tr>
            <th style="width:44px;">ID</th>
            <th>User</th>
            <th style="width:90px;">Role</th>
            <th style="width:100px;">Status</th>
            <th style="width:140px;">Joined</th>
            <th style="width:120px;">Action</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($users as $u):
            $initials = strtoupper(substr($u['name'],0,1));
            $joined   = isset($u['created_at']) ? date('d M Y', strtotime($u['created_at'])) : '—';
          ?>
          <tr>
            <td style="font-size:12px;color:#9b9b9a;font-weight:500;">#<?php echo $u['id']; ?></td>
            <td>
              <div class="user-cell">
                <div class="user-avatar"><?php echo $initials; ?></div>
                <div>
                  <div class="user-name"><?php echo htmlspecialchars($u['name']); ?></div>
                  <div class="user-email"><?php echo htmlspecialchars($u['email']); ?></div>
                </div>
              </div>
            </td>
            <td>
              <span class="role-badge <?php echo $u['role']==='admin'?'role-admin':'role-user'; ?>">
                <?php echo ucfirst(htmlspecialchars($u['role'])); ?>
              </span>
            </td>
            <td>
              <?php if ($u['status'] == 1): ?>
                <span class="status-badge status-active"><div class="status-dot dot-green"></div>Active</span>
              <?php else: ?>
                <span class="status-badge status-inactive"><div class="status-dot dot-red"></div>Inactive</span>
              <?php endif; ?>
            </td>
            <td style="font-size:12px;color:#6b6b6a;"><?php echo $joined; ?></td>
            <td>
              <div class="action-cell">
                <?php if ($u['status'] == 1): ?>
                  <a class="btn btn-amber btn-sm"
                     href="manage_users.php?deactivate=<?php echo $u['id']; ?>"
                     onclick="return confirm('Deactivate <?php echo addslashes($u['name']); ?>?');">
                    <i class="ti ti-user-off" style="font-size:13px;"></i> Deactivate
                  </a>
                <?php else: ?>
                  <a class="btn btn-green btn-sm"
                     href="manage_users.php?activate=<?php echo $u['id']; ?>"
                     onclick="return confirm('Activate <?php echo addslashes($u['name']); ?>?');">
                    <i class="ti ti-user-check" style="font-size:13px;"></i> Activate
                  </a>
                <?php endif; ?>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      <?php else: ?>
        <div class="empty-state">
          <i class="ti ti-users-off"></i>
          <?php echo $search ? "No users match \"$search\"." : "No users registered yet."; ?>
        </div>
      <?php endif; ?>
    </div>
  </main>
</div>
</body>
</html>