<?php

class IISRULES_CMP_AddItemFloatBox extends OW_Component
{
    public function __construct($sectionId)
    {
        parent::__construct();
        $form = IISRULES_BOL_Service::getInstance()->getItemForm($sectionId, OW::getRouter()->urlForRoute('iisrules.admin.add-item', array('sectionId' => $sectionId)));
        $this->addForm($form);
    }
}
