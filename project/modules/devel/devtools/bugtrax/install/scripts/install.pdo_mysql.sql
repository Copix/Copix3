CREATE TABLE bugtrax (
   id_bug INTEGER auto_increment,
   name_bug varchar(50) not null,
   description_bug varchar (1024) not null,
   author_bug varchar (50) not null,
   assignto_bug varchar (50) default '',
   votes_bug integer default 0,
   severity_bug varchar(10) default 0,
   state_bug varchar (20) default 'open',
   date_bug varchar(14) not null,
   modificationdate_bug varchar(14) not null,
   id_bughead integer default NULL,
   PRIMARY KEY (id_bug)
) CHARACTER SET utf8;

CREATE TABLE bugtraxheadings (
   id_bughead INTEGER auto_increment,
   heading_bughead varchar (50) NOT NULL,
   lead_bughead varchar (50) NOT NULL,
   version_bughead varchar (50) NOT NULL,
   PRIMARY KEY (id_bughead)
) CHARACTER SET utf8;
