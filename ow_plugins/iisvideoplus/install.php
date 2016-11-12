<?php

/**
 * iisvideoplus
 */
/**
 * @author Yaser Alimardany <yaser.alimardany@gmail.com>
 * @package ow_plugins.iisvideoplus
 * @since 1.0
 */

$path = OW::getPluginManager()->getPlugin('iisvideoplus')->getRootDir() . 'langs.zip';
BOL_LanguageService::getInstance()->importPrefixFromZip($path, 'iisvideoplus');