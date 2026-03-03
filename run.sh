#!/bin/bash

# =============================================================================
#  🚗  GitaCar Rent — Setup Script
#  Jalankan script ini setelah meng-clone project untuk pertama kali.
#  Cara pakai: bash run.sh
# =============================================================================

set -e  # Hentikan script jika ada perintah yang gagal

# ─────────────────────────────────────────────
#  Warna untuk output
# ─────────────────────────────────────────────
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
CYAN='\033[0;36m'
BOLD='\033[1m'
NC='\033[0m' # No Color

# ─────────────────────────────────────────────
#  Helper functions
# ─────────────────────────────────────────────
info()    { echo -e "${CYAN}[INFO]${NC}  $1"; }
success() { echo -e "${GREEN}[OK]${NC}    $1"; }
warn()    { echo -e "${YELLOW}[WARN]${NC}  $1"; }
error()   { echo -e "${RED}[ERROR]${NC} $1"; exit 1; }
step()    { echo -e "\n${BOLD}${CYAN}==============================${NC}"; echo -e "${BOLD}  $1${NC}"; echo -e "${BOLD}${CYAN}==============================${NC}"; }

echo ""
echo -e "${BOLD}${GREEN}"
echo "  ██████╗ ██╗████████╗ █████╗  ██████╗ █████╗ ██████╗ "
echo "  ██╔════╝ ██║╚══██╔══╝██╔══██╗██╔════╝██╔══██╗██╔══██╗"
echo "  ██║  ███╗██║   ██║   ███████║██║     ███████║██████╔╝"
echo "  ██║   ██║██║   ██║   ██╔══██║██║     ██╔══██║██╔══██╗"
echo "  ╚██████╔╝██║   ██║   ██║  ██║╚██████╗██║  ██║██║  ██║"
echo "   ╚═════╝ ╚═╝   ╚═╝   ╚═╝  ╚═╝ ╚═════╝╚═╝  ╚═╝╚═╝  ╚═╝"
echo -e "${NC}"
echo -e "${BOLD}         🚗  GitaCar Rent — Automated Setup${NC}"
echo ""

# ─────────────────────────────────────────────
#  1. Cek dependensi yang diperlukan
# ─────────────────────────────────────────────
step "1/9 · Memeriksa Dependensi Sistem"

command -v php >/dev/null 2>&1 || error "PHP tidak ditemukan. Silakan install PHP 8.2+ terlebih dahulu."
PHP_VERSION=$(php -r "echo PHP_MAJOR_VERSION.'.'.PHP_MINOR_VERSION;")
info "PHP versi: $PHP_VERSION"

command -v composer >/dev/null 2>&1 || error "Composer tidak ditemukan. Install dari https://getcomposer.org"
info "Composer: $(composer --version --no-ansi 2>/dev/null | head -1)"

command -v node >/dev/null 2>&1 || error "Node.js tidak ditemukan. Install dari https://nodejs.org"
info "Node.js: $(node -v)"

command -v npm >/dev/null 2>&1 || error "NPM tidak ditemukan. Install bersama Node.js."
info "NPM: $(npm -v)"

success "Semua dependensi tersedia!"

# ─────────────────────────────────────────────
#  2. Setup file .env
# ─────────────────────────────────────────────
step "2/9 · Menyiapkan File .env"

if [ ! -f ".env" ]; then
    cp .env.example .env
    success "File .env berhasil dibuat dari .env.example"
else
    warn "File .env sudah ada, dilewati."
fi

# ─────────────────────────────────────────────
#  3. Konfigurasi database interaktif
# ─────────────────────────────────────────────
step "3/9 · Konfigurasi Database"

echo ""
echo -e "${YELLOW}Pilih driver database:${NC}"
echo "  1) MySQL / MariaDB  (direkomendasikan untuk production)"
echo "  2) SQLite           (cepat untuk development/testing)"
echo ""
read -rp "Pilihan Anda [1/2, default: 1]: " DB_CHOICE
DB_CHOICE=${DB_CHOICE:-1}

