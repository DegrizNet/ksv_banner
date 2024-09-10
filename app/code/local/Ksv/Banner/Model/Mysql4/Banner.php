<?php

class Ksv_Banner_Model_Mysql4_Banner extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the bannernext_id refers to the key field in your database table.
        $this->_init('banner/banner', 'banner_id');
    }
}
