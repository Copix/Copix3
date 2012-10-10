DROP TABLE IF EXISTS menu_2;

CREATE TABLE menu_2 (
  id_menu INTEGER UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  father_menu INTEGER UNSIGNED,
  order_menu INTEGER UNSIGNED NOT NULL,
  caption_menu VARCHAR(45),
  tooltip_menu VARCHAR(255),
  picture_menu VARCHAR(255),
  typelink_menu VARCHAR(20) NOT NULL,
  url_menu VARCHAR(255),
  id_cmsp BIGINT UNSIGNED,
  isonline_menu TINYINT UNSIGNED NOT NULL DEFAULT 0,
  popup_menu TINYINT UNSIGNED NOT NULL DEFAULT 0,
  width_menu SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  height_menu SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  var_name_menu varchar(50),
  id_head INTEGER NULL,
  tpl_menu varchar(255)
)CHARACTER SET utf8 COLLATE utf8_unicode_ci;

INSERT INTO copixcapability (name_ccpb, description_ccpb, name_ccpt, values_ccpb) VALUES ('menu_2', 'Menu', 'modules|menu_2', '0;20;70');
INSERT INTO copixcapabilitypath VALUES ('modules|menu_2', 'Menu');
