CREATE TABLE copixtestforeignkeytype (
  "type_test" serial,
  "caption_typetest" varchar(255) NOT NULL default '',
  PRIMARY KEY (type_test),unique(type_test)
) ;

CREATE TABLE copixtestmain (
  "id_test" serial,
  "type_test" integer NOT NULL default '0',
  "titre_test" varchar(255) NOT NULL default '',
  "description_test" text NOT NULL,
  "date_test" TIMESTAMP NOT NULL,
  "version_test" int not null default '0',
  PRIMARY KEY  (id_test), unique(id_test)
) ;

CREATE TABLE copixtestautodao (
  "id_test" serial,
  "type_test" integer NOT NULL default '0',
  "titre_test" varchar(255) NOT NULL default '',
  "description_test" text NOT NULL,
  "nullable_test" integer,
  PRIMARY KEY  (id_test), UNIQUE(id_test)
) ;
