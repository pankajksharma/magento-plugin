<?php
	class MintZone_CheckOut_Block_Pay extends Mage_Checkout_Block_Cart {
		
		private $aadadharup_url = "http://127.0.0.1:3001/api/v1/transactions";
		private $merchantKey="d00414734592ad6cb18cf5b75a654616";
		private $merchantSecret = '3795a80f96d6df4bcb0ac48e04c58efb';

		private function checksum(){
			$data = array(
		        "order_id" => $this->getOrderId(),
		        "amount" =>  $this->getAmount(),
		        "response_url" => $this->getResponseURL(),
		        "key" => $this->merchantKey
	      	);

	      	$secret = $this->merchantSecret;
	      	ksort($data);
	      	foreach ( $data as $data => $value) {
	      		$secret.=('|'.$value);
	      	}
	      	return strtoupper(md5($secret));
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
	    	return $this->aadadharup_url;
	    }
	    public function getMerchantKey(){
	    	return $this->merchantKey;
	    }
	    public function getAmount(){
	    	return Mage::getModel('checkout/cart')->getQuote()->getGrandTotal();
	    }
	    public function getResponseURL(){
	    	return $this->getUrl('checkout_with_mintzone/response');
	    }
	    public function getOrderId(){
	    	return "abc2123";
	    }
	    public function getCheckSum(){
	    	return $this->checksum();
	    }
	}
?>	