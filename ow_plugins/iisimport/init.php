<?php

/**
 * Copyright (c) 2016, Yaser Alimardany
 * All rights reserved.
 */

OW::getRouter()->addRoute(new OW_Route('iisimport.admin', 'iisimport/admin', 'IISIMPORT_CTRL_Admin', 'index'));
OW::getRouter()->addRoute(new OW_Route('iisimport.import.index', 'iisimport', 'IISIMPORT_CTRL_Iisimport', 'index'));
OW::getRouter()->addRoute(new OW_Route('iisimport.import.request', 'iisimport/request/:type', 'IISIMPORT_CTRL_Iisimport', 'request'));
OW::getRouter()->addRoute(new OW_Route('iisimport.import.invitation', 'iisimport/invitation/:type', 'IISIMPORT_CTRL_Iisimport', 'invitation'));
OW::getRouter()->addRoute(new OW_Route('iisimport.yahoo.callback', 'iisimport/yahooc', 'IISIMPORT_CTRL_Iisimport', 'yahooCallBack'));
OW::getRouter()->addRoute(new OW_Route('iisimport.google.callback', 'iisimport/googlec', 'IISIMPORT_CTRL_Iisimport', 'googleCallBack'));

IISIMPORT_CLASS_EventHandler::getInstance()->init();