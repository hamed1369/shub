<?php
OW::getRouter()->addRoute(new OW_Route('iiscontrolkids.index', 'iiscontrolkids/index', 'IISCONTROLKIDS_CTRL_Iiscontrolkids', 'index'));
OW::getRouter()->addRoute(new OW_Route('iiscontrolkids.shadow_login_by_parent', 'iiscontrolkids/shadowLoginByParent/:kidUserId', 'IISCONTROLKIDS_CTRL_Iiscontrolkids', 'shadowLoginByParent'));
OW::getRouter()->addRoute(new OW_Route('iiscontrolkids.logout_from_shadow_login', 'iiscontrolkids/logoutFromShadowLogin', 'IISCONTROLKIDS_CTRL_Iiscontrolkids', 'logoutFromShadowLogin'));
IISCONTROLKIDS_MCLASS_EventHandler::getInstance()->init();