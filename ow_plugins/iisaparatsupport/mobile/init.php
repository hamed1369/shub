<?php

$plugin = OW::getPluginManager()->getPlugin('iisaparatsupport');
OW::getAutoloader()->addClass('VideoProviderAparat', $plugin->getRootDir() . 'classes' . DS . 'video_provider.php');
IISAPARATSUPPORT_MCLASS_EventHandler::getInstance()->init();

