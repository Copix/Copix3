DROP TABLE IF EXISTS `menu_2` ;
DELETE FROM `copixcapability` WHERE `name_ccpb`='menu_2';
DELETE FROM `copixcapabilitypath` WHERE `name_ccpt` like 'modules|menu_2%';
DELETE FROM `copixgroupcapabilities` WHERE `name_ccpb`='menu_2';
