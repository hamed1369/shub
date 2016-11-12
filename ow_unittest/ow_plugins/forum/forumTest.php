<?php
/**
 * User: Issa Moradnejad
 * Date: 2016/09/11
 */

class forumTest extends IISTestUtilites
{
    private $TEST_USER1_NAME = "user1";
    private $TEST_USER2_NAME = "user2";
    private $TEST_PASSWORD = '12345';

    private $userService;
    private $user1,$user2;

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

    protected function setUp()
    {
        $this->setBrowser('firefox');
        $this->setBrowserUrl(OW_URL_HOME);
        $this->userService = BOL_UserService::getInstance();
        $accountType = BOL_QuestionService::getInstance()->getDefaultAccountType()->name;

        IISSecurityProvider::createUser($this->TEST_USER1_NAME,"user1@gmail.com",$this->TEST_PASSWORD,"1969/3/21","1",$accountType);
        IISSecurityProvider::createUser($this->TEST_USER2_NAME,"user2@gmail.com",$this->TEST_PASSWORD,"1969/3/21","1",$accountType);
        $this->user1 = BOL_UserService::getInstance()->findByUsername($this->TEST_USER1_NAME);
        $this->user2 = BOL_UserService::getInstance()->findByUsername($this->TEST_USER2_NAME);

    }

    public function setUpPage()
    {
        parent::setUpPage(); // TODO: Change the autogenerated stub
        $this->timeouts()->implicitWait(15000);
    }

    public function testForum1()
    {
        //----SCENARIO 1 -
        // User1 can see forum.
        // User1 can create new topic.

        $test_caption = "forumTest-testForum1";
        //$this->echoText($test_caption);
        $CURRENT_SESSIONS = $this->prepareSession();
        $CURRENT_SESSIONS->currentWindow()->maximize();

        $this->url(OW_URL_HOME . "dashboard");
        $sessionId = $CURRENT_SESSIONS->cookie()->get(OW_Session::getInstance()->getName());
        $sessionId = str_replace('%2C', ',', $sessionId);
        //----------USER1
        $this->sign_in($this->user1->getUsername(),$this->TEST_PASSWORD,true,true,$sessionId);
        try {
            $this->url(OW_URL_HOME . "forum");
            $this->hide_element('demo-nav');
            $this->byClassName("btn_add_topic")->click();
            $this->hide_element('demo-nav');
            $res = $this->checkIfXPathExists('//input[@name="title"]');
            $this->assertTrue($res);
            $res = $this->checkIfXPathExists('//select[@name="group"]');
            $this->assertTrue($res);
            $res = $this->checkIfXPathExists('//input[@type="submit"]');
            $this->assertTrue($res);
        } catch (Exception $ex) {
            $this->echoText($ex, true);
            if (getenv("SNAPSHOT_DIR"))
                file_put_contents(getenv("SNAPSHOT_DIR") . $test_caption . '.png', $this->currentScreenshot());
            $this->assertTrue(false);
        }
    }


    public function tearDown()
    {
        //delete users
        $questionDao = BOL_QuestionService::getInstance();
        $userDao = BOL_UserDao::getInstance();

        $questionDao->deleteQuestionDataByUserId($this->user1->getId());
        $userDao->deleteById($this->user1->getId());

        $questionDao->deleteQuestionDataByUserId($this->user2->getId());
        $userDao->deleteById($this->user2->getId());
    }
}