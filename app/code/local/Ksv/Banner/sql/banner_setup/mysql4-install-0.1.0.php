<?php

$installer = $this;

$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('banner')};
CREATE TABLE {$this->getTable('banner')} (
 	`banner_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
	`title` VARCHAR(255) NOT NULL DEFAULT '',
	`content` TEXT NOT NULL,
	`stores` TEXT NULL,
	`status` SMALLINT(6) NOT NULL DEFAULT '0',
	`created_time` DATETIME NULL DEFAULT NULL,
	`update_time` DATETIME NULL DEFAULT NULL,
	`height` INT(50) NULL DEFAULT '300',
	`width` INT(50) NULL DEFAULT '685',
	`page_id` TEXT NULL,
	`category_id` TEXT NULL,
	`position` VARCHAR(128) NULL DEFAULT '',
	`advanced_settings` TEXT NULL DEFAULT '',
	PRIMARY KEY (`banner_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    ");

$installer->endSetup(); 
