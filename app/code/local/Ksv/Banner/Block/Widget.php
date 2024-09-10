<?php
class Ksv_Banner_Block_Widget extends Mage_Core_Block_Template implements Mage_Widget_Block_Interface
{
	protected $_collection;
	protected $_isActive = 1;
	
	protected function _construct() {
		parent::_construct();
	}
	
	protected function _getCollection() {
		
		if(!Mage::helper('banner')->isBannerModuleEnabled()){
			return new Varien_Data_Collection();
		}
		/* if ($this->_collection) {
			return $this->_collection;
		} */
		$storeId = Mage::app()->getStore()->getId();
		$bannerId = $this->getData('banner_id');
		$this->_collection = Mage::getModel('banner/banner')->getCollection();
		$this->_collection->addEnableFilter($this->_isActive);
		$this->_collection->addStoreFilter($storeId);
		$this->_collection->addFieldToFilter('banner_id',$bannerId);
		return $this->_collection;		
    }
	
	public function _getBannerImageCollection($bannerCollection){
		return $bannerCollection->getData();	
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
