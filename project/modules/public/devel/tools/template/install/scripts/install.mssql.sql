CREATE TABLE dbo.copixtemplate_theme (
  id_ctpt int NOT NULL IDENTITY (1, 1),
  caption_ctpt varchar(255) NOT NULL default '',
  PRIMARY KEY  (id_ctpt)
);

CREATE TABLE dbo.copixtemplate (
  id_ctpl int NOT NULL IDENTITY (1, 1),
  publicid_ctpl int default NULL,
  qualifier_ctpl varchar(255) NULL default '',
  modulequalifier_ctpl varchar(255) NOT NULL default '',
  caption_ctpl varchar(255) NOT NULL default '',
  content_ctpl text NOT NULL,
  id_ctpt int NULL default '0',
	generated_ctpl text NULL,
  PRIMARY KEY  (id_ctpl)
);

insert into copixtemplate_theme (caption_ctpt) values ('default');