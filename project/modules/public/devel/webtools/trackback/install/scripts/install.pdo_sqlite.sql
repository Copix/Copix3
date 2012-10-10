create table trackbacks (
	id_tb integer primary key,
	blogname_tb varchar (255) not null,
	title_tb varchar(80) not null,
	excerpt_tb varchar(250) not null,
	url_tb varchar(255) not null,
	valid_tb integer default 0,
	target_tb varchar(255) not null,
	date_tb varchar(14) not null,
	spam_tb integer not null default -1
);

