<?php
	class MintZone_CheckOut_ResponseController extends Mage_Core_Controller_Front_Action {        
	    public function indexAction() {
	    	$responseData = Mage::app()->getRequest()->getPost();
	    	$_SESSION['response'] = $responseData;
	    	if($responseData['response_code']=="0"){
	    		$customerId = $this->getUser($responseData);
		   		$addresses = $this->setAddresses($customerId, $responseData);
	    		$this->place_order($customerId, $addresses);
		   		$quote = Mage::getSingleton('checkout/session')->getQuote();
				$quote->delete(); 
		   	}
	   		$this->loadLayout();
	    	$this->renderLayout();
	    }

	    private function place_order($customerId, $addresses){
			Mage::app('default');
			$store = Mage::app()->getStore('default');
			$customer = Mage::getModel('customer/customer');
			$customer->setStore($store);
			$customer->load($customerId);
			$quote = Mage::getModel('sales/quote');
			$quote->setStore($store);
			$quote->assignCustomer($customer);

			$cart = Mage::getSingleton('checkout/cart')->getQuote();
			foreach ($cart->getAllItems() as $item) {
				$product = $item->getProduct();
				$buyInfo = array('qty' => $item->getQty());
				// print_r($buyInfo);
				$quote->addProduct($product, new Varien_Object($buyInfo));
			}
			$billingAddress = $quote->getBillingAddress()->addData($customer->getPrimaryBillingAddress());
			$shippingAddress = $quote->getShippingAddress()->addData($customer->getPrimaryShippingAddress());
			$shippingAddress->setCollectShippingRates(true)->collectShippingRates()
                ->setShippingMethod('flatrate_flatrate')
                ->setPaymentMethod('checkmo');
            $quote->getPayment()->importData(array('method' => 'checkmo'));
			$quote->getShippingAddress()->setShippingMethod('flatrate_flatrate');
			$quote->getShippingAddress()->collectTotals();
			$quote->collectTotals()->save();
			$service = Mage::getModel('sales/service_quote', $quote);
			$service->submitAll();
			$order = $service->getOrder();
			$invoice = Mage::getModel('sales/service_order', $order)->prepareInvoice();
			$invoice->setRequestedCaptureCase(Mage_Sales_Model_Order_Invoice::CAPTURE_ONLINE);
			$invoice->register();
			$transaction = Mage::getModel('core/resource_transaction')
			                    ->addObject($invoice)
			                    ->addObject($invoice->getOrder());
			$transaction->save();
	    }

	    
	  
	    private function setAddresses($cId, $responseData){
	    	$name = split(" ", $responseData['billing_name'], 2);
	    	$lastname = isset($name[1])? $name[1] : 'Not Provided';
	    	// print_r($name);
	    	$_custom_billing_address = array (
			    'firstname' => $name[0],
			    'lastname' => $lastname,
			    'street' => array (
			        '0' => $responseData['billing_street']
			    ),
			    'city' => $responseData['billing_city'],
			    'region_id' => '',
			    'region' => $responseData['billing_state'],
			    'postcode' => $responseData['billing_zipcode'],
			    'country_id' => $responseData['billing_country'], /* Croatia */
			    'telephone' => 'Not Provided'
			);

			$customBillingAddress = Mage::getModel('customer/address');
			$customBillingAddress->setData($_custom_billing_address)
			            ->setCustomerId($cId)
			            ->setIsDefaultBilling('1')
			            ->setIsDefaultShipping('0')
			            ->setSaveInAddressBook('1');
			
	    	$name = split(" ", $responseData['shipping_name'], 2);
	    	$lastname = isset($name[1])? $name[1] : 'Not Provided';

			$_custom_shipping_address = array (
			    'firstname' => $name[0],
			    'lastname' => $lastname,
			    'street' => array (
			        '0' => $responseData['shipping_street']
			    ),
			    'city' => $responseData['shipping_city'],
			    'region_id' => '',
			    'region' => $responseData['shipping_state'],
			    'postcode' => $responseData['shipping_zipcode'],
			    'country_id' => $responseData['shipping_country'], /* Croatia */
			    'telephone' => 'Not Provided'
			);

			$customShippingAddress = Mage::getModel('customer/address');
			$customShippingAddress->setData($_custom_shipping_address)
			            ->setCustomerId($cId)
			            ->setIsDefaultBilling('0')
			            ->setIsDefaultShipping('1')
			            ->setSaveInAddressBook('1');
			
			try {
			    $customBillingAddress->save();
			    $customShippingAddress->save();
	   			return Array("shipping_address" => $customShippingAddress, "billing_address" => $customBillingAddress);
			}
			catch (Exception $ex) {
			    //Zend_Debug::dump($ex->getMessage());
			}
			//Mage::getSingleton('checkout/session')->getQuote()->setBillingAddress(Mage::getSingleton('sales/quote_address')->importCustomerAddress($customAddress));
	    }


	    private function getUser($responseData){
			$customer = Mage::getModel('customer/customer');
			$email = $responseData['customer_email'];
			$password = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 12);;
			$customer->setWebsiteId(Mage::app()->getWebsite()->getId());
			$customer->loadByEmail($email);

			if(!$customer->getId()) {
			    $customer->setEmail($email);
			    $customer->setFirstname($responseData['billing_name']);
			    $customer->setLastname('');
			    $customer->setPassword($password);
				try {
				    $customer->save();
				    $customer->setConfirmation(null);
				    $customer->save();
				    //Make a "login" of new customer
					return $customer->getId();
				}
				catch (Exception $ex) {
				    Zend_Debug::dump($ex->getMessage());
				}
			}
			// print_r($customer);
			Mage::getSingleton('customer/session')->loginById($customer->getId());
			return $customer->getId();
					
		}

		private function checkDataValidity($responseData){
			return 1;
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