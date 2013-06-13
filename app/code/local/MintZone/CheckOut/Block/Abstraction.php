<?php 
	class MintZone_CheckOut_Block_Abstraction extends Mage_Checkout_Block_Cart {
		protected $mintzone_url = "http://127.0.0.1:3001/api/v1/transactions";
		protected $merchantKey = "d00414734592ad6cb18cf5b75a654616";
		protected $merchantSecret = '3795a80f96d6df4bcb0ac48e04c58efb';
		protected $contact_details = "MintZone (contact@mintzone.in)";
	}
?>