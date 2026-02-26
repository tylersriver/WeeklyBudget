<?php

declare(strict_types=1);

/**
 * Lightweight idempotent migration runner for SQLite.
 *
 * Run once at boot (safe to call on every request in worker mode since
 * the container boot phase runs only once).
 *
 * @param \PDO $pdo An open PDO connection to the SQLite database.
 */
return static function (\PDO $pdo): void {
    // ── Users table ──
    $pdo->exec(<<<'SQL'
        CREATE TABLE IF NOT EXISTS users (
            id            INTEGER PRIMARY KEY AUTOINCREMENT,
            email         TEXT NOT NULL UNIQUE,
            password_hash TEXT NOT NULL,
            created_at    TEXT NOT NULL DEFAULT (datetime('now'))
        )
    SQL);

    // ── Categories table ──
    $pdo->exec(<<<'SQL'
        CREATE TABLE IF NOT EXISTS categories (
            id   INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL UNIQUE
        )
    SQL);

    // ── Add 'active' column to budgets if missing ──
    $columns = $pdo->query("PRAGMA table_info(budgets)")->fetchAll(\PDO::FETCH_ASSOC);
    $hasActive = false;
    $hasBudgetUserId = false;
    foreach ($columns as $col) {
        if ($col['name'] === 'active') {
            $hasActive = true;
        }
        if ($col['name'] === 'user_id') {
            $hasBudgetUserId = true;
        }
    }

    if (!$hasActive) {
        $pdo->exec("ALTER TABLE budgets ADD COLUMN active INTEGER NOT NULL DEFAULT 0");
        $pdo->exec("UPDATE budgets SET active = 1 WHERE budgetType = 'weekly'");
    }

    // ── Estimate tables ──
    $pdo->exec(<<<'SQL'
        CREATE TABLE IF NOT EXISTS estimate_incomes (
            id     INTEGER PRIMARY KEY AUTOINCREMENT,
            name   TEXT NOT NULL,
            amount REAL NOT NULL
        )
    SQL);

    $pdo->exec(<<<'SQL'
        CREATE TABLE IF NOT EXISTS estimate_expenses (
            id       INTEGER PRIMARY KEY AUTOINCREMENT,
            name     TEXT NOT NULL,
            amount   REAL NOT NULL,
            category TEXT NOT NULL
        )
    SQL);

    // ── Add user_id to all data tables (migration for existing databases) ──
    if (!$hasBudgetUserId) {
        // Create a default migration user so existing data has an owner
        $pdo->exec(<<<'SQL'
            INSERT OR IGNORE INTO users (id, email, password_hash, created_at)
            VALUES (1, 'admin@weeklybudget.local',
                    '$2y$10$placeholder.hash.not.for.login.000000000000000000000',
                    datetime('now'))
        SQL);

        // Add user_id column to each table and assign existing rows to user 1
        $tables = ['transactions', 'budgets', 'categories', 'estimate_incomes', 'estimate_expenses'];
        foreach ($tables as $table) {
            $cols = $pdo->query("PRAGMA table_info({$table})")->fetchAll(\PDO::FETCH_ASSOC);
            $tableHasUserId = false;
            foreach ($cols as $col) {
                if ($col['name'] === 'user_id') {
                    $tableHasUserId = true;
                    break;
                }
            }
            if (!$tableHasUserId) {
                $pdo->exec("ALTER TABLE {$table} ADD COLUMN user_id INTEGER NOT NULL DEFAULT 1");
            }
        }

        // Seed default categories for migration user if they have none
        $catCount = (int) $pdo->query("SELECT COUNT(*) FROM categories WHERE user_id = 1")->fetchColumn();
        if ($catCount === 0) {
            $stmt = $pdo->prepare('INSERT OR IGNORE INTO categories (name, user_id) VALUES (?, 1)');
            foreach (['Food', 'Groceries', 'Gas', 'Shopping', 'Other'] as $name) {
                $stmt->execute([$name]);
            }
        }
    }
};
