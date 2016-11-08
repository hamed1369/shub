<?php

/**
 * User: Hamed Tahmooresi
 * Date: 12/23/2015
 * Time: 11:00 AM
 */
OW::getRouter()->addRoute(new OW_Route('iissecurityessentials.admin', 'iissecurityessentials/admin', 'IISSECURITYESSENTIALS_CTRL_Admin', 'index'));
OW::getRouter()->addRoute(new OW_Route('iissecurityessentials.admin.currentSection', 'iissecurityessentials/admin/:currentSection', 'IISSECURITYESSENTIALS_CTRL_Admin', 'index'));
OW::getRouter()->addRoute(new OW_Route('iissecurityessentials.edit_privacy', 'iissecurityessentials/edit-privacy', 'IISSECURITYESSENTIALS_CTRL_Iissecurityessentials', 'editPrivacy'));
IISSECURITYESSENTIALS_CLASS_EventHandler::getInstance()->init();