<?php
// TODO: Add unit tests
// @codeCoverageIgnoreStart

class CmsAdmin_ImagesController extends Zend_Controller_Action
{

    /***************************************************************************************/
    /* HTML EDITOR FUNCTIONS                                                               */
    /***************************************************************************************/

    /**
     * Provides an image browser feature for the rich text editor
     *
     * @return void
     */
    public function browseAction() {
        $params = Zend_Registry::get('params');

        // This is displayed inside the editor - so we need set a blank layout (no header/footer)
        $this->_helper->layout->setLayout('popup');

        $gallery = new Datasource_Cms_Gallery();
        $categoryID = $this->getRequest()->getParam('cid');
        $editorFuncNum = $this->getRequest()->getParam('CKEditorFuncNum');

        if ($this->getRequest()->isPost()) {
            // A new image has been sent - handle it
            $upload = new Zend_File_Transfer_Adapter_Http();
            $upload->setDestination($params->cms->imageUploadPath);
            $upload->addValidator('Extension', false, 'jpg,jpeg,png,gif');
            $upload->addValidator('Count', false, 1);
            $upload->addValidator('Size', false, 10240000);
            if ($upload->receive()) {
                // File has been uploaded succesfully
                $this->view->uploadSuccess=true;

                $imageFilename = $upload->getFileName(null,false);
                $imageLocation = $upload->getFileName();

                list($imageWidth, $imageHeight) = getimagesize($imageLocation);

                $imageID = $gallery->addNew(0,$imageFilename,$imageWidth,$imageHeight);

                // Resize and save a few pr
                $cmsImage = new Application_Cms_Image();
                if ($imageWidth>$imageHeight) {
                    $cmsImage->resamplePhoto($imageLocation,$params->cms->imageUploadPath.'/previews/200_'.$imageFilename,200,null);
                    $cmsImage->resamplePhoto($imageLocation,$params->cms->imageUploadPath.'/previews/400_'.$imageFilename,400,null);
                } else {
                    $cmsImage->resamplePhoto($imageLocation,$params->cms->imageUploadPath.'/previews/200_'.$imageFilename,null,150);
                    $cmsImage->resamplePhoto($imageLocation,$params->cms->imageUploadPath.'/previews/400_'.$imageFilename,null,400);
                }

                $this->_helper->getHelper('FlashMessenger')->addMessage(array('deleted' => true));
                // $this->_helper->getHelper('Redirector')->goToUrl('/cms-admin/images/edit?id='. $imageID); // Forward to image editor
                $this->_helper->getHelper('Redirector')->goToUrl('/cms-admin/images/browse?CKEditorFuncNum='.$editorFuncNum);
            } else {
                // An error occurred - deal with the error messages
                $errorMessages = $upload->getMessages();
            }
        } else {
            // No image uploaded - show the gallery
            if (!$categoryID) { $categoryID=0; }
            $imageList = $gallery->getImagesByCategoryID($categoryID);

            $this->view->imageList = $this->view->partialLoop('partials/image-browser-image.phtml',$imageList);
            $this->view->editorFuncNum = $editorFuncNum;
        }
    }


    public function editAction() {
        $params = Zend_Registry::get('params');
        $this->_helper->layout->setLayout('popup');

        $imageID = $this->getRequest()->getParam('id');
        $gallery = new Datasource_Cms_Gallery();
        $filename = $gallery->getFilenameByID($imageID);

        if ($this->getRequest()->isPost()) {
            $method = $this->getRequest()->getParam('method');
            if ($method=='crop') {
                // Crop the image
                // This is DIRTY - need to set validation and get the data through zend properly!!
                $x1 = $_POST["x1"];
                $y1 = $_POST["y1"];
                $x2 = $_POST["x2"]; // not really required as we have width, height and start co-ords
                $y2 = $_POST["y2"]; // not really required as we have width, height and start co-ords
                $w = $_POST["w"];
                $h = $_POST["h"];

                // We now have the co-ordinates and the width/height of the select box on the 400 preview image
                // we need to convert these values into a crop rectangle for the full size image

                $cmsImage = new Cms_Image();
                $imageLocation = $params->cms->imageUploadPath.'/'.$filename;

                list($originalWidth,$originalHeight) = getimagesize($imageLocation);
                $previewLocation = $params->cms->imageUploadPath.'/previews/400_'.$filename;
                list($previewWidth,$previewHeight) = getimagesize($previewLocation);

                // Calculate the crop factor we used to make the 400 preview from the original
                $previewScale = $originalWidth/$previewWidth;

                echo ("original dimensions = ".$originalWidth." x ".$originalHeight."<br>");
                echo ("preview dimensions = ".$previewWidth." x ".$previewHeight."<br>");
                echo ("scale factor = 1:".$previewScale."<br>");
                echo ("crop area = [x1:".$x1."] [x2:".$x2."] [y1:".$y1."] [y2:".$y2."] [width:".$w."] [height:".$h."]<br />");
                echo ("scaled crop area = x1:".$x1*$previewScale." x2:".$x2*$previewScale." y1:".$y1*$previewScale." y2:".$y2*$previewScale." width:".$w*$previewScale." height:".$h*$previewScale."<br>");
                die();

                /*
                if ($imageWidth>$imageHeight) {
                    $cmsImage->resamplePhoto($imageLocation,$params->cms->imageUploadPath.'/previews/200_'.$imageFilename,200,null);
                    $cmsImage->resamplePhoto($imageLocation,$params->cms->imageUploadPath.'/previews/400_'.$imageFilename,400,null);
                } else {
                    $cmsImage->resamplePhoto($imageLocation,$params->cms->imageUploadPath.'/previews/200_'.$imageFilename,null,150);
                    $cmsImage->resamplePhoto($imageLocation,$params->cms->imageUploadPath.'/previews/400_'.$imageFilename,null,400);
                }

                $cropped = resizeThumbnailImage($thumb_image_location, $large_image_location,$w,$h,$x1,$y1,$scale);
                */
            }
        }


        $this->view->imageName = $filename;
    }
}