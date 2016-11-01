CREATE TABLE `user` ( 
	`userId` INT NOT NULL AUTO_INCREMENT,
	`email` VARCHAR(150) NOT NULL , 
	`password` VARCHAR(64) NOT NULL , 
	`name` VARCHAR(50) NULL DEFAULT NULL , 
	`firstname` VARCHAR(50) NULL DEFAULT NULL , 
	`timezone` VARCHAR(50) NOT NULL , 
	`country` VARCHAR(50) NOT NULL , 
	`registerDate` INT NOT NULL , 
	`lastLogin` INT NOT NULL , 
	`resetToken` VARCHAR(10) NULL DEFAULT NULL, 
	PRIMARY KEY (`userId`) 
) ENGINE = InnoDB;

CREATE TABLE `watch` ( 
	`watchId` INT NOT NULL AUTO_INCREMENT , 
	`userId` INT NOT NULL,
	`brand` VARCHAR(50) NOT NULL , 
	`name` VARCHAR(50) NOT NULL , 
	`yearOfBuy` INT(4) NULL DEFAULT NULL , 
	`serial` VARCHAR(150) NULL DEFAULT NULL,
	FOREIGN KEY (`userId`) REFERENCES user(`userId`) ON DELETE CASCADE, 
	PRIMARY KEY (`watchId`)
) ENGINE = InnoDB;

CREATE TABLE `measure` ( 
	`measureId` INT NOT NULL AUTO_INCREMENT , 
	`watchId` INT NOT NULL,
	`referenceTime` INT NOT NULL , 
	`userTime` INT NOT NULL , 
	FOREIGN KEY (`watchId`) REFERENCES watch(`watchId`) ON DELETE CASCADE, 
	PRIMARY KEY (`measureId`)
) ENGINE = InnoDB;