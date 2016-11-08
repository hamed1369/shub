<?php


//BOL_LanguageService::getInstance()->addPrefix('contactus', 'Contact Us');

$sql = "CREATE TABLE `" . OW_DB_PREFIX . "iiscontactus_department` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`email` VARCHAR(200) NOT NULL,
	`label` VARCHAR(200) NOT NULL,
	 UNIQUE KEY `label` (`label`),
	PRIMARY KEY (`id`)
)
ENGINE=MyISAM CHARSET=utf8 AUTO_INCREMENT=1;";
//installing database
OW::getDbo()->query($sql);

OW::getDbo()->query('CREATE TABLE IF NOT EXISTS `' . OW_DB_PREFIX . 'iiscontactus_user_information` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `subject` VARCHAR(1024) NOT NULL,
  `useremail` VARCHAR(256) NOT NULL,
  `label` VARCHAR(128) NOT NULL,
  `message` VARCHAR(2000) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;');
//installing language pack
OW::getLanguage()->importPluginLangs(OW::getPluginManager()->getPlugin('iiscontactus')->getRootDir().'langs.zip', 'iiscontactus');
//adding admin settings page
OW::getPluginManager()->addPluginSettingsRouteName('iiscontactus', 'iiscontactus.admin');

$config = OW::getConfig();
if($config->configExists('iiscontactus', 'adminComment'))
{
    $config->deleteConfig('iiscontactus', 'adminComment');
}