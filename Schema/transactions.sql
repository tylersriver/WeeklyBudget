--
-- Transactions table
--
Use WeeklyBudget;

CREATE TABLE Transactions (
    id int(11) AUTO_INCREMENT primary key,
    dateOccured date NOT NULL,
    description text NOT NULL,
    amount decimal(4,2) NOT NULL
);