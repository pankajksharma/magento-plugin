<?php 

/*
	Edit this file to set your mintzone key and secret provided by Mintzone.
	Also edit contact_details to enable customers to connect with you.
*/
	class MintZone_CheckOut_Block_Abstraction extends Mage_Checkout_Block_Cart {
		protected $mintzone_url = "https://test.mintzone.in/api/v1/transactions";
		protected $merchantKey = "c07a17a1ebd93f85eb7522f3a3fb03ef";
		protected $merchantSecret = 'f19866b153d67703bd43c0dfff613f01';
		protected $contact_details = "Abc (contact@example.in)";
	}
?>