# Panduan Lengkap Setup Podman untuk Laravel Sail

Karena project ini menggunakan Laravel Sail, file konfigurasi Docker/Podman berada di dalam folder `vendor`. Oleh karena itu, kita harus menginstall dependensi PHP (Composer) terlebih dahulu sebelum bisa menjalankan `podman compose up`.

Berikut adalah langkah-langkah dari awal clone project hingga project berjalan sempurna.

## Langkah 1: Install Dependensi PHP (Composer)

Karena PHP mungkin tidak terinstall secara lokal di komputer, kita menggunakan container sementara dari Podman untuk menjalankan `composer install`. Ini akan mengunduh semua package PHP dan membuat folder `vendor` yang berisi file Docker dari Laravel Sail.

Jalankan perintah berikut di terminal komputer (disarankan WSL atau Git Bash):

**Opsi A (Sangat Direkomendasikan, Lebih Cepat & Bebas Error):**
Pindahkan folder project Anda ke dalam file system Linux pada WSL (misalnya `~/gitacar_rent` menggunakan perintah `cp -r /mnt/c/gitacar_rent ~/gitacar_rent`), lalu jalankan:

```bash
podman run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php83-composer:latest \
    composer install --ignore-platform-reqs
```

**Opsi B (Jika tetap ingin di drive `C:\` atau `/mnt/c/`):**
Hapus flag `-u "$(id -u):$(id -g)"` agar berjalan sebagai _root_ di dalam container, untuk menghindari error izin (Operation not permitted) saat Composer mengekstrak file:

```bash
podman run --rm \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php83-composer:latest \
    composer install --ignore-platform-reqs
```

## Langkah 2: Build & Jalankan Podman Compose

Setelah folder `vendor` berhasil dibuat, sekarang `compose.yaml` sudah siap digunakan karena file Docker-nya sudah tersedia.

Jalankan perintah ini untuk mem-build dan menjalankan container di background:

```bash
podman compose up -d --build
```

> **Catatan:** Proses ini memakan waktu beberapa menit karena akan mengunduh OS Ubuntu dan menginstal PHP 8.3 beserta ekstensinya.

## Langkah 3: Setup Project (Key & Database)

Setelah container berjalan (status `Up`), kita perlu mengkonfigurasi Laravel. Jika file `.env` belum ada, pastikan untuk meng-copy dari `.env.example`.

Jalankan perintah artisan melalui container Podman:

```bash
podman compose exec laravel.test php artisan key:generate
podman compose exec laravel.test php artisan migrate
```

## Langkah 4: Install Dependensi Frontend (Node.js/NPM)

Proses instalasi `node_modules` **harus** dilakukan di dalam Linux container agar package seperti `@rollup/rollup-linux-x64-gnu` dapat terinstall dengan benar (tidak bentrok dengan arsitektur Windows).

Jika sebelumnya sudah ada folder `node_modules` di Windows, **hapus folder tersebut terlebih dahulu**.

Kemudian jalankan instalasi Node modules di dalam container:

```bash
podman compose exec laravel.test npm install
```

## Langkah 5: Jalankan Vite (Frontend Server)

Terakhir, jalankan Vite untuk mengkompilasi file CSS/JS. Biarkan perintah ini berjalan di terminal, atau jalankan di tab terminal yang baru:

```bash
podman compose exec laravel.test npm run dev
```

---

## 🎉 Project Siap Digunakan!

Buka browser dan akses URL berikut:

- Aplikasi Web laravel: **http://localhost:8000** (Jika APP_PORT tidak diubah)
- Vite Server: **http://localhost:5173**

## Perintah Dasar Podman Compose (Cheat Sheet)

- Mematikan container: `podman compose down`
- Menjalankan container (jika sudah di-build): `podman compose up -d`
- Melihat log: `podman compose logs -f`
- Masuk ke terminal container bash: `podman compose exec laravel.test bash`
