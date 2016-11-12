<?php

OW::getRouter()->addRoute(new OW_Route('iisadminnotification-admin', 'admin/iisadminnotification/settings', "IISADMINNOTIFICATION_CTRL_Admin", 'settings'));

IISADMINNOTIFICATION_CLASS_EventHandler::getInstance()->init();