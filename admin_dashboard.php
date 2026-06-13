<?php
// ── admin_dashboard.php ───────────────────────────────────────────────────────
// FLAMMA SKY Admin Dashboard — view, accept, reject, delete bookings
// ─────────────────────────────────────────────────────────────────────────────
session_start();
if (empty($_SESSION['FLAMMA_admin'])) {
    header('Location: admin_login.php');
    exit;
}

// LOGOUT
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: admin_login.php');
    exit;
}

// DB CONFIG
$db_host = 'sql309.infinityfree.com';
$db_name = 'if0_42076438_flammasky';
$db_user = 'if0_42076438';
$db_pass = '58OFMLOXZXEZQq';

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('
    <div style="font-family:sans-serif;padding:2rem;background:#1a0a00;color:#f09595;min-height:100vh">
      <h2 style="color:#c8732a;margin-bottom:1rem">Database Connection Failed</h2>
      <p>' . htmlspecialchars($e->getMessage()) . '</p>
      <p style="margin-top:1rem;color:#aaa">Check your InfinityFree control panel for the correct MySQL hostname.<br>
      Go to: InfinityFree CP &rarr; MySQL Databases &rarr; look for "MySQL Server"</p>
      <br><a href="admin_login.php" style="color:#c8732a">Back to Login</a>
    </div>');
}

