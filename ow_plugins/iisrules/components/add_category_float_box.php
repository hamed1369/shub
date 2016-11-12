<?php

class IISRULES_CMP_AddCategoryFloatBox extends OW_Component
{
    public function __construct($sectionId)
    {
        parent::__construct();
        $form = IISRULES_BOL_Service::getInstance()->getCategoryForm(OW::getRouter()->urlForRoute('iisrules.admin.add-category', array('sectionId' => $sectionId)), $sectionId);
        $this->addForm($form);
    }
}
