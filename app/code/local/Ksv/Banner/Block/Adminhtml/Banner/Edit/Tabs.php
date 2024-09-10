<?php
class Ksv_Banner_Block_Adminhtml_Banner_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('banner_tabs');
	  $this->setName('banner_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('banner')->__('Banner Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('general_section', array(
          'label'     => Mage::helper('banner')->__('General Information'),
          'title'     => Mage::helper('banner')->__('General Information'),
          'content'   => $this->getLayout()->createBlock('banner/adminhtml_banner_edit_tab_form')->toHtml(),
      ));	  
	  
	  
		$content = Mage::getSingleton('core/layout')->createBlock('banner/adminhtml_banner_edit_tab_gallery');
        $content->setId($this->getHtmlId() . '_content')->setElement($this);       
	   
		$this->addTab('gallery_section', array(
            'label'     => Mage::helper('banner')->__('Banner Images'),
            'title'     => Mage::helper('banner')->__('Banner Images'),
            'content'   => $content->toHtml(),
        ));
	   
	   
		 $this->addTab('page_section', array(
            'label'     => Mage::helper('banner')->__('Display on Pages'),
            'title'     => Mage::helper('banner')->__('Display on Pages'),
            'content'   => $this->getLayout()->createBlock('banner/adminhtml_banner_edit_tab_page')->toHtml(),
        ));
		 $this->addTab('category_section', array(
            'label'     => Mage::helper('banner')->__('Display on Categories'),
            'title'     => Mage::helper('banner')->__('Display on Categories'),
            'content'   => $this->getLayout()->createBlock('banner/adminhtml_banner_edit_tab_category')->toHtml(),
        ));  
     
      return parent::_beforeToHtml();
  }
}
