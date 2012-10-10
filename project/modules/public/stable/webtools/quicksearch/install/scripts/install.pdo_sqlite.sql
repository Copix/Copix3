CREATE TABLE quicksearchindex (
  idobj_srch varchar(255) NOT NULL  PRIMARY KEY,
  title_srch varchar(255) NULL default ''  PRIMARY KEY,
  kind_srch varchar(30) NULL default '',
  keywords_srch varchar(255) NULL default NULL,
  summary_srch text,
  content_srch text,
  url_srch varchar(255) NOT NULL default ''
);
