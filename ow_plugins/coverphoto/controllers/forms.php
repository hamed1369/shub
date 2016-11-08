<?php

class COVERPHOTO_CTRL_Forms extends COVERPHOTO_CLASS_ActionController
{

    public function index()
    {
        $language = OW::getLanguage();

        OW::getDocument()->setTitle($language->text("coverphoto", "forms_page_title"));
        OW::getDocument()->setHeading($language->text("coverphoto", "forms_page_heading"));

        $form = new COVERPHOTO_CLASS_Form("coverphoto_form");
        $form->setMethod(Form::METHOD_POST);
        $form->setEnctype(Form::ENCTYPE_MULTYPART_FORMDATA);

        $this->addForm($form);

        $form->getElement("title");

        if (OW::getRequest()->isPost() && $form->isValid($_POST)) {
            $values = $form->getValues();

            if ((int)$_FILES['image']['error'] !== 0 || !is_uploaded_file($_FILES['image']['tmp_name']) || !UTIL_File::validateImage($_FILES['image']['name'])) {
                if (!is_uploaded_file($_FILES['image']['tmp_name'])) {
                    OW::getFeedback()->error(OW::getLanguage()->text("coverphoto", "empty_image"));
                    $this->redirect();
                }

                $imageValid = false;
                OW::getFeedback()->error($language->text('coverphoto', 'not_valid_image'));
                $this->redirect();
            } else {
                $imageValid = true;
            }

            $sizeValidator = UTIL_File::checkUploadedFile($_FILES['image']);
            if(!$sizeValidator['result']){
                $imageValid = false;
                OW::getFeedback()->error($sizeValidator['message']);
                $this->redirect();
            }else if((int) $_FILES['image']['size'] > (float) OW::getConfig()->getValue('base', 'tf_max_pic_size') * 1024 * 1024){
                $imageValid = false;
                OW::getFeedback()->error(OW::getLanguage()->text('base', 'upload_file_max_upload_filesize_error'));
                $this->redirect();
            }

            if ($imageValid) {

                //get configured file storage (Cloud files or file system drive, depends on settings in config file)
                $storage = OW::getStorage();

                $imagesDir = OW::getPluginManager()->getPlugin('coverphoto')->getUserFilesDir();
                $imageName = 'coverphoto_' . md5($_FILES['image']['name']. time()) . '.jpg';
                $imagePath = $imagesDir . $imageName;

                if ($storage->fileExists($imagePath)) {
                    $storage->removeFile($imagePath);
                }

                $pluginfilesDir = Ow::getPluginManager()->getPlugin('coverphoto')->getPluginFilesDir();
                $tmpImgPath = $pluginfilesDir . 'cover_photo' . uniqid() . '.jpg';

                $image = new UTIL_Image($_FILES['image']['tmp_name']);
                $image->saveImage($tmpImgPath);

                unlink($_FILES['image']['tmp_name']);

                //Copy file into storage folder
                $storage->copyFile($tmpImgPath, $imagePath);

                unlink($tmpImgPath);
            }

            $this->service->addCover($values["title"], $imageName, time());

            OW::getFeedback()->info(OW::getLanguage()->text("coverphoto", "database_record_saved_info"));

            $this->redirect();
        }

        OW::getDocument()->addStyleSheet(OW::getPluginManager()->getPlugin('coverphoto')->getStaticCssUrl() . 'coverphoto.css');

        $user_cover = COVERPHOTO_BOL_Service::getInstance()->getUserCover(OW::getUser()->getId());
        $list = $this->service->findList(OW::getUser()->getId());
        $tplList = array();
        foreach ($list as $listItem) {
            /* @var $listItem COVERPHOTO_BOL_Record */
            $autherUserName = BOL_UserService::getInstance()->findUserById($listItem->userId)->getUsername();
            $tplList[] = array(
                "title" => $listItem->title,
                "AutherName" => $autherUserName,
                "AutherUrl" => OW::getRouter()->urlForRoute('base_user_profile', array('username' => $autherUserName)),
                "addDateTime" => UTIL_DateTime::formatDate($listItem->addDateTime),
                "coverPhotoImageUrl" => OW::getStorage()->getFileUrl(OW::getPluginManager()->getPlugin('coverphoto')->getUserFilesDir() . $listItem->hash),
                "useThisCoverIcon" => OW::getPluginManager()->getPlugin('coverphoto')->getStaticUrl() . 'img/' . 'choose.png',
                "deleteThisCoverIcon" => OW::getPluginManager()->getPlugin('coverphoto')->getStaticUrl() . 'img/' . 'remove.png',
                "deleteUrl" => OW::getRouter()->urlForRoute('coverphoto-forms-delete-item', array('id' => $listItem->getId())),
                "useCoverUrl" => OW::getRouter()->urlForRoute('coverphoto-forms-use-item', array('id' => $listItem->getId())),
                "isCurrent" => ($user_cover && $user_cover->id == $listItem->id) ? true : false
            );
        }
        $this->assign("is_current_icon", OW::getPluginManager()->getPlugin('coverphoto')->getStaticUrl() . 'img/' . 'is_current.png');
        $this->assign("list", $tplList);
    }

