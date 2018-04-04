--
-- weekMaxValues table
--
Use WeeklyBudget;

CREATE TABLE budgets (
    id int(11) PRIMARY KEY NOT NULL AUTO_INCREMENT,
    budgetType text not null,
    amount int not null
);