<?php

OW::getPluginManager()->addPluginSettingsRouteName('iisnews', 'iisnews-admin');

OW::getPluginManager()->addUninstallRouteName('iisnews', 'iisnews-uninstall');

$dbPrefix = OW_DB_PREFIX;

$sql =
    <<<EOT

CREATE TABLE `{$dbPrefix}iisnews_entry` (
  `id` INTEGER(11) NOT NULL AUTO_INCREMENT,
  `authorId` INTEGER(11) NOT NULL,
  `title` VARCHAR(512) COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `entry` TEXT COLLATE utf8_general_ci NOT NULL,
  `timestamp` INTEGER(11) NOT NULL,
  `isDraft` TINYINT(1) NOT NULL,
  `privacy` varchar(50) NOT NULL default 'everybody',
  PRIMARY KEY (`id`),
  KEY `authorId` (`authorId`)
)ENGINE=MyISAM DEFAULT CHARSET=utf8;

EOT;

OW::getDbo()->query($sql);

if ( !OW::getConfig()->configExists('iisnews', 'results_per_page') )
{
    OW::getConfig()->addConfig('iisnews', 'results_per_page', 10, 'Entry number per page');
}

if ( !OW::getConfig()->configExists('iisnews', 'uninstall_inprogress') )
{
    OW::getConfig()->addConfig('iisnews', 'uninstall_inprogress', 0, '');
}

if ( !OW::getConfig()->configExists('iisnews', 'uninstall_cron_busy') )
{
    OW::getConfig()->addConfig('iisnews', 'uninstall_cron_busy', 0, '');
}

$authorization = OW::getAuthorization();
$groupName = 'iisnews';
$authorization->addGroup($groupName);
$authorization->addAction($groupName, 'add_comment');
$authorization->addAction($groupName, 'add');
$authorization->addAction($groupName, 'view', true);

OW::getLanguage()->importPluginLangs(OW::getPluginManager()->getPlugin('iisnews')->getRootDir() . 'langs.zip', 'iisnews');

OW::getDbo()->query("
DELETE FROM `" . OW_DB_PREFIX . "base_authorization_permission` WHERE `actionId`=
 (SELECT `id` FROM `" . OW_DB_PREFIX . "base_authorization_action` WHERE `groupid` =
    (SELECT `id` FROM `" . OW_DB_PREFIX . "base_authorization_group` WHERE `name` = 'iisnews') AND `name` ='add');");