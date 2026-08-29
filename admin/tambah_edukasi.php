<?php
session_start();
include '../koneksi.php';

if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header('Location: login.php');
    exit;
}

$editMode = false;
$id = 0;
$judul = '';
$isi = '';

if (isset($_GET['edit'])) {
    $editMode = true;
    $id = (int)$_GET['edit'];
    $stmtGet = mysqli_prepare($conn, "SELECT * FROM edukasi WHERE id = ? LIMIT 1");
    mysqli_stmt_bind_param($stmtGet, "i", $id);
    mysqli_stmt_execute($stmtGet);
    $res = mysqli_stmt_get_result($stmtGet);
    $data = mysqli_fetch_assoc($res);

    if ($data) {
        $judul = $data['judul'];
        $isi = $data['isi'];
    } else {
        header('Location: dashboard_edukasi.php');
        exit;
    }
}

if (isset($_POST['simpan'])) {
    $judul = trim($_POST['judul'] ?? '');
    $isi = trim($_POST['isi'] ?? '');

    if (empty($judul) || empty($isi)) {
        $error = 'Judul dan isi harus diisi.';
    } else {
        if ($editMode) {
            $stmtUpdate = mysqli_prepare($conn, "UPDATE edukasi SET judul = ?, isi = ? WHERE id = ?");
            mysqli_stmt_bind_param($stmtUpdate, "ssi", $judul, $isi, $id);
            mysqli_stmt_execute($stmtUpdate);
        } else {
            $stmtInsert = mysqli_prepare($conn, "INSERT INTO edukasi (judul, isi, urutan) VALUES (?, ?, 0)");
            mysqli_stmt_bind_param($stmtInsert, "ss", $judul, $isi);
            mysqli_stmt_execute($stmtInsert);
        }

        header('Location: dashboard_edukasi.php?msg=simpan');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $editMode ? 'Edit' : 'Tambah'; ?> Edukasi</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .form-group {
            margin-bottom: 16px;
        }
        .form-group label {
            display: block;
            margin-bottom: 6px;
            font-weight: 600;
        }
        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-family: inherit;
            font-size: 14px;
        }
        .form-group textarea {
            resize: vertical;
            min-height: 200px;
        }
        .form-actions {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }
        .form-actions button,
        .form-actions a {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }
        .form-actions button {
            background: linear-gradient(135deg, #1e3a8a 0%, #0f172a 100%);
            color: white;
        }
        .form-actions button:hover {
            opacity: 0.9;
        }
        .form-actions a {
            background: #ccc;
            color: #333;
        }
        .form-actions a:hover {
            background: #bbb;
        }
        .error {
            background: #fee;
            border: 1px solid #fcc;
            color: #c00;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 16px;
        }
    </style>
</head>
<body>
<?php include '../navbar.php'; ?>

<div class="container" style="max-width: 600px; margin-top: 20px;">
    <h1><?= $editMode ? 'Edit' : 'Tambah'; ?> Edukasi</h1>

    <?php if (isset($error)) { ?>
        <div class="error"><?= htmlspecialchars($error); ?></div>
    <?php } ?>

    <form method="POST">
        <div class="form-group">
            <label>Judul:</label>
            <input type="text" name="judul" value="<?= htmlspecialchars($judul); ?>" required>
        </div>

        <div class="form-group">
            <label>Isi/Konten:</label>
            <textarea name="isi" required><?= htmlspecialchars($isi); ?></textarea>
        </div>

        <div class="form-actions">
            <button type="submit" name="simpan">Simpan</button>
            <a href="dashboard_edukasi.php">Kembali</a>
        </div>
    </form>
</div>

<script src="../assets/js/script.js"></script>
</body>
</html>
