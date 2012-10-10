CREATE TABLE `csvfile` (
	`id_csvfile` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
	`date_csvfile` VARCHAR (14) NOT NULL,
	`heure_csvfile` VARCHAR ( 6 ) NOT NULL ,
	`nomfichier_csvfile` VARCHAR ( 50 ) NOT NULL
) CHARACTER SET utf8;