<?php

class IISCONTACTUS_CLASS_LabelValidator extends OW_Validator
{
    public function isValid( $label )
    {
        if ( $label === null )
        {
            return false;
        }

        $user = IISCONTACTUS_BOL_DepartmentDao::getInstance()->findIsExistLabel($label);

        if ( !isset($user) )
        {
            return true;
        }
        return false;
    }
}
