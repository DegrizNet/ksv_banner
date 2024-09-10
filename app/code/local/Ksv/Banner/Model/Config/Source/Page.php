<?php 
class Ksv_Banner_Model_Config_Source_Page
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $_collection = Mage::getSingleton('cms/page')->getCollection()->addFieldToFilter('is_active', 1);
        $_result = array();
		$_result[] =array(
			'value' =>'',
			'label' =>'',
		);
        foreach ($_collection as $item) {
            $data = array(
                'value' => $item->getData('page_id'),
                'label' => $item->getData('title'));
            $_result[] = $data;
        }
        return $_result;
    }
}
