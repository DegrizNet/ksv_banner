<?php
class Ksv_Banner_Block_Banner extends Mage_Core_Block_Template
{
	protected $_position = null;
    protected $_isActive = 1;
    protected $_collection;

    protected function _getCollection($position = null) {
		$enabled = Mage::getStoreConfig('banner/general/active');
		if($enabled){

			$storeId = Mage::app()->getStore()->getId();
			$this->_collection = Mage::getModel('banner/banner')->getCollection()
					->addEnableFilter($this->_isActive);
			if (!Mage::app()->isSingleStoreMode()) {
				$this->_collection->addStoreFilter($storeId);
			}

			if($this->getData('position') == 'INSTAGRAM') {
				return $this->_collection;
			}

			if (Mage::registry('current_category')) {
				$_categoryId = Mage::registry('current_category')->getId();
				$this->_collection->addCategoryFilter($_categoryId);
				return $this->_collection;
			} elseif (Mage::app()->getFrontController()->getRequest()->getRouteName() == 'cms') {
				$_pageId = Mage::getBlockSingleton('cms/page')->getPage()->getPageId();
				$this->_collection->addPageFilter($_pageId);
				return $this->_collection;
			}



		} else	{
			return '';
		}
    }
	
	public function _getBannerImageCollection($bannerCollection){
		return $bannerCollection->addPositionFilter($this->getData('position'))->getData();	
	}
	
	public function getSortedImages($content){
		$imagesArray = json_decode($content,true);
		if(isset($imagesArray) && !empty($imagesArray) && count($imagesArray)>0){
			$temp = array();
			foreach($imagesArray as $key=>$image){
				if($image['disabled']){
					unset($imagesArray[$key]);
					continue;
				}
				$temp[$key] = $image['position'];
			}				
			array_multisort($temp, SORT_ASC, $imagesArray);
		}
		return $imagesArray;
	}
}