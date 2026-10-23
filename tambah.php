
<?php
require_once "auth.php";
require_once "config.php";

$bahasa = $_SESSION["bahasa"] ?? "id";
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $kode = trim($_POST["kode_pesanan"] ?? "");
    $nama = trim($_POST["nama_pelanggan"] ?? "");
    $telepon = trim($_POST["no_telepon"] ?? "");
    $berat = filter_var($_POST["berat"] ?? "", FILTER_VALIDATE_FLOAT);
    $layanan = $_POST["jenis_layanan"] ?? "";
    $tanggal = $_POST["tanggal_masuk"] ?? "";
    $status = $_POST["status"] ?? "Menunggu";

    $layananValid = [
        "Cuci Kering",
        "Cuci Setrika",
        "Setrika Saja"
    ];

    $statusValid = [
        "Menunggu",
        "Diproses",
        "Selesai",
        "Sudah Diambil"
    ];

    if (
        $kode === "" || $nama === "" || $telepon === "" ||
        $berat === false || $berat <= 0 ||
        !in_array($layanan, $layananValid, true) ||
        !in_array($status, $statusValid, true) ||
        $tanggal === ""
    ) {
        $error = $bahasa === "en"
            ? "Please complete all fields correctly."
            : "Lengkapi semua data dengan benar.";
    } elseif (!preg_match('/^[A-Za-z0-9-]{3,20}$/', $kode)) {
        $error = $bahasa === "en"
            ? "Order code must contain 3-20 letters, numbers, or hyphens."
            : "Kode pesanan harus berisi 3-20 huruf, angka, atau tanda hubung.";
    } elseif (!preg_match('/^[0-9+ -]{8,20}$/', $telepon)) {
        $error = $bahasa === "en"
            ? "Invalid phone number."
            : "Nomor telepon tidak valid.";
    } else {
        $harga = [
            "Cuci Kering" => 5000,
            "Cuci Setrika" => 7000,
            "Setrika Saja" => 4000
        ];

        $hargaPerKg = $harga[$layanan];
        $totalBiaya = $berat * $hargaPerKg;

        try {
            $stmt = $pdo->prepare(
                "INSERT INTO pesanan_laundry
                (kode_pesanan, nama_pelanggan, no_telepon, berat,
                 jenis_layanan, harga_per_kg, total_biaya,
                 tanggal_masuk, status)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
            );

            $stmt->execute([
                $kode, $nama, $telepon, $berat,
                $layanan, $hargaPerKg, $totalBiaya,
                $tanggal, $status
            ]);

            header("Location: index.php");
            exit;
        } catch (PDOException $e) {
            $error = $bahasa === "en"
                ? "Could not save the order. Check whether the order code already exists."
                : "Pesanan gagal disimpan. Periksa apakah kode pesanan sudah digunakan.";
        }
    }
}

function h($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, "UTF-8");
}
?>
<!DOCTYPE html>
<html lang="<?= $bahasa === "en" ? "en" : "id" ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $bahasa === "en" ? "Add Laundry Order" : "Tambah Pesanan" ?></title>
    <style>
        body { font-family: Arial, sans-serif; background:#f1f5f9; padding:24px; }
        .form-box { max-width:600px; margin:auto; background:white; padding:24px; border-radius:12px; }
        input, select { width:100%; padding:10px; margin:8px 0 16px; box-sizing:border-box; }
        button, a { display:inline-block; padding:10px 14px; border:0; border-radius:6px; background:#0f766e; color:white; text-decoration:none; cursor:pointer; }
        .error { color:#b91c1c; }
    </style>
</head>
<body>
<div class="form-box">
    <h1><?= $bahasa === "en" ? "Add Laundry Order" : "Tambah Pesanan Laundry" ?></h1>

    <?php if ($error !== ""): ?>
        <p class="error"><?= h($error) ?></p>
    <?php endif; ?>

    <form method="POST" action="tambah.php">
        <label><?= $bahasa === "en" ? "Order Code" : "Kode Pesanan" ?></label>
        <input type="text" name="kode_pesanan" required maxlength="20"
               value="<?= h($_POST["kode_pesanan"] ?? "") ?>">

        <label><?= $bahasa === "en" ? "Customer Name" : "Nama Pelanggan" ?></label>
        <input type="text" name="nama_pelanggan" required maxlength="100"
               value="<?= h($_POST["nama_pelanggan"] ?? "") ?>">

        <label><?= $bahasa === "en" ? "Phone Number" : "Nomor Telepon" ?></label>
        <input type="tel" name="no_telepon" required maxlength="20"
               value="<?= h($_POST["no_telepon"] ?? "") ?>">

        <label><?= $bahasa === "en" ? "Weight (kg)" : "Berat Cucian (kg)" ?></label>
        <input type="number" name="berat" min="0.1" max="9999.99"
               step="0.1" required
               value="<?= h($_POST["berat"] ?? "") ?>">

        <label><?= $bahasa === "en" ? "Service Type" : "Jenis Layanan" ?></label>
        <select name="jenis_layanan" required>
            <option value=""><?= $bahasa === "en" ? "Select service" : "Pilih layanan" ?></option>
            <option value="Cuci Kering">Cuci Kering - Rp5.000/kg</option>
            <option value="Cuci Setrika">Cuci Setrika - Rp7.000/kg</option>
            <option value="Setrika Saja">Setrika Saja - Rp4.000/kg</option>
        </select>

        <label><?= $bahasa === "en" ? "Date Received" : "Tanggal Masuk" ?></label>
        <input type="date" name="tanggal_masuk" required
               value="<?= h($_POST["tanggal_masuk"] ?? date("Y-m-d")) ?>">

        <label><?= $bahasa === "en" ? "Status" : "Status Pesanan" ?></label>
        <select name="status">
            <option value="Menunggu">Menunggu</option>
            <option value="Diproses">Diproses</option>
            <option value="Selesai">Selesai</option>
            <option value="Sudah Diambil">Sudah Diambil</option>
        </select>

        <button type="submit"><?= $bahasa === "en" ? "Save Order" : "Simpan Pesanan" ?></button>
        <a href="index.php"><?= $bahasa === "en" ? "Back" : "Kembali" ?></a>
    </form>
</div>
</body>
</html>
