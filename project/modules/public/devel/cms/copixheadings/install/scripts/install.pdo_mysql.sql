CREATE TABLE `copixheadings` (
  `id_head` bigint(11) NOT NULL auto_increment,
  `father_head` bigint(11) default NULL,
  `position_head` int(11) default '0',
  `caption_head` varchar(200) NOT NULL default '',
  `description_head` varchar(255) NOT NULL default '',
  `url_head` varchar(255) NULL,
  PRIMARY KEY  (`id_head`)
)CHARACTER SET utf8 COLLATE utf8_unicode_ci;