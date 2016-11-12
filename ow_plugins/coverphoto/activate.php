<?php

/**
 * Cover Photo
 */
/**
 * @author Yaser Alimardany <yaser.alimardany@gmail.com>
 * @package ow_plugins.coverphoto
 * @since 1.0
 */

OW::getNavigation()->addMenuItem(OW_Navigation::MAIN, 'coverphoto-index', 'coverphoto', 'main_menu_item', OW_Navigation::VISIBLE_FOR_MEMBER);
$widget = BOL_ComponentAdminService::getInstance()->addWidget('COVERPHOTO_CMP_UserCoverPhotoWidget', false);
$placeWidget = BOL_ComponentAdminService::getInstance()->addWidgetToPlace($widget, BOL_ComponentAdminService::PLACE_PROFILE);
BOL_ComponentAdminService::getInstance()->addWidgetToPosition($placeWidget, BOL_ComponentAdminService::SECTION_TOP, 0 );
