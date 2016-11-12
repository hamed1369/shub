<?php

/**
 * IIS Reveal
 */
OW::getRouter()->addRoute(new OW_Route('iisreveal.reload', 'reveal/', 'IISREVEAL_CTRL_Reveal', 'index'));
IISREVEAL_CLASS_EventHandler::getInstance()->init();