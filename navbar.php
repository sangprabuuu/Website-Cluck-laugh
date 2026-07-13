<?php
$prefix = '';
$path = str_replace('\\', '/', $_SERVER['PHP_SELF'] ?? '');

if (strpos($path, '/pages/') !== false || strpos($path, '/admin/') !== false) {
    $prefix = '../';
}
?>
<nav class="navbar">
    <div class="navbar-logo">
        <a href="<?= $prefix; ?>index.php" class="logo-link">
            <img src="<?= $prefix; ?>assets/img/Logo_CluckLaugh.png" alt="Cluck Laugh" class="logo-img">
            <span class="logo-text">Cluck Laugh</span>
        </a>
    </div>
    <div class="navbar-right">
        <div class="navbar-menu">
            <a href="<?= $prefix; ?>index.php">Beranda</a>
            <a href="<?= $prefix; ?>pages/edukasi.php">Edukasi</a>
            <a href="<?= $prefix; ?>pages/artikel.php">Artikel</a>
            <a href="<?= $prefix; ?>pages/galeri.php">Galeri Suara</a>
        </div>
        <div class="navbar-login">
            <a href="<?= $prefix; ?>admin/login.php" class="btn-login">Login</a>
        </div>
    </div>
</nav>
