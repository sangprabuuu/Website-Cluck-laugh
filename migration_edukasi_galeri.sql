-- Tambah table edukasi
CREATE TABLE IF NOT EXISTS edukasi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    judul VARCHAR(255) NOT NULL,
    isi TEXT NOT NULL,
    urutan INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Update table galeri dengan timestamp
ALTER TABLE galeri ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP IF NOT EXISTS;

-- Insert data edukasi default jika table kosong
INSERT INTO edukasi (judul, isi, urutan) SELECT 'Asal-usul', 'Ayam ketawa berasal dari Sulawesi Selatan dan terkenal karena suara kokok unik yang menyerupai tawa.', 1 WHERE NOT EXISTS (SELECT 1 FROM edukasi);
INSERT INTO edukasi (judul, isi, urutan) SELECT 'Perawatan Dasar', 'Jaga kebersihan kandang, atur pakan seimbang, dan pastikan ayam mendapat cukup sinar matahari.', 2 WHERE NOT EXISTS (SELECT 1 FROM edukasi WHERE judul = 'Perawatan Dasar');
INSERT INTO edukasi (judul, isi, urutan) SELECT 'Latihan Vokal', 'Rutin memperdengarkan suara ayam ketawa berkualitas dapat membantu pembentukan pola kokok.', 3 WHERE NOT EXISTS (SELECT 1 FROM edukasi WHERE judul = 'Latihan Vokal');
