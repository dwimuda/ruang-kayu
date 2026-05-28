<?php

// ── 1. DATABASE CONNECTION & INITIALIZATION ──────────────────────────────────
$dbPath = __DIR__ . '/../data/ruangkayu.db';
$dir    = dirname($dbPath);
if (!is_dir($dir)) {
  mkdir($dir, 0755, true);
}

try {
  $db = new PDO("sqlite:$dbPath", null, null, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  ]);
} catch (PDOException $e) {
  http_response_code(500);
  echo json_encode(['error' => 'Database error']);
  exit;
}

$db->exec("
  CREATE TABLE IF NOT EXISTS admin (
    id       INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT    NOT NULL UNIQUE,
    password TEXT    NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
  );

  CREATE TABLE IF NOT EXISTS products (
    id          INTEGER PRIMARY KEY AUTOINCREMENT,
    cat         TEXT    NOT NULL DEFAULT '',
    name        TEXT    NOT NULL,
    price       TEXT    NOT NULL DEFAULT 'Rp -',
    desc        TEXT    NOT NULL DEFAULT '',
    specs       TEXT    NOT NULL DEFAULT '[]',
    imgs        TEXT    NOT NULL DEFAULT '[]',
    created_at  DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at  DATETIME DEFAULT CURRENT_TIMESTAMP
  );

  CREATE TABLE IF NOT EXISTS leads (
    id         INTEGER PRIMARY KEY AUTOINCREMENT,
    product_id INTEGER,
    product_name TEXT,
    message    TEXT,
    source     TEXT    NOT NULL DEFAULT 'wa_click',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id)
  );

  CREATE TABLE IF NOT EXISTS settings (
    key TEXT PRIMARY KEY,
    value TEXT NOT NULL
  );
");

// Seed Admin
$check = $db->query("SELECT COUNT(*) as c FROM admin")->fetch();
if ((int)$check['c'] === 0) {
  $hash = password_hash('ruangkayu123', PASSWORD_DEFAULT);
  $db->prepare("INSERT INTO admin (username, password) VALUES (?, ?)")
     ->execute(['admin', $hash]);
}

// Seed Products (with realistic prices)
$checkProds = $db->query("SELECT COUNT(*) as c FROM products")->fetch();
if ((int)$checkProds['c'] === 0) {
  $defaults = [
    [
      'lemari', 
      'Lemari Kayu Minimalis', 
      'Rp 1.450.000',
      'Lemari penyimpanan minimalis dari kayu pilihan dengan serat alami yang estetis.',
      json_encode([['Bahan','Kayu Jati A+'],['Ukuran','80 x 60 x 35 cm'],['Finishing','Natural Clear Gloss / Satin']]),
      json_encode(['/img/Lemari_1.jpg','/img/Lemari_2.jpg'])
    ],
    [
      'rak', 
      'Rak Serbaguna Kayu Minimalis', 
      'Rp 950.000',
      'Rak TV sekaligus rak display dengan desain asimetris bertingkat.',
      json_encode([['Bahan','Kayu Sungkai'],['Finishing','Natural Walnut / Dark Brown']]),
      json_encode(['/img/Rak_1.jpg','/img/Rak_2.jpg'])
    ],
    [
      'meja,kursi', 
      'Set Kursi Santai', 
      'Rp 2.100.000',
      'Set meja dan kursi outdoor/taman model minimalis rustic.',
      json_encode([['Bahan','Kayu Palet'],['Finishing','Rustic White']]),
      json_encode(['/img/Set_Kursi_1.jpg','/img/Set_Kursi_2.jpg'])
    ],
    [
      'meja,rak', 
      'Meja Kerja dengan Rak Samping', 
      'Rp 1.850.000',
      'Meja kerja dengan rak 4 tingkat di samping, konsep industrial modern.',
      json_encode([['Bahan','Kayu Jati Belanda'],['Rangka','Besi finishing hitam doff']]),
      json_encode(['/img/Meja_Rak_1.jpg','/img/Meja_Rak_2.jpg'])
    ],
    [
      'rak', 
      'Lemari Laci Minimalis 8 Susun', 
      'Rp 3.200.000',
      'Lemari laci 8 susun dengan desain modern industrial.',
      json_encode([['Kapasitas','8 Laci Besar'],['Desain','Industrial minimalis']]),
      json_encode(['/img/Lemari_Laci.jpg'])
    ],
  ];
  $ins = $db->prepare("INSERT INTO products (cat, name, price, `desc`, specs, imgs) VALUES (?, ?, ?, ?, ?, ?)");
  foreach ($defaults as $p) {
    $ins->execute($p);
  }
}

