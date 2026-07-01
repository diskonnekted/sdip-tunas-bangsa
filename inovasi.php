<?php
// Include necessary files
include_once 'includes/settings.php';
require_once 'admin/includes/functions.php';
require_once 'admin/models/Innovation.php';

// Get school info
$school_info = getSchoolInfo();
$contact_info = getContactInfo();

// Initialize database
$database = new Database();
$db = $database->getConnection();
$innovation = new Innovation($db);

// Get filter parameters
$category_filter = $_GET['category'] ?? '';
$year_filter = $_GET['year'] ?? '';
$search = $_GET['search'] ?? '';

// Fetch data
try {
    $featured = $innovation->getFeatured(6);
    $years = $innovation->getAvailableYears();

    // Active innovations for listing
    $items = $innovation->getActive(60, 0, $category_filter, $year_filter);
    if (!empty($search)) {
        $items = array_filter($items, function($it) use ($search) {
            return stripos($it['title'], $search) !== false || stripos($it['description'], $search) !== false;
        });
    }
} catch (Exception $e) {
    $featured = [];
    $years = [];
    $items = [];
}
?>
<?php $page_title = 'Inovasi Pembelajaran'; ?>
<?php include 'includes/header.php'; ?>
<style>
        .page-header {
            background: url('admin/uploads/innovations/innovation_1765965935_6942806f66e2c.jpeg') no-repeat center center/cover !important;
            position: relative;
        }
        .page-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(30, 41, 59, 0.8); /* Dark green-gray overlay */
            z-index: 1;
        }
        .page-header .container {
            position: relative;
            z-index: 2;
        }
        .section { padding: 80px 0; }
        .filter-bar { background: #f8f9fa; padding: 20px 0; }
        .filters { display: flex; gap: 15px; flex-wrap: wrap; align-items: center; justify-content: center; }
        .filters select, .filters input[type="text"] { padding: 10px 14px; border: 2px solid #e5e7eb; border-radius: 10px; }
        .filters button { padding: 10px 16px; }
        .grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; }
        .card { background: #fff; border-radius: 14px; box-shadow: 0 8px 24px rgba(0,0,0,0.08); overflow: hidden; transition: transform .2s ease, box-shadow .2s ease; }
        .card:hover { transform: translateY(-4px); box-shadow: 0 12px 30px rgba(0,0,0,0.12); }
        .thumb { height: 170px; background: #f0fdf4; display:flex; align-items:center; justify-content:center; }
        .thumb img { width: 100%; height: 100%; object-fit: cover; }
        .content { padding: 18px; }
        .badge { display:inline-flex; align-items:center; gap:6px; padding: 4px 10px; border-radius: 9999px; font-size: .75rem; font-weight: 600; }
        .badge.blue { background:#dbeafe; color:#1d4ed8; }
        .badge.green { background:#dcfce7; color:#15803d; }
        .badge.yellow { background:#fef3c7; color:#a16207; }
        .title { font-weight: 700; color:#0f172a; margin: 8px 0; font-size: 1.05rem; min-height: 2.6em; }
        .desc { color:#64748b; font-size: .92rem; line-height: 1.5; min-height: 3.8em; }
        .meta { display:flex; justify-content: space-between; align-items:center; margin-top: 12px; color:#6b7280; font-size:.85rem; }
        .btn { display:inline-flex; align-items:center; gap:8px; border-radius: 10px; text-decoration:none; }
        .btn.primary { background: var(--primary-color, #16a34a); color:white; padding:10px 14px; }
        .btn.outline { border:2px solid #e5e7eb; color:#111827; padding:8px 12px; border-radius: 8px; }
        .label { font-size:.8rem; color:#6b7280; margin-bottom:6px; display:block; }
        .featured { background: linear-gradient(135deg, #fdf4ff, #f0fdf4); border:1px solid #e5e7eb; }
        .pill { padding: 4px 10px; border-radius: 999px; background:#f1f5f9; color:#475569; font-size:.75rem; }
        .modal { display:none; position: fixed; inset: 0; background: rgba(0,0,0,.5); z-index: 1000; }
        .modal .inner { background:#fff; margin: 5% auto; border-radius: 14px; max-width: 960px; max-height:80vh; overflow:auto; padding: 22px; }
        @media (max-width: 768px){ .filters{flex-direction:column; align-items: stretch;} .grid{ grid-template-columns: 1fr; } .modal .inner{ margin: 8% 16px; } }
    </style>


    <!-- Page Header -->
    <section class="page-header">
        <i class="fas fa-lightbulb header-bg-icon"></i>
        <div class="container">
            <div class="page-header-content">
                <h1>Inovasi Pembelajaran</h1>
                <p>Metode, teknologi, kurikulum, dan fasilitas pembelajaran yang mendukung karakter berintegritas</p>
                <nav class="breadcrumb">
                    <a href="index.php">Beranda</a>
                    <span>/</span>
                    <span>Inovasi</span>
                </nav>
            </div>
        </div>
    </section>

    <!-- Filters -->
    <section class="filter-bar">
        <div class="container">
            <form method="GET" class="filters">
                <div>
                    <label class="label">Kategori</label>
                    <select name="category">
                        <option value="">Semua</option>
                        <option value="teknologi" <?= $category_filter==='teknologi'?'selected':'' ?>>Teknologi</option>
                        <option value="metode" <?= $category_filter==='metode'?'selected':'' ?>>Metode</option>
                        <option value="kurikulum" <?= $category_filter==='kurikulum'?'selected':'' ?>>Kurikulum</option>
                        <option value="fasilitas" <?= $category_filter==='fasilitas'?'selected':'' ?>>Fasilitas</option>
                    </select>
                </div>
                <div>
                    <label class="label">Tahun</label>
                    <select name="year">
                        <option value="">Semua</option>
                        <?php foreach ($years as $y): ?>
                            <option value="<?= $y ?>" <?= $year_filter==$y?'selected':'' ?>><?= $y ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div style="min-width:260px; flex:1;">
                    <label class="label">Pencarian</label>
                    <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Cari judul atau deskripsi..." style="width:100%">
                </div>
                <div>
                    <label class="label">&nbsp;</label>
                    <button type="submit" class="btn outline"><i class="fas fa-search"></i> Filter</button>
                </div>
                <div>
                    <label class="label">&nbsp;</label>
                    <a href="inovasi.php" class="btn outline">Reset</a>
                </div>
            </form>
        </div>
    </section>

    <!-- Featured -->
    <?php if (!empty($featured)): ?>
    <section class="section" style="padding-top: 50px;">
        <div class="container">
            <h2 style="margin-bottom: 18px;">Inovasi Unggulan</h2>
            <div class="grid">
                <?php foreach ($featured as $it): ?>
                <div class="card featured">
                    <div class="thumb">
                        <?php if (!empty($it['image'])): ?>
                        <img src="admin/uploads/innovations/<?= htmlspecialchars($it['image']) ?>" alt="<?= htmlspecialchars($it['title']) ?>">
                        <?php else: ?>
                        <i class="fas fa-lightbulb" style="font-size: 2rem; color:#9ca3af;"></i>
                        <?php endif; ?>
                    </div>
                    <div class="content">
                        <span class="badge yellow"><i class="fas fa-star"></i> Unggulan</span>
                        <div class="title"><?= htmlspecialchars($it['title']) ?></div>
                        <div class="desc"><?= htmlspecialchars(substr($it['description'],0,140)) ?>...</div>
                        <div class="meta">
                            <span class="pill"><i class="fas fa-calendar"></i> <?= $it['implementation_year'] ?></span>
                            <button class="btn primary" onclick="openInnovation(<?= $it['id'] ?>)"><i class="fas fa-eye"></i> Lihat</button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- All Innovations -->
    <section class="section" style="padding-top:40px;">
        <div class="container">
            <h2 style="margin-bottom: 18px;">Semua Inovasi<?= $category_filter? ' - '.ucfirst($category_filter):'' ?><?= $year_filter? ' ('.$year_filter.')':'' ?></h2>
            <?php if (empty($items)): ?>
            <div class="coming-soon" style="padding:40px 0;">
                <div class="coming-soon-content" style="text-align:center;">
                    <i class="fas fa-info-circle"></i>
                    <h2>Tidak ada inovasi ditemukan</h2>
                    <p>Coba ubah filter kategori/tahun atau hapus kata pencarian.</p>
                </div>
            </div>
            <?php else: ?>
            <div class="grid">
                <?php foreach ($items as $it): ?>
                <div class="card">
                    <div class="thumb">
                        <?php if (!empty($it['image'])): ?>
                        <img src="admin/uploads/innovations/<?= htmlspecialchars($it['image']) ?>" alt="<?= htmlspecialchars($it['title']) ?>">
                        <?php else: ?>
                        <i class="<?= (new Innovation($db))->getCategoryIcon($it['category']) ?>" style="font-size: 2rem; color:#9ca3af;"></i>
                        <?php endif; ?>
                    </div>
                    <div class="content">
                        <span class="badge blue"><i class="<?= (new Innovation($db))->getCategoryIcon($it['category']) ?>"></i> <?= (new Innovation($db))->getCategoryName($it['category']) ?></span>
                        <div class="title"><?= htmlspecialchars($it['title']) ?></div>
                        <div class="desc"><?= htmlspecialchars(substr($it['description'],0,140)) ?>...</div>
                        <div class="meta">
                            <span><i class="fas fa-calendar"></i> <?= $it['implementation_year'] ?></span>
                            <button class="btn primary" onclick="openInnovation(<?= $it['id'] ?>)"><i class="fas fa-eye"></i> Detail</button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>

    <!-- Modal -->
    <div id="innovationModal" class="modal">
        <div class="inner" id="innovationInner">
            <div style="display:flex; justify-content: space-between; align-items:center; margin-bottom: 10px;">
                <h3 id="modalTitle" style="margin:0;">Detail Inovasi</h3>
                <button onclick="closeInnovation()" class="btn outline">Tutup</button>
            </div>
            <div id="modalBody"></div>
        </div>
    </div>

    <script>
        function openInnovation(id){
            fetch(`innovation_api.php?action=view&id=${id}`)
                .then(r=>r.json())
                .then(d=>{
                    if(d.success){
                        document.getElementById('modalTitle').innerText = d.innovation.title;
                        document.getElementById('modalBody').innerHTML = d.html;
                        document.getElementById('innovationModal').style.display='block';
                        document.body.style.overflow='hidden';
                    }else{
                        alert(d.message||'Gagal memuat data');
                    }
                }).catch(()=>alert('Terjadi kesalahan'))
        }
        function closeInnovation(){
            document.getElementById('innovationModal').style.display='none';
            document.body.style.overflow='auto';
        }
        window.addEventListener('click', (e)=>{
            const modal = document.getElementById('innovationModal');
            if(e.target===modal){ closeInnovation(); }
        });
        document.addEventListener('keydown', (e)=>{ if(e.key==='Escape') closeInnovation(); });
    </script>
</body>
</html>
