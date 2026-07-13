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
$file = '';
$error = '';
$success = '';

if (isset($_GET['edit'])) {
    $editMode = true;
    $id = (int)$_GET['edit'];
    $stmtGet = mysqli_prepare($conn, "SELECT * FROM galeri WHERE id = ? LIMIT 1");
    mysqli_stmt_bind_param($stmtGet, "i", $id);
    mysqli_stmt_execute($stmtGet);
    $res = mysqli_stmt_get_result($stmtGet);
    $data = mysqli_fetch_assoc($res);

    if ($data) {
        $judul = $data['judul'];
        $file = $data['file'];
    } else {
        header('Location: dashboard_galeri.php');
        exit;
    }
}

if (isset($_POST['simpan'])) {
    $judul = trim($_POST['judul'] ?? '');

    if (empty($judul)) {
        $error = 'Judul harus diisi.';
    } else {
        $currentFile = $file;

        // Handle file upload
        if (isset($_FILES['audio']) && $_FILES['audio']['error'] != UPLOAD_ERR_NO_FILE) {
            if ($_FILES['audio']['error'] !== UPLOAD_ERR_OK) {
                $error = 'Error upload file: ' . $_FILES['audio']['error'];
            } else {
                $allowedTypes = ['audio/mpeg', 'audio/mp3', 'audio/wav', 'audio/ogg'];
                if (!in_array($_FILES['audio']['type'], $allowedTypes)) {
                    $error = 'Tipe file tidak didukung. Gunakan MP3, WAV, atau OGG.';
                } else if ($_FILES['audio']['size'] > 50 * 1024 * 1024) { // 50MB max
                    $error = 'Ukuran file terlalu besar. Max 50MB.';
                } else {
                    $fileName = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', basename($_FILES['audio']['name']));
                    $uploadDir = '../assets/audio/';
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0755, true);
                    }

                    if (move_uploaded_file($_FILES['audio']['tmp_name'], $uploadDir . $fileName)) {
                        // Delete old file if edit mode and new file uploaded
                        if ($editMode && !empty($file)) {
                            $oldFile = $uploadDir . $file;
                            if (file_exists($oldFile)) {
                                unlink($oldFile);
                            }
                        }
                        $currentFile = $fileName;
                    } else {
                        $error = 'Gagal upload file.';
                    }
                }
            }
        } else if (!$editMode && empty($currentFile)) {
            $error = 'Silakan upload file audio.';
        }

        if (empty($error)) {
            if ($editMode) {
                $stmtUpdate = mysqli_prepare($conn, "UPDATE galeri SET judul = ?, file = ? WHERE id = ?");
                mysqli_stmt_bind_param($stmtUpdate, "ssi", $judul, $currentFile, $id);
                mysqli_stmt_execute($stmtUpdate);
            } else {
                $stmtInsert = mysqli_prepare($conn, "INSERT INTO galeri (judul, file) VALUES (?, ?)");
                mysqli_stmt_bind_param($stmtInsert, "ss", $judul, $currentFile);
                mysqli_stmt_execute($stmtInsert);
            }

            header('Location: dashboard_galeri.php?msg=simpan');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $editMode ? 'Edit' : 'Tambah'; ?> Galeri</title>
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
        .form-group input[type="text"],
        .form-group input[type="file"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-family: inherit;
            font-size: 14px;
            box-sizing: border-box;
        }
        .form-group input[type="file"] {
            padding: 5px;
        }
        .audio-player {
            margin-top: 10px;
            padding: 10px;
            background: #f5f5f5;
            border-radius: 4px;
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
    <h1><?= $editMode ? 'Edit' : 'Tambah'; ?> Galeri Suara</h1>

    <?php if (!empty($error)) { ?>
        <div class="error"><?= htmlspecialchars($error); ?></div>
    <?php } ?>
    <?php if (!empty($success)) { ?>
        <div class="notice"><?= htmlspecialchars($success); ?></div>
    <?php } ?>

    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label>Judul:</label>
            <input type="text" name="judul" value="<?= htmlspecialchars($judul); ?>" required>
        </div>

        <div class="form-group">
            <label>File Audio (MP3, WAV, OGG - Max 50MB):</label>
            <input type="file" name="audio" accept="audio/*">
            <?php if ($editMode && !empty($file)) { ?>
                <div class="audio-player">
                    <p><strong>File saat ini:</strong> <?= htmlspecialchars($file); ?></p>
                    <audio controls style="width: 100%;">
                        <source src="../assets/audio/<?= urlencode($file); ?>" type="audio/mpeg">
                        Browser Anda tidak mendukung audio player.
                    </audio>
                </div>
            <?php } ?>
        </div>

        <div class="form-actions">
            <button type="submit" name="simpan">Simpan</button>
            <a href="dashboard_galeri.php">Kembali</a>
        </div>
    </form>
</div>

<script src="../assets/js/script.js"></script>
</body>
</html>