// ── 2. CORS HEADERS ──────────────────────────────────────────────────────────
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
  http_response_code(204);
  exit;
}

// ── 3. HELPER FUNCTIONS ───────────────────────────────────────────────────────
function json($data, $code = 200) {
  http_response_code($code);
  echo json_encode($data);
  exit;
}

function err($msg, $code = 400) {
  json(['error' => $msg], $code);
}

function requireAuth($db) {
  $headers = getallheaders();
  $auth    = $headers['Authorization'] ?? $headers['authorization'] ?? '';
  if (!preg_match('/^Bearer\s+(.+)$/i', $auth, $m)) {
    err('Unauthorized', 401);
  }
  
  $token = $m[1];
  $parts = explode(':', base64_decode($token));
  if (count($parts) < 2) {
    err('Invalid token', 401);
  }
  
  $username = $parts[0];
  $stmt = $db->prepare("SELECT * FROM admin WHERE username = ?");
  $stmt->execute([$username]);
  $row = $stmt->fetch(PDO::FETCH_ASSOC);
  if (!$row || !password_verify($parts[1] ?? '', $row['password'])) {
    err('Invalid credentials', 401);
  }
  return $row;
}

// ── 4. ENDPOINT HANDLERS (CONTROLLERS) ───────────────────────────────────────

function handleAuth($db, $method) {
  if ($method !== 'POST') {
    err('Method not allowed', 405);
  }
  
  $body = json_decode(file_get_contents('php://input'), true);
  if (empty($body['username']) || empty($body['password'])) {
    err('Username & password required');
  }
  
  $stmt = $db->prepare("SELECT * FROM admin WHERE username = ?");
  $stmt->execute([$body['username']]);
  $row = $stmt->fetch(PDO::FETCH_ASSOC);
  if (!$row || !password_verify($body['password'], $row['password'])) {
    err('Invalid credentials', 401);
  }
  
  $token = base64_encode($body['username'] . ':' . $body['password']);
  json(['token' => $token, 'username' => $row['username']]);
}

function handleProducts($db, $method) {
  switch ($method) {
    case 'GET':
      $id = $_GET['id'] ?? null;
      if ($id) {
        $stmt = $db->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$id]);
        $p = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$p) {
          err('Product not found', 404);
        }
        $p['specs'] = json_decode($p['specs'], true);
        $p['imgs']  = json_decode($p['imgs'], true);
        json($p);
      }
      
      $search = $_GET['search'] ?? '';
      if ($search) {
        $q = $db->prepare("SELECT * FROM products WHERE name LIKE ? OR cat LIKE ? ORDER BY id ASC");
        $q->execute(["%$search%", "%$search%"]);
      } else {
        $q = $db->query("SELECT * FROM products ORDER BY id ASC");
      }
      $rows = $q->fetchAll();
      foreach ($rows as &$p) {
        $p['specs'] = json_decode($p['specs'], true);
        $p['imgs']  = json_decode($p['imgs'], true);
      }
      json($rows);
      break;

    case 'POST':
      requireAuth($db);
      $body = json_decode(file_get_contents('php://input'), true);
      if (empty($body['name'])) {
        err('Name required');
      }
      $cat   = is_array($body['cat']) ? implode(',', $body['cat']) : ($body['cat'] ?? '');
      $specs = is_array($body['specs']) ? json_encode($body['specs']) : '[]';
      $imgs  = is_array($body['imgs'])  ? json_encode($body['imgs'])  : '[]';
      $ins   = $db->prepare("INSERT INTO products (cat, name, price, `desc`, specs, imgs) VALUES (?,?,?,?,?,?)");
      $ins->execute([$cat, $body['name'], $body['price'] ?? 'Rp -', $body['desc'] ?? '', $specs, $imgs]);
      json(['id' => $db->lastInsertId()], 201);
      break;

    case 'PUT':
      requireAuth($db);
      $body = json_decode(file_get_contents('php://input'), true);
      if (empty($body['id'])) {
        err('ID required');
      }
      $cat   = is_array($body['cat']) ? implode(',', $body['cat']) : ($body['cat'] ?? '');
      $specs = is_array($body['specs']) ? json_encode($body['specs']) : ($body['specs'] ?? '[]');
      $imgs  = is_array($body['imgs'])  ? json_encode($body['imgs'])  : ($body['imgs']  ?? '[]');
      $upd   = $db->prepare("UPDATE products SET cat=?, name=?, price=?, `desc`=?, specs=?, imgs=?, updated_at=CURRENT_TIMESTAMP WHERE id=?");
      $upd->execute([$cat, $body['name'], $body['price'] ?? 'Rp -', $body['desc'] ?? '', $specs, $imgs, $body['id']]);
      json(['ok' => true]);
      break;

    case 'DELETE':
      requireAuth($db);
      $id = $_GET['id'] ?? null;
      if (!$id) {
        err('ID required');
      }
      $db->prepare("DELETE FROM products WHERE id = ?")->execute([$id]);
      json(['ok' => true]);
      break;

    default:
      err('Method not allowed', 405);
  }
}

