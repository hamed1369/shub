<?php
/**
 * User: Issa Moradnejad
 * Date: 2016/06/01
 */

class eventTest extends IISTestUtilites
{
    private $TEST_USER1_NAME = "user1";
    private $TEST_USER2_NAME = "user2";
    private $TEST_USER3_NAME = "user3";
    private $TEST_PASSWORD = '12345';

    private $userService;
    private $user1,$user2,$user3;
    private $event1,$event2,$event3;

    private function echoText($text, $bounding_box=false)
    {
        if ($bounding_box) {
            echo "-----------------------------ISSA------------------------------------\n";
            echo "$text\n";
            echo "---------------------------------------------------------------------\n";
        }else
            echo "==========ISSA:==>$text\n";
    }
    private function hide_element($className){
        try {
            $this->execute(array(
                'script' => "document.getElementsByClassName('" . $className . "')[0].style.visibility = 'hidden';",
                'args' => array()
            ));
        }catch(Exception $ex){
            $this->echoText('hide_element_error:'.$ex);
        }
    }

    public function createEvent($userID, $title,$desc,$location,$startStamp,$endStamp,$whoCanView,$whoCanInvite)
    {
        if($whoCanView=='anyone') $whoCanView= 1; else $whoCanView= 2;
        if($whoCanInvite=='creator') $whoCanInvite= 1; else $whoCanInvite= 2;

        $eventService = EVENT_BOL_EventService::getInstance();
        $data = array();
        $data['title'] = $title;
        $data['desc'] = $desc;
        $data['location']=$location;
        $data['who_can_view']=$whoCanView;
        $data['who_can_invite']=$whoCanInvite;
        $data['start_time'] = 'all_day';
        $data['end_time'] = 'all_day';
        if ( empty($endStamp) )
        {
            $endStamp = strtotime("+1 day", $startStamp);
            $endStamp = mktime(0, 0, 0, date('n',$endStamp), date('j',$endStamp), date('Y',$endStamp));
        }

        $serviceEvent = new OW_Event(EVENT_BOL_EventService::EVENT_BEFORE_EVENT_CREATE, array(), $data);
        OW::getEventManager()->trigger($serviceEvent);
        $data = $serviceEvent->getData();

        $event = new EVENT_BOL_Event();
        $event->setStartTimeStamp($startStamp);
        $event->setEndTimeStamp($endStamp);
        $event->setCreateTimeStamp(time());
        $event->setTitle(htmlspecialchars($data['title']));
        $event->setLocation(UTIL_HtmlTag::autoLink(strip_tags($data['location'])));
        $event->setWhoCanView((int) $data['who_can_view']);
        $event->setWhoCanInvite((int) $data['who_can_invite']);
        $event->setDescription($data['desc']);
        $event->setUserId($userID);
        $event->setEndDateFlag( !empty($endStamp) );
        $event->setStartTimeDisable( $data['start_time'] == 'all_day' );
        $event->setEndTimeDisable( $data['end_time'] == 'all_day' );

        $serviceEvent = new OW_Event(EVENT_BOL_EventService::EVENT_ON_CREATE_EVENT, array(
            'eventDto' => $event,
            "imageValid" => false,
            "imageTmpPath" => $_FILES['image']['tmp_name']
        ));
        OW::getEventManager()->trigger($serviceEvent);

        $eventService->saveEvent($event);

        $eventUser = new EVENT_BOL_EventUser();
        $eventUser->setEventId($event->getId());
        $eventUser->setUserId($userID);
        $eventUser->setTimeStamp(time());
        $eventUser->setStatus(EVENT_BOL_EventService::USER_STATUS_YES);
        $eventService->saveEventUser($eventUser);


        $serviceEvent = new OW_Event(EVENT_BOL_EventService::EVENT_AFTER_CREATE_EVENT, array(
            'eventId' => $event->id,
            'eventDto' => $event
        ), array(

        ));
        OW::getEventManager()->trigger($serviceEvent);

        $event = EVENT_BOL_EventService::getInstance()->findEvent($event->getId());
        return $event;
    }

    protected function setUp()
    {
        $this->setBrowser('firefox');
        $this->setBrowserUrl(OW_URL_HOME);
        $this->userService = BOL_UserService::getInstance();
        $accountType = BOL_QuestionService::getInstance()->getDefaultAccountType()->name;
        IISSecurityProvider::createUser($this->TEST_USER1_NAME,"user1@gmail.com",$this->TEST_PASSWORD,"1987/3/21","1",$accountType);
        IISSecurityProvider::createUser($this->TEST_USER2_NAME,"user2@gmail.com",$this->TEST_PASSWORD,"1987/3/21","1",$accountType);
        IISSecurityProvider::createUser($this->TEST_USER3_NAME,"user3@gmail.com",$this->TEST_PASSWORD,"1987/3/21","1",$accountType);
        $this->user1 = BOL_UserService::getInstance()->findByUsername($this->TEST_USER1_NAME);
        $this->user2 = BOL_UserService::getInstance()->findByUsername($this->TEST_USER2_NAME);
        $this->user3 = BOL_UserService::getInstance()->findByUsername($this->TEST_USER3_NAME);
        // set some info to users

        $friendsQuestionService = FRIENDS_BOL_Service::getInstance();
        $friendsQuestionService->request($this->user1->getId(),$this->user2->getId());
        $friendsQuestionService->accept($this->user2->getId(),$this->user1->getId());

        //+1 year
        $start_stamp1 = mktime(0, 0, 0, date('n',time()), date('j',time()), date('Y',time())+1 );

        $this->event1 = $this->createEvent($this->user1->getId(), 'e1','Seminar','loc1',$start_stamp1,null,'anyone','creator');
        $this->event2 = $this->createEvent($this->user1->getId(), 'e2','Zoo','loc2',$start_stamp1,null,'anyone','participant');
        $this->event3 = $this->createEvent($this->user1->getId(), 'e3','Secret Society','loc3',$start_stamp1,null,'invite','participant');
        EVENT_BOL_EventService::getInstance()->inviteUser($this->event3->id,$this->user3->getId(),$this->user1->getId());

    }

