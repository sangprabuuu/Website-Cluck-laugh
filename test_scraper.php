<?php
/**
 * Script Testing untuk ArtikelScraper
 * Buka di browser untuk test scraping dari URL berbeda
 */

include 'admin/ArtikelScraper.php';

echo '<style>
    body { font-family: Arial; margin: 20px; }
    .test-section { margin: 20px 0; padding: 20px; border: 1px solid #ddd; border-radius: 8px; }
    .success { background: #ecfff0; border-left: 4px solid #10b981; }
    .error { background: #fef2f2; border-left: 4px solid #ef4444; }
    .info { background: #ecfeff; border-left: 4px solid #06b6d4; }
    code { background: #f3f4f6; padding: 2px 6px; border-radius: 4px; }
</style>';

echo '<h1>Testing ArtikelScraper</h1>';

// Test cases
$testUrls = [
    'https://www.medium.com/flutter/flutter-state-management-with-getx-2948a1c14c5c' => 'Medium.com artikel',
    'https://www.detik.com/hiburan' => 'Detik.com',
    'https://www.wikipedia.org/wiki/Chicken' => 'Wikipedia',
];

foreach ($testUrls as $url => $label) {
    echo '<div class="test-section">';
    echo "<h3>Test: $label</h3>";
    echo "URL: <code>$url</code><br><br>";
    
    $result = ArtikelScraper::scrapeArticle($url);
    
    if ($result) {
        echo '<div class="success">';
        echo '<strong>✓ Scraping Berhasil!</strong><br><br>';
        echo '<strong>Judul:</strong> ' . htmlspecialchars($result['judul']) . '<br>';
        echo '<strong>Deskripsi:</strong> ' . htmlspecialchars($result['deskripsi']) . '<br>';
        if ($result['thumbnail']) {
            echo '<strong>Thumbnail:</strong> <a href="' . htmlspecialchars($result['thumbnail']) . '" target="_blank">Lihat gambar</a><br>';
            echo '<img src="' . htmlspecialchars($result['thumbnail']) . '" style="max-width: 200px; margin-top: 10px; border-radius: 4px;"><br>';
        } else {
            echo '<strong>Thumbnail:</strong> Tidak ditemukan<br>';
        }
        echo '</div>';
    } else {
        echo '<div class="error">';
        echo '<strong>✗ Scraping Gagal!</strong><br>';
        echo 'Kemungkinan penyebab:
        <ul>
            <li>URL tidak accessible/timeout</li>
            <li>Website memerlukan JavaScript (client-side rendering)</li>
            <li>Website memblokir automated requests</li>
            <li>Struktur HTML tidak standar</li>
        </ul>';
        echo '</div>';
    }
    
    echo '</div>';
}

echo '<div class="test-section info">';
echo '<h3>📝 Catatan Testing</h3>';
echo '<ul>
    <li>Script ini menggunakan PHP DOMDocument untuk parse HTML</li>
    <li>Hanya bekerja untuk server-rendered HTML</li>
    <li>Timeout: 10 detik per request</li>
    <li>Fallback otomatis jika meta tags tidak ditemukan</li>
    <li>Relative URLs otomatis convert ke absolute URLs</li>
</ul>';
echo '</div>';
?>
