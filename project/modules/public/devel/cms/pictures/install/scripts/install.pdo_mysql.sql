#
# Structure de la table `pictures`
#

CREATE TABLE pictures (
  id_pict bigint(20) NOT NULL default '0',
  name_pict varchar(50) NOT NULL default '',
  id_head bigint(20) default '0',
  x_pict int(11) NOT NULL default '0',
  y_pict int(11) NOT NULL default '0',
  weight_pict int(11) NOT NULL default '0',
  format_pict varchar(10) NOT NULL default '',
  desc_pict varchar(250) default NULL,
  url_pict varchar(250) default NULL,
  status_pict tinyint(2) NOT NULL default '0',
  statusdate_pict VARCHAR( 8 ) NOT NULL ,
  statusauthor_pict VARCHAR( 50 ) NOT NULL ,
  author_pict VARCHAR( 50 ) NOT NULL ,
  statuscomment_pict VARCHAR(255 ) NULL ,
  last_consultation_pict VARCHAR( 8 ),
  nameindex_pict int(11) NOT NULL default '0',
  PRIMARY KEY  (id_pict)
) CHARACTER SET utf8;

# --------------------------------------------------------

#
# Structure de la table `picturesheadings`
#

CREATE TABLE picturesheadings (
  id_head bigint(20) default '0',
  maxX_cpic int(11) default '0',
  maxY_cpic int(11) default '0',
  maxweight_cpic int(11) default '0',
  format_cpic varchar(50) NOT NULL default ''
) CHARACTER SET utf8;

# --------------------------------------------------------

#
# Structure de la table `pictureslinkthemes`
#

CREATE TABLE pictureslinkthemes (
  id_pict bigint(20) NOT NULL default '0',
  id_tpic int(11) NOT NULL default '0'
) CHARACTER SET utf8;

# --------------------------------------------------------

#
# Structure de la table `picturesthemes`
#

CREATE TABLE picturesthemes (
  id_tpic int(11) NOT NULL auto_increment,
  name_tpic varchar(50) NOT NULL default '',
  PRIMARY KEY  (id_tpic)
) CHARACTER SET utf8 AUTO_INCREMENT=1 ;

INSERT INTO copixcapability (name_ccpb, description_ccpb, name_ccpt, values_ccpb) VALUES ('pictures', 'Photothèque', 'modules|copixheadings', '0;20;30;40;50;60;70');
INSERT INTO picturesheadings (id_head, maxX_cpic, maxY_cpic, maxweight_cpic, format_cpic) VALUES (NULL, 0, 0, 0, 'png;gif;jpg');