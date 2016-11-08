<?php

class IISRULES_CMP_EditCategoryFloatBox extends OW_Component
{
    public function __construct($id)
    {
        parent::__construct();
        $category = IISRULES_BOL_Service::getInstance()->getCategory($id);
        $form = IISRULES_BOL_Service::getInstance()->getCategoryForm(OW::getRouter()->urlForRoute('iisrules.admin.edit-category', array('id' => $id)), $category->sectionId, $category->name, $category->icon);
        $this->addForm($form);
    }
}