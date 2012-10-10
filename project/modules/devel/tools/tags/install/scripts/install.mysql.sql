CREATE TABLE `tags` (
    `name_tag` varchar(50) NOT NULL,
    `description_tag` text,
    PRIMARY KEY  (`name_tag`)
) CHARACTER SET utf8;

CREATE TABLE `tags_content` (
    `name_tag` VARCHAR( 50 ) NOT NULL ,
    `kindobj_tag` VARCHAR( 50 ) NOT NULL ,
    `idobj_tag` VARCHAR( 50 ) NOT NULL ,
    PRIMARY KEY ( `name_tag` , `kindobj_tag` , `idobj_tag` )
) CHARACTER SET utf8;

CREATE TABLE `tags_informations` (
    `id_tagi` tinyint(10) NOT NULL auto_increment,
    `name_tag` VARCHAR( 50 ) NOT NULL ,
    `type_tagi` VARCHAR( 20 ) NOT NULL ,
    `content_tagi` TEXT NOT NULL ,
    PRIMARY KEY  (`id_tagi`)
) CHARACTER SET utf8;

ALTER TABLE `tags_content` ADD INDEX ( `kindobj_tag` );
ALTER TABLE `tags_content` ADD INDEX ( `idobj_tag` );
ALTER TABLE `tags_informations` ADD INDEX ( `name_tag` );
