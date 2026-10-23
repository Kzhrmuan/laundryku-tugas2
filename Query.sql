
USE laundryku;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE pesanan_laundry (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kode_pesanan VARCHAR(20) NOT NULL UNIQUE,
    nama_pelanggan VARCHAR(100) NOT NULL,
    no_telepon VARCHAR(20) NOT NULL,
    berat DECIMAL(6,2) NOT NULL,
    jenis_layanan VARCHAR(50) NOT NULL,
    harga_per_kg DECIMAL(10,2) NOT NULL,
    total_biaya DECIMAL(12,2) NOT NULL,
    tanggal_masuk DATE NOT NULL,
    status ENUM(
        'Menunggu',
        'Diproses',
        'Selesai',
        'Sudah Diambil'
    ) NOT NULL DEFAULT 'Menunggu',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
