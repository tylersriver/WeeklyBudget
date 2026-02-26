--
-- SQLite schema for WeeklyBudget
--

CREATE TABLE IF NOT EXISTS transactions (
    id          INTEGER PRIMARY KEY AUTOINCREMENT,
    dateAdded   TEXT NOT NULL,
    type        TEXT NOT NULL,
    description TEXT NOT NULL,
    amount      REAL NOT NULL
);

CREATE TABLE IF NOT EXISTS budgets (
    id         INTEGER PRIMARY KEY AUTOINCREMENT,
    budgetType TEXT NOT NULL,
    amount     INTEGER NOT NULL,
    active     INTEGER NOT NULL DEFAULT 0
);

CREATE TABLE IF NOT EXISTS categories (
    id   INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL UNIQUE
);

INSERT OR IGNORE INTO budgets (id, budgetType, amount, active) VALUES (1, 'weekly', 200, 1);
INSERT OR IGNORE INTO budgets (id, budgetType, amount, active) VALUES (2, 'monthly', 800, 0);

INSERT OR IGNORE INTO categories (id, name) VALUES (1, 'Food');
INSERT OR IGNORE INTO categories (id, name) VALUES (2, 'Groceries');
INSERT OR IGNORE INTO categories (id, name) VALUES (3, 'Gas');
INSERT OR IGNORE INTO categories (id, name) VALUES (4, 'Shopping');
INSERT OR IGNORE INTO categories (id, name) VALUES (5, 'Other');

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
