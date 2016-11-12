<?php

/**
 * Copyright (c) 2016, Yaser Alimardany
 * All rights reserved.
 */

$path = OW::getPluginManager()->getPlugin('iispasswordstrengthmeter')->getRootDir() . 'langs.zip';
BOL_LanguageService::getInstance()->importPrefixFromZip($path, 'iispasswordstrengthmeter');

$config = OW::getConfig();

if ( !$config->configExists('iispasswordstrengthmeter', 'minimumCharacter') )
{
    $config->addConfig('iispasswordstrengthmeter', 'minimumCharacter', 8);
}
if ( !$config->configExists('iispasswordstrengthmeter', 'minimumRequirementPasswordStrength') )
{
    $config->addConfig('iispasswordstrengthmeter', 'minimumRequirementPasswordStrength', 3);
}

OW::getPluginManager()->addPluginSettingsRouteName('iispasswordstrengthmeter', 'iispasswordstrengthmeter.admin');