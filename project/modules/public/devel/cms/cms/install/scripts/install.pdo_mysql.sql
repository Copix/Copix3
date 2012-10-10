CREATE TABLE `cmspage` (
  `id_cmsp` bigint(20) NOT NULL auto_increment,
  `publicid_cmsp` bigint(20) default '0',
  `version_cmsp` int(11) NOT NULL default '0',
  `title_cmsp` varchar(150) default NULL,
  `titlebar_cmsp` varchar(150) default NULL,
  `summary_cmsp` varchar(255) default NULL,
  `id_head` bigint(20) default NULL,
  `author_cmsp` varchar(255) NOT NULL default '',
  `status_cmsp` tinyint(4) NOT NULL default '0',
  `statusdate_cmsp` varchar(8) NOT NULL default '',
  `statusauthor_cmsp` varchar(50) NOT NULL default '',
  `statuscomment_cmsp` varchar(255) default NULL,
  `keywords_cmsp` text,
  `datemax_cmsp` varchar(8) default NULL,
  `datemin_cmsp` varchar(8) default NULL,
  `content_cmsp` text,
  PRIMARY KEY  (`id_cmsp`),
  KEY `publicid_cmsp` (`publicid_cmsp`)
)CHARACTER SET utf8 COLLATE utf8_unicode_ci;


INSERT INTO `copixcapabilitypath` (`name_ccpt`, `description_ccpt`) VALUES ('modules|cms|portlet', 'Portlets disponibles');
INSERT INTO `copixcapability` (`name_ccpb`, `description_ccpb`, `name_ccpt`, `values_ccpb`) VALUES ('cms', 'Gestion de contenu', 'modules|copixheadings', '0;10;20;30;40;50;60;70');
INSERT INTO `copixcapability` (`name_ccpb`, `description_ccpb`, `name_ccpt`, `values_ccpb`) VALUES ('portlet', 'Utilisation d\'une portlet', 'modules|cms|portlet', '0;10');