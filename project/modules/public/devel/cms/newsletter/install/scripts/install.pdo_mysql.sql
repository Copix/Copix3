#
# Table structure for table `newslettergroups`
#
DROP TABLE IF EXISTS `newslettergroups`;
CREATE TABLE `newslettergroups` (
  `id_nlg` int(11) NOT NULL auto_increment,
  `name_nlg` varchar(250) NOT NULL default '',
  `desc_nlg` text,
  PRIMARY KEY  (`id_nlg`)
) CHARACTER SET utf8;

# --------------------------------------------------------

#
# Table structure for table `newslettermail`
#
DROP TABLE IF EXISTS `newslettermail`;
CREATE TABLE `newslettermail` (
  `valid_nlm` tinyint(1) NOT NULL default '0',
  `mail_nlm` varchar(250) NOT NULL default '',
  PRIMARY KEY  (`mail_nlm`)
) CHARACTER SET utf8;

# --------------------------------------------------------

#
# Table structure for table `newslettermaillinkgroups`
#
 DROP TABLE IF EXISTS `newslettermaillinkgroups`;
CREATE TABLE `newslettermaillinkgroups` (
  `id_nlg` int(11) NOT NULL default '0',
  `mail_nlm` varchar(250) NOT NULL default ''
) CHARACTER SET utf8;

# --------------------------------------------------------

#
# Table structure for table `newslettersend`
#
DROP TABLE IF EXISTS `newslettersend`;
CREATE TABLE newslettersend (
  id_cmsp bigint(20)  NOT NULL,
  date_nls varchar(8) NOT NULL,
  id_nlg varchar(255)  NULL default ''  ,
  id_cgrp varchar(255)  NULL default ''  ,
  title_nls varchar(150)  NULL default ''  ,
  htmlcontent_nls text NULL default '',
  PRIMARY KEY  (id_cmsp,date_nls)
) CHARACTER SET utf8;

INSERT INTO copixcapability (name_ccpb, description_ccpb, name_ccpt, values_ccpb) VALUES ('newsletter', 'Lettres d\'information', 'modules|cms|newsletter', '0;50;60;70');
INSERT INTO copixcapabilitypath (name_ccpt, description_ccpt) VALUES ('modules|cms|newsletter', 'Envoi et administration des newsletters');
