<?php include '../koneksi.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Artikel</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<?php include '../navbar.php'; ?>

<div class="container">
    <div class="page-banner card background-logo">
        <div class="banner-content">
            <h1>Daftar Artikel</h1>
        </div>
    </div>

    <?php
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';
    $kategori = isset($_GET['kategori']) ? trim($_GET['kategori']) : '';

    $sql = "SELECT * FROM artikel WHERE 1=1";
    $types = "";
    $params = [];

    if ($search !== '') {
        $sql .= " AND (judul LIKE ? OR isi LIKE ?)";
        $like = "%{$search}%";
        $types .= "ss";
        $params[] = $like;
        $params[] = $like;
    }

    if ($kategori !== '') {
        $sql .= " AND kategori = ?";
        $types .= "s";
        $params[] = $kategori;
    }

    $sql .= " ORDER BY created_at DESC";

    $stmt = mysqli_prepare($conn, $sql);

    if (!empty($params)) {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }

    mysqli_stmt_execute($stmt);
    $query = mysqli_stmt_get_result($stmt);
    ?>

    <form method="GET" class="search-form">
        <input type="text" name="search" value="<?= htmlspecialchars($search); ?>" placeholder="Cari judul atau isi artikel...">
        <select name="kategori">
            <option value="">Semua kategori</option>
            <option value="perawatan" <?= $kategori === 'perawatan' ? 'selected' : ''; ?>>Perawatan</option>
            <option value="lomba" <?= $kategori === 'lomba' ? 'selected' : ''; ?>>Lomba</option>
            <option value="umum" <?= $kategori === 'umum' ? 'selected' : ''; ?>>Umum</option>
        </select>
        <button type="submit">Filter</button>
    </form>

    <?php if ($query && mysqli_num_rows($query) > 0) { ?>
        <div class="articles-grid">
        <?php while ($row = mysqli_fetch_assoc($query)) { ?>
            <article class="card article-card">
                <?php if (!empty($row['thumbnail'])) { ?>
                    <div class="article-thumbnail">
                        <img src="<?= htmlspecialchars($row['thumbnail']); ?>" alt="<?= htmlspecialchars($row['judul']); ?>">
                    </div>
                <?php } ?>
                <div class="article-content">
                    <h3><?= htmlspecialchars($row['judul']); ?></h3>
                    <small class="category-badge">Kategori: <?= htmlspecialchars($row['kategori']); ?></small>
                    <p class="article-excerpt"><?= htmlspecialchars(mb_substr(strip_tags($row['isi']), 0, 160)); ?>...</p>
                    <div class="article-actions">
                        <?php if ($row['is_eksternal'] && !empty($row['url_sumber'])) { ?>
                            <a href="<?= htmlspecialchars($row['url_sumber']); ?>" target="_blank" class="btn-primary">Baca Selengkapnya</a>
                            <small style="display: block; color: #6b7280; margin-top: 8px;">Sumber: <em><?= parse_url($row['url_sumber'], PHP_URL_HOST); ?></em></small>
                        <?php } else { ?>
                            <a href="detail.php?id=<?= (int)$row['id']; ?>" class="btn-primary">Baca Selengkapnya</a>
                        <?php } ?>
                    </div>
                </div>
            </article>
        <?php } ?>
        </div>
    <?php } else { ?>
        <p>Tidak ada artikel yang cocok.</p>
    <?php } ?>
</div>

<script src="../assets/js/script.js"></script>
</body>
</html>
