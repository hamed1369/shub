<?php

OW::getRouter()->addRoute(new OW_Route('iisterms.index', 'iisterms', 'IISTERMS_MCTRL_Terms', 'index'));
OW::getRouter()->addRoute(new OW_Route('iisterms.index.section-id', 'iisterms/:sectionId', 'IISTERMS_MCTRL_Terms', 'index'));
IISTERMS_MCLASS_EventHandler::getInstance()->genericInit();