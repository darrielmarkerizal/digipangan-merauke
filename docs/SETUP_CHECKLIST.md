# DigiPangan — Setup Checklist

Rangkuman konkret: **package** yang perlu diinstall, **modul** yang perlu di-generate, dan **reusable code** yang perlu disiapkan sebelum coding fitur dimulai.

---

## 1. Package / Library

### 1.1 Backend (composer)

**Wajib untuk MVP**

```bash
composer require spatie/laravel-permission     # RBAC: admin, petani
composer require spatie/laravel-sluggable      # slug URL: /produk/{slug}
composer require intervention/image            # olah gambar (crop, resize)
composer require propaganistas/laravel-phone   # validasi nomor WA petani
```

**Dev tools**

```bash
composer require --dev pestphp/pest pestphp/pest-plugin-laravel
composer require --dev larastan/larastan
composer require --dev barryvdh/laravel-ide-helper
composer require --dev laravel/telescope       # debug lokal
```

**v1.1 (menyusul)**

```bash
composer require maatwebsite/excel             # export daftar petani/produk
composer require barryvdh/laravel-dompdf       # cetak profil petani
composer require spatie/laravel-backup         # backup DB & media
composer require predis/predis                 # Redis cache/queue (jika hosting mendukung)
```

**Sudah terpasang, tinggal dipakai:** `inertiajs/inertia-laravel`, `laravel/sanctum`, `nwidart/laravel-modules`, `owen-it/laravel-auditing`, `spatie/laravel-medialibrary`, `spatie/laravel-query-builder`.

### 1.2 Frontend (npm)

**Wajib untuk MVP**

```bash
# UI kit
npx shadcn-vue@latest init                     # install reka-ui + setup Tailwind config
npm install lucide-vue-next                    # icon
npm install class-variance-authority clsx tailwind-merge

# Data & interaction
npm install axios                              # request non-Inertia (Sanctum)
npm install @tanstack/vue-table                # tabel admin
npm install @vueuse/core                       # utilities Vue (sudah ada, verifikasi)

# Media
npm install swiper                             # slider/carousel foto & galeri wilayah
npm install vue-image-cropper                  # crop foto upload (opsional)
```

**Dev tools**

```bash
npm install -D eslint @vue/eslint-config-prettier eslint-plugin-vue prettier
npm install -D typescript vue-tsc              # opsional, sangat direkomendasikan
```

**v1.1**

```bash
npm install @tiptap/vue-3 @tiptap/starter-kit  # WYSIWYG untuk berita
npm install apexcharts vue3-apexcharts         # chart dashboard admin
npm install leaflet @vue-leaflet/vue-leaflet   # peta lokasi wilayah/petani
```

**Sudah terpasang, tinggal dipakai:** `@inertiajs/vue3`, `pinia`, `vee-validate`, `@vee-validate/zod`, `zod`, `vue-sonner`, `date-fns`, `tailwindcss` v4.

---

## 2. Modul (nwidart/laravel-modules)

Struktur modular per domain. Generate dengan:

```bash
php artisan module:make Region
php artisan module:make Farmer
php artisan module:make Product
php artisan module:make Post           # v1.1
```

| Modul | Tanggung jawab | Model utama | Route prefix publik | Route prefix admin |
|---|---|---|---|---|
| **Region** | Wilayah transmigrasi (Muting, Ulilin, Elikobel) — profil, statistik, galeri | `Region` | `/wilayah/{slug}` | `/admin/wilayah` |
| **Farmer** | Petani + Kelompok Tani | `Farmer`, `FarmerGroup` | `/petani/{slug}` | `/admin/petani`, `/admin/kelompok-tani` |
| **Product** | Produk + Kategori Produk | `Product`, `ProductCategory` | `/produk`, `/produk/{slug}` | `/admin/produk`, `/admin/kategori` |
| **Post** (v1.1) | Berita, Pelatihan, Info Harga Pasar | `Post`, `PostCategory` | `/berita`, `/berita/{slug}` | `/admin/berita` |

**Lintas modul (tetap di `app/`, bukan di `Modules/`):**

- `App\Models\User` — auth admin & petani (Sanctum + RBAC).
- `App\Http\Controllers\HomeController` — beranda publik (agregasi produk unggulan + wilayah + berita terbaru).
- `App\Http\Controllers\Auth\*` — login/logout/register.

**Struktur internal tiap modul** (contoh `Modules/Product/`):

