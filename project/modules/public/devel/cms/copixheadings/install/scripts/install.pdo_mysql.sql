CREATE TABLE `copixheadings` (
  `id_head` bigint(11) NOT NULL auto_increment,
  `father_head` bigint(11) default NULL,
  `position_head` int(11) default '0',
  `caption_head` varchar(200) NOT NULL default '',
  `description_head` varchar(255) NOT NULL default '',
  `url_head` varchar(255) NULL,
  PRIMARY KEY  (`id_head`)
)CHARACTER SET utf8 COLLATE utf8_unicode_ci;
INSERT INTO `copixcapability` (`name_ccpb`, `description_ccpb`, `name_ccpt`, `values_ccpb`) VALUES ('copixheadings', 'Rubriques', 'modules|copixheadings', '0;10;30');
INSERT INTO `copixcapabilitypath` (`name_ccpt`, `description_ccpt`) VALUES ('modules|copixheadings', 'Rubrique principale');