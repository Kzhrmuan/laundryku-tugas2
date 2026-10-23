
<?php
require_once "auth.php";
require_once "config.php";

// Pengaturan bahasa
$bahasa = $_SESSION["bahasa"] ?? "id";

$teks = [
    "id" => [
        "welcome" => "Selamat datang",
        "total" => "Total Pesanan",
        "waiting" => "Menunggu",
        "processing" => "Diproses",
        "finished" => "Selesai",
        "list" => "Daftar Pesanan Laundry",
        "add" => "Tambah Pesanan",
        "code" => "Kode",
        "customer" => "Nama Pelanggan",
        "phone" => "Telepon",
        "weight" => "Berat (kg)",
        "service" => "Layanan",
        "cost" => "Total Biaya",
        "date" => "Tanggal Masuk",
        "status" => "Status",
        "action" => "Aksi",
        "empty" => "Belum ada pesanan laundry.",
        "edit" => "Edit",
        "delete" => "Hapus",
        "confirm" => "Yakin ingin menghapus pesanan ini?",
        "taken" => "Sudah Diambil",
        "dry" => "Cuci Kering",
        "wash_iron" => "Cuci Setrika",
        "iron" => "Setrika Saja",
        "logout" => "Logout"
    ],
    "en" => [
        "welcome" => "Welcome",
        "total" => "Total Orders",
        "waiting" => "Waiting",
        "processing" => "Processing",
        "finished" => "Completed",
        "list" => "Laundry Orders",
        "add" => "Add Order",
        "code" => "Code",
        "customer" => "Customer Name",
        "phone" => "Phone",
        "weight" => "Weight (kg)",
        "service" => "Service",
        "cost" => "Total Cost",
        "date" => "Order Date",
        "status" => "Status",
        "action" => "Actions",
        "empty" => "There are no laundry orders yet.",
        "edit" => "Edit",
        "delete" => "Delete",
        "confirm" => "Are you sure you want to delete this order?",
        "taken" => "Picked Up",
        "dry" => "Dry Cleaning",
        "wash_iron" => "Wash and Iron",
        "iron" => "Iron Only",
        "logout" => "Logout"
    ]
];

$t = $teks[$bahasa] ?? $teks["id"];

// Mengambil data pesanan
$stmt = $pdo->query(
    "SELECT * FROM pesanan_laundry ORDER BY id DESC"
);
$pesanan = $stmt->fetchAll();

// Menghitung statistik
$totalPesanan = count($pesanan);
$totalMenunggu = 0;
$totalDiproses = 0;
$totalSelesai = 0;

foreach ($pesanan as $item) {
    if ($item["status"] === "Menunggu") {
        $totalMenunggu++;
    }

    if ($item["status"] === "Diproses") {
        $totalDiproses++;
    }

    if (
        $item["status"] === "Selesai" ||
        $item["status"] === "Sudah Diambil"
    ) {
        $totalSelesai++;
    }
}

// Menerjemahkan status untuk tampilan
function terjemahkanStatus($status, $bahasa, $t)
{
    if ($bahasa === "en") {
        switch ($status) {
            case "Menunggu":
                return "Waiting";
            case "Diproses":
                return "Processing";
            case "Selesai":
                return "Completed";
            case "Sudah Diambil":
                return $t["taken"];
            default:
                return $status;
        }
    }

    return $status;
}

