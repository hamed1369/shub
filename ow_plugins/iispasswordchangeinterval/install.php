<?php

/**
 * Copyright (c) 2016, Yaser Alimardany
 * All rights reserved.
 */

OW::getDbo()->query('CREATE TABLE IF NOT EXISTS `' . OW_DB_PREFIX . 'iispasswordchangeinterval_password_validation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(32) NOT NULL,
  `valid` int(1) NOT NULL,
  `token` VARCHAR(128),
  `email` VARCHAR(128) NOT NULL,
  `tokentime` int(11) NOT NULL,
  `passwordtime` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;');


$path = OW::getPluginManager()->getPlugin('iispasswordchangeinterval')->getRootDir() . 'langs.zip';
BOL_LanguageService::getInstance()->importPrefixFromZip($path, 'iispasswordchangeinterval');

$config = OW::getConfig();

if ( !$config->configExists('iispasswordchangeinterval', 'expire_time') )
{
    $config->addConfig('iispasswordchangeinterval', 'expire_time', 90);
}
if ( !$config->configExists('iispasswordchangeinterval', 'dealWithExpiredPassword') )
{
    $config->addConfig('iispasswordchangeinterval', 'dealWithExpiredPassword', 'normal');
}

OW::getPluginManager()->addPluginSettingsRouteName('iispasswordchangeinterval', 'iispasswordchangeinterval.admin');
