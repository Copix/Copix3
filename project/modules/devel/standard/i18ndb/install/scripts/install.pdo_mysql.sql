CREATE TABLE IF NOT EXISTS `i18ndb` (
	`id` INT NOT NULL auto_increment,
	`lang` VARCHAR( 3 ) NOT NULL,
	`country` VARCHAR( 2 ) ,
	`context` VARCHAR( 50 ) NOT NULL ,
	`key` VARCHAR( 255 ) NOT NULL ,
	`value` TEXT NOT NULL ,
	PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
