<?php
session_start();
include '../koneksi.php';
include 'ArtikelScraper.php';

if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
	header('Location: login.php');
	exit;
}

$editMode = false;
$id = 0;
$judul = '';
$isi = '';
$kategori = '';
$url_sumber = '';
$thumbnail = '';
$is_eksternal = false;

if (isset($_GET['edit'])) {
	$editMode = true;
	$id = (int)$_GET['edit'];
	$stmtGet = mysqli_prepare($conn, "SELECT * FROM artikel WHERE id = ? LIMIT 1");
	mysqli_stmt_bind_param($stmtGet, "i", $id);
	mysqli_stmt_execute($stmtGet);
	$res = mysqli_stmt_get_result($stmtGet);
	$data = mysqli_fetch_assoc($res);

	if ($data) {
		$judul = $data['judul'];
		$isi = $data['isi'];
		$kategori = $data['kategori'];
		$url_sumber = $data['url_sumber'] ?? '';
		$thumbnail = $data['thumbnail'] ?? '';
		$is_eksternal = $data['is_eksternal'] ?? false;
	} else {
		header('Location: dashboard.php');
		exit;
	}
}

if (isset($_POST['fetch_url'])) {
	$url = trim($_POST['url'] ?? '');
	header('Content-Type: application/json');

	if (empty($url)) {
		echo json_encode(['success' => false, 'message' => 'URL tidak boleh kosong']);
		exit;
	}

	$data = ArtikelScraper::scrapeArticle($url);

	if ($data) {
		echo json_encode(['success' => true, 'data' => $data]);
	} else {
		echo json_encode(['success' => false, 'message' => 'Gagal mengambil data dari URL. Pastikan URL valid dan website accessible.']);
	}
	exit;
}

