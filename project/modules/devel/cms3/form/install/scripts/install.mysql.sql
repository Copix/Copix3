-- -----------------------------------------------------
-- Table `cms_form`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `cms_form` (
  `cf_id` INT NOT NULL AUTO_INCREMENT,
  `public_id_hei` INT NULL ,
  `cf_route` VARCHAR(45) NOT NULL ,
  `cf_route_params` text ,
  `cf_deleted_at` datetime NULL,
  PRIMARY KEY (`cf_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

ALTER TABLE `cms_form` ADD CONSTRAINT FOREIGN KEY (`public_id_hei`) REFERENCES `cms_headingelementinformations` (`public_id_hei`);
ALTER TABLE `cms_form` ADD COLUMN description_hei TEXT DEFAULT NULL;

-- -----------------------------------------------------
-- Table `cms_form_element`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `cms_form_element` (
  `cfe_id` INT NOT NULL AUTO_INCREMENT ,
  `cfe_label` VARCHAR(100) NOT NULL ,
  `cfe_type` VARCHAR(45) NOT NULL ,
  `cfe_aide` VARCHAR(150) NULL,
  `cfe_default` TEXT NULL,
  `cfe_default_data` VARCHAR(50) NULL,
  `cfe_orientation` TINYINT NOT NULL DEFAULT '0',
  `cfe_columns` int(11) default '0',
  `cfe_deleted_at` datetime NULL,
  PRIMARY KEY (`cfe_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


-- -----------------------------------------------------
-- Table `cms_form_values`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `cms_form_values` (
  `cfv_id` INT NOT NULL AUTO_INCREMENT ,
  `cfv_id_form` INT NOT NULL ,
  `cfv_id_element` INT NOT NULL ,
  `cfv_value` text NOT NULL,
  `cfv_date` datetime NOT NULL,
  `cfv_ip_user` VARCHAR(20) NULL,
  PRIMARY KEY (`cfv_id`) ,
  INDEX `id_element` (`cfv_id_element` ASC) ,
  INDEX `id_form` (`cfv_id_form` ASC)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- -----------------------------------------------------
-- Table `cms_form_bloc`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `cms_form_bloc` (
  `cfb_id` INT NOT NULL AUTO_INCREMENT ,
  `cfb_nom` VARCHAR(45) NOT NULL ,
  `cfb_description` VARCHAR(45) NULL ,
  PRIMARY KEY (`cfb_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


-- -----------------------------------------------------
-- Table `cms_form_content`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `cms_form_content` (
  `cfc_id` INT NOT NULL AUTO_INCREMENT ,
  `cfc_id_form` INT NOT NULL ,
  `cfc_id_element` INT NULL ,
  `cfc_id_bloc` INT NULL ,
  `cfc_order` INT NOT NULL ,
  `cfc_orientation` TINYINT NOT NULL DEFAULT '0',
  `cfc_required` TINYINT NOT NULL DEFAULT '0',
  PRIMARY KEY (`cfc_id`) ,
  INDEX `id_form` (`cfc_id_form` ASC) ,
  INDEX `id_element` (`cfc_id_element` ASC) ,
  INDEX `id_bloc` (`cfc_id_bloc` ASC)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


-- -----------------------------------------------------
-- Table `cms_form_bloc_content`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `cms_form_bloc_content` (
  `cfbc_id` INT NOT NULL AUTO_INCREMENT ,
  `cfbc_id_bloc` INT NOT NULL ,
  `cfbc_id_element` INT NOT NULL ,
  `cfbc_order` INT NOT NULL ,
  PRIMARY KEY (`cfbc_id`) ,
  INDEX `id_bloc` (`cfbc_id_bloc` ASC) ,
  INDEX `id_element` (`cfbc_id_element` ASC)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- -----------------------------------------------------
-- Table `cms_form_element_values`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `cms_form_element_values` (
  `cfev_id` INT NOT NULL AUTO_INCREMENT ,
  `cfev_id_element` INT NOT NULL ,
  `cfev_value` VARCHAR(150) NOT NULL ,
  `cfev_id_bloc_to_display` INT NULL ,
  `cfev_deleted_at` datetime NULL,
  `cfev_parent_adopt` int(11) DEFAULT '0',
  PRIMARY KEY (`cfev_id`) ,
  INDEX `id_element` (`cfev_id_element` ASC)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
