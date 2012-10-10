CREATE TABLE `copixtemplate_theme` (
  `id_ctpt` int(11) NOT NULL auto_increment,
  `caption_ctpt` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id_ctpt`)
);

CREATE TABLE `copixtemplate` (
  `id_ctpl` int(11) NOT NULL auto_increment,
  `publicid_ctpl` int(11) default NULL,
  `qualifier_ctpl` varchar(255) NULL default '',
  `modulequalifier_ctpl` varchar(255) NOT NULL default '',
  `caption_ctpl` varchar(255) NOT NULL default '',
  `content_ctpl` text NOT NULL,
  `id_ctpt` int(11) NULL default '0',
  `generated_ctpl` text NULL,
  PRIMARY KEY  (`id_ctpl`),
  KEY `publicid_ctpl` (`publicid_ctpl`)
);

insert into copixtemplate_theme(caption_ctpt) values ('default');