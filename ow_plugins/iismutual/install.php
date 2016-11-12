<?php

/**
 * iismutual
 */
/**
 * @author Yaser Alimardany <yaser.alimardany@gmail.com>
 * @package ow_plugins.iismutual
 * @since 1.0
 */

$path = OW::getPluginManager()->getPlugin('iismutual')->getRootDir() . 'langs.zip';
BOL_LanguageService::getInstance()->importPrefixFromZip($path, 'iismutual');

$config = OW::getConfig();

if ( !$config->configExists('iismutual', 'numberOfMutualFriends') )
{
    $config->addConfig('iismutual', 'numberOfMutualFriends', 6);
}

OW::getPluginManager()->addPluginSettingsRouteName('iismutual', 'iismutual.admin');