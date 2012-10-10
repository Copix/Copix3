CREATE TABLE `dbuserextend` (
	`id` TINYINT( 11 ) NOT NULL auto_increment,
	`i18n` BOOL NOT NULL ,
	`caption` VARCHAR( 300 ) NOT NULL ,
	`type` VARCHAR( 25 ) NOT NULL ,
	`parametre` VARCHAR( 255 ) NULL ,
	PRIMARY KEY  (`id`)
) CHARACTER SET utf8;
