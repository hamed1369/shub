<?php

/**
 * iisvideoplus
 */
$service = IISVIDEOPLUS_BOL_Service::getInstance();
OW::getEventManager()->bind(IISEventManager::ON_AFTER_VIDEO_RENDERED, array($service, 'onAfterVideoRendered'));

IISVIDEOPLUS_CLASS_EventHandler::getInstance()->init();
