<?php
class AadhaarUp_CheckOut_IndexController extends Mage_Core_Controller_Front_Action {        
    public function indexAction() {
    	Mage::app()->getResponse()->setRedirect(Mage::helper('adminhtml')->getUrl("checkout/cart"));
    }

} 
?>