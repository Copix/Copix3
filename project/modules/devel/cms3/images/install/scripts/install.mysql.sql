CREATE TABLE `cms_images` (
  `id_image` int(11) NOT NULL auto_increment,
  `file_image` VARCHAR( 250 ) NOT NULL,
  `public_id_hei` int(11) default NULL,
  `size_image` int(11) default NULL,
  PRIMARY KEY  (`id_image`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

ALTER TABLE `cms_images` ADD INDEX ( `public_id_hei` );  
ALTER TABLE `cms_images` ADD CONSTRAINT FOREIGN KEY (`public_id_hei`) REFERENCES `cms_headingelementinformations` (`public_id_hei`);