    public function deleteItem($params)
    {
        $this->service->deleteDatabaseRecord($params['id']);

        OW::getFeedback()->info(OW::getLanguage()->text('coverphoto', 'database_record_deleted'));

        $this->redirect(OW::getRouter()->urlForRoute('coverphoto-forms'));
    }

    public function useItem($params)
    {
        $this->service->useThisCover($params['id']);

        OW::getFeedback()->info(OW::getLanguage()->text('coverphoto', 'database_record_used'));

        $this->redirect(OW::getRouter()->urlForRoute('coverphoto-forms'));
    }


    public function deleteItemFloat($params)
    {
        $this->service->deleteDatabaseRecord($params['id']);

        OW::getFeedback()->info(OW::getLanguage()->text('coverphoto', 'database_record_deleted'));

        $this->redirect(OW::getRouter()->urlForRoute('base_user_profile', array('username' => OW::getUser()->getUserObject()->username)));
    }

    public function useItemFloat($params)
    {

        $this->service->useThisCover($params['id']);

        OW::getFeedback()->info(OW::getLanguage()->text('coverphoto', 'database_record_used'));
        $this->redirect(OW::getRouter()->urlForRoute('base_user_profile', array('username' => OW::getUser()->getUserObject()->username)));
    }

    public function coverCrop()
    {
        $user_cover = COVERPHOTO_BOL_Service::getInstance()->getUserCover(OW::getUser()->getId());
        $this->service->addCroppedCover($user_cover, abs($_POST['pos']), abs($_POST['cover_photo_height']));

        $responseJson = Array(
            "status" => 200,
            "url" => OW::getPluginManager()->getPlugin('coverphoto')->getUserFilesDir() . $user_cover->croppedHash
        );
        exit(json_encode($responseJson));
    }
}

class ThumbAndCrop
{

    private $handleimg;
    private $original = "";
    private $handlethumb;
    private $oldoriginal;

    /*
        Apre l'immagine da manipolare
    */
    public function openImg($file)
    {
        $this->original = $file;

        if ($this->extension($file) == 'jpg' || $this->extension($file) == 'jpeg') {
            $this->handleimg = imagecreatefromjpeg($file);
        } elseif ($this->extension($file) == 'png') {
            $this->handleimg = imagecreatefrompng($file);
        } elseif ($this->extension($file) == 'gif') {
            $this->handleimg = imagecreatefromgif($file);
        } elseif ($this->extension($file) == 'bmp') {
            $this->handleimg = imagecreatefromwbmp($file);
        }
    }

    /*
        Ottiene la larghezza dell'immagine
    */
    public function getWidth()
    {
        return imageSX($this->handleimg);
    }

    /*
        Ottiene la larghezza proporzionata all'immagine partendo da un'altezza
    */
    public function getRightWidth($newheight)
    {
        $oldw = $this->getWidth();
        $oldh = $this->getHeight();

        $neww = ($oldw * $newheight) / $oldh;

        return $neww;
    }

