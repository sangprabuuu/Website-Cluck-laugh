<?php include '../koneksi.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Galeri Suara Ayam Ketawa</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<?php include '../navbar.php'; ?>

<div class="container">
    <h1>Galeri Suara Ayam Ketawa</h1>

    <?php
    $query = mysqli_query($conn, "SELECT * FROM galeri ORDER BY id DESC");
    if ($query && mysqli_num_rows($query) > 0) {
        while ($data = mysqli_fetch_assoc($query)) {
            ?>
            <div class="card">
                <h3><?= htmlspecialchars($data['judul']); ?></h3>
                <audio controls>
                    <source src="../assets/audio/<?= urlencode($data['file']); ?>" type="audio/mpeg">
                    Browser Anda tidak mendukung audio player.
                </audio>
            </div>
            <?php
        }
    } else {
        echo "<p>Belum ada file audio pada galeri.</p>";
    }
    ?>
</div>

<script src="../assets/js/script.js"></script>
</body>
</html>
