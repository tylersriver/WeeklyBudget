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
    // Create categories table if missing
    $pdo->exec(<<<'SQL'
        CREATE TABLE IF NOT EXISTS categories (
            id   INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL UNIQUE
        )
    SQL);

    // Seed default categories
    $stmt = $pdo->prepare('INSERT OR IGNORE INTO categories (id, name) VALUES (?, ?)');
    foreach ([1 => 'Food', 2 => 'Groceries', 3 => 'Gas', 4 => 'Shopping', 5 => 'Other'] as $id => $name) {
        $stmt->execute([$id, $name]);
    }

    // Add 'active' column to budgets if missing
    $columns = $pdo->query("PRAGMA table_info(budgets)")->fetchAll(\PDO::FETCH_ASSOC);
    $hasActive = false;
    foreach ($columns as $col) {
        if ($col['name'] === 'active') {
            $hasActive = true;
            break;
        }
    }

    if (!$hasActive) {
        $pdo->exec("ALTER TABLE budgets ADD COLUMN active INTEGER NOT NULL DEFAULT 0");
        $pdo->exec("UPDATE budgets SET active = 1 WHERE budgetType = 'weekly'");
    }
};
