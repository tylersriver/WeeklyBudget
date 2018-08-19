create database WeeklyBudget;

Use WeeklyBudget;

--
-- Transactions table
--
DROP TABLE IF EXISTS Transactions;
CREATE TABLE Transactions (
    id int(11) AUTO_INCREMENT primary key,
    dateAdded date NOT NULL,
    type text NOT NULL,
    description text NOT NULL,
    amount decimal(4,2) NOT NULL
);

--
-- weekMaxValues table
--
DROP TABLE IF EXISTS budgets;
CREATE TABLE budgets (
    id int(11) PRIMARY KEY NOT NULL AUTO_INCREMENT,
    budgetType text not null,
    amount int not null
);

drop table if exists user;
create table user (
    userId int(11) AUTO_INCREMENT primary key,
    username varchar(256),
    password varchar(512)
);
insert into user (username, password) VALUES ('admin', '$2y$10$2e4PA8LteeZU0/tLdqQ/H.U.ltTa18TWpa4FTbM8YflWDZjvXpw.m');

INSERT INTO budgets (budgetType, amount) values ('weekly', 200);
INSERT INTO budgets (budgetType, amount) values ('monthly', 800);