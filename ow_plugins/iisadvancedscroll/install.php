<?php

$path = OW::getPluginManager()->getPlugin('iisadvancedscroll')->getRootDir() . 'langs.zip';
BOL_LanguageService::getInstance()->importPrefixFromZip($path, 'iisadvancedscroll');

OW::getPluginManager()->addPluginSettingsRouteName('iisadvancedscroll', 'iisadvancedscroll-admin');
if ( !OW::getConfig()->configExists('iisadvancedscroll', 'EaseSpeed') )
    OW::getConfig()->addConfig('iisadvancedscroll', 'EaseSpeed', '400', '');
    
if ( !OW::getConfig()->configExists('iisadvancedscroll', 'Easing') )
    OW::getConfig()->addConfig('iisadvancedscroll', 'Easing', 'linear', '');
    
if ( !OW::getConfig()->configExists('iisadvancedscroll', 'InDelay') )
    OW::getConfig()->addConfig('iisadvancedscroll', 'InDelay', '600', '');
    
if ( !OW::getConfig()->configExists('iisadvancedscroll', 'OutDelay') )
    OW::getConfig()->addConfig('iisadvancedscroll', 'OutDelay', '400', '');
    
if ( !OW::getConfig()->configExists('iisadvancedscroll', 'bottom') )
    OW::getConfig()->addConfig('iisadvancedscroll', 'bottom', '25', '');
    
if ( !OW::getConfig()->configExists('iisadvancedscroll', 'right') )
    OW::getConfig()->addConfig('iisadvancedscroll', 'right', '5', '');
    
if ( !OW::getConfig()->configExists('iisadvancedscroll', 'left') )
    OW::getConfig()->addConfig('iisadvancedscroll', 'left', '0', '');
    
if ( !OW::getConfig()->configExists('iisadvancedscroll', 'adminarea') )
    OW::getConfig()->addConfig('iisadvancedscroll', 'adminarea', 'enable', '');
