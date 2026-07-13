<?php
session_start();
include '../koneksi.php';

if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header('Location: login.php');
    exit;
}

if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: login.php');
    exit;
}

// Reset AUTO_INCREMENT jika tabel kosong
$checkCount = mysqli_query($conn, "SELECT COUNT(*) as total FROM artikel");
$checkResult = mysqli_fetch_assoc($checkCount);
if ($checkResult['total'] == 0) {
    mysqli_query($conn, "ALTER TABLE artikel AUTO_INCREMENT = 1");
}

if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    $stmtDel = mysqli_prepare($conn, "DELETE FROM artikel WHERE id = ?");
    mysqli_stmt_bind_param($stmtDel, "i", $id);
    mysqli_stmt_execute($stmtDel);
    
    // Reset AUTO_INCREMENT berdasarkan data yang tersisa
    $countQuery = mysqli_query($conn, "SELECT COUNT(*) as total FROM artikel");
    $countResult = mysqli_fetch_assoc($countQuery);
    
    if ($countResult['total'] == 0) {
        mysqli_query($conn, "ALTER TABLE artikel AUTO_INCREMENT = 1");
    } else {
        // Dapatkan ID tertinggi dan set AUTO_INCREMENT ke ID tertinggi + 1
        $maxQuery = mysqli_query($conn, "SELECT MAX(id) as max_id FROM artikel");
        $maxResult = mysqli_fetch_assoc($maxQuery);
        $nextId = ($maxResult['max_id'] ?? 0) + 1;
        mysqli_query($conn, "ALTER TABLE artikel AUTO_INCREMENT = " . $nextId);
    }
    
    header('Location: dashboard.php?msg=hapus');
    exit;
}

$msg = $_GET['msg'] ?? '';
$query = mysqli_query($conn, "SELECT * FROM artikel ORDER BY id ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .actions a {
            padding: 6px 12px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 13px;
        }

        .actions a:first-child {
            background: #e3f2fd;
            color: #1e3a8a;
        }

        .actions a:last-child {
            background: #fee;
            color: #c00;
        }
    </style>
</head>
<body>
<?php include '../navbar.php'; ?>

<div class="container admin-layout">
    <aside class="admin-sidebar">
        <div class="admin-sidebar-header">
            <h2>Kelola Admin</h2>
            <p>Halo, <?= htmlspecialchars($_SESSION['username'] ?? 'admin'); ?>.</p>
        </div>

        <nav class="admin-menu">
            <a href="dashboard.php" class="active">Dashboard Artikel</a>
            <a href="dashboard_edukasi.php">Kelola Edukasi</a>
            <a href="dashboard_galeri.php">Kelola Galeri</a>
            <a href="dashboard.php?logout=1" onclick="return confirm('Yakin logout?')">Logout</a>
        </nav>
    </aside>

    <main class="admin-content">
        <h1>Dashboard Admin</h1>

        <?php if ($msg === 'simpan') { ?><div class="notice">Artikel berhasil disimpan.</div><?php } ?>
        <?php if ($msg === 'hapus') { ?><div class="notice">Artikel berhasil dihapus.</div><?php } ?>

        <div class="admin-card table-card">
            <p style="margin-top: 0; margin-bottom: 16px;">
                <a href="tambah_artikel.php" class="btn-primary" style="display:inline-block; padding:10px 20px; background:linear-gradient(135deg, #1e3a8a 0%, #0f172a 100%); color:white; text-decoration:none; border-radius:4px;">+ Tambah Artikel</a>
            </p>

            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Judul</th>
                        <th>Kategori</th>
                        <th>Tanggal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($query && mysqli_num_rows($query) > 0) { ?>
                    <?php while ($row = mysqli_fetch_assoc($query)) { ?>
                        <tr>
                            <td><?= (int)$row['id']; ?></td>
                            <td><?= htmlspecialchars($row['judul']); ?></td>
                            <td><?= htmlspecialchars($row['kategori']); ?></td>
                            <td><?= htmlspecialchars($row['created_at']); ?></td>
                            <td class="actions">
                                <a href="tambah_artikel.php?edit=<?= (int)$row['id']; ?>">Edit</a>
                                <a href="dashboard.php?hapus=<?= (int)$row['id']; ?>" onclick="return confirm('Hapus artikel ini?')">Hapus</a>
                            </td>
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr><td colspan="5">Belum ada artikel.</td></tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </main>
</div>

<script src="../assets/js/script.js"></script>
</body>
</html>
