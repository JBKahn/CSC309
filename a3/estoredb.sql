SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

CREATE SCHEMA IF NOT EXISTS `estore` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci ;
USE `estore` ;

-- -----------------------------------------------------
-- Table `estore`.`customer`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `estore`.`customers` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `first` VARCHAR(24) NOT NULL ,
  `last` VARCHAR(24) NOT NULL ,
  `login` VARCHAR(16) NOT NULL ,
  `password` VARCHAR(16) NOT NULL ,
  `email` VARCHAR(45) NOT NULL ,
  UNIQUE INDEX `id_UNIQUE` (`id` ASC) ,
  UNIQUE INDEX `login_UNIQUE` (`login` ASC) ,
  UNIQUE INDEX `email_UNIQUE` (`email` ASC) ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `estore`.`product`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `estore`.`products` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(45) NOT NULL ,
  `description` LONGTEXT NOT NULL ,
  `photo_url` VARCHAR(128) NOT NULL ,
  `price` FLOAT NOT NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `id_UNIQUE` (`id` ASC) ,
  UNIQUE INDEX `name_UNIQUE` (`name` ASC) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `estore`.`order`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `estore`.`orders` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `customer_id` INT NOT NULL ,
  `order_date` DATE NOT NULL ,
  `order_time` TIME NOT NULL ,
  `total` FLOAT NOT NULL ,
  `creditcard_number` VARCHAR(16) NOT NULL ,
  `creditcard_month` INT NOT NULL ,
  `creditcard_year` INT NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_order_customer` (`customer_id` ASC) ,
  UNIQUE INDEX `id_UNIQUE` (`id` ASC) ,
  CONSTRAINT `fk_order_customer`
    FOREIGN KEY (`customer_id` )
    REFERENCES `estore`.`customers` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `estore`.`order_item`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `estore`.`order_items` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `order_id` INT NOT NULL ,
  `product_id` INT NOT NULL ,
  `quantity` INT NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_order_item_order1` (`order_id` ASC) ,
  INDEX `fk_order_item_product1` (`product_id` ASC) ,
  UNIQUE INDEX `id_UNIQUE` (`id` ASC) ,
  CONSTRAINT `fk_order_item_order1`
    FOREIGN KEY (`order_id` )
    REFERENCES `estore`.`orders` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_order_item_product1`
    FOREIGN KEY (`product_id` )
    REFERENCES `estore`.`products` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;

CREATE USER 'csc309A3' IDENTIFIED BY 'csc309pass';

grant SELECT on TABLE `estore`.`customers` to 'csc309A3'@'%';
grant UPDATE on TABLE `estore`.`customers` to 'csc309A3'@'%';
grant DELETE on TABLE `estore`.`customers` to 'csc309A3'@'%';
grant INSERT on TABLE `estore`.`customers` to 'csc309A3'@'%';

grant SELECT on TABLE `estore`.`orders` to 'csc309A3'@'%';
grant UPDATE on TABLE `estore`.`orders` to 'csc309A3'@'%';
grant DELETE on TABLE `estore`.`orders` to 'csc309A3'@'%';
grant INSERT on TABLE `estore`.`orders` to 'csc309A3'@'%';

grant SELECT on TABLE `estore`.`order_items` to 'csc309A3'@'%';
grant UPDATE on TABLE `estore`.`order_items` to 'csc309A3'@'%';
grant DELETE on TABLE `estore`.`order_items` to 'csc309A3'@'%';
grant INSERT on TABLE `estore`.`order_items` to 'csc309A3'@'%';

grant SELECT on TABLE `estore`.`products` to 'csc309A3'@'%';
grant UPDATE on TABLE `estore`.`products` to 'csc309A3'@'%';
grant DELETE on TABLE `estore`.`products` to 'csc309A3'@'%';
grant INSERT on TABLE `estore`.`products` to 'csc309A3'@'%';



SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