// Auto-create table
$pdo->exec("CREATE TABLE IF NOT EXISTS bookings (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(100) NOT NULL,
    phone      VARCHAR(20)  NOT NULL,
    email      VARCHAR(100),
    date       DATE         NOT NULL,
    time       VARCHAR(20),
    guests     VARCHAR(20),
    seating    VARCHAR(30),
    occasion   VARCHAR(50),
    notes      TEXT,
    status     ENUM('pending','accepted','rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

// HANDLE ACTIONS
$flash = '';
if (isset($_GET['action'], $_GET['id'])) {
    $id  = (int)$_GET['id'];
    $act = $_GET['action'];
    if ($act === 'accept') {
        $pdo->prepare("UPDATE bookings SET status='accepted' WHERE id=?")->execute([$id]);
        $flash = 'ok:Booking #' . $id . ' has been accepted.';
    } elseif ($act === 'reject') {
        $pdo->prepare("UPDATE bookings SET status='rejected' WHERE id=?")->execute([$id]);
        $flash = 'warn:Booking #' . $id . ' has been rejected.';
    } elseif ($act === 'delete') {
        $pdo->prepare("DELETE FROM bookings WHERE id=?")->execute([$id]);
        $flash = 'danger:Booking #' . $id . ' has been deleted.';
    }
    $qs = http_build_query(['filter' => $_GET['filter'] ?? 'all', 'search' => $_GET['search'] ?? '', 'flash' => $flash]);
    header("Location: admin_dashboard.php?$qs");
    exit;
}
if (isset($_GET['flash'])) $flash = $_GET['flash'];

// FILTER + SEARCH
$filter = $_GET['filter'] ?? 'all';
$search = trim($_GET['search'] ?? '');
$where  = [];
$params = [];
if ($filter !== 'all') { $where[] = 'status = ?'; $params[] = $filter; }
if ($search !== '') {
    $where[] = '(name LIKE ? OR phone LIKE ? OR email LIKE ?)';
    $s = '%' . $search . '%';
    array_push($params, $s, $s, $s);
}
$sql  = 'SELECT * FROM bookings' . ($where ? ' WHERE ' . implode(' AND ', $where) : '') . ' ORDER BY created_at DESC';
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

// STATS
$st = $pdo->query("SELECT
    COUNT(*) total,
    SUM(status='pending')  pending,
    SUM(status='accepted') accepted,
    SUM(status='rejected') rejected,
    SUM(date = CURDATE())  today
FROM bookings")->fetch(PDO::FETCH_ASSOC);

// FLASH PARSE
[$ft, $fm] = $flash ? explode(':', $flash, 2) : ['', ''];

// HELPERS
function qurl($extra = []) {
    global $filter, $search;
    return '?' . http_build_query(array_merge(['filter' => $filter, 'search' => $search], $extra));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>FLAMMA SKY – Admin Dashboard</title>
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'Segoe UI', Arial, sans-serif; background: #f5f0ea; color: #1a0a00; min-height: 100vh; }

    /* TOPBAR */
    .topbar { background: #0f0500; display: flex; align-items: center; justify-content: space-between; padding: 0 2rem; height: 62px; border-bottom: 2px solid #c8732a; }
    .topbar .logo { color: #c8732a; font-size: 18px; letter-spacing: 2px; font-weight: 700; }
    .topbar .logo span { color: #888; font-size: 10px; display: block; letter-spacing: 2px; }
    .topbar-right { display: flex; gap: 10px; align-items: center; }
    .topbar-right a { color: #aaa; font-size: 12px; text-decoration: none; padding: 6px 14px; border: 1px solid #3a1a00; border-radius: 3px; letter-spacing: 0.5px; }
    .topbar-right a:hover { border-color: #c8732a; color: #c8732a; }

    /* LAYOUT */
    .wrap { max-width: 1280px; margin: 0 auto; padding: 2rem; }

    /* STATS */
    .stats { display: grid; grid-template-columns: repeat(5, 1fr); gap: 12px; margin-bottom: 1.8rem; }
    .stat { background: #fff; border: 1px solid #ead5bb; border-radius: 6px; padding: 1.3rem; text-align: center; }
    .stat .num { font-size: 30px; font-weight: 700; color: #c8732a; }
    .stat .lbl { font-size: 10px; color: #aaa; letter-spacing: 1px; margin-top: 5px; }
    .stat.s-today .num   { color: #2563eb; }
    .stat.s-pending .num { color: #d97706; }
    .stat.s-ok .num      { color: #16a34a; }
    .stat.s-rej .num     { color: #dc2626; }

    /* FLASH */
    .flash { padding: 12px 16px; border-radius: 4px; font-size: 13px; margin-bottom: 1.5rem; }
    .flash.ok     { background: #dcfce7; border: 1px solid #86efac; color: #166534; }
    .flash.warn   { background: #fef9c3; border: 1px solid #fde047; color: #854d0e; }
    .flash.danger { background: #fee2e2; border: 1px solid #fca5a5; color: #991b1b; }

    /* TOOLBAR */
    .toolbar { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; margin-bottom: 1.3rem; }
    .fb { background: #fff; border: 1px solid #ddd; padding: 7px 16px; font-size: 12px; cursor: pointer; border-radius: 3px; color: #555; text-decoration: none; letter-spacing: 0.5px; }
    .fb.active { background: #c8732a; color: #fff; border-color: #c8732a; }
    .fb:hover:not(.active) { border-color: #c8732a; color: #c8732a; }
    .search-form { display: flex; gap: 6px; margin-left: auto; }
    .search-form input { padding: 8px 12px; border: 1px solid #ddd; border-radius: 3px; font-size: 13px; width: 230px; outline: none; }
    .search-form input:focus { border-color: #c8732a; }
    .search-form button { background: #c8732a; color: #fff; border: none; padding: 8px 16px; border-radius: 3px; cursor: pointer; font-size: 13px; }

    /* TABLE */
    .tbl-wrap { background: #fff; border: 1px solid #ead5bb; border-radius: 6px; overflow-x: auto; }
    table { width: 100%; border-collapse: collapse; font-size: 13px; min-width: 900px; }
    thead th { background: #1a0a00; color: #c8732a; padding: 12px 14px; text-align: left; font-size: 11px; letter-spacing: 1px; white-space: nowrap; }
    tbody td { padding: 12px 14px; border-bottom: 1px solid #f0e8dc; vertical-align: middle; }
    tbody tr:last-child td { border-bottom: none; }
    tbody tr:hover td { background: #fdf8f2; }

    .badge { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; letter-spacing: 0.5px; white-space: nowrap; }
    .badge.pending  { background: #fef3c7; color: #92400e; }
    .badge.accepted { background: #d1fae5; color: #065f46; }
    .badge.rejected { background: #fee2e2; color: #991b1b; }

    .actions { display: flex; gap: 5px; flex-wrap: nowrap; }
    .btn-a { background: #16a34a; color: #fff; border: none; padding: 5px 11px; border-radius: 3px; font-size: 11px; cursor: pointer; text-decoration: none; white-space: nowrap; }
    .btn-r { background: #dc2626; color: #fff; border: none; padding: 5px 11px; border-radius: 3px; font-size: 11px; cursor: pointer; text-decoration: none; white-space: nowrap; }
    .btn-d { background: #6b7280; color: #fff; border: none; padding: 5px 11px; border-radius: 3px; font-size: 11px; cursor: pointer; text-decoration: none; white-space: nowrap; }
    .btn-a:hover { background: #15803d; }
    .btn-r:hover { background: #b91c1c; }
    .btn-d:hover { background: #4b5563; }

    .empty-row td { text-align: center; padding: 3rem; color: #aaa; font-size: 14px; }
    .notes-td { max-width: 160px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; color: #888; }

    @media(max-width: 900px) {
      .stats { grid-template-columns: repeat(2, 1fr); }
      .search-form input { width: 160px; }
    }
    @media(max-width: 600px) {
      .stats { grid-template-columns: 1fr 1fr; }
      .toolbar { flex-direction: column; align-items: flex-start; }
      .search-form { margin-left: 0; width: 100%; }
      .search-form input { flex: 1; width: auto; }
    }
  </style>
</head>
<body>

<div class="topbar">
  <div class="logo">FLAMMA SKY<span>ADMIN DASHBOARD</span></div>
  <div class="topbar-right">
    <a href="index.html" target="_blank">View Website</a>
    <a href="?logout=1" onclick="return confirm('Log out of admin panel?')">Logout</a>
  </div>
</div>

<div class="wrap">

  <!-- STATS -->
  <div class="stats">
    <div class="stat"><div class="num"><?= $st['total'] ?></div><div class="lbl">TOTAL BOOKINGS</div></div>
    <div class="stat s-today"><div class="num"><?= $st['today'] ?></div><div class="lbl">TODAY</div></div>
    <div class="stat s-pending"><div class="num"><?= $st['pending'] ?></div><div class="lbl">PENDING</div></div>
    <div class="stat s-ok"><div class="num"><?= $st['accepted'] ?></div><div class="lbl">ACCEPTED</div></div>
    <div class="stat s-rej"><div class="num"><?= $st['rejected'] ?></div><div class="lbl">REJECTED</div></div>
  </div>

  <!-- FLASH MESSAGE -->
  <?php if ($fm): ?>
    <div class="flash <?= htmlspecialchars($ft) ?>"><?= htmlspecialchars($fm) ?></div>
  <?php endif; ?>

  <!-- TOOLBAR -->
  <div class="toolbar">
    <?php foreach (['all' => 'All Bookings', 'pending' => 'Pending', 'accepted' => 'Accepted', 'rejected' => 'Rejected'] as $k => $v): ?>
      <a href="?filter=<?= $k ?>&search=<?= urlencode($search) ?>" class="fb <?= $filter === $k ? 'active' : '' ?>"><?= $v ?></a>
    <?php endforeach; ?>
    <form class="search-form" method="GET">
      <input type="hidden" name="filter" value="<?= htmlspecialchars($filter) ?>"/>
      <input type="text" name="search" placeholder="Search name / phone / email..." value="<?= htmlspecialchars($search) ?>"/>
      <button type="submit">Search</button>
    </form>
  </div>

  <!-- TABLE -->
  <div class="tbl-wrap">
    <table>
      <thead>
        <tr>
          <th>#</th>
          <th>Name</th>
          <th>Phone</th>
          <th>Email</th>
          <th>Date</th>
          <th>Time</th>
          <th>Guests</th>
          <th>Seating</th>
          <th>Occasion</th>
          <th>Notes</th>
          <th>Status</th>
          <th>Received</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
      <?php if (empty($bookings)): ?>
        <tr class="empty-row"><td colspan="13">No bookings found.</td></tr>
      <?php else: ?>
        <?php foreach ($bookings as $b): ?>
        <tr>
          <td><?= $b['id'] ?></td>
          <td><strong><?= htmlspecialchars($b['name']) ?></strong></td>
          <td><?= htmlspecialchars($b['phone']) ?></td>
          <td style="font-size:12px"><?= htmlspecialchars($b['email'] ?: '—') ?></td>
          <td><?= htmlspecialchars($b['date']) ?></td>
          <td><?= htmlspecialchars($b['time'] ?: '—') ?></td>
          <td><?= htmlspecialchars($b['guests'] ?: '—') ?></td>
          <td><?= htmlspecialchars($b['seating'] ?: '—') ?></td>
          <td><?= htmlspecialchars($b['occasion'] ?: '—') ?></td>
          <td class="notes-td" title="<?= htmlspecialchars($b['notes']) ?>"><?= htmlspecialchars($b['notes'] ?: '—') ?></td>
          <td><span class="badge <?= $b['status'] ?>"><?= ucfirst($b['status']) ?></span></td>
          <td style="font-size:12px;color:#888;white-space:nowrap"><?= date('d M y, H:i', strtotime($b['created_at'])) ?></td>
          <td>
            <div class="actions">
              <?php if ($b['status'] !== 'accepted'): ?>
                <a class="btn-a" href="<?= qurl(['action'=>'accept','id'=>$b['id']]) ?>"
                   onclick="return confirm('Accept booking for <?= addslashes(htmlspecialchars($b['name'])) ?>?')">Accept</a>
              <?php endif; ?>
              <?php if ($b['status'] !== 'rejected'): ?>
                <a class="btn-r" href="<?= qurl(['action'=>'reject','id'=>$b['id']]) ?>"
                   onclick="return confirm('Reject this booking?')">Reject</a>
              <?php endif; ?>
              <a class="btn-d" href="<?= qurl(['action'=>'delete','id'=>$b['id']]) ?>"
                 onclick="return confirm('Permanently delete booking #<?= $b['id'] ?>?')">Delete</a>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
      <?php endif; ?>
      </tbody>
    </table>
  </div>

</div>
</body>
</html>
