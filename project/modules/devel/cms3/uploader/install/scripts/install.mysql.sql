CREATE TABLE `cms_uploader_sessions` (
`id` INT NOT NULL AUTO_INCREMENT ,
`id_session` VARCHAR( 255 ) NOT NULL ,
`create_session` DATETIME NOT NULL ,
`state_session` VARCHAR( 255 ) NOT NULL ,
`path_session` VARCHAR( 255 ) NOT NULL ,
PRIMARY KEY ( `id` )
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE `cms_uploader_files` (
`id_file` INT NOT NULL AUTO_INCREMENT ,
`id_session` VARCHAR( 255 ) NOT NULL ,
`name_file` VARCHAR( 255 ) NOT NULL ,
`create_file` DATETIME NOT NULL ,
PRIMARY KEY ( `id_file` )
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
