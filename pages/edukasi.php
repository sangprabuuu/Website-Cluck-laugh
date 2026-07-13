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
    <div class="grid">
        <?php
        $queryEd = mysqli_query($conn, "SELECT * FROM edukasi ORDER BY urutan ASC");
        if ($queryEd && mysqli_num_rows($queryEd) > 0) {
            while ($rowEd = mysqli_fetch_assoc($queryEd)) {
                ?>
                <div class="card">
                    <h3><?= htmlspecialchars($rowEd['judul']); ?></h3>
                    <p><?= htmlspecialchars($rowEd['isi']); ?></p>
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