```
Modules/Product/
├── Config/config.php
├── Database/
│   ├── Migrations/
│   ├── Seeders/ProductDatabaseSeeder.php
│   └── Factories/ProductFactory.php
├── Http/
│   ├── Controllers/
│   │   ├── ProductController.php          # publik
│   │   └── Admin/ProductController.php    # admin CRUD
│   ├── Requests/
│   │   ├── StoreProductRequest.php
│   │   └── UpdateProductRequest.php
│   └── Resources/ProductResource.php
├── Models/
│   ├── Product.php
│   └── ProductCategory.php
├── Services/ProductService.php
├── Routes/
│   ├── web.php        # /produk/*
│   └── admin.php      # /admin/produk/*
├── Resources/assets/js/Pages/Product/
│   ├── Index.vue
│   ├── Show.vue
│   └── Admin/{Index,Create,Edit}.vue
└── Providers/ProductServiceProvider.php
```

---

## 3. Reusable Code (base layer)

Disiapkan **sekali di awal**, dipakai semua modul. Ini yang harus ada sebelum modul pertama dikerjakan.

### 3.1 Backend

| File | Fungsi |
|---|---|
| `app/Traits/ApiResponse.php` | ✅ Sudah ada — response JSON konsisten (`successResponse`, `errorResponse`, `paginatedResponse`). |
| `app/Http/Requests/BaseFormRequest.php` | Override `failedValidation()` → return JSON 422 pakai `ApiResponse` (biar konsisten Inertia & API). |
| `app/Http/Controllers/Controller.php` | Extend + pakai trait `ApiResponse`, `AuthorizesRequests`. |
| `app/Http/Middleware/HandleInertiaRequests.php` | Share global: `auth.user`, `auth.roles`, `flash.success/error`, `app.name`, `app.env`, `filters` (query string). |
| `app/Services/BaseService.php` | CRUD generic + transaction wrapper (`create`, `update`, `delete`, `paginate`) — di-extend per modul. |
| `app/Support/HasMedia.php` (trait) | Wrapper `spatie/medialibrary`: registerMediaCollections standar (`thumb 300x300`, `card 600x400`, `full 1200`). |
| `app/Support/Filterable.php` (trait) | Preset `AllowedFilter` untuk Spatie Query Builder: search LIKE, range tanggal, filter enum. |
| `app/Enums/UserRole.php` | Enum: `Admin`, `Farmer`, `Guest`. |
| `app/Enums/ProductUnit.php` | Enum: `kg`, `ikat`, `buah`, `karung`, dst. |
| `bootstrap/app.php` → `withExceptions()` | Map: `ValidationException` 422, `ModelNotFound` 404, `Auth` 401, fallback 500 (hide trace di prod). |
| `database/seeders/RolePermissionSeeder.php` | Seed role `admin`, `farmer` + permissions dasar. |
| `database/seeders/RegionSeeder.php` | Seed 3 wilayah: Muting, Ulilin, Elikobel. |

**Contoh `HandleInertiaRequests::share()` yang harus ditulis:**

```php
return [
    ...parent::share($request),
    'auth' => [
        'user' => fn () => $request->user()?->only('id', 'name', 'email', 'phone'),
        'roles' => fn () => $request->user()?->getRoleNames(),
    ],
    'flash' => [
        'success' => fn () => $request->session()->get('success'),
        'error'   => fn () => $request->session()->get('error'),
    ],
    'app' => [
        'name' => config('app.name'),
        'env'  => app()->environment(),
        'whatsapp_admin' => config('digipangan.whatsapp_admin'),
    ],
];
```

### 3.2 Frontend

Struktur folder `resources/js/`:

