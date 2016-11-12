<?php

/**
 * This software is intended for use with Oxwall Free Community Software http://www.oxwall.org/ and is
 * licensed under The BSD license.

 * ---
 * Copyright (c) 2011, Oxwall Foundation
 * All rights reserved.

 * Redistribution and use in source and binary forms, with or without modification, are permitted provided that the
 * following conditions are met:
 *
 *  - Redistributions of source code must retain the above copyright notice, this list of conditions and
 *  the following disclaimer.
 *
 *  - Redistributions in binary form must reproduce the above copyright notice, this list of conditions and
 *  the following disclaimer in the documentation and/or other materials provided with the distribution.
 *
 *  - Neither the name of the Oxwall Foundation nor the names of its contributors may be used to endorse or promote products
 *  derived from this software without specific prior written permission.

 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES,
 * INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR
 * PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO,
 * PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED
 * AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

/**
 * @author
 * @package ow.plugin.iisnews.mobile.controllers
 * @since 1.6.0
 */
class IISNEWS_MCTRL_News extends OW_MobileActionController
{
    /**
     * Forum index
     */
    public function index()
    {
        $this->setPageTitle(OW::getLanguage()->text('iisnews', 'index_page_title'));
        $this->setPageHeading(OW::getLanguage()->text('iisnews', 'index_page_heading'));

        OW::getDocument()->setHeading(OW::getLanguage()->text('iisnews', 'news_index'));
        $page = (!empty($_GET['page']) && intval($_GET['page']) > 0 ) ? $_GET['page'] : 1;
        $service = EntryService::getInstance();
        $rpp = (int) OW::getConfig()->getValue('iisnews', 'results_per_page');

        $first = ($page - 1) * $rpp;

        $count = $rpp;
        $itemsCount= $service->countEntrys();
        $arr = $service->findList($first, $count);
        foreach ( $arr as $item )
        {
            $listDto[] = array('dto' => $item);
        }

        $entrys = array();
        $commentInfo = array();
        foreach ( $listDto as $item )
        {
            $dto = $item['dto'];
            $stringRenderer = OW::getEventManager()->trigger(new OW_Event(IISEventManager::ON_AFTER_NEWSFEED_STATUS_STRING_READ,array('string' => $dto->getEntry())));
            if(isset($stringRenderer->getData()['string'])){
                $dto->setEntry($stringRenderer->getData()['string']);
            }
            $dto->setEntry(UTIL_String::truncate( strip_tags($dto->getEntry()), 350, '...' ));
            $dto->setTitle( UTIL_String::truncate( strip_tags($dto->getTitle()), 350, '...' )  );
            $dto->setTimestamp(UTIL_DateTime::formatDate($dto->getTimestamp()));
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
            $authorUrl = OW_URL_HOME . 'user/' . BOL_UserService::getInstance()->getUserName($dto->getAuthorId());
            $entries[] = array(
                'dto' => $dto,
                'text' => $text,
                'showMore' => $showMore,
                'url' => OW::getRouter()->urlForRoute('user-entry', array('id'=>$dto->getId())),
                'countOfComment' => BOL_CommentService::getInstance()->findCommentCount('news-entry', $dto->getId()),
                'authorName' => BOL_UserService::getInstance()->getDisplayName($dto->getAuthorId()),
                'authorUrl' => $authorUrl
            );

            $authorIdList[] = $dto->authorId;
            $idList[] = $dto->getId();
        }

        $this->assign('list', $entries);
        $paging = new BASE_CMP_PagingMobile($page, ceil($itemsCount / $rpp), 5);
        $this->addComponent('paging', $paging);
    }
}

