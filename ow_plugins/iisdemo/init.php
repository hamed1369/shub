<?php

/**
 * IIS Demo
 */

IISDEMO_CLASS_EventHandler::getInstance()->init();
OW::getRouter()->addRoute(new OW_Route('iisdemo.change-theme', 'change_theme', 'IISDEMO_CTRL_Demo', 'changeTheme'));
OW::getRouter()->addRoute(new OW_Route('update_static_files', 'update-static-files', 'IISDEMO_CTRL_Demo', 'updateStaticFiles'));