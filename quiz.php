<?php
session_start();

include "connection.php";

if (isset($_SESSION['submitted'])) {
    echo "Quiz already submitted."; exit();
}

$subject = $_GET['subject'] ?? '';
$allowedSubjects = ['Aptitude', 'Logical Reasoning', 'Data Structure', 'DBMS', 'Web Development'];

if (!in_array($subject, $allowedSubjects)) {
    die("Invalid subject.");
}

if (!isset($_SESSION['quiz_active'])) {
    $_SESSION['quiz_active'] = true;
    $_SESSION['start_time']  = time();
    $_SESSION['duration']    = 600;
    $_SESSION['end_time']    = time() + 600;
} else {
    echo "Quiz already running."; exit();
}

$query = "SELECT id FROM subjects WHERE name = '$subject'";
$res   = mysqli_query($data, $query);

if (mysqli_num_rows($res) == 0) { die("Invalid subject."); }

$sub_row    = mysqli_fetch_assoc($res);
$subject_id = $sub_row['id'];

$sql    = "SELECT * FROM questions WHERE subject_id = $subject_id";
$result = mysqli_query($data, $sql);

$questions = [];
while ($r = mysqli_fetch_assoc($result)) {
    $questions[] = $r;
}

$total_q  = count($questions);
$user_id  = $_SESSION['user_id'] ?? $_GET['id'] ?? 0;

