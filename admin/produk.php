<?php
session_start();
if (empty($_SESSION['admin_token'])) {
  header('Location: /admin/login.php');
  exit;
}
$token = $_SESSION['admin_token'];
$apiBase = '';

// Fetch products directly from SQLite database to prevent HTTP request deadlock on single-threaded php -S dev server
$dbPath = dirname(__DIR__) . '/data/ruangkayu.db';
try {
  $db = new PDO("sqlite:$dbPath", null, null, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  ]);
  $products = $db->query("SELECT * FROM products ORDER BY id ASC")->fetchAll();
  foreach ($products as &$p) {
    $p['specs'] = json_decode($p['specs'], true);
    $p['imgs']  = json_decode($p['imgs'], true);
  }
} catch (PDOException $e) {
  $products = [];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Produk — Ruang Kayu Admin</title>
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
      <a href="produk.php" class="nav-link active">
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

  <div class="main">
    <div class="topbar">
      <button class="sidebar-toggle" type="button" onclick="toggleSidebar()" aria-label="Buka menu">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
      </button>
      <h1>Kelola Produk</h1>
      <div class="topbar-right">
        <button class="btn-p" onclick="openAddModal()" style="font-size:.68rem;padding:.6rem 1.5rem;">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
          Tambah Produk
        </button>
      </div>
    </div>

    <div class="page">
      <div class="card">
        <div class="card-head">
          <h2><span id="prodCount">0</span> Produk</h2>
          <input type="text" id="searchInput" placeholder="Cari produk..." oninput="filterTable()" style="font-family:var(--fb);font-size:.85rem;padding:.5rem 1rem;border:1.5px solid var(--border);border-radius:6px;background:var(--bg);outline:none;min-width:180px;max-width:100%">
        </div>
        <div class="table-wrap">
          <table id="productsTable" style="display: none;">
            <thead>
              <tr>
                <th>#</th><th>Nama</th><th>Kategori</th><th>Harga</th><th>Gambar</th><th>Aksi</th>
              </tr>
            </thead>
            <tbody id="prodTable">
              <!-- Rendered dynamically by JavaScript -->
            </tbody>
          </table>
          <div class="empty-state" id="emptyState" style="display: none;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M20 7H4a1 1 0 00-1 1v10a1 1 0 001 1h16a1 1 0 001-1V8a1 1 0 00-1-1z"/></svg>
            <p>Belum ada produk. Tambahkan yang pertama!</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal: Add / Edit -->
<div class="modal-overlay" id="prodModal">
  <div class="modal">
    <div class="modal-head">
      <h3 id="modalTitle">Tambah Produk</h3>
      <button class="modal-close" onclick="closeProdModal()">×</button>
    </div>
    <form id="prodForm" onsubmit="saveProd(event)">
      <div class="modal-body">
        <input type="hidden" id="prodId" name="id" value="">

        <div class="form-group">
          <label>Nama Produk</label>
          <input type="text" id="prodName" name="name" required placeholder="cth: Lemari Kayu Minimalis">
        </div>

        <div class="form-row">
          <div class="form-group">
            <label>Kategori (pisahkan koma untuk banyak)</label>
            <input type="text" id="prodCat" name="cat" placeholder="lemari, rak">
          </div>
          <div class="form-group">
            <label>Harga</label>
            <input type="text" id="prodPrice" name="price" placeholder="Rp 1.500.000">
          </div>
        </div>

        <div class="form-group">
          <label>Deskripsi</label>
          <textarea id="prodDesc" name="desc" rows="3" placeholder="Deskripsi lengkap produk..."></textarea>
        </div>

        <!-- Specs -->
        <div class="form-group">
          <label>Spesifikasi</label>
          <div class="specs-wrap" id="specsWrap"></div>
          <button type="button" class="add-spec" onclick="addSpecRow()">+ Tambah Baris</button>
        </div>

        <!-- Drag & Drop Upload Zone -->
        <div class="form-group">
          <label>Unggah Gambar (Drag & Drop)</label>
          <div class="drop-zone" id="dropZone">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4M17 8l-5-5-5 5M12 3v12"/></svg>
            <p>Tarik & lepas file gambar di sini, atau <span>klik untuk memilih</span></p>
            <input type="file" id="fileInput" multiple accept="image/*" style="display:none">
          </div>
        </div>

        <!-- Images -->
        <div class="form-group">
          <label>URL Gambar (1 per baris)</label>
          <div class="imgs-wrap" id="imgsWrap"></div>
          <button type="button" class="add-img" onclick="addImgRow()">+ Tambah Gambar</button>
          <small style="margin-top:.25rem;display:block">Contoh: /img/Lemari_1.jpg</small>
        </div>
      </div>
      <div class="modal-foot">
        <button type="button" class="btn-g" onclick="closeProdModal()" style="font-size:.72rem">Batal</button>
        <button type="submit" class="btn-p" id="submitBtn" style="font-size:.72rem">Simpan</button>
      </div>
    </form>
  </div>
</div>

<!-- Confirm Delete -->
<div class="confirm-overlay" id="confirmDel">
  <div class="confirm-box">
    <h3>Hapus Produk?</h3>
    <p>Produk "<span id="delName"></span>" akan dihapus permanen.</p>
    <div class="confirm-btns">
      <button class="cb-cancel" onclick="closeConfirm()">Batal</button>
      <button class="cb-confirm" id="confirmBtnDel">Hapus</button>
    </div>
  </div>
</div>

<!-- Notif -->
<div class="notif" id="notif"></div>

<script>
const TOKEN = <?= json_encode($token) ?>;
const API  = <?= json_encode($apiBase . '/api/index.php') ?>;
let productsList = <?= json_encode($products) ?>;

function notif(msg, type='ok') {
  const el = document.getElementById('notif');
  el.className = 'notif ' + type;
  el.innerHTML = (type === 'ok'
    ? '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6L9 17l-5-5"/></svg>'
    : '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg>'
  ) + msg;
  el.classList.add('show');
  setTimeout(() => el.classList.remove('show'), 3000);
}

function escapeHtml(str) {
  if (!str) return '';
  return str.toString()
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#039;');
}

function renderTable() {
  const tbody = document.getElementById('prodTable');
  const countEl = document.getElementById('prodCount');
  const tableEl = document.getElementById('productsTable');
  const emptyEl = document.getElementById('emptyState');
  
  tbody.innerHTML = '';
  countEl.textContent = productsList.length;
  
  if (productsList.length === 0) {
    tableEl.style.display = 'none';
    emptyEl.style.display = 'block';
    return;
  }
  
  tableEl.style.display = 'table';
  emptyEl.style.display = 'none';
  
  productsList.forEach(p => {
    const tr = document.createElement('tr');
    tr.setAttribute('data-name', p.name.toLowerCase());
    
    const categories = (p.cat || '').split(',').map(c => c.trim()).filter(Boolean);
    const catBadges = categories.map(c => `<span class="td-cat">${escapeHtml(c)}</span>`).join(' ');
    const imgCount = Array.isArray(p.imgs) ? p.imgs.length : 0;
    
    tr.innerHTML = `
      <td data-label="#" style="color:var(--text2);font-size:.78rem">${p.id}</td>
      <td data-label="Nama" class="td-name">${escapeHtml(p.name)}</td>
      <td data-label="Kategori">${catBadges}</td>
      <td data-label="Harga" class="td-price">${escapeHtml(p.price)}</td>
      <td data-label="Gambar" style="font-size:.78rem;color:var(--text2)">${imgCount} foto</td>
      <td data-label="Aksi" class="td-act">
        <button class="td-btn edit-btn">Edit</button>
        <button class="td-btn danger del-btn">Hapus</button>
      </td>
    `;
    
    tr.querySelector('.edit-btn').addEventListener('click', () => editProd(p));
    tr.querySelector('.del-btn').addEventListener('click', () => confirmDel(p.id, p.name));
    
    tbody.appendChild(tr);
  });
  
  filterTable();
}

async function fetchAndRenderProducts() {
  try {
    const data = await api('GET', '/products');
    if (data && data.error) throw new Error(data.error);
    productsList = Array.isArray(data) ? data : [];
    renderTable();
  } catch (err) {
    notif('Gagal memuat data terbaru: ' + err.message, 'err');
  }
}

function api(method, endpoint, body) {
  return fetch(API + endpoint, {
    method,
    headers: { 'Content-Type': 'application/json', 'Authorization': 'Bearer ' + TOKEN },
    body: body ? JSON.stringify(body) : undefined,
  }).then(r => r.json());
}

// ── Table filter
function filterTable() {
  const q = document.getElementById('searchInput').value.toLowerCase();
  document.querySelectorAll('#prodTable tr').forEach(r => {
    r.style.display = r.dataset.name.includes(q) ? '' : 'none';
  });
}

// ── Spec rows
function addSpecRow(label='', value='') {
  const wrap = document.getElementById('specsWrap');
  const row = document.createElement('div');
  row.className = 'spec-row';
  row.innerHTML = `<input value="${label}" placeholder="Label (cth: Bahan)">
                   <input value="${value}" placeholder="Nilai (cth: Kayu Jati A+)">
                   <button type="button" class="spec-del" onclick="this.parentElement.remove()">×</button>`;
  wrap.appendChild(row);
}

function getSpecs() {
  return Array.from(document.querySelectorAll('.spec-row')).map(r => ({
    l: r.children[0].value, v: r.children[1].value
  })).filter(s => s.l.trim());
}

// ── Img rows
function addImgRow(url='') {
  const wrap = document.getElementById('imgsWrap');
  const row = document.createElement('div');
  row.className = 'img-row';
  row.innerHTML = `<input value="${url}" placeholder="/img/nama.jpg">
                   <button type="button" class="img-del" onclick="this.parentElement.remove()">×</button>`;
  wrap.appendChild(row);
}

function getImgs() {
  return Array.from(document.querySelectorAll('.img-row input')).map(i => i.value.trim()).filter(Boolean);
}

// ── Modal open / close
function closeProdModal() {
  document.getElementById('prodModal').classList.remove('on');
}

function openAddModal() {
  document.getElementById('modalTitle').textContent = 'Tambah Produk';
  document.getElementById('prodId').value = '';
  document.getElementById('prodName').value = '';
  document.getElementById('prodCat').value = '';
  document.getElementById('prodPrice').value = 'Rp -';
  document.getElementById('prodDesc').value = '';
  document.getElementById('specsWrap').innerHTML = '';
  document.getElementById('imgsWrap').innerHTML = '';
  addSpecRow(); addImgRow();
  document.getElementById('prodModal').classList.add('on');
}

function editProd(p) {
  document.getElementById('modalTitle').textContent = 'Edit Produk';
  document.getElementById('prodId').value = p.id || '';
  document.getElementById('prodName').value = p.name || '';
  document.getElementById('prodCat').value = (p.cat || '').split(',').map(c => c.trim()).join(', ');
  document.getElementById('prodPrice').value = p.price || 'Rp -';
  document.getElementById('prodDesc').value = p.desc || '';

  const specsWrap = document.getElementById('specsWrap');
  specsWrap.innerHTML = '';
  (p.specs || []).forEach(s => addSpecRow(s.l || '', s.v || ''));
  if (!specsWrap.children.length) addSpecRow();

  const imgsWrap = document.getElementById('imgsWrap');
  imgsWrap.innerHTML = '';
  (p.imgs || []).forEach(i => addImgRow(i));
  if (!imgsWrap.children.length) addImgRow();

  document.getElementById('prodModal').classList.add('on');
}

async function saveProd(e) {
  e.preventDefault();
  const id    = document.getElementById('prodId').value;
  const body  = {
    name:  document.getElementById('prodName').value,
    cat:   document.getElementById('prodCat').value.split(',').map(c => c.trim()).filter(Boolean),
    price: document.getElementById('prodPrice').value,
    desc:  document.getElementById('prodDesc').value,
    specs: getSpecs(),
    imgs:  getImgs(),
  };
  if (id) body.id = parseInt(id);
  try {
    const res = await api(id ? 'PUT' : 'POST', '/products', body);
    if (res.error) throw new Error(res.error);
    notif(id ? 'Produk berhasil diperbarui!' : 'Produk berhasil ditambahkan!');
    closeProdModal();
    await fetchAndRenderProducts();
  } catch(err) {
    notif(err.message || 'Gagal menyimpan', 'err');
  }
}

// ── Delete confirm
let delId = null;
function confirmDel(id, name) {
  delId = id;
  document.getElementById('delName').textContent = name;
  document.getElementById('confirmDel').classList.add('on');
}
function closeConfirm() { document.getElementById('confirmDel').classList.remove('on'); delId = null; }

document.getElementById('confirmBtnDel').addEventListener('click', async () => {
  if (!delId) return;
  try {
    const res = await api('DELETE', '/products?id=' + delId, null);
    if (res.error) throw new Error(res.error);
    notif('Produk berhasil dihapus!');
    closeConfirm();
    await fetchAndRenderProducts();
  } catch(err) {
    notif(err.message || 'Gagal menghapus', 'err');
  }
});

// Drag and drop image upload handlers
const dz = document.getElementById('dropZone');
const fi = document.getElementById('fileInput');

dz.addEventListener('click', () => fi.click());

dz.addEventListener('dragover', e => {
  e.preventDefault();
  dz.classList.add('dragover');
});
dz.addEventListener('dragleave', () => dz.classList.remove('dragover'));
dz.addEventListener('drop', e => {
  e.preventDefault();
  dz.classList.remove('dragover');
  if (e.dataTransfer.files.length) {
    handleFileUploads(e.dataTransfer.files);
  }
});

fi.addEventListener('change', () => {
  if (fi.files.length) {
    handleFileUploads(fi.files);
  }
});

async function handleFileUploads(files) {
  for (let file of files) {
    if (!file.type.startsWith('image/')) continue;
    
    notif('Mengunggah gambar...');
    
    const fd = new FormData();
    fd.append('file', file);
    
    try {
      const res = await fetch(API + '/upload', {
        method: 'POST',
        headers: { 'Authorization': 'Bearer ' + TOKEN },
        body: fd
      });
      const data = await res.json();
      if (data.error) throw new Error(data.error);
      
      const emptyRow = Array.from(document.querySelectorAll('.img-row input')).find(i => !i.value.trim());
      if (emptyRow) {
        emptyRow.parentElement.remove();
      }
      
      addImgRow(data.url);
      notif('Gambar berhasil diunggah!');
    } catch (err) {
      notif(err.message || 'Gagal mengunggah gambar', 'err');
    }
  }
}

function toggleSidebar() {
  document.body.classList.toggle('sidebar-open');
}

// Close on overlay click
document.getElementById('prodModal').addEventListener('click', e => { if (e.target.id === 'prodModal') closeProdModal(); });
document.getElementById('confirmDel').addEventListener('click', e => { if (e.target.id === 'confirmDel') closeConfirm(); });
document.addEventListener('keydown', e => { if (e.key === 'Escape') { closeProdModal(); closeConfirm(); } });

// Initial client-side render
renderTable();
</script>
<div class="sidebar-backdrop" onclick="toggleSidebar()"></div>

</body>
</html>