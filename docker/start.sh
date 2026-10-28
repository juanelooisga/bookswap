#!/bin/sh
set -eu
PORT="${PORT:-10000}"
case "$PORT" in ''|*[!0-9]*) echo 'Puerto inválido' >&2; exit 1;; esac
sed -i "s/^Listen 80$/Listen ${PORT}/" /etc/apache2/ports.conf
sed -i "s/:80>/:${PORT}>/" /etc/apache2/sites-available/000-default.conf
if [ "${DEMO_INITIALIZE:-false}" = "true" ]; then
    php scripts/setup-demo.php
fi
exec apache2-foreground
