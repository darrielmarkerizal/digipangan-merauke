# Analisis Fitur DigiPangan Merauke

Dokumen ini memetakan fitur yang tertuang dalam *Gambaran Umum Fitur* (UGM, Tim Dr. Faiz Zamzami) terhadap **stack teknis yang sudah tersedia** di repository ini: Laravel 13 + Inertia + Vue 3 + Pinia + VeeValidate/Zod + Tailwind 4 + `nwidart/laravel-modules` + Spatie (Media Library, Query Builder, Auditing) + Sanctum.

Tujuannya: menentukan fitur mana yang **wajib**, **sekunder**, dan **belum perlu** untuk MVP, sekaligus memetakan tiap fitur ke modul backend dan halaman Inertia.

---

## 1. Karakter Platform (penting dipahami dulu)

Berbeda dengan benchmark yang di-review dalam PDF:

| Platform | Model bisnis |
|---|---|
| Segari.id, Astro | Full e-commerce: cart, checkout, payment gateway, kurir |
| Warung Jogja | **Katalog UMKM + kontak langsung** |
| Kelola BUMDes, LokaCella | ERP/manajemen internal koperasi |

**DigiPangan Merauke = Warung Jogja + Profil Wilayah + Profil Petani.** Pada MVP:

- **Tidak ada** cart, checkout, payment gateway, kurir.
- Interaksi pembeli → penjual lewat tombol **"Hubungi Penjual"** (WhatsApp `wa.me/`).
- Fokus utama: **etalase, promosi kawasan transmigrasi, edukasi (berita/pelatihan)**.

Konsekuensi teknis: kompleksitas jauh lebih rendah dari e-commerce biasa, dan sebagian besar sudah bisa di-cover oleh stack yang sudah terpasang.

---

## 2. Pemetaan Fitur → Modul & Halaman

### Legenda prioritas
- 🟢 **MVP** — wajib pada rilis pertama
- 🟡 **v1.1** — penting tetapi bisa menyusul
- 🔵 **Nice-to-have** — opsional / tergantung kebutuhan lapangan
- ⚪ **Belum perlu** — tidak relevan di MVP, jangan di-develop dulu

### A. Halaman publik (Guest)

| Fitur (dari PDF) | Prioritas | Modul BE | Halaman Inertia | Catatan implementasi |
|---|---|---|---|---|
| Beranda (produk unggulan, terbaru, info wilayah, navigasi, login) | 🟢 MVP | `Home` (di app utama) | `Pages/Home.vue` | Query 6-8 produk `is_featured=true` + 6 produk terbaru + 3 kartu wilayah. |
| Produk (daftar + filter kategori) | 🟢 MVP | `Product` | `Pages/Product/Index.vue` | Pakai **Spatie Query Builder** (sudah terpasang) untuk filter & sort URL-driven. |
| Profil Produk (detail + Hubungi Penjual) | 🟢 MVP | `Product` | `Pages/Product/Show.vue` | Tombol WA: `https://wa.me/{petani.phone}?text=...`. |
| Profil Wilayah Transmigrasi (Muting, Ulilin, Elikobel) | 🟢 MVP | `Region` | `Pages/Region/Show.vue` | Konten CMS-like: statistik, galeri, produk unggulan wilayah. |
| Profil Petani (foto, kelompok tani, lokasi, komoditas, produk) | 🟢 MVP | `Farmer` | `Pages/Farmer/Show.vue` | Halaman publik, ambil relasi `farmer.products`. |
| Berita & Informasi (panen, pelatihan, harga pasar) | 🟡 v1.1 | `Post` | `Pages/Post/{Index,Show}.vue` | Post + kategori (berita, pelatihan, harga). |
| Tentang DigiPangan (latar belakang, tujuan, mitra, kontak) | 🟢 MVP | `Page` (static) | `Pages/About.vue` | Bisa statis (hardcoded) untuk MVP, jadikan CMS di v1.1. |
| Pusat Bantuan / FAQ | 🔵 nice | `Page` | `Pages/Help.vue` | Konten Markdown/HTML statis. |
| Search global produk / petani | 🟡 v1.1 | shared | `Components/GlobalSearch.vue` | Spatie Query Builder + `where('name','like',...)`. |

### B. Dashboard Admin

