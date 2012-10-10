CREATE TABLE "webservices" (
  "id_webservices" serial,
  "name_webservices" varchar(32) not NULL,
  "file_webservices" varchar(255) NOT NULL,
  "class_webservices" varchar(255) NOT NULL,
  PRIMARY KEY  ("id_webservices"), UNIQUE ("id_webservices"),
  UNIQUE ("name_webservices")
) ;
