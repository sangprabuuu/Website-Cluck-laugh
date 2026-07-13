<?php include 'koneksi.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Website Edukasi Ayam Ketawa</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="container">
    <section class="hero card background-logo">
        <div class="hero-content">
            <h1>Selamat Datang di Website Edukasi Ayam Ketawa</h1>
            <p class="lead">Portal informasi seputar sejarah, ciri khas, perawatan, dan galeri suara ayam ketawa.</p>
        </div>
    </section>

    <form method="GET" action="pages/artikel.php" class="search-form">
        <input type="text" name="search" placeholder="Cari informasi...">
        <button type="submit">Cari</button>
    </form>

    <h2>Artikel Terbaru</h2>

    <?php
    $query = mysqli_query($conn, "SELECT * FROM artikel ORDER BY created_at DESC LIMIT 3");

    if ($query && mysqli_num_rows($query) > 0) {
        echo '<div class="articles-grid">';
        while ($data = mysqli_fetch_assoc($query)) {
            $ringkas = mb_substr(strip_tags($data['isi']), 0, 140);
            ?>
            <article class="card article-card">
                <?php if (!empty($data['thumbnail'])) { ?>
                    <div class="article-thumbnail">
                        <img src="<?= htmlspecialchars($data['thumbnail']); ?>" alt="<?= htmlspecialchars($data['judul']); ?>">
                    </div>
                <?php } ?>
                <div class="article-content">
                    <h3><?= htmlspecialchars($data['judul']); ?></h3>
                    <small class="category-badge">Kategori: <?= htmlspecialchars($data['kategori']); ?></small>
                    <p class="article-excerpt"><?= htmlspecialchars($ringkas); ?>...</p>
                    <div class="article-actions">
                        <?php if ($data['is_eksternal'] && !empty($data['url_sumber'])) { ?>
                            <a href="<?= htmlspecialchars($data['url_sumber']); ?>" target="_blank" class="btn-primary">Baca Selengkapnya</a>
                            <small style="display: block; color: #6b7280; margin-top: 8px;">Sumber: <em><?= parse_url($data['url_sumber'], PHP_URL_HOST); ?></em></small>
                        <?php } else { ?>
                            <a href="pages/detail.php?id=<?= (int)$data['id']; ?>" class="btn-primary">Baca Selengkapnya</a>
                        <?php } ?>
                    </div>
                </div>
            </article>
            <?php
        }
        echo '</div>';
    } else {
        echo "<p>Belum ada artikel.</p>";
    }
    ?>
</div>

<script src="assets/js/script.js"></script>
</body>
</html>
