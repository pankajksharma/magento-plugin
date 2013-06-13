<?php
	class MintZone_CheckOut_Block_Response extends MintZone_CheckOut_Block_Abstraction {

		private $responseDataGlobal = null;

		public function chooseTemplate() {
	    	$responseData = $_SESSION['response'];
	    	$this->responseDataGlobal = $responseData;
        	if($this->check_secure_hash($responseData) && $responseData['response_code']=="0")
        		$this->setTemplate($this->getSuccessTemplate());
        	else
        		$this->setTemplate($this->getFailureTemplate());	

	        reset($_SESSION);
	    }

	    public function getParam($param){
	    	return $this->responseDataGlobal[$param];
	    }

	    public function getContactDetails(){
	    	return $this->contact_details;
	    }

	    private function check_secure_hash($responseData) {
	    	
	    	$txn_objects = Array("order_id", "amount", "response_url", "txnid");
	    	$add_objects = Array("name", "street", "city", "state", "country", "zipcode");
	    	
	    	sort($txn_objects);
	    	sort($add_objects);

	    	$secure_hash = $responseData['secure_hash'];
	    	
	    	$secret = $this->merchantSecret;
	    	$secret .= ('|'.$responseData['response_code']);
	    	foreach ($txn_objects as $txn_ele ) {
	    		$secret .= ('|'.$responseData[$txn_ele]);
	    	}
	    	$secret .= ('|'.$responseData['customer_email']);
	    	foreach ($add_objects as $add_ele) {
	    		$secret .= ('|'.$responseData['shipping_'.$add_ele]);
	    	}
	    	foreach ($add_objects as $add_ele) {
	    		$secret .= ('|'.$responseData['billing_'.$add_ele]);
	    	}

	    	return $secure_hash == strtoupper(md5($secret));
	    }

	}
?>