<?php
class Ksv_Banner_Model_Config_Source_Position
{
    const CONTENT_TOP       = 'CONTENT_TOP';
    const CONTENT_BOTTOM    = 'CONTENT_BOTTOM';
	const CONTENT_WIDGET    = 'CONTENT_WIDGET';
    const CATEGORIES    = 'CATEGORIES';
    const INSTAGRAM    = 'INSTAGRAM';

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => self::CONTENT_TOP, 'label'=>Mage::helper('adminhtml')->__('Content Top')),
            array('value' => self::CONTENT_BOTTOM, 'label'=>Mage::helper('adminhtml')->__('Content Bottom')),
			array('value' => self::CONTENT_WIDGET, 'label'=>Mage::helper('adminhtml')->__('Anywhere by CMS Widget')),
            array('value' => self::INSTAGRAM, 'label'=>Mage::helper('adminhtml')->__('Instagram')),
            array('value' => self::CATEGORIES, 'label'=>Mage::helper('adminhtml')->__('Categories'))
        );
    }
}
