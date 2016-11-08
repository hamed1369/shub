<?php

/**
 * Copyright (c) 2016, Yaser Alimardany
 * All rights reserved.
 */

OW::getRouter()->addRoute(new OW_Route('iisuserlogin.admin', 'iisuserlogin/admin', 'IISUSERLOGIN_CTRL_Admin', 'index'));
OW::getRouter()->addRoute(new OW_Route('iisuserlogin.index', 'iisuserlogin/index', 'IISUSERLOGIN_CTRL_Iisuserlogin', 'index'));

IISUSERLOGIN_CLASS_EventHandler::getInstance()->init();