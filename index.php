<?php
ini_set("display_errors", "1");
error_reporting(E_ALL);

require __DIR__ . "/data.php";

define("BATAS_DISKON", 1000000);
define("PERSEN_DISKON", 10);

function formatRupiah($angka)
{
    return "Rp" . number_format($angka, 0, ",", ".");
}

function dapatDiskon($harga)
{
    return $harga >= BATAS_DISKON;
}

function hitungHargaDiskon($harga)
{
    return (int) round($harga - ($harga * PERSEN_DISKON / 100));
}

function tentukanStatus($stok)
{
    if ($stok > 0) {
        return "Tersedia";
    }
    return "Stok Habis";
}

function ikonKategori($kategori)
{
    $ikon = [
        "Skincare"    => 'Skincare.png',
        "Makeup"     => 'Makeup.png',
        "Perawatan" => 'Perawatan.png',
    ];
    $namaGambar = $ikon[$kategori];
    $pathGambar = 'assets/' . $namaGambar;

    if (file_exists($pathGambar)) {
        return '<img src="' . $pathGambar . '" alt="' . htmlspecialchars($kategori) . '">';
    }

    return htmlspecialchars($kategori);
}

$totalProduk = count($daftarProduk);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cia Store - Katalog Produk</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header class="navbar">
    <div class="kontainer navbar-isi">
        <a href="#beranda" class="logo">Cia Store</a>
        <nav class="menu" aria-label="Menu utama">
            <a href="#beranda">Beranda</a>
            <a href="#produk">Produk</a>
            <a href="#tentang">Tentang</a>
        </nav>
    </div>
</header>

<main>
    <section class="kontainer" id="beranda">
        <div class="hero">
            <h1>RAVÉLLA</h1>
            <p class="hero-teks">Temukan berbagai produk kecantikan untuk kebutuhanmu.</p>
            <a href="#produk" class="tombol tombol-terang">Lihat Produk</a>
        </div>
    </section>

    <section class="kontainer katalog" id="produk">
        <div class="katalog-kepala">
            <div>
                <p class="label label-gelap">Produk Kami</p>
                <h2>Katalog Produk</h2>
            </div>
            <span class="lencana-total">Total Produk: <strong><?= $totalProduk ?></strong></span>
        </div>

        <div class="grid-produk">
            <?php foreach ($daftarProduk as $id => $produk): ?>
                <?php
                $status = tentukanStatus($produk["stok"]);
                $tersedia = $produk["stok"] > 0;
                $diskon = dapatDiskon($produk["harga"]);
                $hargaAkhir = $diskon ? hitungHargaDiskon($produk["harga"]) : $produk["harga"];
                ?>
                <article class="kartu">
                    <div class="kartu-atas">
                        <span class="ikon"><?= ikonKategori($produk["kategori"]) ?></span>
                        <?php if ($diskon): ?>
                            <span class="lencana-diskon">Diskon <?= PERSEN_DISKON ?>%</span>
                        <?php endif; ?>
                    </div>

                    <p class="kategori"><?= htmlspecialchars($produk["kategori"]) ?></p>
                    <h3><?= htmlspecialchars($produk["nama"]) ?></h3>

                    <div class="harga">
                        <?php if ($diskon): ?>
                            <span class="harga-coret"><?= formatRupiah($produk["harga"]) ?></span>
                            <span class="harga-akhir"><?= formatRupiah(hitungHargaDiskon($produk["harga"])) ?></span>
                        <?php else: ?>
                            <span class="harga-akhir"><?= formatRupiah($produk["harga"]) ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="kartu-info">
                        <span>Stok: <?= $produk["stok"] ?></span>
                        <span class="status <?= $tersedia ? "status-tersedia" : "status-habis" ?>"><?= $status ?></span>
                    </div>

                    <?php if ($tersedia): ?>
                        <button type="button" class="tombol tombol-gelap tombol-beli"
                            data-id="<?= $id ?>"
                            data-nama="<?= htmlspecialchars($produk["nama"]) ?>"
                            data-kategori="<?= htmlspecialchars($produk["kategori"]) ?>"
                            data-harga="<?= $hargaAkhir ?>"
                            data-stok="<?= $produk["stok"] ?>">Beli Sekarang</button>
                    <?php else: ?>
                        <button type="button" class="tombol tombol-gelap tombol-beli"
                            aria-disabled="true"
                            data-nama="<?= htmlspecialchars($produk["nama"]) ?>">Beli Sekarang</button>
                    <?php endif; ?>
                </article>
            <?php endforeach; ?>
        </div>
    </section>
</main>

<footer class="footer" id="tentang">
    <div class="kontainer footer-isi">
        <div>
            <strong>Cia Store</strong>
            <p>Toko Produk Kecantikan .</p>
        </div>
        <p>&copy; <?= date("Y") ?> Cia Store. Semua hak dilindungi.</p>
    </div>
</footer>

<div class="notifikasi" id="notifikasi" role="status" aria-live="polite"></div>

<dialog class="modal" id="modalBayar" aria-labelledby="judulModal">
    <div id="tampilanBayar">
        <div class="modal-kepala">
            <h2 id="judulModal">Pembayaran</h2>
            <button type="button" class="tombol-tutup" data-tutup aria-label="Tutup">&times;</button>
        </div>

        <div class="ringkasan">
            <p class="kategori" id="modalKategori"></p>
            <h3 id="modalNama"></h3>
            <p class="ringkasan-harga">Harga satuan: <strong id="modalHargaSatuan"></strong></p>
        </div>

        <div class="baris-jumlah">
            <span>Jumlah</span>
            <div class="pengatur-jumlah">
                <button type="button" id="kurangiJumlah" aria-label="Kurangi jumlah">&minus;</button>
                <output id="modalJumlah">1</output>
                <button type="button" id="tambahJumlah" aria-label="Tambah jumlah">+</button>
            </div>
        </div>
        <p class="catatan-stok" id="modalStok"></p>

        <fieldset class="metode">
            <legend>Metode pembayaran</legend>
            <label class="pilihan"><input type="radio" name="metode" value="Transfer Bank" checked> Transfer Bank</label>
            <label class="pilihan"><input type="radio" name="metode" value="E-Wallet"> E-Wallet</label>
            <label class="pilihan"><input type="radio" name="metode" value="QRIS"> QRIS</label>
        </fieldset>

        <div class="total">
            <span>Total bayar</span>
            <strong id="modalTotal"></strong>
        </div>

        <button type="button" class="tombol tombol-gelap" id="tombolBayar">Bayar Sekarang</button>
    </div>

    <div id="tampilanSukses" hidden>
        <div class="sukses">
            <span class="ikon ikon-sukses">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12.5l4.5 4.5L19 7.5"/></svg>
            </span>
            <h2>Pembayaran Berhasil</h2>
            <p id="teksSukses"></p>
            <button type="button" class="tombol tombol-gelap" data-tutup>Tutup</button>
        </div>
    </div>
</dialog>

<script src="script.js"></script>
</body>
</html>
