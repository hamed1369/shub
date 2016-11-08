<?php

/**
 * Created by PhpStorm.
 * User: pars
 * Date: 4/5/2016
 * Time: 4:08 PM
 */
class IISTestUtilites extends PHPUnit_Extensions_Selenium2TestCase
{
    /*
     * $id is element's name, id or text
     *                         $this->byLinkText();
                            $this->byCssSelector();
                        $this->byXPath();
                        $this->byTag();
                        $this->byClassName();
                        $this->byId();
                        $this->byName();
     */
    public function waitUntilElementLoaded($searchMethod,$id,$wait_ms=15000)
    {
        $webdriver = $this;
        $this->waitUntil(function() use($webdriver,$searchMethod,$id){
            try{
                if($searchMethod =='byLinkText'){
                    $webdriver->byLinkText($id);
                }
                else if($searchMethod == 'byCssSelector'){
                    $webdriver->byCssSelector($id);
                }
                else if($searchMethod == 'byXPath'){
                    $webdriver->byXPath($id);
                }
                else if($searchMethod == 'byTag'){
                    $webdriver->byTag($id);
                }
                else if($searchMethod == 'byClassName'){
                    $webdriver->byClassName($id);
                }
                else if($searchMethod == 'byId'){
                    $webdriver->byId($id);
                }
                else if($searchMethod =='byName'){
                    $webdriver->byName($id);
                }
                return true;
            }catch (Exception $ex){
                $this->assertTrue(false);
            }

        }, $wait_ms);
    }
    public function checkIfXPathExists($id,$wait_ms=1000)
    {
        $webDriver = $this;
        $exists = $this->waitUntil(function() use($webDriver,$id){
            try{
                $webDriver->byXPath($id);
                return true;
            }catch (Exception $ex){
                return false;
            }
        }, $wait_ms);
        return $exists;
    }
	public function sign_in($identity,$password,$should_success=true,$fillCaptcha=false, $sessionId=0){
        //$should_success==true gives error when function can't login and vice versa.
        //$sessionId is only needed when function should fill captcha field.
        //see privacyTest::testScenario1 for more info :)

		$this->url(OW_URL_HOME.'sign-in');

        //FILL CAPTCHA IF EXISTS AND $fillCaptcha
        try{
            if($fillCaptcha) {
                if($this->checkIfXPathExists('//input[@name="captchaField"]')) {
                    $cp = $this->byName('captchaField');
                    if ($cp->displayed()) {
                        //------------------CAPTCHA, SESSIONS-----------
                        session_id($sessionId);
                        session_start();
                        $captchaText = ($_SESSION['securimage_code_value']);
                        session_write_close();
                        //---------------------------------------------------/
                        $cp->clear();
                        $cp->value($captchaText);
                    }
                }
            }
        }catch(Exception $ex){}

        //FILL Other fields
        try{
            $this->byName('identity')->clear();
            $this->byName('identity')->value($identity);
            $this->byName('password')->clear();
            $this->byName('password')->value($password);
            $this->byName('sign-in')->submit();
           /* $this->waitUntilElementLoaded('byClassName','ow_message_node');
			if($should_success)
	            $this->byCssSelector('.ow_message_node.info');
			else
			    $this->byCssSelector('.ow_message_node.error');*/
        }catch (Exception $ex){
            echo $ex;
            $this->assertTrue(false);
        }	
	}
}