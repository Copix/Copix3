CREATE TABLE IF NOT EXISTS `dbuserextend` (
	`id` INT NOT NULL auto_increment,
	`name` VARCHAR( 255 ) NOT NULL UNIQUE,
	`position` INT NOT NULL ,
	`caption` VARCHAR( 255 ) NOT NULL ,
	`type` VARCHAR( 25 ) NOT NULL ,
	`required` BOOL NOT NULL ,
	`parameters` TEXT NULL ,
	`active` BOOL NOT NULL DEFAULT 1,
	PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `dbuserextendvalue` (
	`id_extend` INT NOT NULL ,
	`id_user` VARCHAR( 50 ) NOT NULL ,
	`id_userhandler` VARCHAR( 50 ) NOT NULL ,
	`value` BLOB ,
	PRIMARY KEY  (`id_extend`,`id_user`,`id_userhandler`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;