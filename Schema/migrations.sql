--
-- Migrations for existing WeeklyBudget databases.
-- Safe to run multiple times (idempotent).
--

-- categories table
CREATE TABLE IF NOT EXISTS categories (
    id   INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL UNIQUE
);

INSERT OR IGNORE INTO categories (id, name) VALUES (1, 'Food');
INSERT OR IGNORE INTO categories (id, name) VALUES (2, 'Groceries');
INSERT OR IGNORE INTO categories (id, name) VALUES (3, 'Gas');
INSERT OR IGNORE INTO categories (id, name) VALUES (4, 'Shopping');
INSERT OR IGNORE INTO categories (id, name) VALUES (5, 'Other');

-- estimate tables
CREATE TABLE IF NOT EXISTS estimate_incomes (
    id     INTEGER PRIMARY KEY AUTOINCREMENT,
    name   TEXT NOT NULL,
    amount REAL NOT NULL
);

CREATE TABLE IF NOT EXISTS estimate_expenses (
    id       INTEGER PRIMARY KEY AUTOINCREMENT,
    name     TEXT NOT NULL,
    amount   REAL NOT NULL,
    category TEXT NOT NULL
);
