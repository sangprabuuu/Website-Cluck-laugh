<?php include '../koneksi.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edukasi Ayam Ketawa</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<?php include '../navbar.php'; ?>

<div class="container">
    <div class="page-banner card background-logo">
        <div class="banner-content">
            <h1>Edukasi Ayam Ketawa</h1>
            <p class="lead">Pelajari karakter ayam ketawa dan praktik perawatan terbaik untuk menjaga kualitas suara kokoknya.</p>
        </div>
    </div>
    <div class="grid edukasi-grid">
        <?php
        $queryEd = mysqli_query($conn, "SELECT * FROM edukasi ORDER BY urutan ASC");
        if ($queryEd && mysqli_num_rows($queryEd) > 0) {
            while ($rowEd = mysqli_fetch_assoc($queryEd)) {
                ?>
                <div class="card edukasi-card">
                    <h3 class="edukasi-title"><?= htmlspecialchars($rowEd['judul']); ?></h3>
                    <div class="edukasi-preview">
                        <p class="edukasi-text edukasi-text--preview"><?= htmlspecialchars($rowEd['isi']); ?></p>
                    </div>
                    <div class="edukasi-full" aria-hidden="true">
                        <p class="edukasi-text edukasi-text--full"><?= htmlspecialchars($rowEd['isi']); ?></p>
                    </div>
                    <button type="button" class="edukasi-toggle" aria-expanded="false">Baca Selengkapnya</button>
                </div>
                <?php
            }
        } else {
            echo '<p>Belum ada konten edukasi.</p>';
        }
        ?>
    </div>
</div>

<script src="../assets/js/script.js"></script>
</body>
</html>
