<?php

/**
 * IIS Terms
 */
/**
 * @author Yaser Alimardany <yaser.alimardany@gmail.com>
 * @package ow_plugins.iisterms
 * @since 1.0
 */

OW::getNavigation()->addMenuItem(OW_Navigation::BOTTOM, 'iisterms.index', 'iisterms', 'bottom_menu_item', OW_Navigation::VISIBLE_FOR_ALL);
OW::getNavigation()->addMenuItem(OW_Navigation::MOBILE_BOTTOM, 'iisterms.index', 'iisterms', 'mobile_bottom_menu_item', OW_Navigation::VISIBLE_FOR_ALL);