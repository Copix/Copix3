CREATE TABLE `menus` (
  `id_menu` int(10) unsigned NOT NULL auto_increment,
  `name_menu` varchar(50) NOT NULL,
  PRIMARY KEY  (`id_menu`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `menusitems` (
  `id_item` int(10) unsigned NOT NULL auto_increment,
  `id_parent_item` int(10) unsigned NOT NULL default 0,
  `id_menu` int(10) NOT NULL,
  `name_item` varchar(50) NOT NULL,
  `link_item` varchar(255) NOT NULL,
  `order_item` mediumint(8) NOT NULL,
  PRIMARY KEY  (`id_item`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
