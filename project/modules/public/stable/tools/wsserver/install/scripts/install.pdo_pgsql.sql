CREATE TABLE "wsservices" (
  "id_wsservices" serial,
  "name_wsservices" varchar(32) not NULL,
  "module_wsservices" varchar(32) NOT NULL,
  "file_wsservices" varchar(32) NOT NULL,
  "class_wsservices" varchar(32) NOT NULL,
  PRIMARY KEY  ("id_wsservices"), UNIQUE ("id_wsservices"),
  UNIQUE ("name_wsservices")
) ;
