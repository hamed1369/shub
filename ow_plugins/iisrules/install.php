<?php

/**
 * IIS Rules
 */
/**
 * @author Yaser Alimardany <yaser.alimardany@gmail.com>
 * @package ow_plugins.iisrules
 * @since 1.0
 */

$path = OW::getPluginManager()->getPlugin('iisrules')->getRootDir() . 'langs.zip';
BOL_LanguageService::getInstance()->importPrefixFromZip($path, 'iisrules');

OW::getDbo()->query("
DROP TABLE IF EXISTS `" . OW_DB_PREFIX . "iisrules_item`;"."
CREATE TABLE IF NOT EXISTS `" . OW_DB_PREFIX . "iisrules_items` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(100) NOT NULL,
  `description` TEXT,
  `icon` varchar(40),
  `tag` varchar(200),
  `order` int(5),
  `categoryId` int(11),
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;");

OW::getDbo()->query("
DROP TABLE IF EXISTS `" . OW_DB_PREFIX . "iisrules_category`;"."
CREATE TABLE IF NOT EXISTS `" . OW_DB_PREFIX . "iisrules_category` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(100) NOT NULL,
  `icon` varchar(40),
  `sectionId` int(5),
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;");

OW::getPluginManager()->addPluginSettingsRouteName('iisrules', 'iisrules.admin');