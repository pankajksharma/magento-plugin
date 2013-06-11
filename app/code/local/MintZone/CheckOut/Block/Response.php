<?php
	class MintZone_CheckOut_Block_Response extends Mage_Checkout_Block_Cart {

		public function chooseTemplate()
	    {
	    	$responseData = $_SESSION['response'];
	    	if(isset($responseData['response_code'])){
	        	if($responseData['response_code']=="0")
	        		$this->setTemplate($this->getSuccessTemplate());
	        	else
	        		$this->setTemplate($this->getFailureTemplate());	
	        }
	        reset($_SESSION);
	    }
	}
?>