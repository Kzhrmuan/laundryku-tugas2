
<?php
require_once "auth.php";
require_once "config.php";

$bahasa = $_SESSION["bahasa"] ?? "id";
$error = "";

$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    header("Location: index.php");
    exit;
}

$stmt = $pdo->prepare(
    "SELECT * FROM pesanan_laundry WHERE id = ?"
);
$stmt->execute([$id]);
$pesanan = $stmt->fetch();

if (!$pesanan) {
    header("Location: index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nama = trim($_POST["nama_pelanggan"] ?? "");
    $telepon = trim($_POST["no_telepon"] ?? "");
    $berat = filter_var($_POST["berat"] ?? "", FILTER_VALIDATE_FLOAT);
    $layanan = $_POST["jenis_layanan"] ?? "";
    $tanggal = $_POST["tanggal_masuk"] ?? "";
    $status = $_POST["status"] ?? "";

    $harga = [
        "Cuci Kering" => 5000,
        "Cuci Setrika" => 7000,
        "Setrika Saja" => 4000
    ];

    $statusValid = [
        "Menunggu",
        "Diproses",
        "Selesai",
        "Sudah Diambil"
    ];

    if (
        $nama === "" ||
        $telepon === "" ||
        $berat === false ||
        $berat <= 0 ||
        !isset($harga[$layanan]) ||
        !in_array($status, $statusValid, true) ||
        $tanggal === ""
    ) {
        $error = $bahasa === "en"
            ? "Please complete all fields correctly."
            : "Lengkapi semua data dengan benar.";
    } elseif (!preg_match('/^[0-9+ -]{8,20}$/', $telepon)) {
        $error = $bahasa === "en"
            ? "Invalid phone number."
            : "Nomor telepon tidak valid.";
    } else {
        $hargaPerKg = $harga[$layanan];
        $totalBiaya = $berat * $hargaPerKg;

        $stmt = $pdo->prepare(
            "UPDATE pesanan_laundry
             SET nama_pelanggan = ?,
                 no_telepon = ?,
                 berat = ?,
                 jenis_layanan = ?,
                 harga_per_kg = ?,
                 total_biaya = ?,
                 tanggal_masuk = ?,
                 status = ?
             WHERE id = ?"
        );

        $stmt->execute([
            $nama,
            $telepon,
            $berat,
            $layanan,
            $hargaPerKg,
            $totalBiaya,
            $tanggal,
            $status,
            $id
        ]);

        header("Location: index.php");
        exit;
    }

    $pesanan["nama_pelanggan"] = $nama;
    $pesanan["no_telepon"] = $telepon;
    $pesanan["berat"] = $_POST["berat"] ?? "";
    $pesanan["jenis_layanan"] = $layanan;
    $pesanan["tanggal_masuk"] = $tanggal;
    $pesanan["status"] = $status;
}

function h($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, "UTF-8");
}

function selected($actual, $expected) {
    return $actual === $expected ? "selected" : "";
}
?>
<!DOCTYPE html>
<html lang="<?= $bahasa === "en" ? "en" : "id" ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $bahasa === "en" ? "Edit Order" : "Edit Pesanan" ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f1f5f9;
            padding: 24px;
        }
        .box {
            max-width: 600px;
            margin: auto;
            background: white;
            padding: 24px;
            border-radius: 12px;
        }
        input, select {
            width: 100%;
            padding: 10px;
            margin: 8px 0 16px;
            box-sizing: border-box;
        }
        button, a {
            display: inline-block;
            padding: 10px 14px;
            border: 0;
            border-radius: 6px;
            background: #0f766e;
            color: white;
            text-decoration: none;
            cursor: pointer;
        }
        .error { color: #b91c1c; }
    </style>
</head>
<body>
<div class="box">
    <h1><?= $bahasa === "en" ? "Edit Laundry Order" : "Edit Pesanan Laundry" ?></h1>

    <?php if ($error !== ""): ?>
        <p class="error"><?= h($error) ?></p>
    <?php endif; ?>

    <p>
        <?= $bahasa === "en" ? "Order Code" : "Kode Pesanan" ?>:
        <strong><?= h($pesanan["kode_pesanan"]) ?></strong>
    </p>

    <form method="POST" action="edit.php?id=<?= (int)$id ?>">
        <label><?= $bahasa === "en" ? "Customer Name" : "Nama Pelanggan" ?></label>
        <input type="text" name="nama_pelanggan" maxlength="100" required
               value="<?= h($pesanan["nama_pelanggan"]) ?>">

        <label><?= $bahasa === "en" ? "Phone Number" : "Nomor Telepon" ?></label>
        <input type="tel" name="no_telepon" maxlength="20" required
               value="<?= h($pesanan["no_telepon"]) ?>">

        <label><?= $bahasa === "en" ? "Weight (kg)" : "Berat Cucian (kg)" ?></label>
        <input type="number" name="berat" min="0.1" max="9999.99"
               step="0.1" required value="<?= h($pesanan["berat"]) ?>">

        <label><?= $bahasa === "en" ? "Service Type" : "Jenis Layanan" ?></label>
        <select name="jenis_layanan" required>
            <option value="Cuci Kering" <?= selected($pesanan["jenis_layanan"], "Cuci Kering") ?>>Cuci Kering</option>
            <option value="Cuci Setrika" <?= selected($pesanan["jenis_layanan"], "Cuci Setrika") ?>>Cuci Setrika</option>
            <option value="Setrika Saja" <?= selected($pesanan["jenis_layanan"], "Setrika Saja") ?>>Setrika Saja</option>
        </select>

        <label><?= $bahasa === "en" ? "Date Received" : "Tanggal Masuk" ?></label>
        <input type="date" name="tanggal_masuk" required
               value="<?= h($pesanan["tanggal_masuk"]) ?>">

        <label><?= $bahasa === "en" ? "Order Status" : "Status Pesanan" ?></label>
        <select name="status" required>
            <option value="Menunggu" <?= selected($pesanan["status"], "Menunggu") ?>>Menunggu</option>
            <option value="Diproses" <?= selected($pesanan["status"], "Diproses") ?>>Diproses</option>
            <option value="Selesai" <?= selected($pesanan["status"], "Selesai") ?>>Selesai</option>
            <option value="Sudah Diambil" <?= selected($pesanan["status"], "Sudah Diambil") ?>>Sudah Diambil</option>
        </select>

        <button type="submit">
            <?= $bahasa === "en" ? "Save Changes" : "Simpan Perubahan" ?>
        </button>
        <a href="index.php">
            <?= $bahasa === "en" ? "Cancel" : "Batal" ?>
        </a>
    </form>
</div>
</body>
</html>
