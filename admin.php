
<?php
require_once "config.php";

$username = "admin";
$password = password_hash("admin123", PASSWORD_DEFAULT);

try {
    $stmt = $pdo->prepare(
        "INSERT INTO users (username, password)
         VALUES (:username, :password)"
    );

    $stmt->execute([
        "username" => $username,
        "password" => $password
    ]);

    echo "Akun admin berhasil dibuat!";
} catch (PDOException $e) {
    echo "Akun admin mungkin sudah tersedia. ";
    echo "Periksa tabel users di HeidiSQL.";
}
?>
