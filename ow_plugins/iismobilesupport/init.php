<?php
OW::getRouter()->addRoute(new OW_Route('iismobilesupport-admin', 'admin/iismobilesupport/settings', "IISMOBILESUPPORT_CTRL_Admin", 'settings'));
OW::getRouter()->addRoute(new OW_Route('iismobilesupport-index', 'mobile/service/:key', "IISMOBILESUPPORT_MCTRL_Service", 'index'));
IISMOBILESUPPORT_CLASS_EventHandler::getInstance()->init();