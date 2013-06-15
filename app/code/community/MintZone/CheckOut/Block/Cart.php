<?php
	// require_once 'Mage/Checkout/Block/Cart.php';
	class MintZone_CheckOut_Block_Cart extends MintZone_CheckOut_Block_Abstraction{
		public function getCheckoutUrl()
	    {
	        return $this->getUrl('checkout_with_mintzone/pay', array('_secure'=>true));
	    }
	}
?>