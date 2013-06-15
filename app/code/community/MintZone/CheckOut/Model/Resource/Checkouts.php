<?php
	class Mypackage_Mymod_Model_Resource_Checkouts extends Mage_Core_Model_Mysql4_Abstract {
	    public function _construct()
	    {   
	        $this->_init('check_out/checkouts', 'id');
	    }
	}	
?>