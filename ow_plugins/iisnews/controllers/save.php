<?php

/**
 * @author Yaser Alimardany <yaser.alimardany@gmail.com>
 * @package ow_plugins.iisnews.controllers
 * @since 1.0
 */
class IISNEWS_CTRL_Save extends OW_ActionController
{

    public function index( $params = array() )
    {
        if (OW::getRequest()->isAjax())
        {
            exit();
        }

        if ( !OW::getUser()->isAuthenticated() )
        {
            throw new AuthenticateException();
        }

        $plugin = OW::getPluginManager()->getPlugin('iisnews');
        OW::getNavigation()->activateMenuItem(OW_Navigation::MAIN, 'iisnews', 'main_menu_item');


        $this->setPageHeading(OW::getLanguage()->text('iisnews', 'save_page_heading'));
        $this->setPageHeadingIconClass('ow_ic_write');

        if ( !OW::getUser()->isAuthorized('iisnews', 'add') && !OW::getUser()->isAdmin() )
        {
            $status = BOL_AuthorizationService::getInstance()->getActionStatus('iisnews', 'add_news');
            throw new AuthorizationException($status['msg']);

            return;
        }

        $this->assign('authMsg', null);

        $id = empty($params['id']) ? 0 : $params['id'];

        $service = EntryService::getInstance(); /* @var $service EntryService */

        $tagService = BOL_TagService::getInstance();

        if ( intval($id) > 0 )
        {
            $entry = $service->findById($id);

            if ($entry->authorId != OW::getUser()->getId() && !OW::getUser()->isAuthorized('iisnews'))
            {
                throw new Redirect404Exception();
            }

            $eventParams = array(
                'action' => EntryService::PRIVACY_ACTION_VIEW_NEWS_POSTS,
                'ownerId' => $entry->authorId
            );

            $privacy = OW::getEventManager()->getInstance()->call('plugin.privacy.get_privacy', $eventParams);
            if (!empty($privacy))
            {
                $entry->setPrivacy($privacy);
            }
            $this->assign('enPublishDate', true);
        }
        else
        {
            $entry = new Entry();

            $eventParams = array(
                'action' => EntryService::PRIVACY_ACTION_VIEW_NEWS_POSTS,
                'ownerId' => OW::getUser()->getId()
            );

            $privacy = OW::getEventManager()->getInstance()->call('plugin.privacy.get_privacy', $eventParams);
            if (!empty($privacy))
            {
                $entry->setPrivacy($privacy);
            }

            $entry->setAuthorId(OW::getUser()->getId());
        }

        $form = new SaveForm($entry);

        if ( OW::getRequest()->isPost() && (!empty($_POST['command']) && in_array($_POST['command'], array('draft', 'publish')) ) && $form->isValid($_POST) )
        {
            $form->process($this);
            OW::getApplication()->redirect(OW::getRouter()->urlForRoute('entry-save-edit', array('id' => $entry->getId())));
        }

        $this->addForm($form);

        $this->assign('info', array('dto' => $entry));

        OW::getDocument()->setTitle(OW::getLanguage()->text('iisnews', 'meta_title_new_news_entry'));
        OW::getDocument()->setDescription(OW::getLanguage()->text('iisnews', 'meta_description_new_news_entry'));

    }

    public function delete( $params )
    {
        if (OW::getRequest()->isAjax() || !OW::getUser()->isAuthenticated())
        {
            exit();
        }
        /*
          @var $service EntryService
         */
        $service = EntryService::getInstance();

        $id = $params['id'];

        $dto = $service->findById($id);

        if ( !empty($dto) )
        {
            if ($dto->authorId == OW::getUser()->getId() || OW::getUser()->isAuthorized('iisnews'))
            {
                OW::getEventManager()->trigger(new OW_Event(EntryService::EVENT_BEFORE_DELETE, array(
                    'entryId' => $id
                )));
                $service->delete($dto);
                OW::getEventManager()->trigger(new OW_Event(EntryService::EVENT_AFTER_DELETE, array(
                    'entryId' => $id
                )));
            }
        }

        if ( !empty($_GET['back-to']) )
        {
            $this->redirect($_GET['back-to']);
        }

        $author = BOL_UserService::getInstance()->findUserById($dto->authorId);

        $this->redirect(OW::getRouter()->urlForRoute('user-iisnews', array('user' => $author->getUsername())));
    }
}

class SaveForm extends Form
{
    /**
     *
     * @var Entry
     */
    private $entry;
    /**
     *
     * @var type EntryService
     */
    private $service;


