<?php

OW::getRouter()->addRoute(new OW_Route('iisadvancedscroll-admin', 'admin/iisadvancedscroll/settings', "IISADVANCEDSCROLL_CTRL_Admin", 'settings'));

IISADVANCEDSCROLL_CLASS_EventHandler::getInstance()->init();