CREATE TABLE cash_collection` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `collection_date` DATETIME NULL,
  `machine_number` VARCHAR(45) NULL,
  `dex_trigger` VARCHAR(100) NULL,
  `collection_amount` FLOAT NULL,
  `coin_balance` FLOAT NULL,
  PRIMARY KEY (`id`));

ALTER TABLE `dex_read`
  ADD COLUMN `notes_in_machine` INT NULL AFTER `dex_trigger`;


ALTER TABLE `aroma_db`.`cash_collection`
  CHANGE COLUMN `collection_date` `collection_date` DATE NULL DEFAULT NULL ,
  ADD COLUMN `collection_time` TIME NULL AFTER `collection_date`;


CREATE TABLE `aroma_maindb`.`customer` (
  `id` INT NOT NULL,
  `unique_code` VARCHAR(45) NULL,
  `first_name` VARCHAR(45) NULL,
  `last_name` VARCHAR(45) NULL,
  `email_address` VARCHAR(255) NULL,
  `gender` VARCHAR(45) NULL,
  `date_of_birth` DATE NULL,
  `country` VARCHAR(45) NULL,
  `postcode` VARCHAR(45) NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC),
  UNIQUE INDEX `unique_code_UNIQUE` (`unique_code` ASC));

CREATE TABLE `aroma_maindb`.`registered_customer_product` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `customer_id` INT NULL,
  `purchase_date` DATE NULL,
  `name` VARCHAR(255) NULL,
  `state` VARCHAR(45) NULL,
  `site` VARCHAR(45) NULL,
  `location` VARCHAR(45) NULL,
  `serial_number` VARCHAR(45) NULL,
  `photo` VARCHAR(255) NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC));

ALTER TABLE `aroma_maindb`.`registered_customer_product`
  ADD COLUMN `created_date` DATETIME NULL AFTER `photo`;

ALTER TABLE `aroma_maindb`.`customer`
  CHANGE COLUMN `id` `id` INT(11) NOT NULL AUTO_INCREMENT ,
  ADD COLUMN `created_date` DATETIME NULL AFTER `postcode`;


ALTER TABLE `aroma_db`.`stock_movement_log`
  CHANGE COLUMN `location_id` `location_id` INT(11) NOT NULL DEFAULT NULL ,
  CHANGE COLUMN `counter_location_id` `counter_location_id` TEXT NOT NULL DEFAULT NULL ,
  CHANGE COLUMN `item_id` `item_id` INT(11) NOT NULL DEFAULT NULL ,
  CHANGE COLUMN `adjustment_type` `adjustment_type` VARCHAR(45) NOT NULL DEFAULT NULL ,
  ADD INDEX `indexes` (`location_id` ASC, `counter_location_id` ASC, `item_id` ASC, `user_id` ASC, `adjustment_type` ASC);


ALTER TABLE `aroma_maindb`.`stock_movement_log`
  ADD INDEX `indexes` (`location_id` ASC, `counter_location_id` ASC, `item_id` ASC, `adjustment_type` ASC, `user_id` ASC);
ALTER TABLE `aroma_db`.`customer`
  ADD COLUMN `promotions` TINYINT NULL AFTER `created_date`;


CREATE TABLE `download` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `requested_date` DATE NULL,
  `requested_by` INT NULL,
  `file` VARCHAR(255) NULL,
  PRIMARY KEY (`id`));

ALTER TABLE `download`
  ADD COLUMN `params` TEXT NULL AFTER `file`,
  ADD COLUMN `completed` TINYINT NULL DEFAULT 0 AFTER `params`;