function handleLeads($db, $method) {
  switch ($method) {
    case 'GET':
      requireAuth($db);
      $rows = $db->query("SELECT * FROM leads ORDER BY id DESC")->fetchAll();
      json($rows);
      break;

    case 'POST':
      $body = json_decode(file_get_contents('php://input'), true);
      $ins = $db->prepare("INSERT INTO leads (product_id, product_name, message, source) VALUES (?,?,?,?)");
      $ins->execute([
        $body['product_id'] ?? null,
        $body['product_name'] ?? '',
        $body['message'] ?? '',
        $body['source'] ?? 'wa_click',
      ]);
      json(['ok' => true], 201);
      break;

    default:
      err('Method not allowed', 405);
  }
}

function handleStats($db, $method) {
  if ($method !== 'GET') {
    err('Method not allowed', 405);
  }
  
  requireAuth($db);
  $prodCount  = $db->query("SELECT COUNT(*) as c FROM products")->fetch()['c'];
  $leadCount  = $db->query("SELECT COUNT(*) as c FROM leads")->fetch()['c'];
  $leadToday  = $db->query("SELECT COUNT(*) as c FROM leads WHERE date(created_at) = date('now')")->fetch()['c'];
  $recentLeads = $db->query("SELECT * FROM leads ORDER BY id DESC LIMIT 10")->fetchAll();
  
  json([
    'product_count' => (int)$prodCount,
    'lead_count'    => (int)$leadCount,
    'lead_today'    => (int)$leadToday,
    'recent_leads'  => $recentLeads,
  ]);
}

function handleUpload($db, $method) {
  if ($method !== 'POST') {
    err('Method not allowed', 405);
  }
  
  requireAuth($db);
  
  if (empty($_FILES['file'])) {
    err('No file uploaded');
  }
  
  $file = $_FILES['file'];
  if ($file['error'] !== UPLOAD_ERR_OK) {
    err('Upload error code: ' . $file['error']);
  }
  
  $mime = mime_content_type($file['tmp_name']);
  $allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
  if (!in_array($mime, $allowedMimes)) {
    err('Invalid file type. Only JPEG, PNG, GIF, and WEBP images are allowed.');
  }
  
  $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
  $allowedExts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
  if (!in_array($ext, $allowedExts)) {
    err('Invalid file extension.');
  }
  
  $uploadDir = dirname(__DIR__) . '/img/uploads';
  if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
  }
  
  $filename = uniqid('img_', true) . '.' . $ext;
  $target = $uploadDir . '/' . $filename;
  
  if (move_uploaded_file($file['tmp_name'], $target)) {
    json(['url' => '/img/uploads/' . $filename]);
  } else {
    err('Failed to save uploaded file');
  }
}

// ── 5. ROUTING & REQUEST DISPATCHING ─────────────────────────────────────────
$method  = $_SERVER['REQUEST_METHOD'];
$path    = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$base    = '/api/';
$endpoint = '';

if (strpos($path, $base) === 0) {
  $endpoint = trim(substr($path, strlen($base)), '/');
  if (strpos($endpoint, 'index.php') === 0) {
    $endpoint = trim(substr($endpoint, strlen('index.php')), '/');
  }
}

switch ($endpoint) {
  case 'auth':
  case 'auth/login':
    handleAuth($db, $method);
    break;
    
  case 'products':
    handleProducts($db, $method);
    break;
    
  case 'leads':
    handleLeads($db, $method);
    break;
    
  case 'stats':
    handleStats($db, $method);
    break;

  case 'upload':
    handleUpload($db, $method);
    break;
    
  case '':
    json(['status' => 'active', 'message' => 'Ruang Kayu API is running']);
    break;
    
  default:
    err('Endpoint not found', 404);
}