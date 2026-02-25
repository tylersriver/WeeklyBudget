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
    amount     INTEGER NOT NULL
);

INSERT OR IGNORE INTO budgets (id, budgetType, amount) VALUES (1, 'weekly', 200);
INSERT OR IGNORE INTO budgets (id, budgetType, amount) VALUES (2, 'monthly', 800);
