#!/bin/sh
set -e

# Bind to Railway's dynamic PORT, default to 80 for local Docker
export SERVER_NAME=":${PORT:-80}"

# Initialise SQLite database if it doesn't exist (volumes aren't mounted at build time)
DB_PATH="${DB_PATH:-var/data/weeklybudget.sqlite}"
if [ ! -f "$DB_PATH" ]; then
    mkdir -p "$(dirname "$DB_PATH")"
    sqlite3 "$DB_PATH" < Schema/weeklyBudget.sql
else
    # Run idempotent migrations on existing databases
    sqlite3 "$DB_PATH" < Schema/migrations.sql

    # Add 'active' column to budgets if missing
    if ! sqlite3 "$DB_PATH" "PRAGMA table_info(budgets);" | grep -q 'active'; then
        sqlite3 "$DB_PATH" "ALTER TABLE budgets ADD COLUMN active INTEGER NOT NULL DEFAULT 0;"
        sqlite3 "$DB_PATH" "UPDATE budgets SET active = 1 WHERE budgetType = 'weekly';"
    fi
fi

# Hand off to the default FrankenPHP entrypoint
exec frankenphp run --config /etc/caddy/Caddyfile
