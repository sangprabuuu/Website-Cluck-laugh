-- Tambah table edukasi
CREATE TABLE IF NOT EXISTS edukasi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    judul VARCHAR(255) NOT NULL,
    isi TEXT NOT NULL,
    urutan INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Update table galeri dengan timestamp
-- Menggunakan prosedur untuk mensimulasikan ADD COLUMN IF NOT EXISTS
DELIMITER //
CREATE PROCEDURE AddCreatedAtToGaleri()
BEGIN
    IF NOT EXISTS (
        SELECT * FROM INFORMATION_SCHEMA.COLUMNS 
        WHERE TABLE_SCHEMA = 'ayam_ketawa' 
        AND TABLE_NAME = 'galeri' 
        AND COLUMN_NAME = 'created_at'
    ) THEN
        ALTER TABLE galeri ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP;
    END IF;
END //
DELIMITER ;
CALL AddCreatedAtToGaleri();
DROP PROCEDURE AddCreatedAtToGaleri;

-- Insert data edukasi default jika table kosong
INSERT INTO edukasi (judul, isi, urutan) 
SELECT 'Asal-usul', 'Ayam ketawa berasal dari Sulawesi Selatan dan terkenal karena suara kokok unik yang menyerupai tawa.', 1 
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM edukasi WHERE judul = 'Asal-usul');

INSERT INTO edukasi (judul, isi, urutan) 
SELECT 'Perawatan Dasar', 'Jaga kebersihan kandang, atur pakan seimbang, dan pastikan ayam mendapat cukup sinar matahari.', 2 
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM edukasi WHERE judul = 'Perawatan Dasar');

INSERT INTO edukasi (judul, isi, urutan) 
SELECT 'Latihan Vokal', 'Rutin memperdengarkan suara ayam ketawa berkualitas dapat membantu pembentukan pola kokok.', 3 
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM edukasi WHERE judul = 'Latihan Vokal');
