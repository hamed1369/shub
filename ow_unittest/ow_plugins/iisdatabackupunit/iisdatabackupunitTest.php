<?php

class iisdatabackupunitTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test of iisdatabackup plugin
     */
    public function testDataBackup()
    {
        $username = 'iisdatabackup_user1';
        IISSecurityProvider::createUser($username, 'iisdatabackup_user1@test.com', '12345678', '1987/3/21', '1');
        $user = BOL_UserService::getInstance()->findByUsername('iisdatabackup_user1');
        $userId = $user->getId();
        IISSecurityProvider::deleteUser('iisdatabackup_user1');
        $table_name = BOL_UserDao::getInstance()->getTableName();
        $table_backup_name = IISSecurityProvider::getTableBackupName($table_name);
        $query = 'select * from `'.$table_backup_name.'` where id = '.$userId.' and username = \''.$username.'\'';
        $result =  OW::getDbo()->queryForRow($query);
        $this->assertEquals(true, !empty($result));
    }
}