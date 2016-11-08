<?php

IISUSERLOGIN_MCLASS_EventHandler::getInstance()->init();
OW::getRouter()->addRoute(new OW_Route('iisuserlogin.index', 'iisuserlogin/index', 'IISUSERLOGIN_MCTRL_Iisuserlogin', 'index'));