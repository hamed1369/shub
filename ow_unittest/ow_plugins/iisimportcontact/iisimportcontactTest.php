<?php

class iisimportcontactTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        IISSecurityProvider::createUser('iissuggestfriend_user1', 'iissuggestfriend_user1@gmail.com', '12345678', '1987/3/21', '1');
        IISSecurityProvider::createUser('iissuggestfriend_user2', 'iissuggestfriend_user2@gmail.com', '12345678', '1987/3/21', '1');
        IISSecurityProvider::createUser('iissuggestfriend_user3', 'iissuggestfriend_user3@gmail.com', '12345678', '1987/3/21', '1');
        IISSecurityProvider::createUser('iissuggestfriend_user4', 'iissuggestfriend_user4@gmail.com', '12345678', '1987/3/21', '1');
    }

    /**
     * Test of iisimport plugin
     */
    public function testFriendsList()
    {
        $user1 = BOL_UserService::getInstance()->findByUsername('iissuggestfriend_user1');
        $user2 = BOL_UserService::getInstance()->findByUsername('iissuggestfriend_user2');
        $user3 = BOL_UserService::getInstance()->findByUsername('iissuggestfriend_user3');
        $user4 = BOL_UserService::getInstance()->findByUsername('iissuggestfriend_user4');

        FRIENDS_BOL_Service::getInstance()->request($user1->getId(), $user2->getId());
        FRIENDS_BOL_Service::getInstance()->accept($user2->getId(), $user1->getId());

        FRIENDS_BOL_Service::getInstance()->request($user1->getId(), $user3->getId());
        FRIENDS_BOL_Service::getInstance()->accept($user3->getId(), $user1->getId());
        $service = IISIMPORT_BOL_Service::getInstance();
        $service->addUser($user1->getId(),  $user2->email, 'google');
        $service->addUser($user1->getId(),  $user3->email, 'google');
        $service->addUser($user1->getId(),  $user4->email, 'google');
        $emails = $service->getEmailsByUserId($user1->getId(), 'google');
        $emailsInformation = $service->getRegisteredExceptFriendEmails($emails,$user1->getId());
        $suggestedEmails = array();
        foreach ($emailsInformation as $emailInformation) {
            $suggestedEmails[] = $emailInformation['email'];
        }
        $this->assertEquals(true, in_array($user4->email, $suggestedEmails));
        $this->assertEquals(true, !in_array($user3->email, $suggestedEmails));
        $this->assertEquals(true, !in_array($user2->email, $suggestedEmails));
    }

    public function tearDown()
    {
        IISSecurityProvider::deleteUser('iissuggestfriend_user1');
        IISSecurityProvider::deleteUser('iissuggestfriend_user2');
        IISSecurityProvider::deleteUser('iissuggestfriend_user3');
        IISSecurityProvider::deleteUser('iissuggestfriend_user4');
    }
}