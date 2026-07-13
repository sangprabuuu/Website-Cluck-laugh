<?php include '../koneksi.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Artikel</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<?php include '../navbar.php'; ?>

<div class="container">
    <?php
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

    $stmt = mysqli_prepare($conn, "SELECT * FROM artikel WHERE id = ? LIMIT 1");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $data = mysqli_fetch_assoc($result);

    if ($data) {
        ?>
        <article class="card">
            <h1><?= htmlspecialchars($data['judul']); ?></h1>
            <small>Kategori: <?= htmlspecialchars($data['kategori']); ?> | Dibuat: <?= htmlspecialchars($data['created_at']); ?></small>
            <p><?= nl2br(htmlspecialchars($data['isi'])); ?></p>
        </article>
        <?php
    } else {
        echo "<p class='error'>Artikel tidak ditemukan.</p>";
    }
    ?>
</div>

<script src="../assets/js/script.js"></script>
</body>
</html>
