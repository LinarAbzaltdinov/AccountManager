CREATE TABLE `Users` (
	`id` INT NOT NULL AUTO_INCREMENT,
	`username` VARCHAR(255) NOT NULL UNIQUE,
	`password` VARCHAR(255) NOT NULL,
	PRIMARY KEY (`id`)
);

CREATE TABLE `Accounts` (
	`id` INT NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(255) NOT NULL,
	`opened` DATETIME NOT NULL,
	`closed` DATETIME,
	PRIMARY KEY (`id`)
);

CREATE TABLE `User_Account` (
	`user_id` INT NOT NULL,
	`acc_id` INT NOT NULL,
	PRIMARY KEY (`user_id`,`acc_id`)
);

CREATE TABLE `Currency` (
	`id` INT NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(255) NOT NULL UNIQUE,
	PRIMARY KEY (`id`)
);

CREATE TABLE `Category` (
	`id` INT NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(255) NOT NULL,
	PRIMARY KEY (`id`)
);

CREATE TABLE `Account_Currency` (
	`acc_id` INT NOT NULL,
	`curr_id` INT NOT NULL,
	`id` INT NOT NULL AUTO_INCREMENT UNIQUE,
	`init_value` FLOAT NOT NULL DEFAULT '0',
	PRIMARY KEY (`acc_id`,`curr_id`)
);

CREATE TABLE `Transactions` (
	`id` INT NOT NULL AUTO_INCREMENT,
	`acc_curr_id_from` INT,
	`acc_curr_id_to` INT,
	`cat_id` INT,
	`value` FLOAT NOT NULL,
	`exchange_rate` FLOAT,
	`tr_date` DATETIME NOT NULL,
	PRIMARY KEY (`id`)
);

CREATE TABLE `Tags` (
	`id` INT NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(255) NOT NULL,
	`user_id` INT NOT NULL,
	PRIMARY KEY (`id`)
);

CREATE TABLE `Accumulations` (
	`id` INT NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(255) NOT NULL,
	`value` FLOAT NOT NULL,
	`cur_id` INT NOT NULL,
	PRIMARY KEY (`id`)
);

CREATE TABLE `Transaction_tag` (
	`trans_id` INT NOT NULL,
	`tag_id` INT NOT NULL
);

CREATE TABLE `Wishlist` (
	`id` INT NOT NULL AUTO_INCREMENT,
	`user_id` INT NOT NULL,
	`name` VARCHAR(255) NOT NULL,
	`value` FLOAT NOT NULL,
	`curr_id` INT NOT NULL,
	PRIMARY KEY (`id`)
);

CREATE TABLE `Templates` (
	`id` INT NOT NULL AUTO_INCREMENT,
	`user_id` INT NOT NULL,
	`currency_id` INT NOT NULL,
	`value` INT NOT NULL,
	`name` VARCHAR(255) NOT NULL UNIQUE,
	PRIMARY KEY (`id`)
);

ALTER TABLE `User_Account` ADD CONSTRAINT `User_Account_fk0` FOREIGN KEY (`user_id`) REFERENCES `Users`(`id`);

ALTER TABLE `User_Account` ADD CONSTRAINT `User_Account_fk1` FOREIGN KEY (`acc_id`) REFERENCES `Accounts`(`id`);

ALTER TABLE `Account_Currency` ADD CONSTRAINT `Account_Currency_fk0` FOREIGN KEY (`acc_id`) REFERENCES `Accounts`(`id`);

ALTER TABLE `Account_Currency` ADD CONSTRAINT `Account_Currency_fk1` FOREIGN KEY (`curr_id`) REFERENCES `Currency`(`id`);

ALTER TABLE `Transactions` ADD CONSTRAINT `Transactions_fk0` FOREIGN KEY (`acc_curr_id_from`) REFERENCES `Account_Currency`(`id`);

ALTER TABLE `Transactions` ADD CONSTRAINT `Transactions_fk1` FOREIGN KEY (`acc_curr_id_to`) REFERENCES `Account_Currency`(`id`);

ALTER TABLE `Transactions` ADD CONSTRAINT `Transactions_fk2` FOREIGN KEY (`cat_id`) REFERENCES `Category`(`id`);

ALTER TABLE `Tags` ADD CONSTRAINT `Tags_fk0` FOREIGN KEY (`user_id`) REFERENCES `Users`(`id`);

ALTER TABLE `Accumulations` ADD CONSTRAINT `Accumulations_fk0` FOREIGN KEY (`cur_id`) REFERENCES `Currency`(`id`);

ALTER TABLE `Transaction_tag` ADD CONSTRAINT `Transaction_tag_fk0` FOREIGN KEY (`trans_id`) REFERENCES `Transactions`(`id`);

ALTER TABLE `Transaction_tag` ADD CONSTRAINT `Transaction_tag_fk1` FOREIGN KEY (`tag_id`) REFERENCES `Tags`(`id`);

ALTER TABLE `Wishlist` ADD CONSTRAINT `Wishlist_fk0` FOREIGN KEY (`user_id`) REFERENCES `Users`(`id`);

ALTER TABLE `Wishlist` ADD CONSTRAINT `Wishlist_fk1` FOREIGN KEY (`curr_id`) REFERENCES `Currency`(`id`);

ALTER TABLE `Templates` ADD CONSTRAINT `Templates_fk0` FOREIGN KEY (`user_id`) REFERENCES `Users`(`id`);

ALTER TABLE `Templates` ADD CONSTRAINT `Templates_fk1` FOREIGN KEY (`currency_id`) REFERENCES `Currency`(`id`);

ALTER TABLE `Templates` ADD CONSTRAINT `Templates_fk2` FOREIGN KEY (`value`) REFERENCES `Currency`(`id`);

INSERT INTO Currency (name) VALUES ('RUB'), ('USD'), ('EUR');