| Fitur (dari PDF) | Prioritas | Modul BE | Halaman Inertia | Catatan |
|---|---|---|---|---|
| Kelola Produk (CRUD) | 🟢 MVP | `Product` | `Pages/Admin/Product/*` | `spatie/medialibrary` untuk foto produk. |
| Kelola Petani (CRUD profil) | 🟢 MVP | `Farmer` | `Pages/Admin/Farmer/*` | Foto profil pakai media library. |
| Kelola Wilayah (statistik, deskripsi, galeri) | 🟢 MVP | `Region` | `Pages/Admin/Region/*` | Field statistik luas panen per komoditas. |
| Kelola Berita (CRUD) | 🟡 v1.1 | `Post` | `Pages/Admin/Post/*` | Butuh WYSIWYG editor (Tiptap). |
| Kelola Kategori Produk | 🟢 MVP | `Product` | `Pages/Admin/Category/*` | Sayuran, buah, perkebunan, peternakan, olahan pangan. |
| Kelola Kelompok Tani | 🟢 MVP | `Farmer` | `Pages/Admin/FarmerGroup/*` | Contoh: "Kelompok Tani Elikobel". |
| Kelola User & Role | 🟢 MVP | `App\Http\Controllers` | `Pages/Admin/User/*` | Butuh `spatie/laravel-permission`. |
| Dashboard statistik (jumlah petani, produk, view) | 🟡 v1.1 | shared | `Pages/Admin/Dashboard.vue` | Cards + chart sederhana. |
| Log aktivitas admin | 🟡 v1.1 | shared | `Pages/Admin/AuditLog.vue` | Sudah ada `owen-it/laravel-auditing`. |

### C. Fitur Petani (self-service)

PDF menggambarkan admin sentral yang mengelola semua data. Namun untuk *keberlanjutan program* (poin RIPPP di halaman "Strategi Implementasi"), fitur self-service petani sangat direkomendasikan di v1.1.

| Fitur | Prioritas | Modul | Catatan |
|---|---|---|---|
| Login petani (Sanctum) | 🟡 v1.1 | `Auth` | Petani punya akun sendiri. |
| Tambah/edit produk sendiri | 🟡 v1.1 | `Product` | Approval oleh admin sebelum tayang. |
| Update stok (tersedia/tidak) | 🟡 v1.1 | `Product` | Toggle boolean. |
| Statistik lihat/kontak produk | 🔵 nice | `Analytics` | Track click tombol WA. |

### D. Fitur yang **belum perlu** di MVP (jangan di-develop dulu)

- ⚪ Cart, checkout, invoice, payment gateway.
- ⚪ Manajemen kurir/pengiriman.
- ⚪ Chat in-app (WA sudah cukup).
- ⚪ Rating & review produk.
- ⚪ Wishlist, voucher, promo code.
- ⚪ Multi-currency, multi-language (default: Bahasa Indonesia).
- ⚪ Aplikasi mobile native (Inertia + PWA sudah cukup).
- ⚪ Fitur akuntansi/laporan keuangan koperasi (itu ranahnya "Kelola BUMDes", bukan DigiPangan).

---

## 3. Modul Backend (nwidart/laravel-modules)

Karena `nwidart/laravel-modules` sudah terpasang, tiap domain jadi modul terpisah. Modul yang perlu di-generate:

```bash
php artisan module:make Region      # wilayah transmigrasi
php artisan module:make Farmer      # petani + kelompok tani
php artisan module:make Product     # produk + kategori
php artisan module:make Post        # berita/informasi (v1.1)
```

Modul lintas (di `app/`, bukan `Modules/`):

- `App\Models\User` + role/permission — auth admin & petani.
- `App\Http\Controllers\HomeController` — beranda publik yang meng-agregasi data lintas modul.

---

## 4. Skema Data (draft awal)

Tabel utama yang perlu di-migrate:

```
regions (id, slug, name, description, area_km2, population,
         active_cooperatives, gallery_json, created_at, updated_at)

farmer_groups (id, region_id, name, description, created_at, updated_at)

farmers (id, farmer_group_id, region_id, user_id?, name, phone,
         address, land_area_ha, commodities_json, bio, created_at, updated_at)

product_categories (id, slug, name, icon)

products (id, farmer_id, region_id, category_id, slug, name,
          description, price, unit, weight, in_stock, is_featured,
          view_count, contact_count, created_at, updated_at)

posts (id, category, slug, title, excerpt, body, published_at)

users (id, name, email, phone, password, role, created_at, updated_at)
```

Semua model yang punya foto pakai trait `InteractsWithMedia` dari Spatie.

---

## 5. Peta Fitur PDF → Package yang Sudah Ada

