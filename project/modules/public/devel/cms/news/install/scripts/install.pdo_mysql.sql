CREATE TABLE news (
  id_news int(11) NOT NULL auto_increment,
  id_head bigint(20) default NULL,
  title_news varchar(50) NOT NULL default '',
  summary_news text,
  content_news text,
  id_pict integer NULL default NULL,
  datewished_news varchar(8) NOT NULL default '',
  status_news int(11) NOT NULL default '0',
  statusdate_news VARCHAR( 8 ) NOT NULL ,
  statusauthor_news VARCHAR( 50 ) NOT NULL ,
  statuscomment_news VARCHAR(255 ) NULL ,
  author_news varchar(50) default NULL,
  editionKind_news varchar(50) NOT NULL,
  PRIMARY KEY  (id_news)
) CHARACTER SET utf8 COLLATE utf8_unicode_ci;