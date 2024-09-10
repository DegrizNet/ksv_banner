<?php

class Ksv_Banner_Adminhtml_Banner_BannerController extends
    Mage_Adminhtml_Controller_Action
{

    protected function _isAllowed()
    {
        return true;
    }

    public function uploadAction()
    {
        try {
            $uploader = new Mage_Core_Model_File_Uploader('file');
            $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
            $uploader->addValidateCallback('catalog_product_image',
                                           Mage::helper('catalog/image'), 'validateUploadFile');
            $uploader->setAllowRenameFiles(true);
            $uploader->setFilesDispersion(true);
            $uploader->addValidateCallback(
                Mage_Core_Model_File_Validator_Image::NAME,
                Mage::getModel('core/file_validator_image'),
                'validate'
            );
            $result = $uploader->save(
                $this->getBaseTmpMediaPath()
            );

            Mage::dispatchEvent('catalog_product_gallery_upload_image_after', array(
                'result' => $result,
                'action' => $this
            ));

            /**
             * Workaround for prototype 1.7 methods "isJSON", "evalJSON" on Windows OS
             */
            $result['tmp_name'] = str_replace(DS, "/", $result['tmp_name']);
            $result['path']     = str_replace(DS, "/", $result['path']);
            $tempUrl            = $this->_prepareFileForUrl($result['file']);
            if ( substr($tempUrl, 0, 1) == '/' ) {
                $tempUrl = substr($tempUrl, 1);
            }
            $result['url'] = $this->getBaseTmpMediaUrl() . '/' . $tempUrl;
            /* $result['url'] = Mage::getSingleton('catalog/product_media_config')->getTmpMediaUrl($result['file']); */

            $result['file']   = $result['file'];
            $result['cookie'] = array(
                'name'     => session_name(),
                'value'    => $this->_getSession()->getSessionId(),
                'lifetime' => $this->_getSession()->getCookieLifetime(),
                'path'     => $this->_getSession()->getCookiePath(),
                'domain'   => $this->_getSession()->getCookieDomain(),
            );

        } catch (Exception $e) {
            $result = array(
                'error' => $e->getMessage(),
                'errorcode' => $e->getCode());
        }

        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    public function getBaseTmpMediaUrl()
    {
        return Mage::getBaseUrl('media') . 'banner';
    }

    public function getBaseTmpMediaPath()
    {
        return Mage::getBaseDir('media') . DS . 'banner';
    }


    protected function _prepareFileForUrl( $file )
    {
        return str_replace(DS, '/', $file);
    }

    protected function _initAction()
    {
        $this->loadLayout()
             ->_setActiveMenu('banner/items')
             ->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Banner Manager'));

        return $this;
    }

    public function indexAction()
    {
        $this->_initAction()
             ->renderLayout();
    }

    public function editAction()
    {
        $id    = $this->getRequest()->getParam('id');
        $model = Mage::getModel('banner/banner')->load($id);

        if ( $model->getId() || $id == 0 ) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if ( !empty($data) ) {
                $model->setData($data);
            }
            //echo '<pre>';
            //$model->setData('stores',json_decode($model->getData('stores')));
            //print_R($model->getData());exit;
            Mage::register('banner_data', $model);

            $this->loadLayout();
            $this->_setActiveMenu('banner/items');

            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Banner Manager'), Mage::helper('adminhtml')->__('Banner Manager'));

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

            $this->_addContent($this->getLayout()->createBlock('banner/adminhtml_banner_edit'))
                 ->_addLeft($this->getLayout()->createBlock('banner/adminhtml_banner_edit_tabs'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('banner')->__('Banner does not exist'));
            $this->_redirect('*/*/');
        }
    }

    public function newAction()
    {
        $this->_title($this->__('New Banner'));

        $_model = Mage::getModel('banner/banner');
        Mage::register('banner_data', $_model);
        Mage::register('current_banner', $_model);

        $this->_initAction();
        $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Banner Manager'), Mage::helper('adminhtml')->__('Banner Manager'), $this->getUrl('*'));
        $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Add Banner'), Mage::helper('adminhtml')->__('Add Banner'));

        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

        $this->_addContent($this->getLayout()->createBlock('banner/adminhtml_banner_edit'))
             ->_addLeft($this->getLayout()->createBlock('banner/adminhtml_banner_edit_tabs'));

        $this->renderLayout();
    }

    public function saveAction()
    {

        if ( $data = $this->getRequest()->getPost() ) {
            if ( isset($_FILES['filename']['name']) && $_FILES['filename']['name'] != '' ) {
                try {
                    /* Starting upload */
                    $uploader = new Varien_File_Uploader('filename');

                    // Any extention would work
                    $uploader->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png'));
                    $uploader->setAllowRenameFiles(false);

                    // Set the file upload mode
                    // false -> get the file directly in the specified folder
                    // true -> get the file in the product like folders
                    //	(file.jpg will go in something like /media/f/i/file.jpg)
                    $uploader->setFilesDispersion(false);

                    // We set media as the upload dir
                    $path = Mage::getBaseDir('media') . DS;
                    $uploader->save($path, $_FILES['filename']['name']);

                } catch ( Exception $e ) {

                }

                //this way the name is saved in DB
                $data['filename'] = $_FILES['filename']['name'];
            }


            $model = Mage::getModel('banner/banner');
            if ( isset($data['banner_tabs']['images']) && !empty($data['banner_tabs']['images']) ) {
                $images = Mage::helper('core')->jsonDecode($data['banner_tabs']['images'], true);
                //$images = json_decode($data['banner_tabs']['images'],true);
                $newArray = array();
                foreach ( $images as $key => $image ) {
                    if ( $image['removed'] != 1 ) {
                        $newArray[] = $image;
                    }
                }
                $content         = Mage::helper('core')->jsonEncode($newArray);
                $data['content'] = $content;
                unset($data['banner_tabs']['images']);
            }

            if ( isset($data['stores']) && !empty($data['stores']) ) {
                if ( in_array('0', $data['stores']) ) {
                    $data['stores'] = array(0);
                }

                /* $stores = Mage::helper('core')->jsonEncode($data['stores']);	 */
                $data['stores'] = implode(',', $data['stores']);
            }
            if ( isset($data['categories']) ) {
                $data['categories'] = explode(',', $data['categories']);
                if ( is_array($data['categories']) ) {
                    $categoryIds = array_unique($data['categories']);
                    if ( empty($categoryIds) ) {
                        $data['category_id'] = '';
                    } else {
                        //$data['category_id'] = Mage::helper('core')->jsonEncode($categoryIds);
                        $data['category_id'] = implode(',', $categoryIds);
                    }

                }
            }
            if ( isset($data['pages']) && !empty($data['pages']) ) {
                if ( empty($data['pages'][0]) ) {
                    unset($data['pages'][0]);
                }
                if ( !empty($data['pages']) ) {
                    //$pageIds = Mage::helper('core')->jsonEncode($data['pages']);
                    $pageIds         = implode(',', $data['pages']);
                    $data['page_id'] = $pageIds;
                } else {
                    $data['page_id'] = '';
                }
            }

            $model->setData($data)->setId($this->getRequest()->getParam('id'));

            try {
                if ( $model->getCreatedTime == null || $model->getUpdateTime() == null ) {
                    $model->setCreatedTime(now())
                          ->setUpdateTime(now());
                } else {
                    $model->setUpdateTime(now());
                }

                $model->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('banner')->__('Banner was successfully saved'));
                Mage::getSingleton('adminhtml/session')->setFormData(false);

                if ( $this->getRequest()->getParam('back') ) {
                    $this->_redirect('*/*/edit', array('id' => $model->getId()));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch ( Exception $e ) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('banner')->__('Unable to find Banner to save'));
        $this->_redirect('*/*/');
    }

    public function deleteAction()
    {
        if ( $this->getRequest()->getParam('id') > 0 ) {
            try {
                $model = Mage::getModel('banner/banner');

                $model->setId($this->getRequest()->getParam('id'))
                      ->delete();

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Banner was successfully deleted'));
                $this->_redirect('*/*/');
            } catch ( Exception $e ) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }

    public function massDeleteAction()
    {
        $bannerIds = $this->getRequest()->getParam('banner');
        if ( !is_array($bannerIds) ) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select Banner(s)'));
        } else {
            try {
                foreach ( $bannerIds as $bannerId ) {
                    $banner = Mage::getModel('banner/banner')->load($bannerId);
                    $banner->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($bannerIds)
                    )
                );
            } catch ( Exception $e ) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    public function massStatusAction()
    {
        $bannerIds = $this->getRequest()->getParam('banner');
        if ( !is_array($bannerIds) ) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select Banner(s)'));
        } else {
            try {
                foreach ( $bannerIds as $bannerId ) {
                    $banner = Mage::getSingleton('banner/banner')
                                  ->load($bannerId)
                                  ->setStatus($this->getRequest()->getParam('status'))
                                  ->setIsMassupdate(true)
                                  ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($bannerIds))
                );
            } catch ( Exception $e ) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    public function categoriesJsonAction()
    {
        $bannerId = $this->getRequest()->getParam('id');
        $_model   = Mage::getModel('banner/banner')->load($bannerId);
        Mage::register('banner_data', $_model);

        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('banner/adminhtml_banner_edit_tab_category')
                 ->getCategoryChildrenJson($this->getRequest()->getParam('category'))
        );
    }
}
