<?php
	require('usps.class.php');
	
	/**
	* 	Initial values: Domestic , International
	*/
	$usps = new USPS_Rate('Domestic');

	/**
	*	Values :
	*		on
	*		off	
	*/
	$usps->setDebugging('off');

	/**
	*	USPS UserID
	*/
	$usps->setUserID('251MERCH7839');

	/**
	*	USPS Password
	*/
	$usps->setPassword('430NU69BR948');

	/**
	*	Values :
	*		FIRST CLASS
	*		FIRST CLASS COMMERCIAL
	*		FIRST CLASS HFP COMMERCIAL
	*		PRIORITY
	*		PRIORITY COMMERCIAL
	*		PRIORITY HFP COMMERCIAL
	*		EXPRESS
	*		EXPRESS COMMERCIAL
	*		EXPRESS SH
	*		EXPRESS SH COMMERCIAL
	*		EXPRESS HFP
	*		EXPRESS HFP COMMERCIAL
	*		PARCEL
	*		MEDIA
	*		LIBRARY
	*		ALL
	*		ONLINE	
	*/
	$usps->setServiceType('ALL');

	/**
	*	If Service Types Are All Kinds of First Class
	*	Values :
	*		LETTER
	*		FLAT
	*		PARCEL
	*		POSTCARD	
	*/
	$usps->setFirstClassMailType('PARCEL');

	/**
	*	Origination Zipcode
	*/
	$usps->setSource('92082');
	
	/**
	*	Destination Zipcode
	*/
	$usps->setDestination('92082');

	/**
	* 	Weight : Pounds, Ounces
	*/
	$usps->setWeight(0,5);
	
	/**
	*	Call USPS API
	*/
	if ( $usps->doAction() ) {
		
		/**
		*	Get Available Services
		*	Values :
		*		nothing
		*		NAME
		*		PRICE
		*		DESCRIPTION
		*/
		$service = $usps->getService();	
		
		echo '<pre>';
		print_r($service);
		echo '</pre>';
		
	} else {
		/**
		*	Get Error Information
		*/
		$_error = $usps->getError();
		echo '<h2>Error</h2>';
		echo 'Code : ' 		  . $_error['number'] 		. '<br>';
		echo 'Source : ' 	  . $_error['source'] 		. '<br>';
		echo 'Description : ' . $_error['description'] 	. '<br>';
	}
	
?>