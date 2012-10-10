create table if not exists tutorial_event1 (
	event_id integer NOT NULL auto_increment,
	titre varchar(30),
    dtcreation date,
	PRIMARY KEY  (event_id)
) CHARACTER SET utf8;
