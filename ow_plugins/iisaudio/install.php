<?php

/**
 * @author Milad Heshmati <milad.heshmati@gmail.com>
 * @package ow_plugins.iisaudio
 * @since 1.0
 */

$path = OW::getPluginManager()->getPlugin('iisaudio')->getRootDir() . 'langs.zip';
BOL_LanguageService::getInstance()->importPrefixFromZip($path, 'iisaudio');

OW::getDbo()->query("
DROP TABLE IF EXISTS `" . OW_DB_PREFIX . "iis_audio`;"."
CREATE TABLE IF NOT EXISTS `" . OW_DB_PREFIX . "iis_audio` (
  `id` int(11) NOT NULL auto_increment,
  `userId` int(11) NOT NULL,
  `title` varchar(32) NOT NULL,
  `hash` varchar(64) NOT NULL,
  `addDateTime` int(10) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;");