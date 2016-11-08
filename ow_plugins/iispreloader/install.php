<?php

/**
 * IIS Demo
 */
/**
 * @author Milad Heshmati <milad.heshmati@gmail.com>
 * @package ow_plugins.iispreloader
 * @since 1.0
 */


$path = OW::getPluginManager()->getPlugin('iispreloader')->getRootDir() . 'langs.zip';
BOL_LanguageService::getInstance()->importPrefixFromZip($path, 'iispreloader');

OW::getPluginManager()->addPluginSettingsRouteName('iispreloader', 'iispreloader-admin');
if (!OW::getConfig()->configExists('iispreloader', 'iispreloadertype')){
    OW::getConfig()->addConfig('iispreloader', 'iispreloadertype', 1);
}