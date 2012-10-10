CREATE TABLE copixmodule (
  "name_cpm" varchar(255) NOT NULL default '',
  "path_cpm" varchar(255) NOT NULL default '', 
  "version_cpm" varchar(255) NULL, 
  PRIMARY KEY (name_cpm)
) ;

CREATE TABLE copixconfig (
  "id_ccfg" varchar(255) NOT NULL default '',
  "module_ccfg" varchar(255) NOT NULL default '',
  "value_ccfg" varchar(255) default NULL,
  PRIMARY KEY  (id_ccfg)
) ;


CREATE TABLE copixlog (
  "date" varchar(255) NOT NULL default '',
  "profile" varchar(255) NOT NULL default '',
  "message" text NOT NULL default '',
  "level" varchar(255) NOT NULL default '',
  "user" varchar(255) default NULL,
  "classname" varchar(255) default NULL,
  "functionname" varchar(255) default NULL,
  "line" varchar(5) default NULL,
  "file" varchar(255) default NULL,
  "type" varchar(50) default NULL
);