$_SESSION['submitted'] = true;
unset($_SESSION['quiz_active']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo htmlspecialchars($subject); ?> Quiz — Quizr</title>
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

    .nav-logo-dot { width: 8px; height: 8px; border-radius: 50%; background: #1D9E75; }

    .nav-center {
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 2px;
    }

    .nav-subject {
      font-size: 14px;
      font-weight: 500;
      color: #111110;
    }

    .nav-progress-track {
      width: 160px;
      height: 3px;
      background: #f0f0ef;
      border-radius: 2px;
      overflow: hidden;
    }

    .nav-progress-fill {
      height: 100%;
      background: #1D9E75;
      border-radius: 2px;
      transition: width 0.4s ease;
    }

    .timer-badge {
      display: flex;
      align-items: center;
      gap: 6px;
      padding: 6px 14px;
      border-radius: 8px;
      border: 0.5px solid rgba(0,0,0,0.14);
      background: #f0f0ef;
      font-size: 14px;
      font-weight: 500;
      font-variant-numeric: tabular-nums;
      transition: background 0.3s, border-color 0.3s, color 0.3s;
    }

    .timer-badge.urgent {
      background: #FCEBEB;
      border-color: #F7C1C1;
      color: #791F1F;
    }

    /* ── PAGE ── */
    .page {
      max-width: 720px;
      margin: 0 auto;
      padding: 32px 24px 80px;
      display: flex;
      flex-direction: column;
      gap: 16px;
    }

    /* ── QUESTION CARD ── */
    .question-card {
      background: #ffffff;
      border: 0.5px solid rgba(0,0,0,0.08);
      border-radius: 14px;
      padding: 24px 24px 20px;
      transition: border-color 0.15s;
      scroll-margin-top: 72px;
    }

    .question-card.answered { border-color: #1D9E75; }

    .q-header {
      display: flex;
      align-items: flex-start;
      gap: 14px;
      margin-bottom: 18px;
    }

    .q-num {
      width: 28px;
      height: 28px;
      border-radius: 8px;
      background: #f0f0ef;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 12px;
      font-weight: 600;
      color: #6b6b6a;
      flex-shrink: 0;
      margin-top: 1px;
    }

    .q-num.answered { background: #E1F5EE; color: #0F6E56; }

    .q-text {
      font-size: 15px;
      font-weight: 500;
      line-height: 1.5;
      color: #111110;
    }

    .options-list {
      display: flex;
      flex-direction: column;
      gap: 8px;
      padding-left: 42px;
    }

    .option-label {
      display: flex;
      align-items: center;
      gap: 12px;
      padding: 11px 14px;
      border: 0.5px solid rgba(0,0,0,0.10);
      border-radius: 10px;
      cursor: pointer;
      font-size: 14px;
      color: #111110;
      transition: all 0.12s;
      user-select: none;
    }

    .option-label:hover { background: #f5f6fa; border-color: rgba(0,0,0,0.18); }

    .option-label input[type="radio"] { display: none; }

    .option-label.selected {
      background: #E1F5EE;
      border-color: #1D9E75;
      color: #0F6E56;
      font-weight: 500;
    }

    .option-key {
      width: 24px;
      height: 24px;
      border-radius: 6px;
      border: 0.5px solid rgba(0,0,0,0.14);
      background: #fafaf9;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 11px;
      font-weight: 600;
      color: #9b9b9a;
      flex-shrink: 0;
    }

    .option-label.selected .option-key {
      background: #1D9E75;
      border-color: #1D9E75;
      color: #fff;
    }

    /* ── STICKY FOOTER ── */
    .quiz-footer {
      position: fixed;
      bottom: 0;
      left: 0;
      right: 0;
      background: #ffffff;
      border-top: 0.5px solid rgba(0,0,0,0.08);
      padding: 14px 24px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 16px;
      z-index: 100;
    }

    .footer-meta {
      font-size: 13px;
      color: #6b6b6a;
    }

    .footer-meta span { font-weight: 600; color: #111110; }

    .btn {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: 10px 20px;
      border-radius: 8px;
      font-size: 14px;
      font-weight: 500;
      cursor: pointer;
      font-family: inherit;
      transition: all 0.12s;
      border: none;
    }

    .btn-primary { background: #111110; color: #fff; }
    .btn-primary:hover { opacity: 0.85; }

    .btn-ghost {
      background: transparent;
      color: #111110;
      border: 0.5px solid rgba(0,0,0,0.14);
    }

    .btn-ghost:hover { background: #f0f0ef; }

    /* ── CONFIRM MODAL ── */
    .modal-backdrop {
      display: none;
      position: fixed;
      inset: 0;
      background: rgba(0,0,0,0.3);
      z-index: 200;
      align-items: center;
      justify-content: center;
    }

    .modal-backdrop.open { display: flex; }

    .modal {
      background: #fff;
      border-radius: 16px;
      padding: 28px;
      width: 100%;
      max-width: 380px;
      border: 0.5px solid rgba(0,0,0,0.10);
    }

    .modal h3 { font-size: 17px; font-weight: 600; margin-bottom: 8px; }
    .modal p  { font-size: 13px; color: #6b6b6a; margin-bottom: 24px; line-height: 1.5; }

    .modal-actions { display: flex; gap: 10px; justify-content: flex-end; }

    .unanswered-warning {
      background: #FAEEDA;
      border: 0.5px solid #FAC775;
      border-radius: 8px;
      padding: 10px 14px;
      font-size: 13px;
      color: #633806;
      margin-bottom: 16px;
      display: flex;
      align-items: center;
      gap: 8px;
    }
  </style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar">
  <a class="nav-logo" href="user.php">
    <div class="nav-logo-dot"></div>
    Quizr
  </a>
  <div class="nav-center">
    <div class="nav-subject"><?php echo htmlspecialchars($subject); ?></div>
    <div class="nav-progress-track">
      <div class="nav-progress-fill" id="navProgress" style="width:0%;"></div>
    </div>
  </div>
  <div class="timer-badge" id="timerBadge">
    <i class="ti ti-clock" style="font-size:15px;"></i>
    <span id="timer">10:00</span>
  </div>
</nav>

<!-- QUESTIONS -->
<form id="quizForm" method="POST"
  action="score.php?id=<?php echo urlencode($user_id); ?>&subject=<?php echo urlencode($subject); ?>">

  <div class="page">
    <?php foreach ($questions as $i => $q):
      $keys = ['A','B','C','D'];
      $opts = [
        $q['option_1'],
        $q['option_2'],
        $q['option_3'],
        $q['option_4'],
      ];
    ?>
    <div class="question-card" id="card-<?php echo $q['id']; ?>">
      <div class="q-header">
        <div class="q-num" id="num-<?php echo $q['id']; ?>"><?php echo $i+1; ?></div>
        <div class="q-text">Q<?php echo $i+1; ?>. <?php echo htmlspecialchars($q['question_text']); ?></div>
      </div>
      <div class="options-list">
        <?php foreach ($opts as $vi => $opt): ?>
        <label class="option-label" id="lbl-<?php echo $q['id'].'-'.($vi+1); ?>">
          <input
            type="radio"
            name="answers[<?php echo $q['id']; ?>]"
            value="<?php echo $vi+1; ?>"
            onchange="markAnswered(<?php echo $q['id']; ?>, <?php echo $vi+1; ?>)"
          >
          <div class="option-key"><?php echo $keys[$vi]; ?></div>
          <?php echo htmlspecialchars($opt); ?>
        </label>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endforeach; ?>
  </div>

  <!-- STICKY FOOTER -->
  <div class="quiz-footer">
    <div class="footer-meta">
      <span id="answeredCount">0</span> / <?php echo $total_q; ?> answered
    </div>
    <div style="display:flex; gap:8px;">
      <a class="btn btn-ghost" href="user.php">
        <i class="ti ti-x" style="font-size:14px;"></i> Exit
      </a>
      <button type="button" class="btn btn-primary" onclick="openConfirm()">
        <i class="ti ti-check" style="font-size:14px;"></i> Submit quiz
      </button>
    </div>
  </div>

</form>

<!-- CONFIRM MODAL -->
<div class="modal-backdrop" id="confirmModal">
  <div class="modal">
    <h3>Submit quiz?</h3>
    <div class="unanswered-warning" id="unansweredWarn" style="display:none;">
      <i class="ti ti-alert-triangle" style="font-size:15px; flex-shrink:0;"></i>
      <span id="unansweredMsg"></span>
    </div>
    <p>Once submitted you won't be able to change your answers.</p>
    <div class="modal-actions">
      <button type="button" class="btn btn-ghost" onclick="closeConfirm()">Go back</button>
      <button type="button" class="btn btn-primary" onclick="document.getElementById('quizForm').submit()">
        Submit
      </button>
    </div>
  </div>
</div>

<script>
  const TOTAL    = <?php echo $total_q; ?>;
  const DURATION = <?php echo $_SESSION['duration']; ?>;
  const START    = <?php echo $_SESSION['start_time']; ?>;

  let answered = {};

  function markAnswered(qid, val) {
    const wasAnswered = answered[qid] !== undefined;
    answered[qid] = val;

    // highlight selected option, clear siblings
    for (let i = 1; i <= 4; i++) {
      const lbl = document.getElementById('lbl-' + qid + '-' + i);
      if (lbl) lbl.classList.toggle('selected', i === val);
    }

    const card = document.getElementById('card-' + qid);
    const num  = document.getElementById('num-' + qid);
    if (card) card.classList.add('answered');
    if (num)  num.classList.add('answered');

    if (!wasAnswered) updateProgress();
  }

  function updateProgress() {
    const count = Object.keys(answered).length;
    document.getElementById('answeredCount').textContent = count;
    const pct = TOTAL > 0 ? (count / TOTAL) * 100 : 0;
    document.getElementById('navProgress').style.width = pct + '%';
  }

  function openConfirm() {
    const unanswered = TOTAL - Object.keys(answered).length;
    const warn = document.getElementById('unansweredWarn');
    if (unanswered > 0) {
      warn.style.display = 'flex';
      document.getElementById('unansweredMsg').textContent =
        unanswered + ' question' + (unanswered > 1 ? 's' : '') + ' left unanswered.';
    } else {
      warn.style.display = 'none';
    }
    document.getElementById('confirmModal').classList.add('open');
  }

  function closeConfirm() {
    document.getElementById('confirmModal').classList.remove('open');
  }

  // Timer
  function updateTimer() {
    const elapsed   = Math.floor(Date.now() / 1000) - START;
    const remaining = Math.max(DURATION - elapsed, 0);

    const m = Math.floor(remaining / 60);
    const s = remaining % 60;
    document.getElementById('timer').textContent =
      m + ':' + (s < 10 ? '0' : '') + s;

    const badge = document.getElementById('timerBadge');
    if (remaining <= 60) {
      badge.classList.add('urgent');
    } else {
      badge.classList.remove('urgent');
    }

    if (remaining <= 0) {
      document.getElementById('quizForm').submit();
    }
  }

  updateTimer();
  setInterval(updateTimer, 1000);
</script>

</body>
</html>