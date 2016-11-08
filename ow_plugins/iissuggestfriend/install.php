<?php

/**
 * iissuggestfriend
 */
/**
 * @author Yaser Alimardany <yaser.alimardany@gmail.com>
 * @package ow_plugins.iissuggestfriend
 * @since 1.0
 */

$path = OW::getPluginManager()->getPlugin('iissuggestfriend')->getRootDir() . 'langs.zip';
BOL_LanguageService::getInstance()->importPrefixFromZip($path, 'iissuggestfriend');