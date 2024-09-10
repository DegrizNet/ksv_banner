<?php
class Ksv_Banner_Model_Observer extends Varien_Object
{

   public function prepareLayoutBefore(Varien_Event_Observer $observer)
   {
	
       /* @var $block Mage_Page_Block_Html_Head */
	   if (!Mage::helper('banner')->isJqueryEnabled()) {
            return $this;
        }
       $block = $observer->getEvent()->getBlock();

       if ("head" == $block->getNameInLayout()) {
           foreach (Mage::helper('banner')->getFiles() as $file) {
               $block->addJs(Mage::helper('banner')->getJQueryPath($file));
           }
       }
       return $this;
   }
}
