<?php
session_start();

$daftarProduk = [
    ["nama" => "Serum Vitamin C 50ml",     "kategori" => "Skincare",    "harga" => 300000, "stok" => 4],
    ["nama" => "Moisturizer Ceramide", "kategori" => "Skincare",     "harga" => 1000000, "stok" => 3],
    ["nama" => "Eye cream",   "kategori" => "Skincare", "harga" => 500000, "stok" => 5],
    ["nama" => "Foundation",  "kategori" => "Makeup",  "harga" => 150000,  "stok" => 12],
    ["nama" => "Lipstik Matte",  "kategori" => "Makeup",  "harga" => 20000,  "stok" => 8],
    ["nama" => "Palet Eyeshadow",        "kategori" => "Makeup",  "harga" => 250000,  "stok" => 0],
    ["nama" => "Body Wash Strawberry",      "kategori" => "Perawatan",     "harga" => 780000,  "stok" => 0],
    ["nama" => "Body Scrub","kategori" => "Perawatan",  "harga" => 350000,  "stok" => 15],
];

if (isset($_GET["reset"])) {
    unset($_SESSION["terjual"]);
    header("Location: index.php");
    exit;
}

foreach ($daftarProduk as $id => $produk) {
    $terjual = isset($_SESSION["terjual"][$id]) ? $_SESSION["terjual"][$id] : 0;
    $daftarProduk[$id]["stok"] = max(0, $produk["stok"] - $terjual);
}
