#!/bin/sh
set -e

# Abort boot if required runtime variables are missing to avoid silent 500s.
REQUIRED_ENVS="APP_KEY APP_URL DB_CONNECTION DB_HOST DB_PORT DB_DATABASE DB_USERNAME DB_PASSWORD"

MISSING=""
for VAR in $REQUIRED_ENVS; do
    VALUE=$(printenv "$VAR")
    if [ -z "$VALUE" ]; then
        MISSING="$MISSING $VAR"
    fi
done

if [ -n "$MISSING" ]; then
    echo "[BOOT ERROR] Missing required environment variables:$MISSING" >&2
    echo "[BOOT ERROR] Please configure these variables before deploying." >&2
    exit 1
fi

# Generate .env from Docker environment variables if they exist
php -r '
$envVars = [
    "APP_NAME", "APP_ENV", "APP_KEY", "APP_DEBUG", "APP_URL",
    "ASSET_URL", "LIVEWIRE_ASSET_URL", "DB_CONNECTION", "DB_HOST",
    "DB_PORT", "DB_DATABASE", "DB_USERNAME", "DB_PASSWORD",
    "SESSION_DRIVER", "SESSION_DOMAIN", "SESSION_SECURE_COOKIE",
    "QUEUE_CONNECTION", "CACHE_STORE", "LOG_CHANNEL", "LOG_LEVEL",
];
$content = "";
foreach ($envVars as $var) {
    $value = getenv($var);
    if ($value !== false) {
        $content .= $var . "=" . $value . PHP_EOL;
    }
}
if ($content !== "") {
    file_put_contents(".env", $content);
    echo "[BOOT] .env generated from environment variables" . PHP_EOL;
}
'

php artisan config:cache
php artisan event:cache
php artisan route:cache
php artisan view:cache
php artisan storage:link --force

exec "$@"