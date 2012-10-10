DROP TABLE IF EXISTS `document`;
CREATE TABLE `document` (
	`id_doc` BIGINT( 11 ) NOT NULL ,
	`title_doc` VARCHAR( 50 ) NOT NULL ,
	`nameindex_doc` INT NOT NULL default 0,
	`desc_doc` VARCHAR( 255 ) ,
	`id_head` BIGINT( 11 ),
	`weight_doc` INT NOT NULL ,
	`status_doc` INT NOT NULL,
	`extension_doc` VARCHAR( 10 ) NOT NULL ,
	`statusdate_doc` VARCHAR( 8 ) NOT NULL ,
	`statusauthor_doc` VARCHAR( 50 ) NOT NULL ,
	`author_doc` VARCHAR( 50 ) NOT NULL ,
	`statuscomment_doc` VARCHAR(255 ) NULL ,
	`version_doc` INT NOT NULL default 0,
	PRIMARY KEY ( `id_doc` , `version_doc`)
) CHARACTER SET utf8;

INSERT INTO `copixcapability` (`name_ccpb`, `description_ccpb`, `name_ccpt`, `values_ccpb`) VALUES ('document', 'Documents', 'modules|copixheadings', '0;30;40;50;60;70');