
<?php
require_once "auth.php";
require_once "config.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.php");
    exit;
}

$id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    header("Location: index.php");
    exit;
}

$stmt = $pdo->prepare(
    "DELETE FROM pesanan_laundry WHERE id = ?"
);
$stmt->execute([$id]);

header("Location: index.php");
exit;
?>
