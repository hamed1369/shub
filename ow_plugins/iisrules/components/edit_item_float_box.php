<?php

class IISRULES_CMP_EditItemFloatBox extends OW_Component
{
    public function __construct($id)
    {
        parent::__construct();
        $item = IISRULES_BOL_Service::getInstance()->getItem($id);
        $category = IISRULES_BOL_Service::getInstance()->getCategory($item->categoryId);
        $form = IISRULES_BOL_Service::getInstance()->getItemForm($category->sectionId, OW::getRouter()->urlForRoute('iisrules.admin.edit-item', array('id' => $id)), $item->name, $item->description, $item->icon, $item->categoryId, $item->tag);
        $this->addForm($form);
    }
}