    public function __construct( Entry $entry, $tags = array() )
    {
        parent::__construct('save');
        if( $entry->getTimestamp()!=null) {
            $currentYear = date('Y', time());
            $publishDate = new DateField('publish_date');
            $publishDate->setMinYear($currentYear - 10);
            $publishDate->setMaxYear($currentYear + 10);
            $publishDate->setRequired();
            $publishDate->setLabel(OW::getLanguage()->text('iisnews', 'save_form_lbl_date'));
            $this->addElement($publishDate);
            $publishDate = date('Y', $entry->getTimestamp()) . '/' . date('n', $entry->getTimestamp()) . '/' . date('j', $entry->getTimestamp());
            $this->getElement('publish_date')->setValue($publishDate);

            $enPublishDate = new CheckboxField('enPublishDate');
            $enPublishDate->setLabel(OW::getLanguage()->text('iisnews', 'save_form_lbl_date_enable'));
            $enPublishDate->addAttribute("onclick", "initPublishDateField('.published_date');");
            $this->addElement($enPublishDate);
        }
        OW::getDocument()->addScript(OW::getPluginManager()->getPlugin('iisnews')->getStaticJsUrl().'iisnews.js');
        $language = OW::getLanguage();
        $enRoleList = new CheckboxField('enSentNotification');
        $enRoleList->setLabel($language->text('iisnews', 'notification_form_lbl_published'));
        $this->addElement($enRoleList);
        $this->service = EntryService::getInstance();

        $this->entry = $entry;

        $this->setMethod('post');

        $titleTextField = new TextField('title');

        $this->addElement($titleTextField->setLabel(OW::getLanguage()->text('iisnews', 'save_form_lbl_title'))->setValue($entry->getTitle())->setRequired(true));

        $buttons = array(
            BOL_TextFormatService::WS_BTN_BOLD,
            BOL_TextFormatService::WS_BTN_ITALIC,
            BOL_TextFormatService::WS_BTN_UNDERLINE,
            BOL_TextFormatService::WS_BTN_IMAGE,
            BOL_TextFormatService::WS_BTN_LINK,
            BOL_TextFormatService::WS_BTN_ORDERED_LIST,
            BOL_TextFormatService::WS_BTN_UNORDERED_LIST,
            BOL_TextFormatService::WS_BTN_MORE,
            BOL_TextFormatService::WS_BTN_SWITCH_HTML,
            BOL_TextFormatService::WS_BTN_HTML,
            BOL_TextFormatService::WS_BTN_VIDEO
        );

        $entryTextArea = new WysiwygTextarea('entry', $buttons);
        $entryTextArea->setSize(WysiwygTextarea::SIZE_L);
        $entryTextArea->setLabel(OW::getLanguage()->text('iisnews', 'save_form_lbl_entry'));
        $stringRenderer = OW::getEventManager()->trigger(new OW_Event(IISEventManager::ON_AFTER_NEWSFEED_STATUS_STRING_READ,array('string' => $entry->getEntry())));
        if(isset($stringRenderer->getData()['string'])){
            $entry->setEntry($stringRenderer->getData()['string']);
        }
        $entryTextArea->setValue($entry->getEntry());
        $entryTextArea->setRequired(true);
        $this->addElement($entryTextArea);

        $draftSubmit = new Submit('draft');
        $draftSubmit->addAttribute('onclick', "$('#save_entry_command').attr('value', 'draft');");

        if ( $entry->getId() != null && !$entry->isDraft() )
        {
            $text = OW::getLanguage()->text('iisnews', 'change_status_draft');
        }
        else
        {
            $text = OW::getLanguage()->text('iisnews', 'sava_draft');
        }

        $this->addElement($draftSubmit->setValue($text));

        if ( $entry->getId() != null && !$entry->isDraft() )
        {
            $text = OW::getLanguage()->text('iisnews', 'update');
        }
        else
        {
            $text = OW::getLanguage()->text('iisnews', 'save_publish');
        }

        $publishSubmit = new Submit('publish');
        $publishSubmit->addAttribute('onclick', "$('#save_entry_command').attr('value', 'publish');");

        $this->addElement($publishSubmit->setValue($text));

        $tagService = BOL_TagService::getInstance();

        $tags = array();

        if ( intval($this->entry->getId()) > 0 )
        {
            $arr = $tagService->findEntityTags($this->entry->getId(), 'news-entry');

            foreach ( (!empty($arr) ? $arr : array() ) as $dto )
            {
                $tags[] = $dto->getLabel();
            }
        }

        $tf = new TagsInputField('tf');
        $tf->setLabel(OW::getLanguage()->text('iisnews', 'tags_field_label'));
        $tf->setValue($tags);

        $this->addElement($tf);
    }

