<?php

$path = OW::getPluginManager()->getPlugin('iisadminnotification')->getRootDir() . 'langs.zip';
BOL_LanguageService::getInstance()->importPrefixFromZip($path, 'iisadminnotification');

OW::getPluginManager()->addPluginSettingsRouteName('iisadminnotification', 'iisadminnotification-admin');

if (!OW::getConfig()->configExists('iisadminnotification', 'emailSendTo')){
    OW::getConfig()->addConfig('iisadminnotification', 'emailSendTo', '');
}

if (!OW::getConfig()->configExists('iisadminnotification', 'registerNotification')){
    OW::getConfig()->addConfig('iisadminnotification', 'registerNotification', true);
}

if (!OW::getConfig()->configExists('iisadminnotification', 'topicForumNotification')){
    OW::getConfig()->addConfig('iisadminnotification', 'topicForumNotification', true);
}

if (!OW::getConfig()->configExists('iisadminnotification', 'newsCommentNotification')) {
    OW::getConfig()->addConfig('iisadminnotification', 'newsCommentNotification', true);
}