| Kebutuhan dari PDF | Package sudah ada | Perlu tambah |
|---|---|---|
| Foto produk & petani (multi ukuran) | `spatie/medialibrary` ✅ | — |
| Filter, sort, search produk | `spatie/query-builder` ✅ | — |
| Modularisasi per domain | `nwidart/laravel-modules` ✅ | — |
| Log aktivitas admin | `owen-it/laravel-auditing` ✅ | — |
| Form validation FE | `vee-validate` + `zod` ✅ | — |
| Toast notifikasi | `vue-sonner` ✅ | — |
| State FE | `pinia` ✅ | — |
| RBAC (admin vs petani) | — | `spatie/laravel-permission` 🔴 |
| WYSIWYG untuk berita | — | `tiptap/vue-3` (v1.1) 🟡 |
| Table admin (sort/paginate) | — | `@tanstack/vue-table` 🔴 |
| UI komponen (button, dialog, dsb) | — | `shadcn-vue` + `reka-ui` 🔴 |
| Ikon | — | `lucide-vue-next` 🔴 |
| Chart di dashboard admin | — | `apexcharts` / `chart.js` (v1.1) 🟡 |
| PDF/Excel export daftar petani | — | `barryvdh/dompdf` + `maatwebsite/excel` (v1.1) 🟡 |
| Peta lokasi wilayah/petani | — | `leaflet` (opsional) 🔵 |
| Slug URL | — | `spatie/laravel-sluggable` 🔴 |

---

## 6. Rekomendasi Rilis Bertahap

### MVP (Rilis 1) — target: platform bisa tayang & dipromosikan
1. Setup base: `spatie/permission`, `shadcn-vue`, `lucide`, `sluggable`, `@tanstack/vue-table`.
2. Modul `Region`, `Farmer`, `Product` (+ Category, FarmerGroup).
3. Halaman publik: Beranda, Produk (list + detail), Wilayah, Petani, Tentang.
4. Dashboard admin: CRUD produk/petani/wilayah/kategori/user.
5. Auth admin (single role: `admin`).
6. Tombol "Hubungi Penjual" via WhatsApp.

### v1.1 — target: aktivasi konten & self-service
1. Modul `Post` (Berita, Pelatihan, Harga Pasar) + WYSIWYG Tiptap.
2. Auth petani + self-service produk (dengan alur approval).
3. Dashboard admin: statistik dasar + chart.
4. Track view/kontak produk (analytics ringan).
5. Export daftar petani (Excel/PDF).

### v1.2+ — sesuai temuan lapangan
- Peta lokasi (Leaflet), notifikasi email, mitra bisnis directory, integrasi harga pasar dari sumber eksternal (Panel Harga Pangan Nasional), dsb.

---

## 7. Yang Perlu Diklarifikasi ke Tim

Sebelum coding dimulai, konfirmasi ke tim (Dr. Faiz, Bapak Riam dkk):

1. **Kontak penjual** — WA saja, atau juga telepon & alamat fisik?
2. **Approval produk** — apakah produk yang diinput petani harus di-review admin?
3. **Kelompok tani** — apakah satu petani bisa masuk >1 kelompok?
4. **Harga pasar** — sumber datanya manual input admin, atau scraping/API dari Panel Harga Pangan?
5. **Wilayah** — akan bertambah di luar Muting/Ulilin/Elikobel? Kalau ya, tabel `regions` harus fleksibel (sudah).
6. **Bahasa** — cukup Bahasa Indonesia?
7. **Domain & hosting** — sudah ada? (mempengaruhi setup Sanctum SPA + CORS.)
8. **Login petani** — MVP admin-only, atau petani sudah bisa login dari awal?

---

## 8. Ringkasan

- Stack yang sudah terpasang **cocok** untuk kebutuhan DigiPangan; tidak perlu ganti framework atau pattern.
- Kebutuhan sisa yang paling mendesak: **RBAC (`spatie/permission`)**, **UI kit (`shadcn-vue`)**, **table (`vue-table`)**, dan **sluggable**.
- **Jangan overbuild**: PDF tidak menyebutkan cart/checkout — cukup katalog + kontak.
- Modular via nwidart membuat setiap fitur (Region, Farmer, Product, Post) jadi paket independen yang mudah dipelihara.

> Selanjutnya: jika disetujui, generate modul `Region`, `Farmer`, `Product` sekaligus base layer (BaseFormRequest, HandleInertiaRequests share, AppLayout, Axios instance, FormField component).
