<?php

/**
 * iismutual
 */
/**
 * @author Yaser Alimardany <yaser.alimardany@gmail.com>
 * @package ow_plugins.iissuggestfriend
 * @since 1.0
 */

$widget = BOL_ComponentAdminService::getInstance()->addWidget('IISSUGGESTFRIEND_CMP_UserIisSuggestFriendWidget', false);
$placeWidget = BOL_ComponentAdminService::getInstance()->addWidgetToPlace($widget, BOL_ComponentAdminService::PLACE_DASHBOARD);
BOL_ComponentAdminService::getInstance()->addWidgetToPosition($placeWidget, BOL_ComponentAdminService::SECTION_RIGHT, 2 );
