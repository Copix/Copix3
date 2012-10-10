CREATE TABLE `languageslocks` (
	`id_lock` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
	`id_dbuser` INT UNSIGNED NOT NULL ,
	`id_session` VARCHAR( 32 ) NOT NULL ,
	`module_lock` VARCHAR( 40 ) NOT NULL ,
	`file_lock` VARCHAR( 40 ) NOT NULL ,
	`time_lock` INT( 11 ) NOT NULL
) ENGINE = MYISAM ;