<?php

/**
 * Copyright (c) 2016, Yaser Alimardany
 * All rights reserved.
 */

OW::getDbo()->query('CREATE TABLE IF NOT EXISTS `' . OW_DB_PREFIX . 'iiscontrolkids_kids_relationship` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kidUserId` int(11) NOT NULL,
  `parentUserId` int(11),
  `parentEmail` varchar(100) NOT NULL,
  `time` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;');


$path = OW::getPluginManager()->getPlugin('iiscontrolkids')->getRootDir() . 'langs.zip';
BOL_LanguageService::getInstance()->importPrefixFromZip($path, 'iiscontrolkids');

$config = OW::getConfig();

if ( !$config->configExists('iiscontrolkids', 'kidsAge') )
{
    $config->addConfig('iiscontrolkids', 'kidsAge', 13);
}
if ( !$config->configExists('iiscontrolkids', 'marginTime') )
{
    $config->addConfig('iiscontrolkids', 'marginTime', 1);
}

OW::getPluginManager()->addPluginSettingsRouteName('iiscontrolkids', 'iiscontrolkids.admin');