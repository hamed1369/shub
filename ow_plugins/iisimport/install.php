<?php

/**
 * Copyright (c) 2016, Yaser Alimardany
 * All rights reserved.
 */

OW::getDbo()->query('CREATE TABLE IF NOT EXISTS `' . OW_DB_PREFIX . 'iisimport_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` varchar(40) NOT NULL,
  `email` varchar(100) NOT NULL,
  `type` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;');

OW::getDbo()->query('CREATE TABLE IF NOT EXISTS `' . OW_DB_PREFIX . 'iisimport_users_try` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` varchar(40) NOT NULL,
  `time` int(11) NOT NULL,
  `type` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;');


$path = OW::getPluginManager()->getPlugin('iisimport')->getRootDir() . 'langs.zip';
BOL_LanguageService::getInstance()->importPrefixFromZip($path, 'iisimport');

$config = OW::getConfig();

if ( !$config->configExists('iisimport', 'use_import_yahoo') )
{
    $config->addConfig('iisimport', 'use_import_yahoo', false);
}

if ( !$config->configExists('iisimport', 'yahoo_id') )
{
    $config->addConfig('iisimport', 'yahoo_id', '');
}

if ( !$config->configExists('iisimport', 'yahoo_secret') )
{
    $config->addConfig('iisimport', 'yahoo_secret', '');
};

if ( !$config->configExists('iisimport', 'use_import_google') )
{
    $config->addConfig('iisimport', 'use_import_google', false);
}

if ( !$config->configExists('iisimport', 'google_id') )
{
    $config->addConfig('iisimport', 'google_id', '');
}

if ( !$config->configExists('iisimport', 'google_secret') )
{
    $config->addConfig('iisimport', 'google_secret', '');
};

OW::getPluginManager()->addPluginSettingsRouteName('iisimport', 'iisimport.admin');
