@REM <?php
@REM include 'db_connection.php';
@REM if (session_id() === '') { session_start(); }
@REM if (!isset($_SESSION['login_as']) || $_SESSION['login_as'] !== 'student') {
@REM     header('location:index.php'); exit();
@REM }

@REM /* -------------------------------------------------------------------------
@REM    1.  CONFIG  – table map & icons (4 dues + “all”)
@REM ---------------------------------------------------------------------------*/
@REM $tables = [
@REM   'account' => ['tbl'=>'account_section_due', 'label'=>'Account Section', 'icon'=>'bi-cash-coin'],
@REM   'student' => ['tbl'=>'student_section_due', 'label'=>'Student Section', 'icon'=>'bi-people-fill'],
@REM   'bus'     => ['tbl'=>'bus_due',             'label'=>'Bus Due',         'icon'=>'bi-bus-front-fill'],
@REM   'hostel'  => ['tbl'=>'hostel_due',          'label'=>'Hostel Due',      'icon'=>'bi-building-fill'],
@REM   'all'     => ['tbl'=>null,                  'label'=>'All Dues',        'icon'=>'bi-list-ul']
@REM ];

@REM $type  = strtolower($_GET['type'] ?? 'account');
@REM if (!isset($tables[$type])) $type = 'account';
@REM $isAll = ($type === 'all');

@REM /* -------------------------------------------------------------------------
@REM    2.  STUDENT DETAILS
@REM ---------------------------------------------------------------------------*/
@REM $stmt = $connection->prepare("SELECT grn, name FROM student WHERE grn = ?");
@REM $stmt->bind_param("s", $_SESSION['grn']);
@REM $stmt->execute();
@REM $stu = $stmt->get_result()->fetch_assoc();
@REM $stmt->close();

@REM /* -------------------------------------------------------------------------
@REM    3.  FETCH DUES
@REM ---------------------------------------------------------------------------*/
@REM $rows = [];
@REM if ($isAll) {
@REM     foreach ($tables as $k => $meta) {
@REM         if (!$meta['tbl']) continue;
@REM         $sql = "SELECT *, '$k' AS __type FROM {$meta['tbl']} WHERE grn = ?";
@REM         $stmt = $connection->prepare($sql);
@REM         if (!$stmt) die("Prepare failed for {$meta['tbl']}");
@REM         $stmt->bind_param("s", $_SESSION['grn']);
@REM         $stmt->execute();
@REM         foreach ($stmt->get_result()->fetch_all(MYSQLI_ASSOC) as $row) {
@REM             $rows[] = $row + ['__label' => $meta['label'], '__type' => $k];
@REM         }
@REM         $stmt->close();
@REM     }
@REM } else {
@REM     $tbl  = $tables[$type]['tbl'];
@REM     $stmt = $connection->prepare("SELECT * FROM {$tbl} WHERE grn = ? LIMIT 1");
@REM     $stmt->bind_param("s", $_SESSION['grn']);
@REM     $stmt->execute();
@REM     $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
@REM     $stmt->close();
@REM }

@REM /* -------------------------------------------------------------------------
@REM    3a.  SHOULD “FILL FORM” BE ENABLED?  (only relevant when $isAll)
@REM ---------------------------------------------------------------------------*/
@REM $fillEnabled = false;
@REM if ($isAll) {
@REM     $fillEnabled = !empty($rows);                     // need at least one row
@REM     foreach ($rows as $r) {
@REM         if (!in_array($r['status'] ?? 1, [0, 2])) {   // any status other than 0 or 2 disables it
@REM             $fillEnabled = false;
@REM             break;
@REM         }
@REM     }
@REM }

@REM /* -------------------------------------------------------------------------
@REM    4.  SUMMARY CARDS
@REM ---------------------------------------------------------------------------*/
@REM $agg = ['total' => 0, 'last' => 0, 'next' => null];
@REM foreach ($rows as $r) {
@REM     $agg['total'] += $r['due_amount'];
@REM     $agg['last']   = max($agg['last'], $r['last_payment'] ?? 0);
@REM     $d = $r['due_date'] ?? null;
@REM     if ($d && ($agg['next'] === null || $d < $agg['next'])) $agg['next'] = $d;
@REM }
@REM if ($agg['next'] === null) $agg['next'] = date('Y-m-d', strtotime('+30 days'));

