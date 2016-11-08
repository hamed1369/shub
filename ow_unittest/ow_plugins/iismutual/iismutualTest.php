<?php

class iismutualTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        IISSecurityProvider::createUser('iismutual_user1', 'iismutual_user1@test.com', '12345678', '1987/3/21', '1');
        IISSecurityProvider::createUser('iismutual_user2', 'iismutual_user2@test.com', '12345678', '1987/3/21', '1');
        IISSecurityProvider::createUser('iismutual_user3', 'iismutual_user3@test.com', '12345678', '1987/3/21', '1');
    }

    /**
     * Test of iismutual plugin
     */
    public function testMutual()
    {

        $user1 = BOL_UserService::getInstance()->findByUsername('iismutual_user1');
        $user2 = BOL_UserService::getInstance()->findByUsername('iismutual_user2');
        $user3 = BOL_UserService::getInstance()->findByUsername('iismutual_user3');

        FRIENDS_BOL_Service::getInstance()->request($user1->getId(), $user2->getId());
        FRIENDS_BOL_Service::getInstance()->accept($user2->getId(), $user1->getId());

        FRIENDS_BOL_Service::getInstance()->request($user1->getId(), $user3->getId());
        FRIENDS_BOL_Service::getInstance()->accept($user3->getId(), $user1->getId());

        $mutuals = IISMUTUAL_CLASS_Mutual::getInstance()->getMutualFriends($user2->getId(), $user3->getId());
        $this->assertEquals(true, in_array($user1->getId(), $mutuals));
    }

    public function tearDown()
    {
        IISSecurityProvider::deleteUser('iismutual_user1');
        IISSecurityProvider::deleteUser('iismutual_user2');
        IISSecurityProvider::deleteUser('iismutual_user3');
    }
}