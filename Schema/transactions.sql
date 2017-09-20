--
-- Transactions table
--
Use WeeklyBudget;

CREATE TABLE Transactions (
    dateOccured date NOT NULL,
    description text NOT NULL,
    amount decimal(4,2) NOT NULL
);