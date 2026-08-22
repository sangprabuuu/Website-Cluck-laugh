<?php
session_start();
include '../koneksi.php';

if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header('Location: login.php');
    exit;
}

if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    
    // Get file name first
    $stmtGet = mysqli_prepare($conn, "SELECT file FROM galeri WHERE id = ?");
    mysqli_stmt_bind_param($stmtGet, "i", $id);
    mysqli_stmt_execute($stmtGet);
    $res = mysqli_stmt_get_result($stmtGet);
    $data = mysqli_fetch_assoc($res);
    
    if ($data) {
        // Delete file
        $filePath = '../assets/audio/' . $data['file'];
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }
    
    // Delete from database
    $stmtDel = mysqli_prepare($conn, "DELETE FROM galeri WHERE id = ?");
    mysqli_stmt_bind_param($stmtDel, "i", $id);
    mysqli_stmt_execute($stmtDel);
    
    header('Location: dashboard_galeri.php?msg=hapus');
    exit;
}

$msg = $_GET['msg'] ?? '';
$query = mysqli_query($conn, "SELECT * FROM galeri ORDER BY id ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Galeri Suara</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .actions {
            display: flex;
            gap: 8px;
        }
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
            <a href="dashboard.php">Dashboard Artikel</a>
            <a href="dashboard_edukasi.php">Kelola Edukasi</a>
            <a href="dashboard_galeri.php" class="active">Kelola Galeri</a>
            <a href="dashboard.php?logout=1" onclick="return confirm('Yakin logout?')">Logout</a>
        </nav>
    </aside>

    <main class="admin-content">
        <h1>Kelola Galeri Suara</h1>

        <?php if ($msg === 'simpan') { ?><div class="notice">Galeri berhasil disimpan.</div><?php } ?>
        <?php if ($msg === 'hapus') { ?><div class="notice">Galeri berhasil dihapus.</div><?php } ?>

        <div class="admin-card">
            <p style="margin-top: 0;">
                <a href="tambah_galeri.php" class="btn-primary" style="display:inline-block; padding:10px 20px; background:linear-gradient(135deg, #1e3a8a 0%, #0f172a 100%); color:white; text-decoration:none; border-radius:4px;">+ Tambah Galeri</a>
            </p>

            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Judul</th>
                        <th>File</th>
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
                            <td><?= htmlspecialchars($row['file']); ?></td>
                            <td><?= htmlspecialchars($row['created_at']); ?></td>
                            <td class="actions">
                                <a href="tambah_galeri.php?edit=<?= (int)$row['id']; ?>">Edit</a>
                                <a href="dashboard_galeri.php?hapus=<?= (int)$row['id']; ?>" onclick="return confirm('Hapus galeri ini?')">Hapus</a>
                            </td>
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr><td colspan="5">Belum ada galeri.</td></tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </main>
</div>

<script src="../assets/js/script.js"></script>
</body>
</html>
