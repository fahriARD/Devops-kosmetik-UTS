<?php
$host = getenv('DB_HOST') ?: "mysql";
$user = getenv('DB_USER') ?: "root";
$pass = getenv('DB_PASSWORD') ?: "root";
$dbname = getenv('DB_NAME') ?: "produk_db";

$conn = new mysqli($host, $user, $pass);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Buat database jika belum ada
$conn->query("CREATE DATABASE IF NOT EXISTS $dbname");

// Pilih database
$conn->select_db($dbname);

// Buat tabel products jika belum ada
$conn->query("
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    stock INT,
    price DECIMAL(10,2)
)
");
?>