// Menerjemahkan nama layanan untuk tampilan
function terjemahkanLayanan($layanan, $bahasa, $t)
{
    if ($bahasa === "en") {
        switch ($layanan) {
            case "Cuci Kering":
                return $t["dry"];
            case "Cuci Setrika":
                return $t["wash_iron"];
            case "Setrika Saja":
                return $t["iron"];
            default:
                return $layanan;
        }
    }

    return $layanan;
}
?>
<!DOCTYPE html>
<html lang="<?= $bahasa === "en" ? "en" : "id" ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Dashboard LaundryKu</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: Arial, Helvetica, sans-serif;
            color: #153f79;
            background:
                radial-gradient(
                    circle at 10% 58%,
                    #e6f3ff 0 30px,
                    transparent 31px
                ),
                radial-gradient(
                    circle at 92% 58%,
                    #e9f4ff 0 180px,
                    transparent 181px
                ),
                linear-gradient(
                    125deg,
                    #f8fbff,
                    #edf6ff 55%,
                    #f9fcff
                );
            position: relative;
            overflow-x: hidden;
        }

        /* Ornamen pojok kiri atas */
        body::before {
            content: "";
            position: fixed;
            top: -100px;
            left: -100px;
            width: 330px;
            height: 260px;
            background: #bfddff;
            border-radius: 40% 0 65% 0;
            transform: rotate(-10deg);
            z-index: -1;
            pointer-events: none;
        }

        /* Ornamen pojok kanan bawah */
        body::after {
            content: "";
            position: fixed;
            bottom: -150px;
            right: -60px;
            width: 300px;
            height: 260px;
            background: #b7d9ff;
            border-radius: 70% 0 0 0;
            z-index: -1;
            pointer-events: none;
        }

        .container {
            width: min(1200px, 94%);
            margin: 0 auto;
            padding: 38px 0 55px;
            position: relative;
            z-index: 1;
        }

        /* HEADER */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
            flex-wrap: wrap;
            margin-bottom: 30px;
        }

        .brand h1 {
            color: #153f79;
            font-size: 29px;
            margin: 0 0 17px;
            font-weight: 800;
        }

        .brand p {
            margin: 0;
            font-size: 14px;
            color: #45617f;
        }

        .brand strong {
            color: #153f79;
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 7px;
            flex-wrap: wrap;
        }

        .btn {
            display: inline-block;
            text-decoration: none;
            text-align: center;
            border: none;
            border-radius: 7px;
            padding: 9px 13px;
            font-size: 13px;
            font-family: inherit;
            cursor: pointer;
            transition: 0.2s;
        }

        .btn:hover {
            transform: translateY(-1px);
            filter: brightness(0.95);
        }

        .btn-language {
            background: #435773;
            color: white;
        }

        .btn-logout {
            background: #e53935;
            color: white;
        }

        .btn-add {
            background: #078e93;
            color: white;
            padding: 10px 15px;
            font-weight: 600;
        }

        .btn-edit {
            background: #e8a323;
            color: #fff;
            padding: 7px 10px;
            margin: 2px;
        }

        .btn-delete {
            background: #dc3545;
            color: white;
            padding: 7px 10px;
            margin: 2px;
        }

        /* KARTU STATISTIK */
        .stats {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 12px;
            margin-bottom: 15px;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.92);
            border: 1px solid rgba(255, 255, 255, 0.95);
            border-radius: 14px;
            padding: 18px 16px;
            box-shadow: 0 8px 22px rgba(67, 133, 205, 0.10);
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 25px rgba(67, 133, 205, 0.15);
        }

        .stat-label {
            display: block;
            color: #45617f;
            font-size: 14px;
            margin-bottom: 2px;
        }

        .stat-number {
            color: #153f79;
            font-size: 25px;
            font-weight: 800;
        }

        .stat-card.total {
            border-top: 3px solid #2585d8;
        }

        .stat-card.waiting {
            border-top: 3px solid #e7a323;
        }

        .stat-card.processing {
            border-top: 3px solid #159ca5;
        }

        .stat-card.finished {
            border-top: 3px solid #28a66b;
        }

        /* PANEL TABEL */
        .table-card {
            background: rgba(255, 255, 255, 0.94);
            border: 1px solid rgba(255, 255, 255, 0.95);
            border-radius: 16px;
            padding: 22px 16px 15px;
            box-shadow: 0 8px 22px rgba(67, 133, 205, 0.10);
            overflow: hidden;
        }

        .table-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
            margin-bottom: 15px;
        }

        .table-header h2 {
            margin: 0;
            color: #153f79;
            font-size: 20px;
        }

        .table-wrapper {
            width: 100%;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            white-space: nowrap;
            font-size: 14px;
        }

        thead {
            background: #f4f8fc;
        }

        th {
            padding: 12px 10px;
            text-align: left;
            color: #183b67;
            font-weight: 700;
            border-bottom: 1px solid #dce7f2;
        }

        td {
            padding: 12px 10px;
            border-bottom: 1px solid #e2eaf3;
            color: #34516f;
        }

        tbody tr:hover {
            background: #f6faff;
        }

        .empty-row {
            text-align: center;
            padding: 25px 10px;
            color: #7a91aa;
        }

        .status {
            display: inline-block;
            padding: 6px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
        }

        .status-menunggu {
            background: #fff2d8;
            color: #9b6500;
        }

        .status-diproses {
            background: #dff5f5;
            color: #08777c;
        }

        .status-selesai {
            background: #def7e8;
            color: #167344;
        }

        .status-diambil {
            background: #e7edff;
            color: #4057a4;
        }

        .action-form {
            display: inline;
        }

        .currency {
            font-weight: 700;
            color: #153f79;
        }

        /* RESPONSIVE */
        @media (max-width: 850px) {
            .stats {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .container {
                width: 94%;
                padding-top: 25px;
            }
        }

        @media (max-width: 480px) {
            .header {
                align-items: flex-start;
                flex-direction: column;
            }

            .brand h1 {
                font-size: 25px;
            }

            .stats {
                grid-template-columns: 1fr 1fr;
                gap: 9px;
            }

            .stat-card {
                padding: 14px 12px;
            }

            .stat-label {
                font-size: 12px;
            }

            .stat-number {
                font-size: 22px;
            }

            .table-card {
                padding: 18px 12px 12px;
            }

            .table-header h2 {
                font-size: 18px;
            }
        }
    </style>
</head>

<body>
<div class="container">

    <!-- HEADER DASHBOARD -->
    <header class="header">
        <div class="brand">
            <h1>LaundryKu</h1>
            <p>
                <?= htmlspecialchars($t["welcome"], ENT_QUOTES, "UTF-8") ?>,
                <strong>
                    <?= htmlspecialchars($_SESSION["username"] ?? "Admin", ENT_QUOTES, "UTF-8") ?>
                </strong>
            </p>
        </div>

        <nav class="header-actions" aria-label="Menu utama">
            <a class="btn btn-language" href="bahasa.php?lang=id">
                Indonesia
            </a>

            <a class="btn btn-language" href="bahasa.php?lang=en">
                English
            </a>

            <a class="btn btn-logout" href="logout.php">
                <?= htmlspecialchars($t["logout"], ENT_QUOTES, "UTF-8") ?>
            </a>
        </nav>
    </header>

    <!-- STATISTIK -->
    <section class="stats">
        <div class="stat-card total">
            <span class="stat-label">
                <?= htmlspecialchars($t["total"], ENT_QUOTES, "UTF-8") ?>
            </span>
            <div class="stat-number"><?= $totalPesanan ?></div>
        </div>

        <div class="stat-card waiting">
            <span class="stat-label">
                <?= htmlspecialchars($t["waiting"], ENT_QUOTES, "UTF-8") ?>
            </span>
            <div class="stat-number"><?= $totalMenunggu ?></div>
        </div>

        <div class="stat-card processing">
            <span class="stat-label">
                <?= htmlspecialchars($t["processing"], ENT_QUOTES, "UTF-8") ?>
            </span>
            <div class="stat-number"><?= $totalDiproses ?></div>
        </div>

        <div class="stat-card finished">
            <span class="stat-label">
                <?= htmlspecialchars($t["finished"], ENT_QUOTES, "UTF-8") ?>
            </span>
            <div class="stat-number"><?= $totalSelesai ?></div>
        </div>
    </section>

    <!-- TABEL PESANAN -->
    <section class="table-card">
        <div class="table-header">
            <h2>
                <?= htmlspecialchars($t["list"], ENT_QUOTES, "UTF-8") ?>
            </h2>

            <a href="tambah.php" class="btn btn-add">
                + <?= htmlspecialchars($t["add"], ENT_QUOTES, "UTF-8") ?>
            </a>
        </div>

        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th><?= htmlspecialchars($t["code"], ENT_QUOTES, "UTF-8") ?></th>
                        <th><?= htmlspecialchars($t["customer"], ENT_QUOTES, "UTF-8") ?></th>
                        <th><?= htmlspecialchars($t["phone"], ENT_QUOTES, "UTF-8") ?></th>
                        <th><?= htmlspecialchars($t["weight"], ENT_QUOTES, "UTF-8") ?></th>
                        <th><?= htmlspecialchars($t["service"], ENT_QUOTES, "UTF-8") ?></th>
                        <th><?= htmlspecialchars($t["cost"], ENT_QUOTES, "UTF-8") ?></th>
                        <th><?= htmlspecialchars($t["date"], ENT_QUOTES, "UTF-8") ?></th>
                        <th><?= htmlspecialchars($t["status"], ENT_QUOTES, "UTF-8") ?></th>
                        <th><?= htmlspecialchars($t["action"], ENT_QUOTES, "UTF-8") ?></th>
                    </tr>
                </thead>

                <tbody>
                <?php if (empty($pesanan)): ?>
                    <tr>
                        <td colspan="9" class="empty-row">
                            <?= htmlspecialchars($t["empty"], ENT_QUOTES, "UTF-8") ?>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($pesanan as $item): ?>
                        <?php
                        $statusAsli = $item["status"];

                        $classStatus = "status-menunggu";

                        if ($statusAsli === "Diproses") {
                            $classStatus = "status-diproses";
                        } elseif ($statusAsli === "Selesai") {
                            $classStatus = "status-selesai";
                        } elseif ($statusAsli === "Sudah Diambil") {
                            $classStatus = "status-diambil";
                        }
                        ?>
                        <tr>
                            <td>
                                <strong>
                                    <?= htmlspecialchars($item["kode_pesanan"], ENT_QUOTES, "UTF-8") ?>
                                </strong>
                            </td>

                            <td>
                                <?= htmlspecialchars($item["nama_pelanggan"], ENT_QUOTES, "UTF-8") ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($item["no_telepon"], ENT_QUOTES, "UTF-8") ?>
                            </td>

                            <td>
                                <?= number_format((float)$item["berat"], 2, ",", ".") ?>
                            </td>

                            <td>
                                <?= htmlspecialchars(
                                    terjemahkanLayanan($item["jenis_layanan"], $bahasa, $t),
                                    ENT_QUOTES,
                                    "UTF-8"
                                ) ?>
                            </td>

                            <td class="currency">
                                Rp <?= number_format((float)$item["total_biaya"], 0, ",", ".") ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($item["tanggal_masuk"], ENT_QUOTES, "UTF-8") ?>
                            </td>

                            <td>
                                <span class="status <?= $classStatus ?>">
                                    <?= htmlspecialchars(
                                        terjemahkanStatus($statusAsli, $bahasa, $t),
                                        ENT_QUOTES,
                                        "UTF-8"
                                    ) ?>
                                </span>
                            </td>

                            <td>
                                <a
                                    href="edit.php?id=<?= (int)$item["id"] ?>"
                                    class="btn btn-edit"
                                >
                                    <?= htmlspecialchars($t["edit"], ENT_QUOTES, "UTF-8") ?>
                                </a>

                                <form
                                    class="action-form"
                                    method="POST"
                                    action="hapus.php"
                                    onsubmit="return confirm('<?= htmlspecialchars($t["confirm"], ENT_QUOTES, "UTF-8") ?>');"
                                >
                                    <input
                                        type="hidden"
                                        name="id"
                                        value="<?= (int)$item["id"] ?>"
                                    >

                                    <button type="submit" class="btn btn-delete">
                                        <?= htmlspecialchars($t["delete"], ENT_QUOTES, "UTF-8") ?>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>

</div>
</body>
</html>
