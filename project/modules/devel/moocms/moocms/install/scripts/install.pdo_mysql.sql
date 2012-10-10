create table moocms_pages (
    name_moocmspage varchar(255) not null,
    date_moocmspage varchar(14) not null,
    template_moocmspage varchar(80) not null,
    id_moocmsh int default null
) CHARACTER SET utf8;

create table moocms_heading (
   id_moocmsh int(11) auto_increment,
   name_moocmsh varchar(255) not null,
   description_moocmsh text default null,
   PRIMARY KEY(id_moocmsh)
) CHARACTER SET utf8;

create table moocms_boxes (
  id_moocmsbox int(11) auto_increment,
  params_moocmsbox varchar(1024) default null,
  order_moocmsbox int default 0,
  date_moocmsbox varchar(14) not null,
  name_moocmspage varchar(255) not null,
  PRIMARY KEY(id_moocmsbox)
) CHARACTER SET utf8;
