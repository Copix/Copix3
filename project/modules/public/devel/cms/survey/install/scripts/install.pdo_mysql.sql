DROP TABLE IF EXISTS `survey`;
CREATE TABLE `survey` (
	`id_svy` BIGINT( 11 ) NOT NULL ,
	`title_svy` VARCHAR( 50 ) NOT NULL ,
	`id_head` BIGINT( 11 ) NULL,
	`option_svy` TEXT NOT NULL ,
	`response_svy` INT NOT NULL,
	`authuser_svy` TINYINT(1) default '0',
	PRIMARY KEY ( `id_svy` )
) CHARACTER SET utf8;

INSERT INTO `copixcapability` (`name_ccpb`, `description_ccpb`, `name_ccpt`, `values_ccpb`) VALUES ('survey', 'Sondages', 'modules|copixheadings', '0;30;60;70');