@REM /* -------------------------------------------------------------------------
@REM    5.  PAYMENT HANDLER (unchanged)
@REM ---------------------------------------------------------------------------*/
@REM $msg = '';
@REM if (!$isAll && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['payment_amount'])) {
@REM     $pay = max(0, floatval($_POST['payment_amount']));
@REM     $row = $rows[0] ?? null;

@REM     if (!$row || $row['due_amount'] <= 0)
@REM         $msg = alert('warning', 'No due amount to pay.');
@REM     elseif ($pay <= 0)
@REM         $msg = alert('danger', 'Enter a valid amount.');
@REM     elseif ($pay > $row['due_amount'])
@REM         $msg = alert('warning', 'Amount exceeds due.');
@REM     else {
@REM         $newDue = $row['due_amount'] - $pay;
@REM         $stmt = $connection->prepare(
@REM             "UPDATE {$tables[$type]['tbl']}
@REM              SET due_amount = ?, last_payment = ?, due_date = ?, added_on = NOW(),
@REM                  status = IF(? = 0, 0, status)
@REM              WHERE grn = ?"
@REM         );
@REM         $nextDue = date('Y-m-d', strtotime('+30 days'));
@REM         $stmt->bind_param("dddss", $newDue, $pay, $nextDue, $newDue, $_SESSION['grn']);
@REM         $stmt->execute();
@REM         $stmt->close();
@REM         header("Location: student-dues.php?type=$type&paid=1"); exit();
@REM     }
@REM }
@REM if (isset($_GET['paid'])) $msg = alert('success', 'Dummy payment successful!');
@REM function alert($t,$m){return "<div class='alert alert-$t mt-3 animate-fadeIn'>".htmlspecialchars($m)."</div>";}
@REM ?>
@REM <!DOCTYPE html>
@REM <html lang="en">
@REM <head>
@REM <meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
@REM <title><?= htmlspecialchars($stu['name']).' – '.$tables[$type]['label'] ?></title>
@REM <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
@REM <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
@REM <style>
@REM /* --- original CSS preserved --- */
@REM @keyframes fadeIn{from{opacity:0;}to{opacity:1;}}
@REM @keyframes slideUp{from{transform:translateY(30px);opacity:0;}to{transform:translateY(0);opacity:1;}}
@REM @keyframes hoverBounce{0%{transform:scale(1);}50%{transform:scale(1.05);}100%{transform:scale(1);}}
@REM .animate-fadeIn{animation:fadeIn .8s ease-out both;}
@REM .animate-slideUp{animation:slideUp .8s ease-out both;}
@REM body{background:linear-gradient(135deg,#e0f2fe,#bae6fd);font-family:'Inter',sans-serif;padding:1rem;margin:0;color:#1e293b;min-height:100vh;display:flex;flex-direction:column;align-items:center;}
@REM .navbar{background:linear-gradient(90deg,#0ea5e9,#2563eb);box-shadow:0 4px 12px rgb(38 132 255/.6);margin-bottom:1.5rem;width:100%;max-width:1100px;border-radius:.5rem;}
@REM .navbar-brand{font-weight:700;font-size:1.3rem;color:#fff !important;display:flex;align-items:center;gap:.5rem;}
@REM .due-tabs{display:flex;justify-content:center;gap:.75rem;margin-bottom:1.5rem;flex-wrap:wrap;max-width:1100px;width:100%;}
@REM .due-tabs button{background:#e0f2fe;border:2px solid transparent;padding:.55rem 1.25rem;border-radius:.5rem;font-weight:700;color:#0284c7;cursor:pointer;transition:transform .3s;}
@REM .due-tabs button:hover{animation:hoverBounce .4s ease;background:#0284c7;color:#fff;}
@REM .due-tabs button.active{background:#0369a1;color:#fff;}
@REM .card-summary{display:flex;justify-content:center;gap:1.2rem;max-width:1100px;margin-bottom:1.5rem;flex-wrap:wrap;}
@REM .card-summary .card{background:#e0f2fe;padding:1.25rem 1.5rem;border-radius:.75rem;box-shadow:0 6px 18px rgb(0 132 255/.15);text-align:center;animation:slideUp .8s ease forwards;}
@REM .due-table{background:#fff;border-radius:.75rem;box-shadow:0 6px 20px rgb(0 0 0/.1);padding:1rem 1.25rem;width:100%;max-width:1100px;animation:fadeIn 1s ease-in-out;}
@REM .due-table table{width:100%;border-collapse:collapse;}
@REM .due-table th,.due-table td{padding:.75rem 1rem;border-bottom:1px solid #e2e8f0;}
@REM .status-paid{color:#16a34a;font-weight:700;}
@REM .status-pending{color:#dc2626;font-weight:700;}
@REM .payment-form{background:#fff;max-width:500px;margin:2rem auto;padding:1.5rem 2rem;border-radius:.75rem;box-shadow:0 10px 25px rgb(3 105 161/.3);font-weight:600;animation:slideUp .6s ease-out;}
@REM .payment-form input{padding:.5rem 1rem;border-radius:.5rem;border:2px solid #0284c7;font-size:1.1rem;width:100%;}
@REM .payment-form button{background:#0284c7;color:#fff;font-weight:700;padding:.75rem;border-radius:.5rem;font-size:1.1rem;margin-top:.5rem;width:100%;border:none;}
@REM </style>
@REM </head>
@REM <body>

@REM <nav class="navbar navbar-expand-lg px-3">
@REM   <a class="navbar-brand" href="#"><i class="bi bi-person-badge"></i>
@REM       <?= htmlspecialchars($stu['name']) ?> (GRN: <?= htmlspecialchars($stu['grn']) ?>)</a>
@REM   <div class="ms-auto">
@REM     <a href="logout.php" class="btn btn-danger">Logout <i class="bi bi-box-arrow-right"></i></a>
@REM   </div>
@REM </nav>

@REM <div class="due-tabs">
@REM   <?php foreach ($tables as $k=>$meta): ?>
@REM     <button class="<?= $type===$k?'active':'' ?>" onclick="location.href='?type=<?= $k ?>'">
@REM       <i class="bi <?= $meta['icon'] ?>"></i> <?= $meta['label'] ?>
@REM     </button>
@REM   <?php endforeach; ?>
@REM </div>

@REM <div class="card-summary">
@REM   <div class="card" style="flex:1;">
@REM     <h4>Total Due</h4>
@REM     <p style="font-size:1.6rem;color:#dc2626;">₹<?= number_format($agg['total'],2) ?></p>
@REM   </div>
@REM   <div class="card" style="flex:1;">
@REM     <h4>Last Payment</h4>
@REM     <p><?= $agg['last']?date('d M Y',strtotime($agg['last'])):'N/A' ?></p>
@REM   </div>
@REM   <div class="card" style="flex:1;">
@REM     <h4>Next Due Date</h4>
@REM     <p><?= date('d M Y',strtotime($agg['next'])) ?></p>
@REM   </div>
@REM </div>

@REM <div class="due-table">
@REM <?php if ($isAll): ?>
@REM     <p class="text-center mb-3">Select an individual tab to see detailed dues.</p>

@REM     <!-- Fill Form button (only in “All Dues”) -->
@REM     <div class="text-center">
@REM       <button class="btn btn-success" <?= $fillEnabled ? '' : 'disabled' ?>>Fill Form</button>
@REM     </div>

@REM <?php else: ?>
@REM     <?php if (empty($rows)): ?>
@REM         <p>No dues found for this category.</p>
@REM     <?php else: ?>
@REM     <table>
@REM       <thead>
@REM         <tr>
@REM           <th>Due Date</th>
@REM           <th>Due Amount (₹)</th>
@REM           <th>Last Payment (₹)</th>
@REM           <th>Status</th>
@REM         </tr>
@REM       </thead>
@REM       <tbody>
@REM         <?php foreach ($rows as $r): ?>
@REM         <tr>
@REM           <td><?= htmlspecialchars($r['due_date']??'-') ?></td>
@REM           <td><?= number_format($r['due_amount'],2) ?></td>
@REM           <td><?= number_format($r['last_payment']??0,2) ?></td>
@REM           <td class="<?= ($r['status']??1)==0?'status-paid':'status-pending' ?>">
@REM             <?= ($r['status']??1)==0?'Paid':'Pending' ?>
@REM           </td>
@REM         </tr>
@REM         <?php endforeach; ?>
@REM       </tbody>
@REM     </table>
@REM     <?php endif; ?>
@REM <?php endif; ?>
@REM </div>

@REM <?php if(!$isAll && !empty($rows) && ($rows[0]['due_amount']??0)>0): ?>
@REM <form class="payment-form" method="post">
@REM   <label for="payment_amount">Enter Payment Amount (₹):</label>
@REM   <input type="number" name="payment_amount" id="payment_amount"
@REM          min="1" max="<?= (int)$rows[0]['due_amount'] ?>" step="0.01" required>
@REM   <button type="submit">Pay Now</button>
@REM </form>
@REM <?php endif; ?>

@REM <?= $msg ?>

@REM </body>
@REM </html>