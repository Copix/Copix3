CREATE TABLE `cms_medias` (
  `id_media` int(11) NOT NULL auto_increment,
  `file_media` VARCHAR( 250 ) NOT NULL,
  `image_media` VARCHAR(255) default NULL,
  `public_id_hei` int(11) default NULL,
  `size_media` int(11) default NULL,
  PRIMARY KEY  (`id_media`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

ALTER TABLE `cms_medias` ADD INDEX ( `public_id_hei` );
ALTER TABLE `cms_medias` ADD CONSTRAINT FOREIGN KEY (`public_id_hei`) REFERENCES `cms_headingelementinformations` (`public_id_hei`);
