<?php

/**
 * iismutual
 */
/**
 * @author Yaser Alimardany <yaser.alimardany@gmail.com>
 * @package ow_plugins.iismutual
 * @since 1.0
 */

$widget = BOL_ComponentAdminService::getInstance()->addWidget('IISMUTUAL_CMP_UserIisMutualWidget', false);
$placeWidget = BOL_ComponentAdminService::getInstance()->addWidgetToPlace($widget, BOL_ComponentAdminService::PLACE_PROFILE);
BOL_ComponentAdminService::getInstance()->addWidgetToPosition($placeWidget, BOL_ComponentAdminService::SECTION_LEFT, 0 );
