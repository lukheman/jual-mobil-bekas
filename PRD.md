# PRD - Website Showroom Mobil Bekas

|  |  |
| --- | --- |
| Versi | 1.0 (draf) |
| Tanggal | 26 Oktober 2026 |
| Penulis / Pemilik produk | \[Nama Anda\] |
| Status | Draf untuk dikerjakan |

## 1. Ringkasan

Website untuk showroom mobil bekas yang menampilkan stok secara online dan mengubah pengunjung menjadi lead (calon pembeli yang menghubungi showroom). Website **tidak memproses pembayaran**; transaksi terjadi secara langsung setelah lead ditindaklanjuti.

## 2. Latar Belakang & Masalah

- Calon pembeli di wilayah provinsi target sulit menemukan informasi mobil bekas yang lengkap, jelas, dan terpercaya di satu tempat.
- Showroom bergantung pada marketplace atau WhatsApp saja, sehingga stok sulit dicari, tidak rapi, dan tidak terukur (tidak diketahui unit mana yang paling diminati).
- Tidak ada pencatatan lead yang terpusat.

## 3. Tujuan & Non-Tujuan

### Tujuan

1. Menampilkan seluruh stok aktif dengan foto dan spesifikasi lengkap.
2. Memudahkan pembeli mencari mobil sesuai budget dan kebutuhan lewat pencarian dan filter.
3. Menghasilkan lead melalui WhatsApp, telepon, dan formulir minat/test drive.
4. Memberi admin alat sederhana untuk mengelola stok dan lead tanpa bantuan developer.

### Non-Tujuan (versi 1.0)

- Pembayaran online, booking fee, atau kredit/leasing terintegrasi.
- Multi-penjual/marketplace.
- Aplikasi mobile native.
- Pengiriman unit dan integrasi dengan sistem akuntansi.

## 4. Target Pengguna

| Persona | Deskripsi | Kebutuhan utama |
| --- | --- | --- |
| Pembeli (pengunjung) | Calon pembeli mobil bekas di provinsi target, mayoritas memakai ponsel | Cari mobil sesuai budget, lihat foto & kondisi jujur, hubungi cepat |
| Admin showroom | Pemilik atau staf yang mengelola stok | Input mobil cepat, ubah status, kelola lead |

## 5. Metrik Keberhasilan

| Metrik | Cara ukur | Target awal (dapat disesuaikan) |
| --- | --- | --- |
| Lead per bulan | Data tabel lead + klik WhatsApp | Tetapkan setelah 1 bulan data dasar |
| Rasio konversi pengunjung ke lead | Analytics | Tetapkan setelah data dasar |
| Kecepatan halaman | Lighthouse / PageSpeed | Skor performa mobile 80 ke atas |
| Kelengkapan data unit | Persentase unit dengan 8+ foto dan spesifikasi lengkap | 90% |
| Waktu input satu unit oleh admin | Uji dengan pengguna admin | Di bawah 10 menit |

## 6. Ruang Lingkup Rilis 1.0

**Termasuk:** beranda, katalog dengan pencarian/filter/pengurutan, detail mobil, formulir minat dan test drive, tombol WhatsApp/telepon, halaman informasi (tentang, kontak & peta, FAQ, kebijakan privasi), panel admin (mobil, merek/model, foto, lead, halaman), SEO dasar, dan antarmuka responsif.

**Ditunda (rilis berikutnya):** bandingkan mobil, favorit, simulasi cicilan, formulir tukar tambah, blog, testimoni dinamis, notifikasi otomatis lanjutan.

## 7. User Stories & Kriteria Penerimaan

### Pembeli

**US-01 Mencari mobil.** Sebagai pembeli, saya ingin mencari dan memfilter mobil agar cepat menemukan yang sesuai budget.

- Filter tersedia: merek, model, tahun, rentang harga, transmisi, bahan bakar, tipe bodi, kilometer, kota/kabupaten.
- Hasil dapat diurutkan: terbaru, harga terendah, harga tertinggi, kilometer terendah.
- Hasil diperbarui tanpa membuat filter yang sudah dipilih hilang; URL memuat filter agar bisa dibagikan.
- Jika tidak ada hasil, tampil pesan kosong dengan saran melonggarkan filter.

**US-02 Melihat detail.** Sebagai pembeli, saya ingin melihat detail lengkap sebuah mobil agar yakin sebelum menghubungi.

- Menampilkan galeri foto (dapat digeser dan diperbesar), harga, spesifikasi, fitur, kondisi, kelengkapan surat, dan deskripsi.
- Menampilkan status (tersedia/dipesan/terjual); unit terjual tetap bisa dibuka tetapi tombol kontak dinonaktifkan dan diberi label jelas.
- Menampilkan mobil serupa (merek atau rentang harga yang mirip).

**US-03 Menghubungi showroom.** Sebagai pembeli, saya ingin menghubungi showroom dengan satu ketukan.

