<?php
	// require_once 'Mage/Checkout/Block/Cart.php';
	class AadhaarUp_CheckOut_Block_Cart extends Mage_Checkout_Block_Cart{
		public function getCheckoutUrl()
	    {
	        return $this->getUrl('checkout_with_aadhaarup/pay', array('_secure'=>true));
	    }
	}
?>