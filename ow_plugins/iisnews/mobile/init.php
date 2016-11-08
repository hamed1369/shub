<?php

$plugin = OW::getPluginManager()->getPlugin('iisnews');

OW::getAutoloader()->addClass('Entry', $plugin->getBolDir() . 'dto' . DS . 'entry.php');
OW::getAutoloader()->addClass('EntryDao', $plugin->getBolDir() . 'dao' . DS . 'entry_dao.php');
OW::getAutoloader()->addClass('EntryService', $plugin->getBolDir() . 'service' . DS . 'entry_service.php');
OW::getRouter()->addRoute(new OW_Route('event.user_list', 'event/:eventId/users/:list', 'EVENT_CTRL_Base', 'eventUserLists'));
OW::getRouter()->addRoute(new OW_Route('iisnews-default', 'news', 'IISNEWS_MCTRL_News', 'index'));
OW::getRouter()->addRoute(new OW_Route('user-entry', 'news/:id', "IISNEWS_MCTRL_View", 'index'));
OW::getRouter()->addRoute(new OW_Route('entry', 'news/:id', "IISNEWS_MCTRL_View", 'index'));
OW::getRouter()->addRoute(new OW_Route('entry-save-new', 'news/entry/new', "IISNEWS_CTRL_Save", 'index'));
$eventHandler = IISNEWS_CLASS_EventHandler::getInstance();
$eventHandler->genericInit();

$mobileEventHandler = IISNEWS_MCLASS_EventHandler::getInstance();
$mobileEventHandler->init();

IISNEWS_CLASS_ContentProvider::getInstance()->init();