- Tombol WhatsApp membuka chat dengan pesan terisi otomatis (nama unit dan tautan halaman).
- Tombol telepon tersedia di ponsel.
- Setiap klik tercatat untuk analitik.

**US-04 Mengajukan minat / test drive.** Sebagai pembeli, saya ingin mengisi formulir agar showroom menghubungi saya.

- Field wajib: nama, nomor HP; opsional: email, jadwal yang diinginkan, catatan.
- Validasi nomor HP Indonesia; pesan kesalahan jelas.
- Perlindungan spam (honeypot dan pembatasan jumlah kiriman).
- Setelah kirim, tampil konfirmasi dan data tersimpan sebagai lead berstatus "baru".
- Ada persetujuan penggunaan data pribadi sebelum kirim.

### Admin

**US-05 Login aman.** Admin masuk dengan email dan kata sandi; akses panel admin hanya untuk pengguna terautentikasi. Percobaan login dibatasi.

**US-06 Mengelola mobil.** Admin dapat menambah, mengubah, mengarsipkan, dan menghapus mobil.

- Form mencakup seluruh atribut mobil (bagian 9) dengan validasi.
- Unggah banyak foto sekaligus, atur urutan, pilih foto utama; foto dikompres dan dibuat ukuran thumbnail otomatis.
- Slug URL dibuat otomatis dan unik.

**US-07 Mengubah status.** Admin dapat mengubah status unit dan menandai unit unggulan dalam dua klik dari daftar mobil.

**US-08 Mengelola lead.** Admin melihat daftar lead terbaru, memfilter menurut status, mengubah status (baru, dihubungi, test drive, deal, batal), menambah catatan, dan mengekspor ke CSV.

**US-09 Mengelola data master.** Admin dapat mengelola merek, model, dan daftar fitur.

**US-10 Mengelola konten.** Admin dapat mengubah banner beranda, nomor WhatsApp/telepon, alamat, jam buka, dan FAQ tanpa mengubah kode.

## 8. Kebutuhan Fungsional

| ID | Kebutuhan | Prioritas |
| --- | --- | --- |
| FR-01 | Katalog menampilkan kartu mobil (foto utama, nama, tahun, harga, kilometer, transmisi, kota) dengan paginasi | P0 |
| FR-02 | Pencarian teks pada nama, merek, dan model | P0 |
| FR-03 | Filter dan pengurutan sesuai US-01 | P0 |
| FR-04 | Halaman detail sesuai US-02 | P0 |
| FR-05 | Tombol WhatsApp dan telepon dengan pencatatan klik | P0 |
| FR-06 | Formulir minat/test drive dan penyimpanan lead | P0 |
| FR-07 | Panel admin: CRUD mobil, foto, status, unggulan | P0 |
| FR-08 | Panel admin: manajemen lead dan ekspor CSV | P0 |
| FR-09 | Panel admin: merek, model, fitur, pengaturan situs | P0 |
| FR-10 | Halaman statis: tentang, kontak & peta, FAQ, kebijakan privasi | P0 |
| FR-11 | Notifikasi email ke admin saat lead baru masuk | P1 |
| FR-12 | Dasbor ringkas: jumlah unit per status, lead per minggu, unit paling banyak dilihat | P1 |
| FR-13 | Sitemap XML dan data terstruktur untuk setiap unit | P0 |
| FR-14 | Penghitung jumlah dilihat per unit | P1 |
| FR-15 | Log aktivitas admin | P2 |

Keterangan prioritas: P0 wajib untuk rilis 1.0, P1 sebaiknya ada, P2 opsional.

## 9. Model Data (tingkat tinggi)

| Tabel | Atribut utama |
| --- | --- |
| users | id, nama, email, kata sandi (hash), peran |
| brands | id, nama, slug, logo |
| car\_models | id, brand\_id, nama, slug |
| cars | id, car\_model\_id, varian, slug, tahun, harga, kilometer, transmisi, bahan\_bakar, kapasitas\_mesin, tipe\_bodi, warna, jumlah\_pemilik, wilayah\_plat, masa\_pajak, kelengkapan\_surat, kondisi, deskripsi, kota, status, unggulan, jumlah\_dilihat, dibuat/diubah/diarsipkan |
| car\_photos | id, car\_id, path, urutan, utama |
| features | id, nama |
| car\_feature | car\_id, feature\_id |
| leads | id, car\_id (boleh kosong), nama, no\_hp, email, jenis (tanya/test drive), jadwal, pesan, status, catatan, dibuat |
| click\_logs | id, car\_id, jenis (whatsapp/telepon), dibuat |
| settings | kunci, nilai (nomor WhatsApp, alamat, jam buka, dsb.) |
| pages / faqs | konten halaman statis dan FAQ |

## 10. Peta Halaman