```
resources/js/
├── app.js                          # ✅ sudah ada
├── Pages/                          # per-route Inertia (per modul di-copy ke sini via build)
├── Layouts/
│   ├── GuestLayout.vue             # navbar + footer publik
│   ├── AppLayout.vue               # sidebar admin + topbar + toast listener
│   └── AuthLayout.vue              # login/register
├── Components/
│   ├── ui/                         # dari shadcn-vue: Button, Input, Dialog, Dropdown, Table, Card, Badge
│   ├── form/
│   │   ├── FormField.vue           # wrap VeeValidate + error message
│   │   ├── FormInput.vue
│   │   ├── FormTextarea.vue
│   │   ├── FormSelect.vue
│   │   ├── FormImageUpload.vue     # single/multi upload + preview
│   │   └── FormPhone.vue           # input nomor WA (validasi ID)
│   ├── table/
│   │   ├── DataTable.vue           # bungkus @tanstack/vue-table + Spatie QB
│   │   ├── DataTableToolbar.vue    # search + filter
│   │   └── Pagination.vue
│   ├── product/
│   │   ├── ProductCard.vue         # dipakai di Beranda, Wilayah, Produk index
│   │   └── ContactSellerButton.vue # tombol "Hubungi via WhatsApp"
│   └── shared/
│       ├── Navbar.vue
│       ├── Sidebar.vue
│       ├── Breadcrumb.vue
│       ├── EmptyState.vue
│       └── ConfirmDialog.vue
├── Composables/
│   ├── useForm.js                  # wrap Inertia useForm + Zod schema + toast on error
│   ├── useDataTable.js             # sync URL ↔ Spatie QB (page, sort, filter[])
│   ├── useConfirm.js               # promise-based confirm dialog
│   ├── useToast.js                 # wrap vue-sonner
│   ├── useAuth.js                  # akses auth prop + hasRole/can
│   └── useWhatsApp.js              # build wa.me link + template pesan
├── Stores/
│   ├── useAuthStore.js
│   └── useUiStore.js               # sidebar collapsed, dark mode
├── Lib/
│   ├── axios.js                    # instance + interceptor Sanctum
│   ├── utils.js                    # cn(), formatIDR(), formatDate(), truncate()
│   ├── constants.js                # PRODUCT_UNITS, REGIONS, CATEGORIES
│   └── whatsapp.js                 # buildWhatsAppUrl(phone, product)
├── Schemas/
│   ├── product.schema.js           # Zod schema CRUD produk
│   ├── farmer.schema.js
│   └── region.schema.js
└── plugins/
    └── toast.js
```

### 3.3 Contoh implementasi kunci

**`Lib/whatsapp.js`** (dipakai di tombol "Hubungi Penjual"):

```js
export function buildWhatsAppUrl(phone, { productName, productUrl }) {
  const cleanPhone = phone.replace(/\D/g, '').replace(/^0/, '62');
  const message = `Halo, saya tertarik dengan produk "${productName}" di DigiPangan Merauke. ${productUrl}`;
  return `https://wa.me/${cleanPhone}?text=${encodeURIComponent(message)}`;
}
```

**`Components/product/ContactSellerButton.vue`** — reusable di halaman produk, wilayah, petani.

**`Components/table/DataTable.vue`** — dipakai semua CRUD admin (produk, petani, wilayah, berita, user).

**`Composables/useForm.js`** — semua form admin & petani pakai ini, konsisten error handling & toast.

---

## 4. Urutan Pengerjaan

1. **Install semua package MVP** (composer + npm) — 30 menit.
2. **Bikin base layer backend** (BaseFormRequest, HandleInertiaRequests share, exception handler, enums, seeders role) — 1–2 jam.
3. **Setup shadcn-vue + layouts + komponen shared** (Navbar, Sidebar, FormField, DataTable, ProductCard, ContactSellerButton) — 3–4 jam.
4. **Generate modul `Region`** (paling sederhana — CRUD + halaman publik) — 2 jam.
5. **Generate modul `Farmer`** (mirip Region + relasi ke Region + FarmerGroup) — 3 jam.
6. **Generate modul `Product`** (paling banyak field, media, kategori, "Hubungi Penjual") — 4 jam.
7. **Beranda publik** — agregasi ketiga modul — 2 jam.
8. **Auth admin** + seed akun awal — 1 jam.
9. **Deploy staging** untuk feedback tim UGM.

Total estimasi MVP: ~2–3 hari kerja penuh.

---

## 5. Ringkasan Singkat

- **Backend tambahan wajib**: `spatie/permission`, `spatie/sluggable`, `intervention/image`, `propaganistas/laravel-phone`.
- **Frontend tambahan wajib**: `shadcn-vue`, `lucide-vue-next`, `axios`, `@tanstack/vue-table`, `swiper`.
- **Modul**: `Region`, `Farmer`, `Product` (MVP) → `Post` (v1.1).
- **Reusable**: BaseFormRequest, share Inertia, HasMedia trait, DataTable, FormField, ContactSellerButton, useForm, useDataTable, whatsapp helper.
- **Tidak perlu**: cart, checkout, payment, kurir, rating, chat in-app.
