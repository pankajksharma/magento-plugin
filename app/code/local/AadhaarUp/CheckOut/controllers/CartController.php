<?php
require_once 'Mage/Checkout/controllers/CartController.php';
class AadhaarUp_CheckOut_CartController extends Mage_Checkout_CartController {        

	function indexAction(){
		$this->loadLayout();
		$this->renderLayout();
	}
}

?>