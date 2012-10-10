DROP TABLE IF EXISTS `flash`;

CREATE TABLE `flash` (
`id_flash` BIGINT( 11 ) NOT NULL ,
`name_flash` VARCHAR( 50 ) NOT NULL ,
`desc_flash` VARCHAR( 255 ) ,
`id_head` BIGINT( 11 ),
`author_flash` VARCHAR( 50 ) NOT NULL,
`version_flash` INT NOT NULL default 0,
PRIMARY KEY ( `id_flash` , `version_flash`)
) CHARACTER SET utf8 COLLATE utf8_unicode_ci;

INSERT INTO `copixcapability` (`name_ccpb`, `description_ccpb`, `name_ccpt`, `values_ccpb`) VALUES ('flash', 'Documents Flash', 'modules|copixheadings', '0;20;70');
