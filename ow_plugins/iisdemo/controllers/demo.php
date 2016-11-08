<?php

class IISDEMO_CTRL_Demo extends OW_ActionController
{

    public function changeTheme($params)
    {
        if(isset($_POST['themeValue'])){
            $themeValue = $_POST['themeValue'];
            if (OW::getThemeManager()->getThemeService()->themeExists($themeValue)) {
                OW::getThemeManager()->getThemeService()->updateThemeList();
                OW::getConfig()->saveConfig('base', 'selectedTheme', $themeValue);
            }
        }
        exit(true);
    }

    public function updateStaticFiles(){
        if(OW::getUser()->isAuthenticated() && OW::getUser()->isAdmin()){
            IISSecurityProvider::updateStaticFiles();
            OW::getFeedback()->info('Static files updated successfully');
        }
        $this->redirect(OW_URL_HOME);
    }
}