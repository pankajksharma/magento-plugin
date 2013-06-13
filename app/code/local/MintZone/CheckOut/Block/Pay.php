<?php
	class MintZone_CheckOut_Block_Pay extends MintZone_CheckOut_Block_Abstraction {
		
		private $_order_id = null;

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
	    	return $this->mintzone_url;
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
	    	if ($this->_order_id == null)
	    		$this->_order_id = $this->generate_rand_order(null, 25);
	    	return $this->_order_id;
	    }
	    public function getCheckSum(){
	    	return $this->checksum();
	    }

	    private function generate_rand_order($chars = null, $length = 1024) {
		    if($chars == null) {
		        $chars = 'aAeEiIoOuUyYabcdefghijkmnopqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ23456789';
		    }

		    $rand = '';
		    for($i = 0; $i < $length; $i++) {
		        $rand .= $chars[ rand(0, strlen($chars) - 1) ];
		    }

		    return strtoupper(md5($rand));
		}
	}
?>	