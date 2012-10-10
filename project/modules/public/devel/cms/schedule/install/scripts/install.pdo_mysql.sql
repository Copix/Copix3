CREATE TABLE scheduleevents (
  id_evnt int(32) NOT NULL auto_increment,
  id_head bigint(32) default NULL,
  title_evnt varchar(50) NOT NULL default '',
  content_evnt text NOT NULL,
  statusdate_evnt VARCHAR( 8 ) NOT NULL ,
  statusauthor_evnt VARCHAR( 50 ) NOT NULL ,
  statuscomment_evnt VARCHAR(255 ) NULL ,
  author_evnt varchar(50) default NULL,
  status_evnt tinyint(1) NOT NULL default '0',
  datefrom_evnt varchar(8) NOT NULL default '',
  dateto_evnt varchar(8) default '',
  datedisplayfrom_evnt varchar(8) NOT NULL default '',
  datedisplayto_evnt varchar(8) NOT NULL default '',
  preview_evnt varchar(255) default NULL,
  editionkind_evnt varchar(50) NOT NULL,
  subscribeenabled_evnt int(1) default 0,
  PRIMARY KEY  (id_evnt)
) CHARACTER SET utf8 COLLATE utf8_unicode_ci; 

# --------------------------------------------------------
INSERT INTO copixcapability (name_ccpb, description_ccpb, name_ccpt, values_ccpb) VALUES ('schedule', 'Gestion Agenda', 'modules|copixheadings', '0;30;40;50;60;70');
INSERT INTO copixgroupcapabilities (id_cgrp, name_ccpb, name_ccpt, value_cgcp) VALUES (1, 'schedule', 'modules|copixheadings', 70);
