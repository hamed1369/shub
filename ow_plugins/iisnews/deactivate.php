<?php

OW::getNavigation()->deleteMenuItem('iisnews', 'main_menu_item');
OW::getNavigation()->deleteMenuItem('iisnews', 'iisnews_mobile');
BOL_ComponentAdminService::getInstance()->deleteWidget('IISNEWS_CMP_UserNewsWidget');
BOL_ComponentAdminService::getInstance()->deleteWidget('IISNEWS_CMP_NewsWidget');