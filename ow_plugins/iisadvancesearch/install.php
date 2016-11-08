<?php

/**
 * IIS Advance Search
 */
/**
 * @author Yaser Alimardany <yaser.alimardany@gmail.com>
 * @package ow_plugins.iisadvancesearch
 * @since 1.0
 */

$path = OW::getPluginManager()->getPlugin('iisadvancesearch')->getRootDir() . 'langs.zip';
BOL_LanguageService::getInstance()->importPrefixFromZip($path, 'iisadvancesearch');