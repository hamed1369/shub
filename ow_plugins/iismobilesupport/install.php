<?php

$path = OW::getPluginManager()->getPlugin('iismobilesupport')->getRootDir() . 'langs.zip';
BOL_LanguageService::getInstance()->importPrefixFromZip($path, 'iismobilesupport');


OW::getDbo()->query('CREATE TABLE IF NOT EXISTS `' . OW_DB_PREFIX . 'iismobilesupport_device` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `token` longtext NOT NULL,
  `time` int(1),
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;');


OW::getPluginManager()->addPluginSettingsRouteName('iismobilesupport', 'iismobilesupport-admin');

if (!OW::getConfig()->configExists('iismobilesupport', 'fcm_api_key')){
    OW::getConfig()->addConfig('iismobilesupport', 'fcm_api_key', '');
}

if (!OW::getConfig()->configExists('iismobilesupport', 'fcm_api_url')){
    OW::getConfig()->addConfig('iismobilesupport', 'fcm_api_url', 'https://fcm.googleapis.com/fcm/send');
}

if (!OW::getConfig()->configExists('iismobilesupport', 'constraint_user_device')){
    OW::getConfig()->addConfig('iismobilesupport', 'constraint_user_device', '10');
}