    public function setUpPage()
    {
        parent::setUpPage(); // TODO: Change the autogenerated stub
        $this->timeouts()->implicitWait(15000);
    }

    public function testEvent1()
    {
        //----SCENARIO 1 - Seminar
        //User1 create Event1 : everyone can join, only user1 can invite
        //User2 Maybe attends, can't invite, can post
        //User3 Won't attend, can't invite, can post

        //----SCENARIO 2 - Zoo
        //User1 create Event2 : everyone can join and invite
        //User2 Maybe attends, can invite, can post

        //----SCENARIO 3 - Secret Society
        //User1 create Event3 : join with invite link, invites user3
        //User2 can't attend or read
        //User3 attends


        $test_caption = "eventTest-testEvent1";
        //$this->echoText($test_caption);
        $CURRENT_SESSIONS = $this->prepareSession();
        $CURRENT_SESSIONS->currentWindow()->maximize();

        $this->url(OW_URL_HOME . "dashboard");
        $sessionId = $CURRENT_SESSIONS->cookie()->get(OW_Session::getInstance()->getName());
        $sessionId = str_replace('%2C', ',', $sessionId);
        //----------USER2
        $this->sign_in($this->user2->getUsername(),$this->TEST_PASSWORD,true,true,$sessionId);
        try {
            $this->url(OW_URL_HOME . 'event/'.$this->event1->id);
            $this->hide_element('demo-nav');
            $this->byId('event_attend_maybe_btn')->click();
            $this->url(OW_URL_HOME . 'event/'.$this->event1->id); //refresh page for invite link
            $res = $this->checkIfXPathExists('//*[@class="ow_comments_input"]');
            $this->assertTrue($res);
            $res = $this->checkIfXPathExists('//*[@id="inviteLink"]');
            $this->assertTrue(!$res);


            $this->url(OW_URL_HOME . 'event/'.$this->event2->id);
            $this->hide_element('demo-nav');
            $this->byId('event_attend_maybe_btn')->click();
            $this->url(OW_URL_HOME . 'event/'.$this->event2->id);
            $res = $this->checkIfXPathExists('//*[@class="ow_comments_input"]');
            $this->assertTrue($res);
            $res = $this->checkIfXPathExists('//*[@id="inviteLink"]');
            $this->assertTrue($res);


            $this->url(OW_URL_HOME . 'event/'.$this->event3->id);
            $this->hide_element('demo-nav');
            $res = $this->checkIfXPathExists('//*[@class="ow_comments_input"]');
            $this->assertTrue(!$res);
            sleep(1);
            $this->url('sign-out');
        } catch (Exception $ex) {
            $this->echoText($ex, true);
            if (getenv("SNAPSHOT_DIR"))
                file_put_contents(getenv("SNAPSHOT_DIR") . $test_caption . '.png', $this->currentScreenshot());
            $this->assertTrue(false);
        }

        //----------USER3
        $this->sign_in($this->user3->getUsername(),$this->TEST_PASSWORD,true,true,$sessionId);
        try {
            $this->url(OW_URL_HOME . 'event/'.$this->event1->id);
            $this->hide_element('demo-nav');
            $this->byId('event_attend_no_btn')->click();
            $this->url(OW_URL_HOME . 'event/'.$this->event1->id); //refresh page for invite link
            $res = $this->checkIfXPathExists('//*[@class="ow_comments_input"]');
            $this->assertTrue($res);
            $res = $this->checkIfXPathExists('//*[@id="inviteLink"]');
            $this->assertTrue(!$res);

            $this->url(OW_URL_HOME . 'event/'.$this->event3->id);
            $this->hide_element('demo-nav');
            $res = $this->checkIfXPathExists('//*[@class="ow_comments_input"]');
            $this->assertTrue($res);
            $this->byId('event_attend_no_btn')->click();
        } catch (Exception $ex) {
            $this->echoText($ex, true);
            if (getenv("SNAPSHOT_DIR"))
                file_put_contents(getenv("SNAPSHOT_DIR") . $test_caption . '.png', $this->currentScreenshot());
            $this->assertTrue(false);
        }
    }


    public function tearDown()
    {
        //delete events
        $eventDto = EVENT_BOL_EventService::getInstance();
        $eventDto->deleteEvent($this->event1->id);
        $eventDto->deleteEvent($this->event2->id);
        $eventDto->deleteEvent($this->event3->id);

        //delete users
        $questionDao = BOL_QuestionService::getInstance();
        $userDao = BOL_UserDao::getInstance();
        $friendsQuestionService = FRIENDS_BOL_Service::getInstance();

        $friendsQuestionService->deleteUserFriendships($this->user1->getId());
        $questionDao->deleteQuestionDataByUserId($this->user1->getId());
        $userDao->deleteById($this->user1->getId());

        $questionDao->deleteQuestionDataByUserId($this->user2->getId());
        $userDao->deleteById($this->user2->getId());

        $questionDao->deleteQuestionDataByUserId($this->user3->getId());
        $userDao->deleteById($this->user3->getId());
    }
}