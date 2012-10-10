CREATE TABLE `cms_articles` (
  `id_article` int(11) NOT NULL auto_increment,
  `summary_article` text,
  `content_article` text NOT NULL,
  `public_id_hei` int(11) default NULL,
  `editor_article` int(11) NOT NULL default '1',
  PRIMARY KEY  (`id_article`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

ALTER TABLE `cms_articles` ADD INDEX ( `public_id_hei` );
ALTER TABLE `cms_articles` ADD CONSTRAINT FOREIGN KEY (`public_id_hei`) REFERENCES `cms_headingelementinformations` (`public_id_hei`);
