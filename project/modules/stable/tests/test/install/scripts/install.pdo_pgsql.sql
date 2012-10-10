CREATE TABLE testforeignkeytype (
  type_test serial,
  caption_typetest varchar(255) NOT NULL default '',
  PRIMARY KEY (type_test),unique(type_test)
) ;

CREATE TABLE testmain (
  id_test serial,
  type_test integer NOT NULL default '0',
  titre_test varchar(255) NOT NULL default '',
  description_test text NOT NULL,
  date_test TIMESTAMP NOT NULL,
  version_test int not null default '0',
  PRIMARY KEY  (id_test), unique(id_test)
) ;

CREATE TABLE testcategory (
  id_ctest SERIAL,
  caption_ctest varchar(255) NOT NULL,
  PRIMARY KEY (id_ctest)
);

CREATE TABLE testlevel (
  id_level SERIAL,
  caption_level varchar(255) NOT NULL,
  email varchar(512) NOT NULL,
  log varchar(255) default NULL,
  PRIMARY KEY  (id_level)
);

INSERT INTO testlevel (id_level, caption_level, email, log) VALUES
(1, 'Critique', 'a.julien@alptis.fr', NULL),
(2, 'Important', 'a.julien@alptis.fr', NULL),
(3, 'Moyen', 'a.julien@alptis.fr', NULL),
(4, 'Bas', 'a.julien@alptis.fr', NULL),
(5, 'Très bas', 'a.julien@alptis.fr', NULL);


CREATE TABLE test (
  id_test SERIAL,
  caption_test varchar(255) NOT NULL,
  type_test varchar(255) NOT NULL,
  category_test int NOT NULL,
  level_test int NOT NULL,
  PRIMARY KEY  (id_test)
);


CREATE TABLE testautodao (
  id_test SERIAL,
  type_test int NOT NULL default '0',
  titre_test varchar(255) NOT NULL default '',
  description_test text NOT NULL,
  date_test varchar(8) NOT NULL default '',
  nullable_test int,
  PRIMARY KEY  (id_test)
);

CREATE TABLE testhistory (
  id_history SERIAL,
  id_test int NOT NULL,
  time_date timestamp NOT NULL default CURRENT_TIMESTAMP,
  result smallint NOT NULL,
  exception varchar(255) default NULL,
  timing varchar(255) default NULL,
  PRIMARY KEY  (id_history)
);


CREATE TABLE datetimetests (
  id_dtt serial,
  date_dtt date default NULL,
  datetime_dtt timestamp default NULL,
  time_dtt time default NULL,
  PRIMARY KEY  (id_dtt)
) ;

INSERT INTO datetimetests (
	date_dtt ,
	datetime_dtt ,
	time_dtt
)
VALUES (
	'2007-11-21', '2007-11-29 10:12:22', '10:12:22'
), (
	'2007-11-20', '2007-11-20 10:12:42', '10:12:42'
);