    /*
        Ottiene l'altezza dell'immagine
    */
    public function getHeight()
    {
        return imageSY($this->handleimg);
    }

    /*
        Ottiene l'altezza proporzionata all'immagine partendo da una larghezza
    */
    public function getRightHeight($newwidth)
    {
        $oldw = $this->getWidth();
        $oldh = $this->getHeight();

        $newh = ($oldh * $newwidth) / $oldw;

        return $newh;
    }

    /*
        Crea una miniatura dell'immagine
    */
    public function creaThumb($newWidth, $newHeight)
    {
        $oldw = $this->getWidth();
        $oldh = $this->getHeight();

        $this->handlethumb = imagecreatetruecolor($newWidth, $newHeight);

        return imagecopyresampled($this->handlethumb, $this->handleimg, 0, 0, 0, 0, $newWidth, $newHeight, $oldw, $oldh);
    }

    /*
        Ritaglia un pezzo dell'immagine
    */
    public function cropThumb($width, $height, $x, $y)
    {
        $oldw = $this->getWidth();
        $oldh = $this->getHeight();

        $this->handlethumb = imagecreatetruecolor($width, $height);

        return imagecopy($this->handlethumb, $this->handleimg, 0, 0, $x, $y, $width, $height);
    }

    /*
        Salva su file la Thumbnail
    */
    public function saveThumb($path, $qualityJpg = 100)
    {
        if ($this->extension($this->original) == 'jpg' || $this->extension($this->original) == 'jpeg') {
            return imagejpeg($this->handlethumb, $path, $qualityJpg);
        } elseif ($this->extension($this->original) == 'png') {
            return imagepng($this->handlethumb, $path);
        } elseif ($this->extension($this->original) == 'gif') {
            return imagegif($this->handlethumb, $path);
        } elseif ($this->extension($this->original) == 'bmp') {
            return imagewbmp($this->handlethumb, $path);
        }
    }

    /*
        Stampa a video la Thumbnail
    */
    public function printThumb()
    {
        if ($this->extension($this->original) == 'jpg' || $this->xtension($this->original) == 'jpeg') {
            header("Content-Type: image/jpeg");
            imagejpeg($this->handlethumb);
        } elseif ($this->extension($this->original) == 'png') {
            header("Content-Type: image/png");
            imagepng($this->handlethumb);
        } elseif ($this->extension($this->original) == 'gif') {
            header("Content-Type: image/gif");
            imagegif($this->handlethumb);
        } elseif ($this->extension($this->original) == 'bmp') {
            header("Content-Type: image/bmp");
            imagewbmp($this->handlethumb);
        }
    }

    /*
        Distrugge le immagine per liberare le risorse
    */
    public function closeImg()
    {
        imagedestroy($this->handleimg);
        imagedestroy($this->handlethumb);
    }

    /*
        Imposta la thumbnail come immagine sorgente,
        in questo modo potremo combinare la funzione crea con la funzione crop
    */
    public function setThumbAsOriginal()
    {
        $this->oldoriginal = $this->handleimg;
        $this->handleimg = $this->handlethumb;
    }

    /*
        Resetta l'immagine originale
    */
    public function resetOriginal()
    {
        $this->handleimg = $this->oldoriginal;
    }

    /*
        Estrae l'estensione da un file o un percorso
    */
    private function extension($percorso)
    {
        if (eregi("[\|\\]", $percorso)) {
            // da percorso
            $nome = $this->nomefile($percorso);

            $spezzo = explode(".", $nome);

            return strtolower(trim(array_pop($spezzo)));
        } else {
            //da file
            $spezzo = explode(".", $percorso);

            return strtolower(trim(array_pop($spezzo)));
        }
    }

    /*
        Estrae il nome del file da un percorso
    */
    private function nomefile($path, $ext = true)
    {
        $diviso = spliti("[/|\\]", $path);

        if ($ext) {
            return trim(array_pop($diviso));
        } else {
            $nome = explode(".", trim(array_pop($diviso)));

            array_pop($nome);

            return trim(implode(".", $nome));
        }
    }
}