if [ "$DB_CHOICE" = "2" ]; then
    # SQLite
    info "Menggunakan SQLite..."
    SQLITE_PATH="$(pwd)/database/database.sqlite"
    touch "$SQLITE_PATH"

    # Update .env untuk SQLite
    sed -i.bak \
        -e 's/^DB_CONNECTION=.*/DB_CONNECTION=sqlite/' \
        -e 's/^DB_HOST=.*/#DB_HOST=127.0.0.1/' \
        -e 's/^DB_PORT=.*/#DB_PORT=3306/' \
        -e 's/^DB_DATABASE=.*/#DB_DATABASE=gita_car_rent/' \
        -e 's/^DB_USERNAME=.*/#DB_USERNAME=root/' \
        -e 's/^DB_PASSWORD=.*/#DB_PASSWORD=/' \
        .env
    rm -f .env.bak
    success "Konfigurasi SQLite selesai. File: $SQLITE_PATH"
else
    # MySQL
    info "Menggunakan MySQL..."
    echo ""
    read -rp "  DB Host     [default: 127.0.0.1]: " DB_HOST
    read -rp "  DB Port     [default: 3306]: "      DB_PORT
    read -rp "  DB Name     [default: gita_car_rent]: " DB_NAME
    read -rp "  DB Username [default: root]: "      DB_USER
    read -rsp "  DB Password [default: kosong]: "   DB_PASS
    echo ""

    DB_HOST=${DB_HOST:-127.0.0.1}
    DB_PORT=${DB_PORT:-3306}
    DB_NAME=${DB_NAME:-gita_car_rent}
    DB_USER=${DB_USER:-root}
    DB_PASS=${DB_PASS:-}

    # Cek apakah MySQL dapat dihubungi
    if command -v mysql >/dev/null 2>&1; then
        info "Memeriksa koneksi ke MySQL..."
        if mysql -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USER" ${DB_PASS:+-p"$DB_PASS"} -e ";" 2>/dev/null; then
            # Buat database jika belum ada
            mysql -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USER" ${DB_PASS:+-p"$DB_PASS"} \
                -e "CREATE DATABASE IF NOT EXISTS \`$DB_NAME\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" 2>/dev/null \
                && success "Database '$DB_NAME' siap digunakan." \
                || warn "Tidak bisa membuat database secara otomatis, buat manual jika belum ada."
        else
            warn "Tidak dapat terhubung ke MySQL sekarang. Pastikan server MySQL berjalan."
        fi
    else
        warn "mysql CLI tidak ditemukan. Buat database '$DB_NAME' secara manual jika belum ada."
    fi

    # Update .env
    sed -i.bak \
        -e "s/^DB_CONNECTION=.*/DB_CONNECTION=mysql/" \
        -e "s/^#\?DB_HOST=.*/DB_HOST=$DB_HOST/" \
        -e "s/^#\?DB_PORT=.*/DB_PORT=$DB_PORT/" \
        -e "s/^#\?DB_DATABASE=.*/DB_DATABASE=$DB_NAME/" \
        -e "s/^#\?DB_USERNAME=.*/DB_USERNAME=$DB_USER/" \
        -e "s/^#\?DB_PASSWORD=.*/DB_PASSWORD=$DB_PASS/" \
        .env
    rm -f .env.bak
    success "Konfigurasi MySQL disimpan ke .env"
fi

# Konfigurasi APP_URL
echo ""
read -rp "APP_URL [default: http://localhost]: " APP_URL_INPUT
APP_URL_INPUT=${APP_URL_INPUT:-http://localhost}
sed -i.bak "s|^APP_URL=.*|APP_URL=$APP_URL_INPUT|" .env
rm -f .env.bak
success "APP_URL diset ke: $APP_URL_INPUT"

# ─────────────────────────────────────────────
#  4. Install Composer dependencies
# ─────────────────────────────────────────────
step "4/9 · Menginstall PHP Dependencies (Composer)"

composer install --no-interaction --prefer-dist --optimize-autoloader
success "Composer dependencies berhasil diinstall!"

# ─────────────────────────────────────────────
#  5. Generate APP_KEY
# ─────────────────────────────────────────────
step "5/9 · Generate Application Key"

php artisan key:generate --force
success "Application key berhasil digenerate!"

# ─────────────────────────────────────────────
#  6. Jalankan migrasi & seeder
# ─────────────────────────────────────────────
step "6/9 · Migrasi Database & Seeding"

info "Menjalankan migrasi database..."
php artisan migrate --force
success "Migrasi selesai!"

echo ""
read -rp "Jalankan database seeder (data awal: roles, permissions, dll)? [Y/n]: " RUN_SEED
RUN_SEED=${RUN_SEED:-Y}
if [[ "$RUN_SEED" =~ ^[Yy]$ ]]; then
    php artisan db:seed --force
    success "Seeding selesai!"
else
    warn "Seeding dilewati."
fi

# ─────────────────────────────────────────────
#  7. Setup storage & symlink
# ─────────────────────────────────────────────
step "7/9 · Setup Storage"

# Pastikan direktori storage ada dan writable
php artisan storage:link 2>/dev/null || warn "Storage link sudah ada atau gagal dibuat."

# Buat folder yang mungkin diperlukan
mkdir -p storage/app/public/img
mkdir -p storage/app/public/bukti_pembayaran
mkdir -p storage/app/public/ktp_pasport

# Set permissions
chmod -R 775 storage bootstrap/cache 2>/dev/null || warn "Tidak bisa ubah permission (mungkin perlu sudo)."

success "Storage siap!"

# ─────────────────────────────────────────────
#  8. Install Node.js dependencies & build assets
# ─────────────────────────────────────────────
step "8/9 · Menginstall NPM Dependencies & Build Assets"

info "Menginstall Node.js dependencies..."
npm install
success "NPM dependencies terinstall!"

echo ""
read -rp "Build assets untuk production (npm run build)? [Y/n]: " RUN_BUILD
RUN_BUILD=${RUN_BUILD:-Y}
if [[ "$RUN_BUILD" =~ ^[Yy]$ ]]; then
    npm run build
    success "Assets berhasil di-build!"
else
    warn "Build dilewati. Jalankan 'npm run dev' saat development."
fi

# ─────────────────────────────────────────────
#  9. Cache & optimasi (opsional)
# ─────────────────────────────────────────────
step "9/9 · Optimasi & Cache"

php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
success "Cache dibersihkan!"

# ─────────────────────────────────────────────
#  Selesai!
# ─────────────────────────────────────────────
echo ""
echo -e "${GREEN}${BOLD}"
echo "  ╔══════════════════════════════════════════════╗"
echo "  ║   ✅  Setup GitaCar Rent Selesai!             ║"
echo "  ╚══════════════════════════════════════════════╝"
echo -e "${NC}"
echo -e "  ${BOLD}Langkah selanjutnya:${NC}"
echo ""
echo -e "  1. Pastikan server MySQL berjalan (jika menggunakan MySQL)"
echo -e "  2. Jalankan dev server:"
echo -e "     ${CYAN}php artisan serve${NC}"
echo ""
echo -e "  3. Atau jalankan mode development lengkap (server + vite):"
echo -e "     ${CYAN}composer run dev${NC}"
echo ""
echo -e "  4. Buka browser ke: ${CYAN}$APP_URL_INPUT${NC}"
echo ""
echo -e "  📋 ${BOLD}Akun Admin Default (lihat DatabaseSeeder.php)${NC}"
echo -e "     Buat akun admin via: ${CYAN}php artisan db:seed${NC}"
echo ""
