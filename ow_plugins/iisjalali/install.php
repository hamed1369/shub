<?php

OW::getLanguage()->importPluginLangs(OW::getPluginManager()->getPlugin('iisjalali')->getRootDir().'langs.zip','iisjalali');

$config = OW::getConfig();
if ( !$config->configExists('iisjalali', 'dateLocale') )
{
    $config->addConfig('iisjalali', 'dateLocale',1);
}
OW::getPluginManager()->addPluginSettingsRouteName('iisjalali','iisjalali_admin_config');