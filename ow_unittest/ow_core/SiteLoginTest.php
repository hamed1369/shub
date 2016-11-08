<?php

/**
 * User: Hamed Tahmooresi
 * Date: 2/9/2016
 * Time: 2:11 PM
 */
//require_once 'PHPUnit/Extensions/SeleniumTestCase.php';

class SiteViewTest extends IISTestUtilites
{
    private $TEST_USERNAME = 'adminForLoginTest';
    private $TEST_EMAIL = 'admin@gmail.com';
    private $TEST_CORRECT_PASSWORD = 'asdf@1111';
    private $TEST_WRONG_PASSWORD = '123';

    private $userService;
    private $user;
    protected function setUp()
    {
        $this->setBrowser('firefox');
        $this->setBrowserUrl(OW_URL_HOME);
        $accountType = BOL_QuestionService::getInstance()->getDefaultAccountType()->name;
        IISSecurityProvider::createUser($this->TEST_USERNAME,$this->TEST_EMAIL,$this->TEST_CORRECT_PASSWORD,"1987/3/21","1",$accountType);
        $this->user = BOL_UserService::getInstance()->findByUsername($this->TEST_USERNAME);
    }
    public function testSuccessfulLogin()
    {
        sleep(4);
        $CURRENT_SESSIONS = $this->prepareSession();
        $CURRENT_SESSIONS->currentWindow()->maximize();

        sleep(4);
        $this->url(OW_URL_HOME . "dashboard");
        $sessionId = $CURRENT_SESSIONS->cookie()->get(OW_Session::getInstance()->getName());
        $sessionId = str_replace('%2C', ',', $sessionId);
        try{
            sleep(1);
            $this->sign_in($this->user->getUsername(),$this->TEST_CORRECT_PASSWORD,true,true,$sessionId);
        }catch (Exception $ex){
            echo $ex;
            return;
        }
        try
        {
            $this->waitUntilElementLoaded('byName','status');
            $this->assertTrue(true);
        }catch (Exception $ex){
            echo $ex;
            $this->assertTrue(false);
        }
    }
    public function testFailedLogin()
    {
        OW::getDbo()->query('truncate table `ow_iisblockingip_block_ip`');
        $this->prepareSession()->currentWindow()->maximize();
        $this->url(OW_URL_HOME.'sign-in');
        try{
            //$this->byClassName('ow_signin_label')->click();
            $this->byName('identity')->clear();
            $this->byName('identity')->value($this->TEST_USERNAME);
            $this->byName('password')->clear();
            $this->byName('password')->value($this->TEST_WRONG_PASSWORD);
            $this->byName('sign-in')->submit();
            $this->waitUntilElementLoaded('byName','captchaField');
            $this->assertTrue(true);
        }catch (Exception $ex){
            echo $ex;
            if (getenv("SNAPSHOT_DIR")) file_put_contents(getenv("SNAPSHOT_DIR").'SiteViewTest-testFailedLogin.png', $this->currentScreenshot());
            $this->assertTrue(false);
        }
    }
    public function tearDown()
    {
        OW::getDbo()->query('truncate table `ow_iisblockingip_block_ip`');
        $questionDao = BOL_QuestionService::getInstance();
        $questionDao->deleteQuestionDataByUserId($this->user->getId());
        $userDao = BOL_UserDao::getInstance();
        $userDao->deleteById($this->user->getId());
    }
}