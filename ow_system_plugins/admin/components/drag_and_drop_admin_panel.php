<?php

/**
 * EXHIBIT A. Common Public Attribution License Version 1.0
 * The contents of this file are subject to the Common Public Attribution License Version 1.0 (the “License”);
 * you may not use this file except in compliance with the License. You may obtain a copy of the License at
 * http://www.oxwall.org/license. The License is based on the Mozilla Public License Version 1.1
 * but Sections 14 and 15 have been added to cover use of software over a computer network and provide for
 * limited attribution for the Original Developer. In addition, Exhibit A has been modified to be consistent
 * with Exhibit B. Software distributed under the License is distributed on an “AS IS” basis,
 * WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License for the specific language
 * governing rights and limitations under the License. The Original Code is Oxwall software.
 * The Initial Developer of the Original Code is Oxwall Foundation (http://www.oxwall.org/foundation).
 * All portions of the code written by Oxwall Foundation are Copyright (c) 2011. All Rights Reserved.

 * EXHIBIT B. Attribution Information
 * Attribution Copyright Notice: Copyright 2011 Oxwall Foundation. All rights reserved.
 * Attribution Phrase (not exceeding 10 words): Powered by Oxwall community software
 * Attribution URL: http://www.oxwall.org/
 * Graphic Image as provided in the Covered Code.
 * Display of Attribution Information is required in Larger Works which are defined in the CPAL as a work
 * which combines Covered Code or portions thereof with code not governed by the terms of the CPAL.
 */

/**
 * Widgets admin panel
 *
 * @author Sergey Kambalin <greyexpert@gmail.com>
 * @package ow_system_plugins.admin.components
 * @since 1.0
 */
class ADMIN_CMP_DragAndDropAdminPanel extends BASE_CMP_DragAndDropPanel
{

    public function __construct( $placeName, array $componentList, $template = 'drag_and_drop_panel' )
    {
        parent::__construct($placeName, $componentList, $template);

        $customizeAllowed = BOL_ComponentAdminService::getInstance()->findPlace($placeName)->editableByUser;
        $event = OW::getEventManager()->trigger(new OW_Event(IISEventManager::BEFORE_CUSTOMIZATION_PAGE_RENDERER, array('this' => $this, 'customizeAllowed' => $customizeAllowed, 'placeName' => $placeName)));
        if(isset($event->getData()['customizeAllowed'])){
            $customizeAllowed = $event->getData()['customizeAllowed'];
        }
        $this->assign('customizeAllowed', $customizeAllowed);

        $this->assign('placeName', $placeName);
    }
    
    public function onBeforeRender()
    {
        parent::onBeforeRender();
        
        $sharedData = array(
            'additionalSettings' => $this->additionalSettingList,
            'place' => $this->placeName
        );
        
        $this->initializeJs('BASE_CTRL_AjaxComponentAdminPanel', 'OW_Components_DragAndDrop', $sharedData);
        
        $jsDragAndDropUrl = OW::getPluginManager()->getPlugin('ADMIN')->getStaticJsUrl() . 'drag_and_drop.js';
        OW::getDocument()->addScript($jsDragAndDropUrl);
    }
}