CREATE TABLE `cms_documents` (
  `id_document` int(11) NOT NULL auto_increment,
  `file_document` VARCHAR( 250 ) NOT NULL,
  `public_id_hei` int(11) default NULL,
  `size_document` int(11) default NULL,
  PRIMARY KEY  (`id_document`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

ALTER TABLE `cms_documents` ADD CONSTRAINT FOREIGN KEY (`public_id_hei`) REFERENCES `cms_headingelementinformations` (`public_id_hei`);
