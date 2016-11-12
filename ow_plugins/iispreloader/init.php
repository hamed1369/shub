<?php

/**
 * IIS Preloader
 */
OW::getRouter()->addRoute(new OW_Route('iispreloader-admin', 'admin/iispreloader/settings', "IISPRELOADER_CTRL_Admin", 'settings'));

IISPRELOADER_CLASS_EventHandler::getInstance()->init();
