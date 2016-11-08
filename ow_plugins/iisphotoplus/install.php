<?php

/**
 * iisvideoplus
 */
/**
 * @author Mohammad Agha Abbasloo <a.mohammad85@gmail.com>
 * @package ow_plugins.iisphotoplus
 * @since 1.0
 */

$path = OW::getPluginManager()->getPlugin('iisphotoplus')->getRootDir() . 'langs.zip';
BOL_LanguageService::getInstance()->importPrefixFromZip($path, 'iisphotooplus');