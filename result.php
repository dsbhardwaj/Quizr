<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); exit();
}

$score   = isset($_GET['score'])   ? (int)$_GET['score']          : 0;
$total   = isset($_GET['total'])   ? (int)$_GET['total']          : 0;
$subject = isset($_GET['subject']) ? trim($_GET['subject'])        : 'Quiz';
$user_id = $_SESSION['user_id'];

$percentage = $total > 0 ? round(($score / $total) * 100) : 0;

if      ($percentage >= 80) { $grade = 'Excellent';  $grade_bg = '#EAF3DE'; $grade_color = '#27500A'; $emoji = '🏆'; $msg = 'Outstanding work!'; }
elseif  ($percentage >= 60) { $grade = 'Good';       $grade_bg = '#E6F1FB'; $grade_color = '#0C447C'; $emoji = '👏'; $msg = 'Solid effort!'; }
elseif  ($percentage >= 40) { $grade = 'Average';    $grade_bg = '#FAEEDA'; $grade_color = '#633806'; $emoji = '📈'; $msg = 'Room to improve.'; }
else                        { $grade = 'Needs work'; $grade_bg = '#FCEBEB'; $grade_color = '#791F1F'; $emoji = '💪'; $msg = 'Keep practising!'; }

$wrong   = $total - $score;
$stroke  = $percentage >= 60 ? '#1D9E75' : ($percentage >= 40 ? '#EF9F27' : '#E24B4A');
$circ    = round(2 * M_PI * 54, 2);
$offset  = round($circ - ($percentage / 100) * $circ, 2);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Result — <?php echo htmlspecialchars($subject); ?> — Quizr</title>
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

    .btn {
      display: inline-flex; align-items: center; gap: 6px;
      padding: 8px 16px; border-radius: 8px;
      font-size: 13px; font-weight: 500;
      cursor: pointer; font-family: inherit;
      transition: all 0.12s; text-decoration: none;
      border: 0.5px solid rgba(0,0,0,0.14);
    }

    .btn-ghost { background: transparent; color: #111110; }
    .btn-ghost:hover { background: #f0f0ef; }
    .btn-primary { background: #111110; color: #fff; border-color: #111110; }
    .btn-primary:hover { opacity: 0.85; }

    /* ── PAGE ── */
    .page {
      max-width: 520px; margin: 0 auto;
      padding: 48px 24px 72px;
      display: flex; flex-direction: column;
      align-items: center; gap: 24px;
    }

    /* ── SUBJECT PILL ── */
    .subject-pill {
      display: inline-flex; align-items: center; gap: 6px;
      background: #f0f0ef; border: 0.5px solid rgba(0,0,0,0.10);
      border-radius: 100px; padding: 5px 14px;
      font-size: 12px; font-weight: 600;
      color: #6b6b6a; text-transform: uppercase; letter-spacing: 0.06em;
    }

    /* ── SCORE RING ── */
    .ring-wrap { position: relative; width: 150px; height: 150px; }
    .ring-wrap svg { width: 150px; height: 150px; }

    .ring-center {
      position: absolute; top: 50%; left: 50%;
      transform: translate(-50%, -50%);
      text-align: center;
    }

    .ring-pct {
      font-size: 34px; font-weight: 300;
      letter-spacing: -0.03em; line-height: 1;
    }

    .ring-sub { font-size: 11px; color: #6b6b6a; margin-top: 3px; }

    /* ── GRADE BADGE ── */
    .grade-badge {
      display: inline-flex; align-items: center; gap: 6px;
      padding: 5px 16px; border-radius: 100px;
      font-size: 13px; font-weight: 600;
    }

    /* ── HEADLINE ── */
    .headline { text-align: center; }
    .headline h2 { font-size: 24px; font-weight: 600; letter-spacing: -0.02em; }
    .headline p  { font-size: 14px; color: #6b6b6a; margin-top: 6px; }

    /* ── BREAKDOWN ── */
    .breakdown {
      display: grid; grid-template-columns: repeat(3, 1fr);
      gap: 12px; width: 100%;
    }

    .bd-card {
      background: #ffffff; border: 0.5px solid rgba(0,0,0,0.08);
      border-radius: 12px; padding: 16px;
      display: flex; flex-direction: column; align-items: center; gap: 4px;
    }

    .bd-icon {
      width: 32px; height: 32px; border-radius: 8px;
      display: flex; align-items: center; justify-content: center;
      font-size: 15px; margin-bottom: 4px;
    }

    .bd-val { font-size: 24px; font-weight: 300; letter-spacing: -0.02em; }
    .bd-lbl { font-size: 11px; color: #6b6b6a; }

    /* ── PROGRESS BAR CARD ── */
    .progress-card {
      width: 100%; background: #ffffff;
      border: 0.5px solid rgba(0,0,0,0.08);
      border-radius: 12px; padding: 20px;
    }

    .progress-label {
      display: flex; justify-content: space-between;
      font-size: 13px; margin-bottom: 10px;
    }

    .progress-label span:first-child { font-weight: 500; }
    .progress-label span:last-child  { color: #6b6b6a; }

    .progress-track {
      height: 8px; background: #f0f0ef;
      border-radius: 4px; overflow: hidden;
    }

    .progress-fill {
      height: 100%; border-radius: 4px;
      transition: width 1s ease;
    }

    /* ── ACTIONS ── */
    .actions { display: flex; gap: 10px; flex-wrap: wrap; justify-content: center; }

    @media (max-width: 420px) {
      .breakdown { grid-template-columns: 1fr 1fr; }
    }
  </style>
</head>
<body>

<nav class="navbar">
  <a class="nav-logo" href="user.php">
    <div class="nav-logo-dot"></div>
    Quizr
  </a>
  <div style="display:flex; gap:8px;">
    <a class="btn btn-ghost" href="scoreboard.php?id=<?php echo $user_id; ?>">
      <i class="ti ti-trophy" style="font-size:14px;"></i> Scoreboard
    </a>
    <a class="btn btn-ghost" href="user.php">
      <i class="ti ti-home" style="font-size:14px;"></i> Dashboard
    </a>
  </div>
</nav>

<div class="page">

  <!-- Subject label -->
  <div class="subject-pill">
    <i class="ti ti-book" style="font-size:13px;"></i>
    <?php echo htmlspecialchars($subject); ?>
  </div>

  <!-- Score ring -->
  <div class="ring-wrap">
    <svg viewBox="0 0 130 130" fill="none">
      <circle cx="65" cy="65" r="54" stroke="#f0f0ef" stroke-width="8"/>
      <circle cx="65" cy="65" r="54"
        stroke="<?php echo $stroke; ?>"
        stroke-width="8"
        stroke-linecap="round"
        stroke-dasharray="<?php echo $circ; ?>"
        stroke-dashoffset="<?php echo $circ; ?>"
        transform="rotate(-90 65 65)"
        id="scoreArc"/>
    </svg>
    <div class="ring-center">
      <div class="ring-pct"><?php echo $percentage; ?>%</div>
      <div class="ring-sub">score</div>
    </div>
  </div>

  <!-- Grade -->
  <span class="grade-badge"
    style="background:<?php echo $grade_bg; ?>; color:<?php echo $grade_color; ?>;">
    <?php echo $emoji; ?> <?php echo $grade; ?>
  </span>

  <!-- Headline -->
  <div class="headline">
    <h2><?php echo $msg; ?></h2>
    <p>
      You scored <strong><?php echo $score; ?></strong> out of
      <strong><?php echo $total; ?></strong> on
      <?php echo htmlspecialchars($subject); ?>
    </p>
  </div>

  <!-- Breakdown -->
  <div class="breakdown">
    <div class="bd-card">
      <div class="bd-icon" style="background:#E1F5EE;">
        <i class="ti ti-check" style="color:#0F6E56;"></i>
      </div>
      <div class="bd-val" style="color:#1D9E75;"><?php echo $score; ?></div>
      <div class="bd-lbl">Correct</div>
    </div>
    <div class="bd-card">
      <div class="bd-icon" style="background:#FCEBEB;">
        <i class="ti ti-x" style="color:#A32D2D;"></i>
      </div>
      <div class="bd-val" style="color:#E24B4A;"><?php echo $wrong; ?></div>
      <div class="bd-lbl">Incorrect</div>
    </div>
    <div class="bd-card">
      <div class="bd-icon" style="background:#f0f0ef;">
        <i class="ti ti-list" style="color:#6b6b6a;"></i>
      </div>
      <div class="bd-val"><?php echo $total; ?></div>
      <div class="bd-lbl">Total</div>
    </div>
  </div>

  <!-- Progress bar -->
  <div class="progress-card">
    <div class="progress-label">
      <span>Accuracy</span>
      <span><?php echo $score; ?> / <?php echo $total; ?> correct</span>
    </div>
    <div class="progress-track">
      <div class="progress-fill" id="progressBar"
        style="width:0%; background:<?php echo $stroke; ?>;"></div>
    </div>
  </div>

  <!-- Actions -->
  <div class="actions">
    <a class="btn btn-ghost"
       href="quiz.php?subject=<?php echo urlencode($subject); ?>&id=<?php echo $user_id; ?>">
      <i class="ti ti-refresh" style="font-size:14px;"></i> Retake
    </a>
    <a class="btn btn-ghost" href="scoreboard.php?id=<?php echo $user_id; ?>">
      <i class="ti ti-trophy" style="font-size:14px;"></i> Scoreboard
    </a>
    <a class="btn btn-primary" href="user.php">
      <i class="ti ti-home" style="font-size:14px;"></i> Dashboard
    </a>
  </div>

</div>

<script>
  // Animate ring
  const arc = document.getElementById('scoreArc');
  const circ = <?php echo $circ; ?>;
  const offset = <?php echo $offset; ?>;
  setTimeout(() => {
    arc.style.transition = 'stroke-dashoffset 1s ease';
    arc.style.strokeDashoffset = offset;
  }, 100);

  // Animate progress bar
  setTimeout(() => {
    document.getElementById('progressBar').style.width = '<?php echo $percentage; ?>%';
  }, 200);
</script>

</body>
</html>