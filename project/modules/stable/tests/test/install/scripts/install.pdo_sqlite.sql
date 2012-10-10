CREATE TABLE testforeignkeytype (
  type_test INTEGER PRIMARY KEY,
  caption_typetest varchar(255) NOT NULL
);

CREATE TABLE testmain (
  id_test INTEGER PRIMARY KEY,
  type_test INTEGER NOT NULL,
  titre_test varchar(255) NOT NULL,
  description_test text NOT NULL,
  date_test varchar(8) NOT NULL,
  version_test INTEGER not null
);

CREATE TABLE testautodao (
  id_test INTEGER PRIMARY KEY,
  type_test INTEGER NOT NULL,
  titre_test varchar(255) NOT NULL,
  description_test text NOT NULL,
  date_test varchar(8) NOT NULL,
  nullable_test INTEGER
);
