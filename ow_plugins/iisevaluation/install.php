<?php

/**
 * iisevaluation
 */
/**
 * @author Yaser Alimardany <yaser.alimardany@gmail.com>
 * @package ow_plugins.iisevaluation
 * @since 1.0
 */

$path = OW::getPluginManager()->getPlugin('iisevaluation')->getRootDir() . 'langs.zip';
BOL_LanguageService::getInstance()->importPrefixFromZip($path, 'iisevaluation');

OW::getDbo()->query('CREATE TABLE IF NOT EXISTS `' . OW_DB_PREFIX . 'iisevaluation_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` TEXT NOT NULL,
  `description` TEXT,
  `icon` varchar(40),
  `order` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;');

OW::getDbo()->query('CREATE TABLE IF NOT EXISTS `' . OW_DB_PREFIX . 'iisevaluation_question` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` TEXT NOT NULL,
  `description` TEXT NOT NULL,
  `hasDescribe` int(1),
  `hasFile` int(1),
  `weight` int(11) NOT NULL,
  `level` varchar(100) NOT NULL,
  `hasVerification` int(1),
  `categoryId` int(11) NOT NULL,
  `order` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;');

OW::getDbo()->query('CREATE TABLE IF NOT EXISTS `' . OW_DB_PREFIX . 'iisevaluation_value` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` TEXT NOT NULL,
  `value` int(11) NOT NULL,
  `questionId` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;');

OW::getDbo()->query('CREATE TABLE IF NOT EXISTS `' . OW_DB_PREFIX . 'iisevaluation_answer` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sign` TEXT,
  `questionId` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `description` TEXT,
  `file` varchar(40),
  `valueId` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;');

OW::getDbo()->query('CREATE TABLE IF NOT EXISTS `' . OW_DB_PREFIX . 'iisevaluation_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `username` varchar(32) NOT NULL,
  `lock` int(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;');

OW::getPluginManager()->addPluginSettingsRouteName('iisevaluation', 'iisevaluation.admin');