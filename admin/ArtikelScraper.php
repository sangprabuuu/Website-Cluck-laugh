<?php
/**
 * Utility untuk scraping data artikel dari URL
 * Mengambil: judul, deskripsi, thumbnail
 */

class ArtikelScraper {
    
    /**
     * Ambil data artikel dari URL
     * @param string $url URL artikel
     * @return array|false Data artikel atau false jika gagal
     */
    public static function scrapeArticle($url) {
        // Validasi URL
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return false;
        }

        try {
            // Set timeout dan user agent
            $context = stream_context_create([
                'http' => [
                    'timeout' => 10,
                    'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
                ]
            ]);

            // Ambil HTML dari URL
            $html = @file_get_contents($url, false, $context);
            
            if (!$html) {
                return false;
            }

            // Buat DOM Document
            $dom = new DOMDocument('1.0', 'UTF-8');
            @$dom->loadHTML('<?xml encoding="UTF-8"?>' . $html);
            
            $data = [
                'judul' => self::extractTitle($dom, $url),
                'deskripsi' => self::extractDescription($dom),
                'thumbnail' => self::extractThumbnail($dom, $url),
                'url_sumber' => $url
            ];

            // Validasi minimal
            if (empty($data['judul'])) {
                return false;
            }

            return $data;

        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Ambil judul dari HTML
     */
    private static function extractTitle($dom, $url) {
        $xpath = new DOMXPath($dom);

        // Cek og:title
        $ogTitle = $xpath->query("//meta[@property='og:title']/@content");
        if ($ogTitle->length > 0) {
            return trim($ogTitle->item(0)->nodeValue);
        }

        // Cek title tag
        $title = $xpath->query("//title");
        if ($title->length > 0) {
            $titleText = trim($title->item(0)->nodeValue);
            // Bersihkan title (hapus suffix domain)
            $titleText = preg_replace('/\s*[-|]\s*.*$/', '', $titleText);
            return $titleText;
        }

        // Fallback: ambil dari h1 pertama
        $h1 = $xpath->query("//h1");
        if ($h1->length > 0) {
            return trim($h1->item(0)->nodeValue);
        }

        return parse_url($url, PHP_URL_HOST);
    }

    /**
     * Ambil deskripsi dari HTML
     */
    private static function extractDescription($dom) {
        $xpath = new DOMXPath($dom);

        // Cek og:description
        $ogDesc = $xpath->query("//meta[@property='og:description']/@content");
        if ($ogDesc->length > 0) {
            return trim($ogDesc->item(0)->nodeValue);
        }

        // Cek meta description
        $metaDesc = $xpath->query("//meta[@name='description']/@content");
        if ($metaDesc->length > 0) {
            return trim($metaDesc->item(0)->nodeValue);
        }

        // Fallback: ambil teks dari paragraph pertama
        $paragraphs = $xpath->query("//p");
        foreach ($paragraphs as $p) {
            $text = trim($p->nodeValue);
            if (strlen($text) > 50) {
                // Potong ke 160 karakter
                return substr($text, 0, 160) . '...';
            }
        }

        return 'Artikel dari website eksternal';
    }

    /**
     * Ambil thumbnail dari HTML
     */
    private static function extractThumbnail($dom, $baseUrl) {
        $xpath = new DOMXPath($dom);

        // Cek og:image
        $ogImage = $xpath->query("//meta[@property='og:image']/@content");
        if ($ogImage->length > 0) {
            $imageUrl = trim($ogImage->item(0)->nodeValue);
            return self::normalizeUrl($imageUrl, $baseUrl);
        }

        // Cek twitter:image
        $twitterImage = $xpath->query("//meta[@name='twitter:image']/@content");
        if ($twitterImage->length > 0) {
            $imageUrl = trim($twitterImage->item(0)->nodeValue);
            return self::normalizeUrl($imageUrl, $baseUrl);
        }

        // Cek img pertama dalam article/main
        $images = $xpath->query("//article//img | //main//img");
        if ($images->length > 0) {
            $imgSrc = $images->item(0)->getAttribute('src');
            if (!empty($imgSrc)) {
                return self::normalizeUrl($imgSrc, $baseUrl);
            }
        }

        return null;
    }

    /**
     * Normalisasi URL gambar (handle relative URLs)
     */
    private static function normalizeUrl($url, $baseUrl) {
        // Jika sudah absolute URL
        if (filter_var($url, FILTER_VALIDATE_URL)) {
            return $url;
        }

        // Handle relative URLs
        $base = parse_url($baseUrl);
        $basePath = isset($base['path']) ? dirname($base['path']) : '';
        
        if (strpos($url, '/') === 0) {
            // Absolute path
            return $base['scheme'] . '://' . $base['host'] . $url;
        } else {
            // Relative path
            return $base['scheme'] . '://' . $base['host'] . $basePath . '/' . $url;
        }
    }
}
