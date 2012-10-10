CREATE TABLE `simplehelp` (
	`id_sh` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
	`title_sh` VARCHAR( 100 ) NOT NULL ,
	`contenu_sh` TEXT NOT NULL ,
	`page_sh` VARCHAR( 255 ) NOT NULL ,
	`key_sh` VARCHAR( 255 ) NOT NULL
) ENGINE = MYISAM ;
