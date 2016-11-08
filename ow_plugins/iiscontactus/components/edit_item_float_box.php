<?php

class IISTERMS_CMP_EditItemFloatBox extends OW_Component
{
    public function __construct($id)
    {
        parent::__construct();
        $form = IISTERMS_BOL_Service::getInstance()->getItemForm($id,null);

        $this->addForm($form);
    }
}
