CREATE TABLE `wikipages` (
  `title_wiki` varchar(50) NOT NULL,
  `displayedtitle_wiki` varchar(50) default '',
  `heading_wiki` varchar(255)  default "",
  `content_wiki` text NOT NULL,
  `author_wiki` varchar(80) default NULL,
  `keywords_wiki` varchar(255) default NULL,
  `description_wiki` varchar(255) default NULL,
  `lang_wiki` varchar (3) default NULL,
  `translatefrom_wiki` varchar(50) default NULL,
  `fromlang_wiki` varchar(3) default NULL,
  `modificationdate_wiki` varchar(14) NOT NULL,
  `creationdate_wiki` varchar(14) NOT NULL,
  `lock_wiki` varchar(1) NOT NULL default '0',
  `deleted_wiki` varchar(1) NOT NULL default '0',
   PRIMARY KEY(`title_wiki`,`lang_wiki`,`modificationdate_wiki`)
) CHARACTER SET utf8;

CREATE TABLE `wikiimages` (
   `title_wikiimage` VARCHAR( 255 ) NOT NULL ,
   `file_wikiimage` VARCHAR( 255 ) NOT NULL ,
   `page_wikiimage` VARCHAR( 50 ) NOT NULL,
   PRIMARY KEY ( `title_wikiimage` )
) CHARACTER SET utf8;

CREATE TABLE `wikiheadings` (
   `heading_wikihead` VARCHAR( 255 ) NOT NULL,
   PRIMARY KEY (`heading_wikihead`) 
) CHARACTER SET utf8;

/**
 * Création de la table wiki pour compatibilité avec le wiki trac
 */
CREATE TABLE wiki (
	`name` text not null,
	`version` int(11) NOT NULL default '0',
	`time` int(11) default NULL,
	`author` text,
	`ipnr` text,
	`text` text,
	`comment` text,
	`readonly` int(11) default NULL,
	PRIMARY KEY(`name`(166), `version`),
	KEY `wiki_time_idx` (`time`)
)CHARACTER SET utf8;