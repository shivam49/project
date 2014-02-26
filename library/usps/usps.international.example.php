<?php
	require('usps.class.php');
	
	/**
	* 	Initial values: Domestic , International
	*/
	$usps = new USPS_Rate('International');

	/**
	*	Values :
	*		on
	*		off	
	*/
	$usps->setDebugging('on');

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
	*		Package
	*		Postcards
	*		Envelope
	*		LargeEnvelope
	*		FlatRate
	*/
	$usps->setMailType('Package');


	/**
	*	Destination Country
	*/
	$usps->setCountry("Puerto Rico, United States (Domestic Mail)");
	
	/**
	*	Value of Content
	*/
	$usps->setValueOfContent(100);
	
	/**
	* 	Weight : Pounds, Ounces
	*/
	$usps->setWeight(0,5);

	/**
	* 	Dimensions
	*/
	$usps->setDimension(0,0,0,0);

	/**
	* 	Container
	*	Values :
	*		RECTANGULAR
	*		NONRECTANGULAR
	*/
	$usps->setContainer('RECTANGULAR');

	
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