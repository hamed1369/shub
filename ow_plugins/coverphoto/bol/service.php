<?php

/**
 *
 */

/**
 * coverphoto Service.
 * 
 * @author Yaser Alimardany <yaser.alimardany@gmail.com>
 * @package ow_plugins.coverphoto.bol
 * @since 1.0
 */
final class COVERPHOTO_BOL_Service
{
    /**
     * @var COVERPHOTO_BOL_CoverDao
     */
    private $coverDao;

    const COVER_CHANGE_GALLERY_LIMIT = 12;

    /**
     * Constructor.
     */
    private function __construct()
    {
        $this->coverDao = COVERPHOTO_BOL_CoverDao::getInstance();
    }
    /**
     * Singleton instance.
     *
     * @var COVERPHOTO_BOL_Service
     */
    private static $classInstance;

    /**
     * Returns an instance of class (singleton pattern implementation).
     *
     * @return COVERPHOTO_BOL_Service
     */
    public static function getInstance()
    {
        if ( self::$classInstance === null )
        {
            self::$classInstance = new self();
        }

        return self::$classInstance;
    }

    /***
     * @param $title
     * @param $hash
     * @param $addDateTime
     * @param int $from_top
     * @param null $coverPhotoHeight
     * @return COVERPHOTO_BOL_Cover
     */
    public function addCover($title, $hash, $addDateTime, $from_top = 0, $coverPhotoHeight= null)
    {
        $cover = new COVERPHOTO_BOL_Cover();
        $cover->userId = OW::getUser()->getId();
        $cover->hash = $hash;
        $cover->title = $title;
        $cover->addDateTime = $addDateTime;
        $cover->isCurrent = 1;

        $user_covers = $this->coverDao->findCoversByUserId(OW::getUser()->getId());
        if(count($user_covers)>0) {
            foreach ($user_covers as $key => $user_cover) {
                $user_covers[$key]->isCurrent = 0;
            }

            OW::getDbo()->batchInsertOrUpdateObjectList($this->coverDao->getTableName(), $user_covers);
        }
        $this->coverDao->save($cover);
        $this->addCroppedCover($cover, $from_top, $coverPhotoHeight);

        return $cover;
    }

    /***
     * @param $old_cover
     * @param $from_top
     * @param null $coverPhotoHeight
     * @return COVERPHOTO_BOL_Cover
     */
    public function addCroppedCover( $old_cover, $from_top, $coverPhotoHeight = null)
    {
        if($old_cover != null) {
            $user_cover_hash = $old_cover->hash;
            $user_cover_old_path = OW::getPluginManager()->getPlugin('coverphoto')->getUserFilesDir() . $user_cover_hash;
            $default_cover_height = 320;//$imageInformationHeight/($imageInformationWidth/$default_cover_width);

            if($coverPhotoHeight!=null && $coverPhotoHeight<$default_cover_height){
                $from_top = ($from_top * $default_cover_height)/$coverPhotoHeight;
            }
            $default_cover_width = 918;
            $imageInformation = getimagesize(OW::getPluginManager()->getPlugin('coverphoto')->getUserFilesDir() . $user_cover_hash);
//            $imageInformationWidth = $imageInformation[0];
//            $imageInformationHeight = $imageInformation[1];
            $tb = new ThumbAndCrop();
            $tb->openImg($user_cover_old_path); //original cover image

            $newHeight = $tb->getRightHeight($default_cover_width);
            if($newHeight<$default_cover_height){
                $default_cover_height = $newHeight;
            }
            $tb->creaThumb($default_cover_width, $newHeight);
            $tb->setThumbAsOriginal();
            $tb->cropThumb($default_cover_width, $default_cover_height, 0, $from_top);

            $imagesDir = OW::getPluginManager()->getPlugin('coverphoto')->getUserFilesDir();
            $new_cover_name = 'coverphoto_' . md5(rand()) . '.jpg';
            $imagePath = $imagesDir . $new_cover_name;

            $tb->saveThumb($imagePath); //save cropped cover image
            $tb->resetOriginal();
            $tb->closeImg();

            $old_cover->croppedHash = $new_cover_name;
            $this->coverDao->save($old_cover);
            return $old_cover;
        }else{

            $imagesDir = OW::getPluginManager()->getPlugin('coverphoto')->getUserFilesDir();
            $imageName = 'coverphoto_' . md5($_FILES['image']['name']) . '.jpg';
            $imagePath = $imagesDir . $imageName;

            $storage = OW::getStorage();
            if ($storage->fileExists($imagePath)) {
                $storage->removeFile($imagePath);
            }

            $pluginfilesDir = Ow::getPluginManager()->getPlugin('coverphoto')->getPluginFilesDir();
            $tmpImgPath = $pluginfilesDir . 'cover_photo' . uniqid() . '.jpg';

            $image = new UTIL_Image(OW::getPluginManager()->getPlugin('coverphoto')->getStaticDir() . 'img' . DS . 'empty_original_cover.jpg');
            $image->saveImage($tmpImgPath);

            //Copy file into storage folder
            $storage->copyFile($tmpImgPath, $imagePath);

            return $this->addCover('cover', $imageName, time(), $from_top, $coverPhotoHeight);
        }
    }

    public function deleteDatabaseRecord($id)
    {
        $this->coverDao->deleteById($id);
    }

    public function useThisCover($id)
    {
        $user_covers = $this->coverDao->findCoversByUserId(OW::getUser()->getId());

        if(count($user_covers)>0) {
            foreach ($user_covers as $key => $user_cover) {
                $user_covers[$key]->isCurrent = 0;
            }

            OW::getDbo()->batchInsertOrUpdateObjectList($this->coverDao->getTableName(), $user_covers);
        }

        $cover = $this->coverDao->findCoverById($id);
        $cover->isCurrent = 1;

        $this->coverDao->save($cover);
    }

    public function getUserCover($userId){
        return $this->coverDao->findCoverByUserId($userId);
    }

    /**
     *
     * @return array
     */
    public function findList($userId)
    {
        return $this->coverDao->findListOrderedByTitle($userId);
    }
}