**Publik:** Beranda, Katalog (dengan filter), Detail Mobil, Tentang Kami, Kontak & Lokasi, FAQ, Kebijakan Privasi.

**Admin:** Login, Dasbor, Mobil (daftar, tambah, ubah), Merek & Model, Fitur, Lead (daftar, detail), Pengaturan Situs, Halaman & FAQ, Pengguna.

## 11. Kebutuhan Non-Fungsional

- **Performa:** halaman katalog dan detail termuat di bawah 3 detik pada jaringan seluler biasa; gambar WebP, lazy loading, dan ukuran responsif.
- **Responsif:** mobile-first dengan Bootstrap; diuji pada lebar 360 px ke atas.
- **SEO:** URL bersih, judul dan meta deskripsi per unit, Open Graph untuk pratinjau saat dibagikan, sitemap, schema.org (Vehicle/Product), SEO lokal untuk wilayah provinsi.
- **Keamanan:** HTTPS, hash kata sandi, perlindungan CSRF/XSS/SQL injection bawaan Laravel, validasi tipe dan ukuran file unggahan, pembatasan laju login dan formulir, peran akses admin.
- **Privasi:** persetujuan eksplisit pada formulir, kebijakan privasi, akses data lead hanya untuk admin; mengikuti prinsip UU PDP.
- **Keandalan:** backup basis data dan folder foto terjadwal; target uptime sekitar 99%.
- **Aksesibilitas:** kontras memadai, teks alternatif gambar, dapat dioperasikan dengan keyboard.
- **Lokalisasi:** Bahasa Indonesia, mata uang Rupiah, zona waktu WITA/WIB sesuai wilayah.
- **Kemudahan pemeliharaan:** kode mengikuti konvensi Laravel, migration dan seeder tersedia, uji otomatis untuk alur inti (filter, formulir lead, login admin).

## 12. Teknologi

- **Dipilih:** Laravel, MySQL, Bootstrap.
- **Disarankan:** Blade untuk templating, Laravel Breeze untuk autentikasi, Filament (atau panel buatan sendiri) untuk admin, Intervention Image untuk pengolahan foto.
- **Pendukung:** hosting dengan PHP 8.2 atau lebih baru dan MySQL, Cloudflare, Google Analytics dan Search Console.

## 13. Rencana Rilis

Dikerjakan oleh satu pengembang. Estimasi kasar total 8-12 minggu, bergantung pada waktu yang tersedia.

| Milestone | Isi | Hasil |
| --- | --- | --- |
| M1. Fondasi | Setup proyek, migration, seeder, autentikasi admin | Kerangka berjalan |
| M2. Admin stok | CRUD mobil, foto, merek/model/fitur, status | Admin bisa input unit |
| M3. Publik | Beranda, katalog, filter, detail | Pengunjung bisa mencari |
| M4. Lead | Tombol WhatsApp/telepon, formulir, daftar lead, ekspor | Lead tercatat |
| M5. Penyempurnaan | Halaman statis, SEO, performa, keamanan, uji | Siap rilis |
| M6. Rilis | Domain, hosting, SSL, analytics, isi data awal | Live |

## 14. Risiko & Mitigasi

| Risiko | Dampak | Mitigasi |
| --- | --- | --- |
| Cakupan melebar saat dikerjakan sendiri | Jadwal molor | Pegang daftar P0; fitur lain ke rilis berikutnya |
| Stok tidak diperbarui | Unit terjual masih tampil, pembeli kecewa | Ubah status dalam dua klik; rutinitas pengecekan mingguan |
| Foto berat | Situs lambat | Kompresi otomatis, WebP, CDN |
| Spam formulir | Lead tidak berkualitas | Honeypot, pembatasan laju, validasi |
| Kebocoran data lead | Pelanggaran privasi | Akses berperan, HTTPS, backup aman |
| Foto dan data awal belum siap | Peluncuran tertunda | Siapkan 10-20 unit lengkap sebelum M5 |

## 15. Asumsi & Pertanyaan Terbuka

**Asumsi:** showroom memiliki stok sendiri dan menangani transaksi secara langsung; ada nomor WhatsApp aktif untuk menerima lead; foto unit dipotret oleh showroom.

**Terbuka:**

1. Jumlah stok aktif yang diperkirakan.
2. Anggaran (domain, hosting, desain) dan tenggat peluncuran.
3. Nama showroom, logo, dan identitas visual.
4. Siapa saja yang membutuhkan akun admin.
5. Apakah perlu mencantumkan lebih dari satu cabang atau lokasi.

## 16. Glosarium

- **Lead:** calon pembeli yang menyatakan minat dan datanya tercatat.
- **MVP:** versi minimum yang sudah berguna untuk dirilis.
- **Slug:** bagian URL yang terbaca manusia, misalnya toyota-avanza-2018.
- **P0/P1/P2:** tingkat prioritas kebutuhan.
