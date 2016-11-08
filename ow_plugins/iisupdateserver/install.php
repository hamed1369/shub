<?php

/**
 * Copyright (c) 2016, Yaser Alimardany
 * All rights reserved.
 */
OW::getLanguage()->importPluginLangs(OW::getPluginManager()->getPlugin('iisupdateserver')->getRootDir().'langs.zip','iisupdateserver');

OW::getDbo()->query('CREATE TABLE IF NOT EXISTS `' . OW_DB_PREFIX . 'iisupdateserver_update_information` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `time` int(11) NOT NULL,
  `buildNumber` varchar(100) NOT NULL,
  `key` varchar(100) NOT NULL,
  `version` varchar(100),
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;');

OW::getDbo()->query('CREATE TABLE IF NOT EXISTS `' . OW_DB_PREFIX . 'iisupdateserver_users_information` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `time` int(11) NOT NULL,
  `ip` varchar(60) NOT NULL,
  `key` varchar(100),
  `developerKey` varchar(100),
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;');

OW::getDbo()->query('CREATE TABLE IF NOT EXISTS `' . OW_DB_PREFIX . 'iisupdateserver_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(60) NOT NULL,
  `description` longtext,
  `key` varchar(100) NOT NULL,
  `image` varchar(64) NOT NULL,
  `type` varchar(20) NOT NULL,
  `order` int(11) NOT NULL,
  `guidelineurl` longtext,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;');

OW::getPluginManager()->addPluginSettingsRouteName('iisupdateserver', 'iisupdateserver.admin');

$config = OW::getConfig();
if ( !$config->configExists('iisupdateserver', 'prefix_download_path') )
{
    $config->addConfig('iisupdateserver', 'prefix_download_path', '');
}