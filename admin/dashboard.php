<?php
// Admin dashboard — stats + recent leads
session_start();
if (empty($_SESSION['admin_token'])) {
  header('Location: /admin/login.php');
  exit;
}
$username = $_SESSION['admin_user'];
$token    = $_SESSION['admin_token'];

// Fetch stats directly from SQLite database to prevent HTTP request deadlock on single-threaded php -S dev server
$dbPath = dirname(__DIR__) . '/data/ruangkayu.db';
try {
  $db = new PDO("sqlite:$dbPath", null, null, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  ]);
  $prodCount   = $db->query("SELECT COUNT(*) as c FROM products")->fetch()['c'];
  $leadCount   = $db->query("SELECT COUNT(*) as c FROM leads")->fetch()['c'];
  $leadToday   = $db->query("SELECT COUNT(*) as c FROM leads WHERE date(created_at) = date('now')")->fetch()['c'];
  $recentLeads = $db->query("SELECT * FROM leads ORDER BY id DESC LIMIT 10")->fetchAll();
  
  $stats = [
    'product_count' => (int)$prodCount,
    'lead_count'    => (int)$leadCount,
    'lead_today'    => (int)$leadToday,
    'recent_leads'  => $recentLeads,
  ];
} catch (PDOException $e) {
  $stats = null;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard — Ruang Kayu Admin</title>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,300;1,9..144,300&family=Jost:wght@300;400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/style.css">
</head>
<body>

<div class="admin-layout">
  <!-- Sidebar -->
  <aside class="sidebar">
    <div class="sidebar-logo">Ruang <i>Kayu</i></div>
    <nav>
      <a href="dashboard.php" class="nav-link active">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
        Dashboard
      </a>
      <a href="produk.php" class="nav-link">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M20 7H4a1 1 0 00-1 1v10a1 1 0 001 1h16a1 1 0 001-1V8a1 1 0 00-1-1z"/><path d="M16 12V7"/></svg>
        Produk
      </a>
      <a href="leads.php" class="nav-link">
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

  <!-- Main -->
  <div class="main">
    <div class="topbar">
      <button class="sidebar-toggle" type="button" onclick="toggleSidebar()" aria-label="Buka menu">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
      </button>
      <h1>Dashboard</h1>
      <div class="topbar-right">
        <div class="topbar-user"><span><?= substr($username, 0, 1) ?></span><?= htmlspecialchars($username) ?></div>
      </div>
    </div>

    <div class="page">
      <!-- Stats -->
      <div class="stats-grid">
        <div class="stat-card">
          <div class="stat-n"><?= $stats['product_count'] ?? '-' ?></div>
          <div class="stat-l">Total Produk</div>
        </div>
        <div class="stat-card orange">
          <div class="stat-n"><?= $stats['lead_count'] ?? '-' ?></div>
          <div class="stat-l">Total Leads</div>
        </div>
        <div class="stat-card green">
          <div class="stat-n"><?= $stats['lead_today'] ?? '-' ?></div>
          <div class="stat-l">Leads Hari Ini</div>
        </div>
        <div class="stat-card blue">
          <div class="stat-n"><?= $stats['product_count'] > 0 ? round(($stats['lead_count'] ?? 0) / max($stats['product_count'], 1), 1) : 0 ?></div>
          <div class="stat-l">Rata-rata Lead/Produk</div>
        </div>
      </div>

      <!-- Recent Leads -->
      <div class="card">
        <div class="card-head">
          <h2>Leads Terbaru</h2>
          <a href="leads.php" class="btn-g" style="font-size:.68rem;padding:.55rem 1.2rem;">Lihat Semua</a>
        </div>
        <div class="table-wrap">
          <?php if (!empty($stats['recent_leads'])): ?>
          <table>
            <thead>
              <tr>
                <th>Produk</th>
                <th>Pesan</th>
                <th>Sumber</th>
                <th>Waktu</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach (array_slice($stats['recent_leads'], 0, 8) as $l): ?>
              <tr>
                <td data-label="Produk" class="td-name"><?= htmlspecialchars($l['product_name'] ?: '-') ?></td>
                <td data-label="Pesan" style="max-width:280px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap"><?= htmlspecialchars($l['message'] ?: '-') ?></td>
                <td data-label="Sumber"><span class="td-status s-<?= $l['source'] ?? 'ok' ?>"><?= strtoupper($l['source'] ?? 'ok') ?></span></td>
                <td data-label="Waktu" class="td-date"><?= date('d M Y, H:i', strtotime($l['created_at'])) ?></td>
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
      </div>
    </div>
  </div>
</div>
<div class="sidebar-backdrop" onclick="toggleSidebar()"></div>
<script>
  function toggleSidebar() {
    document.body.classList.toggle('sidebar-open');
  }
</script>
</body>
</html>