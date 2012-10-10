DROP TABLE IF EXISTS easy_menu;

CREATE TABLE easy_menu (
  id_menu INTEGER UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  father_menu INTEGER UNSIGNED,
  order_menu INTEGER UNSIGNED NOT NULL,
  caption_menu VARCHAR(45),
  tooltip_menu VARCHAR(255),
  picture_menu VARCHAR(255),
  url_menu VARCHAR(255),
  isonline_menu TINYINT UNSIGNED NOT NULL DEFAULT 0
)CHARACTER SET utf8 COLLATE utf8_unicode_ci;
