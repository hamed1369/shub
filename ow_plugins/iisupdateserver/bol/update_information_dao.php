<?php

/**
 * IIS Terms
 */

/**
 * Data Access Object for `UpdateInformation` table.
 *
 * @author Yaser Alimardany <yaser.alimardany@gmail.com>
 * @package ow_plugins.iisupdateserver.bol
 * @since 1.0
 */
class IISUPDATESERVER_BOL_UpdateInformationDao extends OW_BaseDao
{
    /**
     * Singleton instance.
     *
     * @var IISUPDATESERVER_BOL_UpdateInformationDao
     */
    private static $classInstance;

    /**
     * Returns an instance of class (singleton pattern implementation).
     *
     * @return IISUPDATESERVER_BOL_UpdateInformationDao
     */
    public static function getInstance()
    {
        if ( self::$classInstance === null )
        {
            self::$classInstance = new self();
        }

        return self::$classInstance;
    }

    /**
     * Constructor.
     */
    protected function __construct()
    {
        parent::__construct();
    }

    /**
     * @see OW_BaseDao::getDtoClassName()
     *
     */
    public function getDtoClassName()
    {
        return 'IISUPDATESERVER_BOL_UpdateInformation';
    }

    /**
     * @see OW_BaseDao::getTableName()
     *
     */
    public function getTableName()
    {
        return OW_DB_PREFIX . 'iisupdateserver_update_information';
    }

    /***
     * @param $key
     * @param $buildNumber
     * @return IISUPDATESERVER_BOL_UpdateInformation
     */
    public function hasExist($key, $buildNumber)
    {
        $ex = new OW_Example();
        $ex->andFieldEqual('key', $key);
        $ex->andFieldEqual('buildNumber', $buildNumber);
        $updateInformation = $this->findObjectByExample($ex);

        if($updateInformation == null){
            return false;
        }

        return true;
    }

    /***
     * @param null $key
     * @return array
     */
    public function getAllVersion($key = null){
        $ex = new OW_Example();
        if($key!=null) {
            $ex->andFieldEqual('key', $key);
        }
        $ex->setOrder('`buildNumber` DESC');
        return $this->findListByExample($ex);
    }

    /*
     * @param $key
     * @param $buildNumber
     * @return item
     */
    public function getItemByKeyAndBuildNumber($key,$buildNumber)
    {
        $ex = new OW_Example();
        $ex->andFieldEqual('key', $key);
        $ex->andFieldEqual('buildNumber', $buildNumber);
        $item = $this->findObjectByExample($ex);
        return $item;
    }

    private static function deleteVersionsFolders($dir){
        $it = new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS);
        $files = new RecursiveIteratorIterator($it,
            RecursiveIteratorIterator::CHILD_FIRST);
        foreach($files as $file) {
            if ($file->isDir()){
                rmdir($file->getRealPath());
            } else {
                unlink($file->getRealPath());
            }
        }
        rmdir($dir);
    }
    public function deleteAllVersions(){
        $dir = 'ow_pluginfiles' . DIRECTORY_SEPARATOR . 'iisupdateserver' .DIRECTORY_SEPARATOR . 'core';
        if(is_dir($dir))
            $this->deleteVersionsFolders($dir);
        $dir = 'ow_pluginfiles' . DIRECTORY_SEPARATOR . 'iisupdateserver' .DIRECTORY_SEPARATOR . 'plugins';
        if(is_dir($dir))
            $this->deleteVersionsFolders($dir);
        $dir = 'ow_pluginfiles' . DIRECTORY_SEPARATOR . 'iisupdateserver' .DIRECTORY_SEPARATOR . 'themes';
        if(is_dir($dir))
            $this->deleteVersionsFolders($dir);
        $sql = 'TRUNCATE TABLE ' . $this->getTableName();
        $this->dbo->delete($sql);
        $this->clearCache();
    }

    public function deleteVersion($buildNumber,$key){
        if(!isset($buildNumber) || !isset($key) ){
            return false;
        }
        else{
            $coreMainDir = 'ow_pluginfiles' . DIRECTORY_SEPARATOR . 'iisupdateserver' .DIRECTORY_SEPARATOR . $key. DIRECTORY_SEPARATOR .'main'.DIRECTORY_SEPARATOR . $buildNumber ;
            $coreUpdateDir = 'ow_pluginfiles' . DIRECTORY_SEPARATOR . 'iisupdateserver' .DIRECTORY_SEPARATOR . $key. DIRECTORY_SEPARATOR .'updates'.DIRECTORY_SEPARATOR . $buildNumber ;
            $themeDir = 'ow_pluginfiles' . DIRECTORY_SEPARATOR . 'iisupdateserver' .DIRECTORY_SEPARATOR . 'themes'.DIRECTORY_SEPARATOR .$key.DIRECTORY_SEPARATOR . $buildNumber ;
            $pluginDir = 'ow_pluginfiles' . DIRECTORY_SEPARATOR . 'iisupdateserver' .DIRECTORY_SEPARATOR . 'plugins'.DIRECTORY_SEPARATOR .$key.DIRECTORY_SEPARATOR . $buildNumber ;
            if(is_dir($coreMainDir))
            {
                $this->deleteVersionsFolders($coreMainDir);
                if(is_dir($coreUpdateDir)) {
                    $this->deleteVersionsFolders($coreUpdateDir);
                }
                return true;
            }
            elseif(is_dir($themeDir))
            {
                $this->deleteVersionsFolders($themeDir);
                return true;
            }
            elseif(is_dir($pluginDir))
            {
                $this->deleteVersionsFolders($pluginDir);
                return true;
            }
            return false;
        }
    }
    public function deleteItem($item,$buildNumber,$key){
        if(isset($item)) {
            $this->deleteById($item->id);
        }
        $result = $this->deleteVersion($buildNumber,$key);
        return $result;
    }
}