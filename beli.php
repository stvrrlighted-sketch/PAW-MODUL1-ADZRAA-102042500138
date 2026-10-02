<?php
require __DIR__ . "/data.php";

header("Content-Type: application/json; charset=utf-8");

function balas($data, $kodeHttp = 200)
{
    http_response_code($kodeHttp);
    echo json_encode($data);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    balas(["berhasil" => false, "pesan" => "Permintaan tidak valid."], 405);
}

$id = isset($_POST["id"]) ? (int) $_POST["id"] : -1;
$jumlah = isset($_POST["jumlah"]) ? (int) $_POST["jumlah"] : 0;

if (!isset($daftarProduk[$id])) {
    balas(["berhasil" => false, "pesan" => "Produk tidak ditemukan."], 404);
}

$stok = $daftarProduk[$id]["stok"];

if ($stok <= 0) {
    balas(["berhasil" => false, "pesan" => "Maaf, produk ini sudah habis."], 409);
}

if ($jumlah < 1 || $jumlah > $stok) {
    balas(["berhasil" => false, "pesan" => "Jumlah melebihi stok. Stok tersisa: " . $stok], 409);
}

$sudahTerjual = isset($_SESSION["terjual"][$id]) ? $_SESSION["terjual"][$id] : 0;
$_SESSION["terjual"][$id] = $sudahTerjual + $jumlah;

balas([
    "berhasil" => true,
    "stokBaru" => $stok - $jumlah,
    "nomorPesanan" => "CS-" . mt_rand(100000, 999999),
]);
