CREATE TABLE `grid` (
  `grid_id` int(11) NOT NULL AUTO_INCREMENT,
  `grid_type` varchar(45) DEFAULT NULL,
  `grid_size` int(11) DEFAULT NULL,
  `grid_name` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`grid_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `oddworld`.`square` (
  `square_id` INT NOT NULL AUTO_INCREMENT,
  `grid_id` INT NULL,
  `square_x` INT NULL,
  `square_y` INT NULL,
  `square_type` VARCHAR(45) NULL,
  PRIMARY KEY (`square_id`));

CREATE TABLE `oddworld`.`feature` (
  `feature_id` INT NOT NULL AUTO_INCREMENT,
  `feature_type` VARCHAR(45) NULL,
  `feature_name` VARCHAR(45) NULL,
  `square_id` INT NULL,
  `feature_variant` VARCHAR(45),
  PRIMARY KEY (`feature_id`));