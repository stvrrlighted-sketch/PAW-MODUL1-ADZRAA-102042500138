const modalBayar = document.getElementById("modalBayar");
const tampilanBayar = document.getElementById("tampilanBayar");
const tampilanSukses = document.getElementById("tampilanSukses");
const notifikasi = document.getElementById("notifikasi");
const teksJumlah = document.getElementById("modalJumlah");

let produkDipilih = null;
let jumlah = 1;
let waktuNotifikasi = null;
let adaPembelian = false;

function formatRupiah(angka) {
    return "Rp" + angka.toLocaleString("id-ID");
}

function tampilkanNotifikasi(pesan) {
    notifikasi.textContent = pesan;
    notifikasi.classList.add("tampil");
    clearTimeout(waktuNotifikasi);
    waktuNotifikasi = setTimeout(function () {
        notifikasi.classList.remove("tampil");
    }, 2800);
}

function perbaruiTotal() {
    teksJumlah.textContent = jumlah;
    document.getElementById("modalTotal").textContent = formatRupiah(produkDipilih.harga * jumlah);
    document.getElementById("kurangiJumlah").disabled = jumlah <= 1;
    document.getElementById("tambahJumlah").disabled = jumlah >= produkDipilih.stok;
}

function bukaPembayaran(tombol) {
    produkDipilih = {
        id: tombol.dataset.id,
        nama: tombol.dataset.nama,
        kategori: tombol.dataset.kategori,
        harga: parseInt(tombol.dataset.harga, 10),
        stok: parseInt(tombol.dataset.stok, 10)
    };
    jumlah = 1;

    document.getElementById("modalNama").textContent = produkDipilih.nama;
    document.getElementById("modalKategori").textContent = produkDipilih.kategori;
    document.getElementById("modalHargaSatuan").textContent = formatRupiah(produkDipilih.harga);
    document.getElementById("modalStok").textContent = "Stok tersedia: " + produkDipilih.stok;

    tampilanBayar.hidden = false;
    tampilanSukses.hidden = true;
    perbaruiTotal();
    modalBayar.showModal();
}

function prosesPembayaran() {
    const tombolBayar = document.getElementById("tombolBayar");
    const metode = document.querySelector('input[name="metode"]:checked').value;
    const total = formatRupiah(produkDipilih.harga * jumlah);
    const data = new URLSearchParams({ id: produkDipilih.id, jumlah: jumlah, metode: metode });

    tombolBayar.disabled = true;

    fetch("beli.php", { method: "POST", body: data })
        .then(function (respons) {
            return respons.json();
        })
        .then(function (hasil) {
            if (!hasil.berhasil) {
                modalBayar.close();
                tampilkanNotifikasi(hasil.pesan);
                setTimeout(function () {
                    location.reload();
                }, 2000);
                return;
            }

            document.getElementById("teksSukses").textContent =
                "Pesanan " + hasil.nomorPesanan + " (" + jumlah + " x " + produkDipilih.nama + ") sebesar " +
                total + " melalui " + metode + " sudah kami terima. Sisa stok: " + hasil.stokBaru + ".";

            adaPembelian = true;
            tampilanBayar.hidden = true;
            tampilanSukses.hidden = false;
        })
        .catch(function () {
            modalBayar.close();
            tampilkanNotifikasi("Pembayaran gagal. Pastikan halaman dibuka lewat server PHP.");
        })
        .finally(function () {
            tombolBayar.disabled = false;
        });
}

document.querySelectorAll(".tombol-beli").forEach(function (tombol) {
    tombol.addEventListener("click", function () {
        if (tombol.getAttribute("aria-disabled") === "true") {
            tampilkanNotifikasi("Maaf, " + tombol.dataset.nama + " sedang habis.");
            return;
        }
        bukaPembayaran(tombol);
    });
});

document.getElementById("kurangiJumlah").addEventListener("click", function () {
    if (jumlah > 1) {
        jumlah--;
        perbaruiTotal();
    }
});

document.getElementById("tambahJumlah").addEventListener("click", function () {
    if (jumlah < produkDipilih.stok) {
        jumlah++;
        perbaruiTotal();
    }
});

document.getElementById("tombolBayar").addEventListener("click", prosesPembayaran);

document.querySelectorAll("[data-tutup]").forEach(function (tombol) {
    tombol.addEventListener("click", function () {
        modalBayar.close();
    });
});

modalBayar.addEventListener("click", function (peristiwa) {
    if (peristiwa.target === modalBayar) {
        modalBayar.close();
    }
});

modalBayar.addEventListener("close", function () {
    if (adaPembelian) {
        adaPembelian = false;
        location.reload();
    }
});
