<?php
class Ksv_Banner_Block_Adminhtml_Banner_Edit_Tab_Page extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $_model = Mage::registry('banner_data');
		if($_model->getPageId())
		{		
			//$_model->setPageId(Mage::helper('core')->jsonDecode($_model->getPageId()));
			$_model->setPageId(explode(',',$_model->getPageId()));
		}
        $form = new Varien_Data_Form();
        $this->setForm($form);

        $fieldset = $form->addFieldset('banner_form', array('legend'=>Mage::helper('banner')->__('Banner Pages')));
        $fieldset->addField('pages', 'multiselect', array(
            'label'     => Mage::helper('banner')->__('Visible In'),
            //'required'  => true,
            'name'      => 'pages[]',
            'values'    => Mage::getSingleton('banner/config_source_page')->toOptionArray(),
            'value'     => $_model->getPageId()
        ));
        
        return parent::_prepareForm();
    }
}
