<?php
	class AadhaarUp_CheckOut_ResponseController extends Mage_Core_Controller_Front_Action {        
	    public function indexAction() {
	    	$responseData = Mage::app()->getRequest()->getPost();
	    	$_SESSION['response'] = $responseData;
	    	if($responseData['response_code']=="0"){
	    		$customerId = $this->getUser($responseData);
		   		$this->setAddresses($customerId, $responseData);
		   		$quote = Mage::getSingleton('checkout/session')->getQuote();
				$quote->delete(); 
		   	}
	   		$this->loadLayout();
	    	$this->renderLayout();
	    }

	    private function setAddresses($cId, $responseData){
	    	$_custom_billing_address = array (
			    'firstname' => $responseData['billing_name'],
			    'lastname' => '',
			    'street' => array (
			        '0' => $responseData['billing_street']
			    ),
			    'city' => $responseData['billing_city'],
			    'region_id' => '',
			    'region' => $responseData['billing_state'],
			    'postcode' => $responseData['billing_zipcode'],
			    'country_id' => $responseData['billing_country'], /* Croatia */
			    'telephone' => ''
			);

			$customBillingAddress = Mage::getModel('customer/address');
			$customBillingAddress->setData($_custom_billing_address)
			            ->setCustomerId($cId)
			            ->setIsDefaultBilling('1')
			            ->setIsDefaultShipping('0')
			            ->setSaveInAddressBook('1');
			
			$_custom_shipping_address = array (
			    'firstname' => $responseData['shipping_name'],
			    'lastname' => '',
			    'street' => array (
			        '0' => $responseData['shipping_street']
			    ),
			    'city' => $responseData['shipping_city'],
			    'region_id' => '',
			    'region' => $responseData['shipping_state'],
			    'postcode' => $responseData['shipping_zipcode'],
			    'country_id' => $responseData['shipping_country'], /* Croatia */
			    'telephone' => ''
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
			}
			catch (Exception $ex) {
			    //Zend_Debug::dump($ex->getMessage());
			}
			//Mage::getSingleton('checkout/session')->getQuote()->setBillingAddress(Mage::getSingleton('sales/quote_address')->importCustomerAddress($customAddress));
	    }

	    private function putOrder($customer, $responseData){
			
		}

	    private function getUser($responseData){
			$customer = Mage::getModel('customer/customer');
			$email = $responseData['email'];
			$password = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 12);;
			$customer->setWebsiteId(Mage::app()->getWebsite()->getId());
			$customer->loadByEmail($email);

			if(!$customer->getId()) {
			    $customer->setEmail($email);
			    $customer->setFirstname($responseData['billing_name']);
			    $customer->setLastname('');
			    $customer->setPassword($password);
				return $customer->getId();
				try {
				    $customer->save();
				    $customer->setConfirmation(null);
				    $customer->save();
				    //Make a "login" of new customer
				    Mage::getSingleton('customer/session')->loginById($customer->getId());
				}
				catch (Exception $ex) {
				    //Zend_Debug::dump($ex->getMessage());
				}
			}
			else{
				return $customer->getId();
			}			
		}

		private function checkDataValidity($responseData){
			return 1;
		}
	}
?>