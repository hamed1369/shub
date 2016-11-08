<?php

/**
 * @author Yaser Alimardany <yaser.alimardany@gmail.com>
 * @package ow_plugins.iisnews.controllers
 * @since 1.0
 */
class IISNEWS_CTRL_News extends OW_ActionController
{

    public function index($params)
    {
        if ( empty($params['list']) )
        {
            $params['list'] = 'latest';
        }

        $plugin = OW::getPluginManager()->getPlugin('iisnews');
        OW::getNavigation()->activateMenuItem(OW_Navigation::MAIN, 'iisnews', 'main_menu_item');

        $this->setPageHeading(OW::getLanguage()->text('iisnews', 'list_page_heading'));
        $this->setPageHeadingIconClass('ow_ic_write');

        if ( !OW::getUser()->isAdmin() && !OW::getUser()->isAuthorized('iisnews', 'view') )
        {
            $status = BOL_AuthorizationService::getInstance()->getActionStatus('iisnews', 'view');
            throw new AuthorizationException($status['msg']);

            return;
        }
        if ( OW::getUser()->isAuthorized('iisnews', 'add')  || OW::getUser()->isAdmin())
        {
            $this->assign('my_drafts_url', OW::getRouter()->urlForRoute('iisnews-manage-drafts'));
        }

        /*
          @var $service EntryService
         */
        $service = EntryService::getInstance();

        $page = (!empty($_GET['page']) && intval($_GET['page']) > 0 ) ? $_GET['page'] : 1;

        $addNew_promoted = false;
        $addNew_isAuthorized = false;
        if (OW::getUser()->isAuthenticated())
        {
            if (OW::getUser()->isAuthorized('iisnews', 'add') || OW::getUser()->isAdmin())
            {
                $addNew_isAuthorized = true;
            }
            else
            {
                $status = BOL_AuthorizationService::getInstance()->getActionStatus('iisnews', 'add');
                if ($status['status'] == BOL_AuthorizationService::STATUS_PROMOTED)
                {
                    $addNew_promoted = true;
                    $addNew_isAuthorized = true;
                    $script = '$("#btn-add-new-entry").click(function(){
                        OW.authorizationLimitedFloatbox('.json_encode($status['msg']).');
                        return false;
                    });';
                    OW::getDocument()->addOnloadScript($script);
                }
                else
                {
                    $addNew_isAuthorized = false;
                }
            }
        }
        $addNew_isAuthorized = false;
        if(OW::getUser()->isAuthorized('iisnews', 'add') || OW::getUser()->isAdmin()){
            $addNew_isAuthorized = true;
        }
        $this->assign('addNew_isAuthorized', $addNew_isAuthorized);
        $this->assign('addNew_promoted', $addNew_promoted);

        $rpp = (int) OW::getConfig()->getValue('iisnews', 'results_per_page');

        $first = ($page - 1) * $rpp;

        $count = $rpp;

        $case = $params['list'];
        if ( !in_array($case, array( 'latest', 'browse-by-tag', 'most-discussed', 'top-rated' )) )
        {
            throw new Redirect404Exception();
        }
        $showList = true;
        $isBrowseByTagCase = $case == 'browse-by-tag';

        $contentMenu = $this->getContentMenu();
        $contentMenu->getElement($case)->setActive(true);
        $this->addComponent('menu', $contentMenu );
        $this->assign('listType', $case);

        $this->assign('isBrowseByTagCase', $isBrowseByTagCase);

        $tagSearch = new BASE_CMP_TagSearch(OW::getRouter()->urlForRoute('iisnews.list', array('list'=>'browse-by-tag')));

        $this->addComponent('tagSearch', $tagSearch);

        $tagCount = null;
        if ( $isBrowseByTagCase )
        {
            $tagCount = 1000;
        }

        $tagCloud = new BASE_CMP_EntityTagCloud('news-entry', OW::getRouter()->urlForRoute('iisnews.list', array('list'=>'browse-by-tag')), $tagCount);

        if ( $isBrowseByTagCase )
        {
            $tagCloud->setTemplate(OW::getPluginManager()->getPlugin('base')->getCmpViewDir() . 'big_tag_cloud.html');

            $tag = !(empty($_GET['tag'])) ? strip_tags(UTIL_HtmlTag::stripTags($_GET['tag'])) : '';
            $this->assign('tag', $tag );

            if (empty($tag))
            {
                $showList = false;
            }
        }

        $this->addComponent('tagCloud', $tagCloud);


        $this->assign('showList', $showList);

        $list = array();
        $itemsCount = 0;

        list($list, $itemsCount) = $this->getData($case, $first, $count);

        $entrys = array();

        $toolbars = array();

        $userService = BOL_UserService::getInstance();

        $authorIdList = array();

        $previewLength = 50;

        foreach ( $list as $item )
        {
            $dto = $item['dto'];
            $stringRenderer = OW::getEventManager()->trigger(new OW_Event(IISEventManager::ON_AFTER_NEWSFEED_STATUS_STRING_READ,array('string' => $dto->getEntry())));
            if(isset($stringRenderer->getData()['string'])){
                $dto->setEntry($stringRenderer->getData()['string']);
            }
            $dto->setEntry($dto->getEntry());
            $dto->setTitle( UTIL_String::truncate( strip_tags($dto->getTitle()), 350, '...' )  );

            $text = explode("<!--more-->", $dto->getEntry());

            $isPreview = count($text) > 1;

            if ( !$isPreview )
            {
                $text = explode('<!--page-->', $text[0]);
                $showMore = count($text) > 1;
            }
            else
            {
                $showMore = true;
            }

            $text = $text[0];

            $entrys[] = array(
                'dto' => $dto,
                'text' => $text,
                'showMore' => $showMore,
                'url' => OW::getRouter()->urlForRoute('user-entry', array('id'=>$dto->getId()))
            );

            $authorIdList[] = $dto->authorId;
            $idList[] = $dto->getId();
        }

        if ( !empty($idList) )
        {
            $avatars = BOL_AvatarService::getInstance()->getDataForUserAvatars($authorIdList, true, false);
            $this->assign('avatars', $avatars);

            $nlist = array();
            foreach ( $avatars as $userId => $avatar )
            {
                $nlist[$userId] = $avatar['title'];
            }
            $urls = BOL_UserService::getInstance()->getUserUrlsForList($authorIdList);
            $this->assign('toolbars', $this->getToolbar($idList, $list, $urls, $nlist));
        }

        $this->assign('list', $entrys);
        $this->assign('url_new_entry', OW::getRouter()->urlForRoute('entry-save-new'));

        $paging = new BASE_CMP_Paging($page, ceil($itemsCount / $rpp), 5);

        $this->addComponent('paging', $paging);
    }

    private function getData( $case, $first, $count )
    {
        $service = EntryService::getInstance();

        $list = array();
        $itemsCount = 0;

        switch ( $case )
        {
            case 'most-discussed':

                OW::getDocument()->setTitle(OW::getLanguage()->text('iisnews', 'most_discussed_title'));
                OW::getDocument()->setDescription(OW::getLanguage()->text('iisnews', 'most_discussed_description'));

                $commentService = BOL_CommentService::getInstance();

                $info = array();

                $info = $commentService->findMostCommentedEntityList('news-entry', $first, $count);

                $idList = array();

                foreach ( $info as $item )
                {
                    $idList[] = $item['id'];
                }

                if ( empty($idList) )
                {
                    break;
                }

                $dtoList = $service->findListByIdList($idList);

                foreach ( $dtoList as $dto )
                {
                    if ($dto->isDraft())
                    {
                        continue;
                    }
                    $info[$dto->id]['dto'] = $dto;

                    $list[] = array(
                        'dto' => $dto,
                        'commentCount' => $info[$dto->id] ['commentCount'],
                    );
                }

                function sortMostCommented( $e, $e2 )
                {

                    return $e['commentCount'] < $e2['commentCount'];
                }
                usort($list, 'sortMostCommented');

                $itemsCount = $commentService->findCommentedEntityCount('news-entry');

                break;

            case 'top-rated':

                OW::getDocument()->setTitle(OW::getLanguage()->text('iisnews', 'top_rated_title'));
                OW::getDocument()->setDescription(OW::getLanguage()->text('iisnews', 'top_rated_description'));

                $info = array();

                $info = BOL_RateService::getInstance()->findMostRatedEntityList('news-entry', $first, $count);

                $idList = array();

                foreach ( $info as $item )
                {
                    $idList[] = $item['id'];
                }

                if ( empty($idList) )
                {
                    break;
                }

                $dtoList = $service->findListByIdList($idList);

                foreach ( $dtoList as $dto )
                {
                    if ($dto->isDraft())
                    {
                        continue;
                    }
                    $list[] = array(
                        'dto' => $dto,
                        'avgScore' => $info[$dto->id] ['avgScore'],
                        'ratesCount' => $info[$dto->id] ['ratesCount']
                    );
                }

                function sortTopRated( $e, $e2 )
                {
                    if ($e['avgScore'] == $e2['avgScore'])
                    {
                        if ($e['ratesCount'] == $e2['ratesCount'])
                        {
                            return 0;
                        }

                        return $e['ratesCount'] < $e2['ratesCount'];
                    }
                    return $e['avgScore'] < $e2['avgScore'];
                }
                usort($list, 'sortTopRated');

                $itemsCount = BOL_RateService::getInstance()->findMostRatedEntityCount('news-entry');

                break;

            case 'browse-by-tag':
                if ( empty($_GET['tag']) )
                {
                    $mostPopularTagsArray = BOL_TagService::getInstance()->findMostPopularTags('news-entry', 20);
                    $mostPopularTags = "";

                    foreach ( $mostPopularTagsArray as $tag )
                    {
                        $mostPopularTags .= $tag['label'] . ", ";
                    }

                    OW::getDocument()->setTitle(OW::getLanguage()->text('iisnews', 'browse_by_tag_title'));
                    OW::getDocument()->setDescription(OW::getLanguage()->text('iisnews', 'browse_by_tag_description', array('tags' => $mostPopularTags)));

                    break;
                }

                $info = BOL_TagService::getInstance()->findEntityListByTag('news-entry', strip_tags(UTIL_HtmlTag::stripTags($_GET['tag'])), $first, $count);

                $itemsCount = BOL_TagService::getInstance()->findEntityCountByTag('news-entry', strip_tags(UTIL_HtmlTag::stripTags($_GET['tag'])));

                foreach ( $info as $item )
                {
                    $idList[] = $item;
                }

                if ( empty($idList) )
                {
                    break;
                }

                $dtoList = $service->findListByIdList($idList);

                function sortByTimestamp( $entry1, $entry2 )
                {
                    return $entry1->timestamp < $entry2->timestamp;
                }
                usort($dtoList, 'sortByTimestamp');


                foreach ( $dtoList as $dto )
                {
                    if ($dto->isDraft())
                    {
                        continue;
                    }
                    $list[] = array('dto' => $dto);
                }

//                OW::getDocument()->setTitle(OW::getLanguage()->text('iisnews', 'browse_by_tag_item_title', array('tag' => strip_tags(UTIL_HtmlTag::stripTags($_GET['tag'])))));
//                OW::getDocument()->setDescription(OW::getLanguage()->text('iisnews', 'browse_by_tag_item_description', array('tag' => strip_tags(UTIL_HtmlTag::stripTags($_GET['tag'])))));

                break;

            case 'latest':
                OW::getDocument()->setTitle(OW::getLanguage()->text('iisnews', 'latest_title'));
                OW::getDocument()->setDescription(OW::getLanguage()->text('iisnews', 'latest_description'));

                $arr = $service->findList($first, $count);

                foreach ( $arr as $item )
                {
                    $list[] = array('dto' => $item);
                }

                $itemsCount = $service->countEntrys();

                break;
        }

        return array($list, $itemsCount);
    }

    /**
     * Get top menu for News entry list
     *
     * @return BASE_CMP_ContentMenu
     */
    private function getContentMenu()
    {
        $menuItems = array();

        $listNames = array(
            'browse-by-tag' => array('iconClass' => 'ow_ic_tag'),
            'most-discussed' => array('iconClass' => 'ow_ic_comment'),
            'top-rated' => array('iconClass' => 'ow_ic_star'),
            'latest' => array('iconClass' => 'ow_ic_clock')
        );

        foreach ( $listNames as $listKey => $listArr )
        {
            $menuItem = new BASE_MenuItem();
            $menuItem->setKey($listKey);
            $menuItem->setUrl(OW::getRouter()->urlForRoute('iisnews.list', array('list' => $listKey)));
            $menuItemKey = explode('-', $listKey);
            $listKey = "";
            foreach ($menuItemKey as $key)
            {
                $listKey .= strtoupper(substr($key, 0, 1)).substr($key, 1);
            }

            $menuItem->setLabel(OW::getLanguage()->text('iisnews', 'menuItem'.$listKey));
            $menuItem->setIconClass($listArr['iconClass']);
            $menuItems[] = $menuItem;
        }

        return new BASE_CMP_ContentMenu($menuItems);
    }

    private function getToolbar( $idList, $list, $ulist, $nlist )
    {
        if ( empty($idList) )
        {
            return array();
        }

        $info = array();

        $info['comment'] = BOL_CommentService::getInstance()->findCommentCountForEntityList('news-entry', $idList);

        $info['rate'] = BOL_RateService::getInstance()->findRateInfoForEntityList('news-entry', $idList);

        $info['tag'] = BOL_TagService::getInstance()->findTagListByEntityIdList('news-entry', $idList);

        $toolbars = array();

        foreach ( $list as $item )
        {
            $id = $item['dto']->id;

            $toolbars[$id] = array(
                array(
                    'class' => 'ow_ipc_date',
                    'label' => UTIL_DateTime::formatDate($item['dto']->timestamp)
                ),
            );

            if ( $info['rate'][$id]['avg_score'] > 0 )
            {
                $toolbars[$id][] = array(
                    'label' => OW::getLanguage()->text('iisnews', 'rate') . ' <span class="ow_txt_value">' . ( ( $info['rate'][$id]['avg_score'] - intval($info['rate'][$id]['avg_score']) == 0 ) ? intval($info['rate'][$id]['avg_score']) : sprintf('%.2f', $info['rate'][$id]['avg_score']) ) . '</span>',
                );
            }

            if ( !empty($info['comment'][$id]) )
            {
                $toolbars[$id][] = array(
                    'label' => OW::getLanguage()->text('iisnews', 'comments') . ' <span class="ow_txt_value">' . $info['comment'][$id] . '</span>',
                );
            }


            if ( empty($info['tag'][$id]) )
            {
                continue;
            }

            $value = "<span class='ow_wrap_normal'>" . OW::getLanguage()->text('iisnews', 'tags') . ' ';

            foreach ( $info['tag'][$id] as $tag )
            {
                $value .='<a href="' . OW::getRouter()->urlForRoute('iisnews.list', array('list'=>'browse-by-tag')) . "?tag={$tag}" . "\">{$tag}</a>, ";
            }

            $value = mb_substr($value, 0, mb_strlen($value) - 2);
            $value .= "</span>";
            $toolbars[$id][] = array(
                'label' => $value,
            );
        }

        return $toolbars;
    }
}