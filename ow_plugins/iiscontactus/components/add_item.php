<?php

class IISCONTACTUS_CMP_AddItem extends OW_Component
{
    public function __construct($sectionId)
    {
        parent::__construct();
        $form = IISCONTACTUS_BOL_Service::getInstance()->getItemForm(null,$sectionId);

        $this->addForm($form);
    }
}