    public function process( $ctrl )
    {
        OW::getCacheManager()->clean( array( EntryDao::CACHE_TAG_POST_COUNT ));

        $service = EntryService::getInstance(); /* @var $entryDao EntryService */

        $data = $this->getValues();




        $data['title'] = UTIL_HtmlTag::stripJs($data['title']);

        $entryIsNotPublished = $this->entry->getStatus() == 2;

        $text = UTIL_HtmlTag::sanitize($data['entry']);
        $stringRenderer = OW::getEventManager()->trigger(new OW_Event(IISEventManager::ON_BEFORE_NEWSFEED_STATUS_STRING_WRITE,array('string' => $text)));
        if(isset($stringRenderer->getData()['string'])){
            $text = $stringRenderer->getData()['string'];
        }
        /* @var $entry Entry */
        $this->entry->setTitle($data['title']);
        $this->entry->setEntry($text);
        $this->entry->setIsDraft($_POST['command'] == 'draft');

        $isCreate = empty($this->entry->id);
        if ( $isCreate )
        {
            $this->entry->setTimestamp(time());
            //Required to make #698 and #822 work together
            if ($_POST['command'] == 'draft')
            {
                $this->entry->setIsDraft(2);
            }

            BOL_AuthorizationService::getInstance()->trackAction('iisnews', 'add_news');
        }
        else
        {
            //If entry is not new and saved as draft, remove their item from newsfeed
            if ($_POST['command'] == 'draft')
            {
                OW::getEventManager()->trigger(new OW_Event('feed.delete_item', array('entityType' => 'news-entry', 'entityId' => $this->entry->id)));
            }
            if($data['enPublishDate']!=null && $data['enPublishDate']==true) {
                $dateArray = explode('/', $data['publish_date']);

                $timeStamp = mktime(date('h'), date('i'), date('s'), $dateArray[1], $dateArray[2], $dateArray[0]);

                $this->entry->setTimestamp($timeStamp);
            }
        }

        $service->save($this->entry);
        if ($_POST['command'] != 'draft' && $data['enSentNotification']!=null && $data['enSentNotification']==true)
        {
            // trigger event comment add
            $event = new OW_Event('base_add_news', array(
                'entityType' => 'news-entry',
                'entityId' =>  $this->entry->getId(),
                'userId' => OW::getUser()->getId(),
                'roles' =>$data['userRoles'],
                'pluginKey' => 'iisnews'
            ));

            OW::getEventManager()->trigger($event);
        }
        $tags = array();
        if ( intval($this->entry->getId()) > 0 )
        {
            $tags = $data['tf'];
            foreach ($tags as $id => $tag)
            {
                $tags[$id] = UTIL_HtmlTag::stripTags($tag);
            }
        }
        $tagService = BOL_TagService::getInstance();
        $tagService->updateEntityTags($this->entry->getId(), 'news-entry', $tags );

        if ($this->entry->isDraft())
        {
            $tagService->setEntityStatus('news-entry', $this->entry->getId(), false);

            if ($isCreate)
            {
                OW::getFeedback()->info(OW::getLanguage()->text('iisnews', 'create_draft_success_msg'));
            }
            else
            {
                OW::getFeedback()->info(OW::getLanguage()->text('iisnews', 'edit_draft_success_msg'));
            }
        }
        else
        {
            $tagService->setEntityStatus('news-entry', $this->entry->getId(), true);

            //Newsfeed
            $event = new OW_Event('feed.action', array(
                'pluginKey' => 'iisnews',
                'entityType' => 'news-entry',
                'entityId' => $this->entry->getId(),
                'userId' => $this->entry->getAuthorId(),
            ));
            OW::getEventManager()->trigger($event);

            if ($isCreate)
            {
                OW::getFeedback()->info(OW::getLanguage()->text('iisnews', 'create_success_msg'));

                OW::getEventManager()->trigger(new OW_Event(EntryService::EVENT_AFTER_ADD, array(
                    'entryId' => $this->entry->getId()
                )));
            }
            else
            {
                OW::getFeedback()->info(OW::getLanguage()->text('iisnews', 'edit_success_msg'));
                OW::getEventManager()->trigger(new OW_Event(EntryService::EVENT_AFTER_EDIT, array(
                    'entryId' => $this->entry->getId()
                )));
            }

            $ctrl->redirect(OW::getRouter()->urlForRoute('entry', array('id' => $this->entry->getId())));
        }
    }
}

?>
