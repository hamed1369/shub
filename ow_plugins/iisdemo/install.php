<?php

/**
 * IIS Demo
 */
/**
 * @author Yaser Alimardany <yaser.alimardany@gmail.com>
 * @package ow_plugins.iisdemo
 * @since 1.0
 */

$path = OW::getPluginManager()->getPlugin('iisdemo')->getRootDir() . 'langs.zip';
BOL_LanguageService::getInstance()->importPrefixFromZip($path, 'iisdemo');