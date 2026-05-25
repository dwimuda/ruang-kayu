<?php
session_start();
if (empty($_SESSION['admin_token'])) {
  header('Location: /admin/login.php');
  exit;
}
$token = $_SESSION['admin_token'];

// Fetch leads directly from SQLite database to prevent HTTP request deadlock on single-threaded php -S dev server
$dbPath = dirname(__DIR__) . '/data/ruangkayu.db';
try {
  $db = new PDO("sqlite:$dbPath", null, null, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  ]);
  $leads = $db->query("SELECT * FROM leads ORDER BY id DESC")->fetchAll();
} catch (PDOException $e) {
  $leads = [];
}

// Pagination
$perPage = 20;
$page    = max(1, intval($_GET['page'] ?? 1));
$total   = count($leads);
$pages   = ceil($total / $perPage);
$slice   = array_slice($leads, ($page - 1) * $perPage, $perPage);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Leads — Ruang Kayu Admin</title>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,300;1,9..144,300&family=Jost:wght@300;400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/style.css">
</head>
<body>

<div class="admin-layout">
  <aside class="sidebar">
    <div class="sidebar-logo">Ruang <i>Kayu</i></div>
    <nav>
      <a href="dashboard.php" class="nav-link">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
        Dashboard
      </a>
      <a href="produk.php" class="nav-link">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M20 7H4a1 1 0 00-1 1v10a1 1 0 001 1h16a1 1 0 001-1V8a1 1 0 00-1-1z"/><path d="M16 12V7"/></svg>
        Produk
      </a>
      <a href="leads.php" class="nav-link active">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg>
        Leads
      </a>
    </nav>
    <div class="sidebar-logout">
      <form method="POST" action="logout.php">
        <button type="submit" class="logout-btn">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4M16 17l5-5-5-5M21 12H9"/></svg>
          Logout
        </button>
      </form>
    </div>
  </aside>

  <div class="main">
    <div class="topbar">
      <h1>Leads</h1>
      <div class="topbar-right" style="font-size:.82rem;color:var(--text2)">
        <?= count($leads) ?> total leads
      </div>
    </div>

    <div class="page">
      <div class="card">
        <div class="card-head">
          <h2>Daftar Leads</h2>
        </div>
        <div class="table-wrap">
          <?php if (count($slice)): ?>
          <table>
            <thead>
              <tr>
                <th>ID</th><th>Produk</th><th>Pesan</th><th>Sumber</th><th>Waktu</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($slice as $l): ?>
              <tr>
                <td style="color:var(--text2);font-size:.78rem">#<?= $l['id'] ?></td>
                <td class="td-name"><?= htmlspecialchars($l['product_name'] ?: '-') ?></td>
                <td style="max-width:260px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-size:.85rem"><?= htmlspecialchars($l['message'] ?: '-') ?></td>
                <td><span class="td-status s-<?= $l['source'] ?? 'wa' ?>"><?= strtoupper($l['source'] ?? 'WA') ?></span></td>
                <td class="td-date"><?= date('d M Y, H:i', strtotime($l['created_at'])) ?></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
          <?php else: ?>
          <div class="empty-state">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg>
            <p>Belum ada leads masuk</p>
          </div>
          <?php endif; ?>
        </div>

        <?php if ($pages > 1): ?>
        <div class="pagination">
          <?php if ($page > 1): ?><a href="?page=<?= $page-1 ?>" class="pg-btn">‹</a><?php endif; ?>
          <?php for ($i = 1; $i <= $pages; $i++): ?>
            <a href="?page=<?= $i ?>" class="pg-btn <?= $i === $page ? 'active' : '' ?>"><?= $i ?></a>
          <?php endfor; ?>
          <?php if ($page < $pages): ?><a href="?page=<?= $page+1 ?>" class="pg-btn">›</a><?php endif; ?>
        </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

</body>
</html>