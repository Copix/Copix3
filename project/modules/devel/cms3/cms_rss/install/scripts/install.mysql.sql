CREATE TABLE IF NOT EXISTS `cms_rss` (
  `id_rss` int(11) NOT NULL AUTO_INCREMENT,
  `public_id_hei` int(11) DEFAULT NULL,
  `heading_public_id_rss` int(11) NOT NULL,
  `order_rss` int(11) NOT NULL,
  PRIMARY KEY (`id_rss`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

ALTER TABLE `cms_rss` ADD INDEX ( `public_id_hei` )  ;

ALTER TABLE `cms_rss` ADD CONSTRAINT FOREIGN KEY (`public_id_hei`) REFERENCES `cms_headingelementinformations` (`public_id_hei`);

CREATE TABLE IF NOT EXISTS `cms_rss_headingelementinformations` (
  `id_rss` int(11) NOT NULL,
  `headingelement_public_id` int(11) NOT NULL,
  `rss_public_id` int(11) NOT NULL,
  PRIMARY KEY (`id_rss`,`headingelement_public_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

ALTER TABLE `cms_rss` ADD `element_types_rss` VARCHAR( 255 ) NOT NULL DEFAULT 'article' ;
ALTER TABLE `cms_rss` ADD `recursive_flag` INT( 1 ) NOT NULL DEFAULT 0;