if (isset($_POST['submit'])) {
	$judul = trim($_POST['judul'] ?? '');
	$isi = trim($_POST['isi'] ?? '');
	$kategori = trim($_POST['kategori'] ?? '');
	$url_sumber = trim($_POST['url_sumber'] ?? '');
	$thumbnail = trim($_POST['thumbnail'] ?? '');
	$is_eksternal = !empty($_POST['is_eksternal']) ? 1 : 0;

	if ($judul !== '' && $isi !== '' && $kategori !== '') {
		if (isset($_POST['id']) && $_POST['id'] !== '') {
			$id = (int)$_POST['id'];
			$stmt = mysqli_prepare($conn, "UPDATE artikel SET judul = ?, isi = ?, kategori = ?, url_sumber = ?, thumbnail = ?, is_eksternal = ? WHERE id = ?");
			mysqli_stmt_bind_param($stmt, "sssssii", $judul, $isi, $kategori, $url_sumber, $thumbnail, $is_eksternal, $id);
			mysqli_stmt_execute($stmt);
		} else {
			$stmt = mysqli_prepare($conn, "INSERT INTO artikel (judul, isi, kategori, url_sumber, thumbnail, is_eksternal) VALUES (?, ?, ?, ?, ?, ?)");
			mysqli_stmt_bind_param($stmt, "sssssi", $judul, $isi, $kategori, $url_sumber, $thumbnail, $is_eksternal);
			mysqli_stmt_execute($stmt);
		}

		header('Location: dashboard.php?msg=simpan');
		exit;
	}
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?= $editMode ? 'Edit' : 'Tambah'; ?> Artikel</title>
	<link rel="stylesheet" href="../assets/css/style.css">
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
			<a href="dashboard_galeri.php">Kelola Galeri</a>
			<a href="dashboard.php?logout=1" onclick="return confirm('Yakin logout?')">Logout</a>
		</nav>
	</aside>

	<main class="admin-content">
		<h1><?= $editMode ? 'Edit' : 'Tambah'; ?> Artikel</h1>

		<form method="POST" class="card admin-card" id="articleForm">
			<?php if ($editMode) { ?>
				<input type="hidden" name="id" value="<?= (int)$id; ?>">
			<?php } ?>

			<div id="scrapeSection">
				<label><strong>Atau Ambil dari URL Artikel Lain</strong></label>
				<div style="display: flex; gap: 10px; margin-bottom: 16px;">
					<input type="url" id="urlInput" placeholder="https://contoh.com/artikel" value="<?= htmlspecialchars($url_sumber); ?>">
					<button type="button" id="fetchBtn" style="width: auto; cursor: pointer;">Ambil Data</button>
				</div>
				<div id="scrapeMessage" style="display: none; padding: 10px; border-radius: 8px; margin-bottom: 16px;"></div>
				<div id="scrapePreview" style="display: none; padding: 16px; background: #f0f4ff; border-radius: 8px; margin-bottom: 16px; border: 1px solid #d1d5db;"></div>
			</div>

			<hr style="margin: 20px 0; border: none; border-top: 1px solid #d1d5db;">

			<label>Judul</label>
			<input type="text" name="judul" placeholder="Judul" value="<?= htmlspecialchars($judul); ?>" required>

			<label>Isi / Deskripsi Singkat</label>
			<textarea name="isi" rows="8" required><?= htmlspecialchars($isi); ?></textarea>

			<label>Kategori</label>
			<input type="text" name="kategori" placeholder="Contoh: perawatan" value="<?= htmlspecialchars($kategori); ?>" required>

			<label>URL Sumber (opsional)</label>
			<input type="url" name="url_sumber" placeholder="https://contoh.com/artikel" value="<?= htmlspecialchars($url_sumber); ?>">

			<label>URL Thumbnail (opsional)</label>
			<input type="url" name="thumbnail" placeholder="https://contoh.com/gambar.jpg" value="<?= htmlspecialchars($thumbnail); ?>">
			<?php if (!empty($thumbnail)) { ?>
				<img src="<?= htmlspecialchars($thumbnail); ?>" style="max-width: 200px; margin-top: 10px; border-radius: 8px;">
			<?php } ?>

			<label style="margin-top: 16px;">
				<input type="checkbox" name="is_eksternal" value="1" <?= $is_eksternal ? 'checked' : ''; ?>>
				Artikel dari website eksternal
			</label>

			<div style="display: flex; gap: 10px; margin-top: 20px;">
				<button name="submit" type="submit">Simpan</button>
				<a href="dashboard.php" style="padding: 10px 16px; background: #6b7280; color: white; text-decoration: none; border-radius: 8px; display: inline-block;">Kembali</a>
			</div>
		</form>

		<script>
		const fetchBtn = document.getElementById('fetchBtn');
		const urlInput = document.getElementById('urlInput');
		const scrapeMessage = document.getElementById('scrapeMessage');
		const scrapePreview = document.getElementById('scrapePreview');

		fetchBtn.addEventListener('click', async () => {
			const url = urlInput.value.trim();

			if (!url) {
				showMessage('URL tidak boleh kosong', 'error');
				return;
			}

			fetchBtn.disabled = true;
			fetchBtn.textContent = 'Mengambil...';
			scrapeMessage.style.display = 'none';
			scrapePreview.style.display = 'none';

			try {
				const formData = new FormData();
				formData.append('fetch_url', '1');
				formData.append('url', url);

				const response = await fetch('', {
					method: 'POST',
					body: formData
				});

				const result = await response.json();

				if (result.success) {
					const data = result.data;
					document.querySelector('[name="judul"]').value = data.judul;
					document.querySelector('[name="isi"]').value = data.deskripsi;
					document.querySelector('[name="url_sumber"]').value = data.url_sumber;
					if (data.thumbnail) {
						document.querySelector('[name="thumbnail"]').value = data.thumbnail;
					}
					document.querySelector('[name="is_eksternal"]').checked = true;

					showPreview(data);
					showMessage('Data berhasil diambil! Modifikasi jika diperlukan.', 'success');
				} else {
					showMessage(result.message || 'Gagal mengambil data', 'error');
				}
			} catch (error) {
				showMessage('Error: ' + error.message, 'error');
			} finally {
				fetchBtn.disabled = false;
				fetchBtn.textContent = 'Ambil Data';
			}
		});

		function showMessage(msg, type) {
			scrapeMessage.textContent = msg;
			scrapeMessage.style.display = 'block';
			scrapeMessage.style.background = type === 'success' ? '#ecfeff' : '#fef2f2';
			scrapeMessage.style.borderLeft = type === 'success' ? '4px solid #06b6d4' : '4px solid #ef4444';
			scrapeMessage.style.color = type === 'success' ? '#0f766e' : '#b91c1c';
		}

		function showPreview(data) {
			scrapePreview.innerHTML = `
				<strong>Judul:</strong> ${data.judul}<br>
				<strong>Deskripsi:</strong> ${data.deskripsi}<br>
				${data.thumbnail ? '<strong>Thumbnail:</strong> <img src="' + data.thumbnail + '" style="max-width: 150px; margin-top: 8px; border-radius: 4px;">' : ''}
			`;
			scrapePreview.style.display = 'block';
		}

		urlInput.addEventListener('keypress', (e) => {
			if (e.key === 'Enter') {
				e.preventDefault();
				fetchBtn.click();
			}
		});
		</script>
	</main>
</div>

<script src="../assets/js/script.js"></script>
</body>
</html>

