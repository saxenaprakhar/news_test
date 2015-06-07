<?php
class Prakhar_Customcheck_Model_Mysql4_Type_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract{

    public function _construct(){

        $this->_init('prakhar_customcheck/type');
        parent::_construct();
    }
}