<?php

class IISRULES_CTRL_Rules extends IISTERMS_CLASS_ActionController
{

    public function index($params)
    {
        OW::getNavigation()->activateMenuItem(OW_Navigation::MAIN, 'iisrules', 'bottom_menu_item');
        $service = IISRULES_BOL_Service::getInstance();
        $sectionId = $service->getGuideLineSectionName();
        if (isset($params['sectionId'])) {
            $sectionId = $params['sectionId'];
        }
        $this->assign('sectionId', $sectionId);
        $this->addComponent('sections', $service->getSections($sectionId, false));

        if($sectionId != $service->getGuideLineSectionName()) {

            $allItems = $service->getAllItems($sectionId);
            $items = array();
            $categories = array();
            $categoryMarked = array();
            $tags = array();
            $count = 0;
            foreach ($allItems as $item) {
                $count++;
                $category = $service->getCategory($item->categoryId);
                $itemInformation = array(
                    'name' => $item->name,
                    'categoryId' => $item->categoryId,
                    'categoryName' => $category->name,
                    'tag' => $item->tag,
                    'description' => $this->parsRuleDescription($item->description),
                    'numberingLabel' => OW::getLanguage()->text('iisrules', 'numberingLabel', array('value' => $count))
                );
                if ($service->isCountryRuleSection($sectionId)) {
                    $itemInformation['numberingLabel'] = OW::getLanguage()->text('iisrules', 'numberingRuleLabel', array('value' => $count));
                }
                $categoryInformation = array(
                    'name' => $category->name,
                    'id' => $category->id
                );
                if (!empty($item->icon)) {
                    $itemInformation['icon'] = $service->getIconUrl($item->icon);
                }

                if (!empty($category->icon)) {
                    $itemInformation['categoryIcon'] = $service->getIconUrl($category->icon);
                    $categoryInformation['icon'] = $service->getIconUrl($category->icon);
                }

                $explodedTags = explode('.', $item->tag);
                foreach ($explodedTags as $explodedTag) {
                    if (!empty($explodedTag) && !in_array($explodedTag, $tags)) {
                        $tags[] = $explodedTag;
                    }
                }
                $items[] = $itemInformation;
                if (!in_array($category->id, $categoryMarked)) {
                    $categoryMarked[] = $category->id;
                    $categories[] = $categoryInformation;
                }
            }

            $this->assign('itemFloatCss', BOL_LanguageService::getInstance()->getCurrent()->getRtl() ? 'float: right;margin-left: 10px;' : 'float: left;margin-right: 10px;');
            $this->assign('sectionsHeader', $service->getSectionsHeader($sectionId));
            $this->assign('tags', $tags);
            $this->assign('items', $items);
            $this->assign('categories', $categories);

            OW::getDocument()->addScript(OW::getPluginManager()->getPlugin('iisrules')->getStaticJsUrl() . 'iisrules.js');
        }else{
            $iisrules_guidline = OW::getConfig()->getValue('iisrules', 'iisrules_guidline');
            if($iisrules_guidline==null){
                $iisrules_guidline = '';
            }
            $this->assign('iisrules_guidline', $iisrules_guidline);
        }
    }

    public function parsRuleDescription($description){
        $description = str_replace('*','<img style="width: 16px;" src="'.IISRULES_BOL_Service::getInstance()->getIconUrl('star').'" />',$description);
        return $description;
    }
}