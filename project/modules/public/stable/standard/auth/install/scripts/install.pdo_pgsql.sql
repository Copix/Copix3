CREATE TABLE "dbgroup" (
  "id_dbgroup" serial,
  "caption_dbgroup" varchar(255) NOT NULL,
  "description_dbgroup" text NULL,
  "superadmin_dbgroup" smallint NOT NULL,
  "public_dbgroup" smallint NOT NULL,
  "registered_dbgroup" smallint NOT NULL,
  PRIMARY KEY  ("id_dbgroup"), UNIQUE("id_dbgroup")
) ;

INSERT INTO "dbgroup" ("caption_dbgroup", "description_dbgroup", "superadmin_dbgroup", "public_dbgroup", "registered_dbgroup") VALUES ('Admin', 'Groupe administrateur', 1, 0, 0);

CREATE TABLE "dbgroup_users" (
  "id_dbgroup" integer NOT NULL,
  "userhandler_dbgroup" varchar(255) NOT NULL,
  "user_dbgroup" varchar(255) NOT NULL
) ;

INSERT INTO "dbgroup_users" ("id_dbgroup", "userhandler_dbgroup", "user_dbgroup") VALUES (1, 'auth|dbuserhandler', '1');

CREATE TABLE "dbuser" (
  "id_dbuser" serial,
  "login_dbuser" varchar(32) NOT NULL,
  "password_dbuser" varchar(32) NOT NULL,
  "email_dbuser" varchar(255) NOT NULL,
  "enabled_dbuser" smallint NOT NULL,
  PRIMARY KEY  ("id_dbuser"), UNIQUE ("id_dbuser"),
  UNIQUE ("login_dbuser")
) ;


INSERT INTO "dbuser" ("login_dbuser", "password_dbuser", "email_dbuser", "enabled_dbuser") VALUES ('admin', '21232f297a57a5a743894a0e4a801fc3', 'root@localhost', 1);


CREATE TABLE modulecredentials (
    id_mc serial,
    module_mc VARCHAR(255),
    name_mc VARCHAR(255) NOT NULL,
    primary key(id_mc), UNIQUE (id_mc) 
);

CREATE TABLE modulecredentialsvalues (
    id_mcv serial,
    value_mcv VARCHAR(255) NOT NULL,
    id_mc INT NOT NULL,
    level_mcv INT,
    primary key (id_mcv), UNIQUE (id_mcv)
);

CREATE TABLE modulecredentialsoverpass (
    id_mco serial,
    id_mc INT,
    overpass_id_mc INT,
    overpath_id_mc INT,
    primary key(id_mco), UNIQUE (id_mco)
);

CREATE TABLE modulecredentialsgroups (
    id_mcg serial,
    id_mc INT NOT NULL,
    id_mcv INT,
    handler_group VARCHAR(255),
    id_group VARCHAR(255),
    primary key(id_mcg), UNIQUE (id_mcg)
);


CREATE TABLE dynamiccredentials (
    id_dc serial,
    name_dc VARCHAR(255) NOT NULL,
    primary key(id_dc) ,  UNIQUE (id_dc)
);

CREATE TABLE dynamiccredentialsvalues (
    id_dcv serial,
    value_dcv VARCHAR(255) NOT NULL,
    id_dc INT NOT NULL,
    level_dcv INT,
    primary key (id_dcv),  UNIQUE (id_dcv)
);

CREATE TABLE dynamiccredentialsgroups (
    id_dcg serial,
    id_dc INT NOT NULL,
    id_dcv INT,
    handler_group VARCHAR(255),
    id_group VARCHAR(255),
    primary key(id_dcg),  UNIQUE (id_dcg)
);
