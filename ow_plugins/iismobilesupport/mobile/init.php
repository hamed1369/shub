<?php
OW::getRouter()->addRoute(new OW_Route('iismobilesupport-index', 'mobile/service/:key', "IISMOBILESUPPORT_MCTRL_Service", 'index'));
IISMOBILESUPPORT_MCLASS_EventHandler::getInstance()->init();