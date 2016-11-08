<?php

/**
 * IIS Terms
 */
/**
 * @author Yaser Alimardany <yaser.alimardany@gmail.com>
 * @package ow_plugins.iisterms
 * @since 1.0
 */

$path = OW::getPluginManager()->getPlugin('iisterms')->getRootDir() . 'langs.zip';
BOL_LanguageService::getInstance()->importPrefixFromZip($path, 'iisterms');

OW::getDbo()->query("
DROP TABLE IF EXISTS `" . OW_DB_PREFIX . "iisterms_items`;"."
CREATE TABLE IF NOT EXISTS `" . OW_DB_PREFIX . "iisterms_items` (
  `id` int(11) NOT NULL auto_increment,
  `langId` int(11) NOT NULL,
  `use` int(1),
  `notification` int(1),
  `email` int(1),
  `order` int(11) NOT NULL,
  `sectionId` int (11) NOT NULL,
  `description` text NOT NULL,
  `header` varchar(250),
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;");

OW::getDbo()->query("
DROP TABLE IF EXISTS `" . OW_DB_PREFIX . "iisterms_item_version`;"."
CREATE TABLE IF NOT EXISTS `" . OW_DB_PREFIX . "iisterms_item_version` (
  `id` int(11) NOT NULL auto_increment,
  `langId` int(11) NOT NULL,
  `order` int(11) NOT NULL,
  `sectionId` int (11) NOT NULL,
  `description` text NOT NULL,
  `header` varchar(250),
  `time` int (11) NOT NULL,
  `version` int (11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;");

OW::getPluginManager()->addPluginSettingsRouteName('iisterms', 'iisterms.admin');

$config = OW::getConfig();
if ( !$config->configExists('iisterms', 'importDefaultItem') )
{
    $config->addConfig('iisterms', 'importDefaultItem', false);
}
if ( !$config->configExists('iisterms', 'showOnRegistrationForm') )
{
    $config->addConfig('iisterms', 'showOnRegistrationForm', false);
}
if ( !$config->configExists('iisterms', 'terms1') )
{
    $config->addConfig('iisterms', 'terms1', true);
}
if ( !$config->configExists('iisterms', 'terms2') )
{
    $config->addConfig('iisterms', 'terms2', true);
}
if ( !$config->configExists('iisterms', 'terms3') )
{
    $config->addConfig('iisterms', 'terms3', false);
}
if ( !$config->configExists('iisterms', 'terms4') )
{
    $config->addConfig('iisterms', 'terms4', false);
}
if ( !$config->configExists('iisterms', 'terms5') )
{
    $config->addConfig('iisterms', 'terms5', false);
}