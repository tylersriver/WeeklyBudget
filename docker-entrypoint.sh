#!/bin/sh
set -e

# Bind to Railway's dynamic PORT, default to 80 for local Docker
export SERVER_NAME=":${PORT:-80}"

# Initialise SQLite database if it doesn't exist (volumes aren't mounted at build time)
DB_PATH="${DB_PATH:-var/data/weeklybudget.sqlite}"
if [ ! -f "$DB_PATH" ]; then
    mkdir -p "$(dirname "$DB_PATH")"
    sqlite3 "$DB_PATH" < Schema/weeklyBudget.sql
fi

# Hand off to the default FrankenPHP entrypoint
exec frankenphp run --config /etc/caddy/Caddyfile
