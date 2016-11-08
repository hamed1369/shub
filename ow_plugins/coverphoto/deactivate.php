<?php

/**
 * Cover Photo
 */
/**
 * @author Yaser Alimardani <yaser.alimardany@gmail.com>
 * @package ow_plugins.coverphoto
 * @since 1.0
 */

OW::getNavigation()->deleteMenuItem('coverphoto', 'main_menu_item');
BOL_ComponentAdminService::getInstance()->deleteWidget('COVERPHOTO_CMP_UserCoverPhotoWidget');