<?php

/**
 * Cover Photo
 */
/**
 * @author Yaser Alimardany <yaser.alimardany@gmail.com>
 * @package ow_plugins.coverphoto
 * @since 1.0
 */

$path = OW::getPluginManager()->getPlugin('coverphoto')->getRootDir() . 'langs.zip';
BOL_LanguageService::getInstance()->importPrefixFromZip($path, 'coverphoto');

OW::getDbo()->query("
DROP TABLE IF EXISTS `" . OW_DB_PREFIX . "cover_photo`;"."
CREATE TABLE IF NOT EXISTS `" . OW_DB_PREFIX . "cover_photo` (
  `id` int(11) NOT NULL auto_increment,
  `userId` int(11) NOT NULL,
  `title` varchar(32) NOT NULL,
  `hash` varchar(64) NOT NULL,
  `croppedHash` varchar(64),
  `addDateTime` int(10) NOT NULL,
  `isCurrent` int(2) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;");
