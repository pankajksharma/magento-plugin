<?php
	class AadhaarUp_CheckOut_Block_Pay extends Mage_Checkout_Block_Cart {
		
		private $merchantKey="";
		private $merchantSecret = 'c252784899fe8e43a94abeddd1b7fd00';

		private function checksum(){
			$data = array(
		        "order_id" => $this->getOrderId(),
		        "amount" =>  $this->getAmount(),
		        "response_url" => $this->getResponseURL(),
		        "key" => $this->getMerchantKey()
	      	);
	      	$secret = $merchantSecret;
	      	foreach ($data.sort() as $data => $value) {
	      		$secret.=('|'.$value);
	      	}
	      	return md5($secret);
		}

	    public function chooseTemplate()
	    {
	        $itemsCount = $this->getItemsCount() ? $this->getItemsCount() : $this->getQuote()->getItemsCount();
	        if ($itemsCount) {
	            $this->setTemplate($this->getPayTemplate());
	        } else {
	            $this->setTemplate($this->getEmptyTemplate());
	        }
	    }
	    public function getPaymentUrl(){
	    	return "http://test.mintzone.in/api/v1/transactions";
	    }
	    public function getMerchantKey(){
	    	return $merchantKey;
	    }
	    public function getAmount(){
	    	return Mage::getModel('checkout/cart')->getQuote()->getGrandTotal();
	    }
	    public function getResponseURL(){
	    	return $this->getUrl('checkout_with_aadhaarup/response');
	    }
	    public function getOrderId(){
	    	return $order = Mage::getModel('sales/order')->load($orderId);
	    }
	    public function getCheckSum(){
	    	return $this->checksum();
	    }
	}
?>