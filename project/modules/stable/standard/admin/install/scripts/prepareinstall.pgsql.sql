
DROP TABLE IF EXISTS copixmodule;
CREATE TABLE copixmodule (
  "name_cpm" VARCHAR(255) NOT NULL DEFAULT '',
  "path_cpm" VARCHAR(255) NOT NULL DEFAULT '', 
  "version_cpm" VARCHAR(255) NULL, 
  PRIMARY KEY (name_cpm)
) ;

DROP TABLE IF EXISTS copixconfig;
CREATE TABLE copixconfig (
  "id_ccfg" VARCHAR(255) NOT NULL DEFAULT '',
  "module_ccfg" VARCHAR(255) NOT NULL DEFAULT '',
  "value_ccfg" TEXT DEFAULT NULL,
  PRIMARY KEY  (id_ccfg)
) ;


DROP TABLE IF EXISTS copixlog;
CREATE TABLE copixlog (
  "id_log" BIGSERIAL NOT NULL,
  "date_log" TIMESTAMP NOT NULL DEFAULT 'now',
  "message_log" TEXT NOT NULL DEFAULT '',
  "profile_log" VARCHAR(100) NOT NULL DEFAULT '',
  "level_log" SMALLINT NOT NULL DEFAULT '0',
  "type_log" VARCHAR(100) NOT NULL DEFAULT '',
  PRIMARY KEY  (id_log)
);
ALTER TABLE `copixlog` ADD INDEX ( `profile_log` ) ;

DROP TABLE IF EXISTS "copixlogextras";
CREATE TABLE "copixlogextras" (
  "id_extra" BIGSERIAL NOT NULL,
  "id_log" BIGINT NOT NULL DEFAULT '0',
  "key_extra" VARCHAR(255) NOT NULL DEFAULT '',
  "value_extra" TEXT NOT NULL DEFAULT '',
  PRIMARY KEY  (id_extra)
);

DROP TABLE IF EXISTS "copixuserpreferences";
CREATE TABLE "copixuserpreferences" (
  "id_pref" BIGSERIAL NOT NULL,
  "id_user" VARCHAR(50) NOT NULL,
  "id_userhandler" VARCHAR(50) NOT NULL,
  "login_user" VARCHAR(255) NOT NULL,
  "name_pref" VARCHAR(50) NOT NULL,
  "value_pref" VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY  ("id_pref"),
  UNIQUE ("name_pref", "id_user", "id_userhandler")
);

DROP TABLE IF EXISTS "copixgrouppreferences";
CREATE TABLE "copixgrouppreferences" (
  "id_pref" BIGSERIAL unsigned NOT NULL auto_increment,
  "id_group" varchar(50) NOT NULL,
  "id_grouphandler" varchar(50) NOT NULL,
  "name_group" varchar(255) NOT NULL,
  "name_pref" varchar(50) NOT NULL,
  "value_pref" varchar(255) default NULL,
  PRIMARY KEY  ("id_pref"),
  UNIQUE ("name_pref", "id_group", "id_grouphandler")
);