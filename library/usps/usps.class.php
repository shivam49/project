<?php
/**
* USPS Rate Calculator PHP Class
*
* There is a PHP class file for useing USPS rate calculator API
*
* LICENSE: Gomerch Company
*
* @category   PHP
* @package    -
* @subpackage -
* @copyright  Copyright (c) 2012 Gomerch USA Inc. (http://www.gomerch.com)
* @author	  Masoud Pour Zahmatkesh  (www.masoud-pourzahmatkesh.com)	
* @license    BSD License
* @version    1.3
* @link       http://www.gomerch.com
* @since      File available since Release 1.3.0
* @updated    2012-02-01
*/

require_once("xmlparser.php");

define('DEMO_API','http://production.shippingapis.com/ShippingAPI.dll');	
define('LIVE_API','http://production.shippingapis.com/ShippingAPI.dll');
define('DOMESTIC_API_NAME','RateV4');
define('INTERNATIONAL_API_NAME','IntlRateV2');
define('DOMESTIC_EXTRA_COST',3.0);
define('INTERNATIONAL_EXTRA_COST',4.0);

/**
* 			  USPS_Rate Class Ver 1.3
* 			  Domestic API Ver 4.0
*             International API Ver 2.0
*/
class USPS_Rate {

	public $defaultPriceUnder13OunceUSA = 6.00;
    public $defaultPriceUpper13OunceUSA = 9.00;
    public $defaultPriceUnder4PoundInternational = 15.00;
    public $defaultPriceUpper4PoundInternational = 30.00;
    public $defaultPricePriorityInternational = 30.00;
    public $defaultPriceExpressInternational = 50.00;

	private $_debugging;
	
	private $_api;
	private $_apiMode;
	private $_userID;
	private $_password;
	private $_serviceType;
	private $_firstClassMailType;
	private	$_revision;
	private $_country;
	private $_source;
	private $_destination;
	private $_pounds;
	private $_ounces;
	private	$_size;
	private $_width;
	private $_length;
	private $_height;
	private $_girth;
	private	$_machinable;
	private	$_mailType;
	private $_container;
	private	$_valueOfContent;
	private $_request;	
	private $_response;
	private $_error;
	private $_errors;
	
	
	
	/**
	* 	API Modes : Domestic , International	
	*/
	function __construct( $apiMode = 'Domestic' ) {
		$this->_debugging 	= false;
		$this->_error 		= false;
		$this->_api			= LIVE_API;
		$this->_apiMode 	= $apiMode;	
		$this->_userID 		= '';
		$this->_password 	= '';
		$this->_serviceType = '';
		$this->_firstClassMailType = '';
		$this->_revision	= '';
	    $this->_country 	= 'USA';
		$this->_source 		= '';
		$this->_destination = '';
		$this->_pounds		= 0;
		$this->_ounces		= 0;
		$this->_size		= 'REGULAR';
		$this->_width		= 0;
		$this->_length		= 0;
		$this->_height		= 0;
		$this->_girth		= 0;
		$this->_machinable  = false;
		$this->_mailType	= 'Package';
		$this->_valueOfContent = 0;
		$this->_container	= '';
		$this->_request 	= '';
		$this->_response 	= '';
		$this->_errors 		= array('Number'=>'0','Source'=>'','Description'=>'');
		return true;
	}


	/**
	*
	*/
	function __destruct() {
		
	}


	/**
	*	Values:  on  ,  off
	*/
	public function setDebugging( $flag='on' ) {
		if ( strtolower($flag)=='on' )
			$this->_debugging = true;
		else
			$this->_debugging = false;	
		$this->debug('Debugging Mode',strtolower($flag));
		return true;
	}
	

	/**
	*
	*/
	private function debug( $comment='', $value='' ) {
		if( $this->_debugging ) {
			echo '<br><b>' . $message . '</b><br>';
			if( is_array($value) ) {
				echo '<pre>';
				print_r($value);
				echo '</pre>';
			} else {
				echo $value;
			}
			echo '<hr>';
		}
	}


	/**
	*
	*/
	public function setError( $number, $source, $description ) {
		if( $description != '' ) {
			$this->_error = true;
			$this->_errors['number'] 		= $number;
			$this->_errors['source'] 		= $source;
			$this->_errors['description'] 	= $description;
			$this->debug('Error',$number . ' : ' . $source . ' : ' . $description);
			return true;
		} else {
			return false;
		}
	}


	/**
	* 	Error Message is a Array contains : Number, Source, Description
	*/
	public function getError() {
		if ( $this->_error )
			return $this->_errors;
		else
			return array();	
	}


	/**
	*
	*/
	public function setAPI( $api ) {
		$this->_api = $api;
		$this->debug('API',$this->_api);
		return true;		
	}


	/**
	*
	*/
	public function setUserID( $userID ) {
		$this->_userID = $userID;
		$this->debug('UserID',$this->_userID);
		return true;
	}


	/**
	*
	*/
	public function setPassword( $password ) {
		$this->_password = $password;
		$this->debug('Password',$this->_password);
		return true;
	}


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
	public function setServiceType( $serviceType='ALL' ) {
		$this->_serviceType = $serviceType;
		$this->debug('Service Type',$this->_serviceType);
		return true;
	}

	/**
	*	Values :
	*		Package
	*		Postcards
	*		Envelope
	*		LargeEnvelope
	*		FlatRate
	*/
	public function setMailType( $mailType='Package' ) {
		$this->_mailType = $mailType;
		$this->debug('Mail Type',$this->_mailType);
		return true;
	}


	/**
	*
	*/
	public function setCountry( $country ) {
		$this->_country = str_replace(' ', '%20', $country);
		$this->debug('Country',$this->_country);
		return true;
	}


	/**
	* 	Lenght = 5
	*/
	public function setSource( $source ) {
		$this->_source = $source;
		$this->debug('Source ZipCode',$this->_source);
		return true;
	}


	/**
	* 	Value of Content
	*/
	public function setValueOfContent( $vofc ) {
		$this->_valueOfContent = $vofc;
		$this->debug('Source ZipCode',$this->_valueOfContent);
		return true;
	}


	/**
	* 	Lenght = 5
	*/
	public function setDestination( $destination ) {
		$this->_destination = $destination;
		$this->debug('Destination ZipCode',$this->_destination);
		return true;
	}


	/**
	* 	Maximum Pounds is 70, Minimum Pounds is 0
	* 	Maximum Ounces is 1120.0, Minimum Onces is 0.0
	*/
	public function setWeight( $pounds=0 , $ounces=0 ) {
		$this->_pounds = $pounds;
		$this->_ounces = $ounces;
		$this->debug('Weight',$this->_pounds . ' : ' . $this->_ounces);
		return true;
	}


	/**
	* 	Values are REGULAR and LARGE
	* 		REGULAR : dimensions are 12" or less
	*		LARGE   : dimension is greater than 12"
	*/
	public function setSize( $size='REGULAR' ) {
		$this->_size = $size;
		$this->debug('Size',$this->_size);
		return true;
	}


	/**
	* 	Values :
	* 		default = VARIABLE
	*		FLAT RATE ENVELOPE
	*		PADDED FLAT RATE ENVELOPE
	*   	LEGAL FLAT RATE ENVELOPE
	*		SM FLAT RATE ENVELOPE
	*   	WINDOW FLAT RATE ENVELOPE
	*		GIFT CARD FLAT RATE ENVELOPE
	*		FLAT RATE BOX
	*		SM FLAT RATE BOX
	*		MD FLAT RATE BOX
	*		LG FLAT RATE BOX
	*		REGIONALRATEBOXA
	*		REGIONALRATEBOXB
	*		RECTANGULAR
	*		NONRECTANGULAR	
	*	
	*		If Package Size is LARGE, RECTANGULAR or NONRECTANGULAR Must be Indicated
	*/
	public function setContainer( $container='' ) {
		$this->_container = $container;
		$this->debug('Container',$this->_container);
		return true;
	}


	/**
	*	This Function Set Revision Field to 2 for More Functionality	
	*/
	public function setFullFunctionality( $mode=false ) {
		if ( $mode )
			$this->_revision = '2';
		else
			$this->_revision = '';	
		$this->debug('API Full Functionality',$mode);
		return true;
	}


	/**
	*	If Service Types Are All Kinds of First Class
	*	Values :
	*		LETTER
	*		FLAT
	*		PARCEL
	*		POSTCARD	
	*/
	public function setFirstClassMailType( $mailType='' ) {
		$this->_firstClassMailType = $mailType;
		$this->debug('First Class Mail Type',$this->_firstClassMailType);
		return true;
	}


	/**
	*
	*/
	public function setMachinable( $flag=false ) {
		$this->_machinable = $flag;
		if ( $this->_machinable )
			$temp = 'true';
		else
			$temp = 'false';
			
		$this->debug('Machinable',$temp);	
		return true;
	}


	/**
	*
	*/
	public function setDimension( $width, $length, $height, $girth ) {
		$this->_width 	= $width;
		$this->_length 	= $length;
		$this->_height 	= $height;
		$this->_girth	= $girth;
		$this->debug('Width, Length, Height, Girth',$this->_width . ' , ' . $this->_length . ' , ' . $this->_height . ' , ' . $this->_girth);
		return true;
	}



	/**
	*	Validate The Input Values And Make The XML Request
	*/
	private function doPrepare() {
		if ( $this->_api != '' ) {
			$this->_request = $this->_api;
			if ( $this->_apiMode == 'Domestic' )
				$this->_request .= '?API=' . DOMESTIC_API_NAME . '&XML=';
			else
				$this->_request .= '?API=' . INTERNATIONAL_API_NAME . '&XML=';
		} else {
			$this->setError('1','Gomerch USPS Class','API not defined.');
			return false;
		}
		
		if ( $this->_userID == '' ) {
			$this->setError('2','Gomerch USPS Class','UserID not defined.');
			return false;
		}
		


		$_xml = '';
		if ( $this->_apiMode == 'Domestic' ) {
			
			$_validList = array( 'FIRST CLASS',
								 'FIRST CLASS COMMERCIAL',
								 'FIRST CLASS HFP COMMERCIAL',
								 'PRIORITY',
								 'PRIORITY COMMERCIAL',
								 'PRIORITY HFP COMMERCIAL',
								 'EXPRESS',
								 'EXPRESS COMMERCIAL',
								 'EXPRESS SH',
								 'EXPRESS SH COMMERCIAL',
								 'EXPRESS HFP',
								 'EXPRESS HFP COMMERCIAL',
								 'PARCEL',
								 'MEDIA',
								 'LIBRARY',
								 'ALL',
								 'ONLINE'
								);
		
			if ( ! in_array($this->_serviceType,$_validList) ) {
				$this->setError('3','Gomerch USPS Class','Service type is not valid.');
				return false;
			}
	
			if ( $this->_source == '' ) {
				$this->setError('4','Gomerch USPS Class','Source zipcode not defined.');
				return false;
			}
			
			if ( $this->_destination == '' ) {
				$this->setError('5','Gomerch USPS Class','Destination zipcode not defined.');
				return false;
			}
	
			$_validList = array( 'FIRST CLASS',
								 'FIRST CLASS COMMERCIAL',
								 'FIRST CLASS HFP COMMERCIAL'
								);
		
			if ( in_array($this->_serviceType,$_validList) ) {
				if ( $this->_firstClassMailType == '' ) {
					$this->setError('6','Gomerch USPS Class','First class mail type not defined.');
					return false;
				} else {
					$_validList = array( 'LETTER',
										 'FLAT',
										 'PARCEL',
										 'POSTCARD'
										);

					if ( ! in_array($this->_firstClassMailType,$_validList) ) {
						$this->setError('7','Gomerch USPS Class','First class mail type is not valid.');
						return false;
					}
				}
				
			} else {
				$this->_firstClassMailType = '';
			}
		

			if ( $this->_container == '' ) {
				if ( $this->_size == 'LARGE' ) {
					$this->setError('8','Gomerch USPS Class','Container must be indicated when size=LARGE');
					return false;
				}
			}
			
			
			if ( ($this->_size != 'REGULAR') and ($this->_size != 'LARGE') ) {
				$this->setError('9','Gomerch USPS Class','Size value is not valid. (REGULAR or LARGE)');
				return false;
			}
			
	
			$_xml .= '<' . DOMESTIC_API_NAME . 'Request%20USERID="' . urlencode($this->_userID) . '"%20PASSWORD="' . urlencode($this->_password) . '">';
			
			if ( $this->_revision != '' )
				$_xml .= '<Revision>' . $this->_revision . '</Revision>';
			else
				$_xml .= '<Revision/>';
			
			$_xml .= '<Package%20ID="0">';
			$_xml .= '<Service>' . $this->_serviceType . '</Service>';
			if ( $this->_firstClassMailType != '' )
				$_xml .= '<FirstClassMailType>' . $this->_firstClassMailType . '</FirstClassMailType>';	
				
			$_xml .= '<ZipOrigination>' . $this->_source . '</ZipOrigination>';
			$_xml .= '<ZipDestination>' . $this->_destination . '</ZipDestination>';	
			
			$_xml .= '<Pounds>' . $this->_pounds . '</Pounds>';	
			$_xml .= '<Ounces>' . $this->_ounces . '</Ounces>';	
				
			if ( $this->_container != '' )
				$_xml .= '<Container>' . $this->_container . '</Container>';	
			else
				$_xml .= '<Container/>';	
			
			
			$_xml .= '<Size>' . $this->_size . '</Size>';
			if ( $this->_machinable )
				$_xml .= '<Machinable>true</Machinable>';
			else
				$_xml .= '<Machinable>false</Machinable>';
			
			if ( $this->_size == 'LARGE' ) {
				$_xml .= '<Width>' 	. $this->_width  . '</Width>';
				$_xml .= '<Length>' . $this->_length . '</Length>';
				$_xml .= '<Height>' . $this->_height . '</Height>';
				if ( $this->_girth != '' ) {
					$_xml .= '<Girth>' 	. $this->_girth  . '</Girth>';
				}
			}
			
			$_xml .= '</Package>';
			$_xml .= '</' . DOMESTIC_API_NAME . 'Request>';
		} else {
		
			// International
			$_validList = array( 'Package',
								 'Postcards',
								 'Envelope',
								 'LargeEnvelope',
								 'FlatRate'
								);
		
			if ( ! in_array($this->_mailType,$_validList) ) {
				$this->setError('13','Gomerch USPS Class','Mail Type value is not valid. (Package, Postcards, Envelope, LargeEnvelope, FlatRate)');
				return false;
			}

			if ( $this->_country == '' ) {
				$this->setError('10','Gomerch USPS Class','Country value not defined.');
				return false;
			}
		
			if ( ($this->_size != 'REGULAR') and ($this->_size != 'LARGE') ) {
				$this->setError('9','Gomerch USPS Class','Size value is not valid. (REGULAR or LARGE)');
				return false;
			}

			
			if ( $this->_size == 'LARGE' ) {
				if ( ($this->_container != 'RECTANGULAR') and ($this->_container != 'NONRECTANGULAR') ) {
					$this->setError('8','Gomerch USPS Class','Container must be indicated when size=LARGE. (RECTANGULAR or NONRECTANGULAR)');
					return false;
				}
				
				if ( ($this->_width == 0) or ($this->_height == 0) or ($this->_length == 0) ) {
					$this->setError('11','Gomerch USPS Class','Dimension values are not valid. (Width, Height, Length)');
					return false;
				}
				
				if ( $this->_container == 'NONRECTANGULAR' ) {
					if ( $this->_girth == 0 ) {
						$this->setError('11','Gomerch USPS Class','Dimension value is not valid. (Girth)');
						return false;
					}
				}
			}

			if ( $this->_valueOfContent == '' ) {
				$this->setError('12','Gomerch USPS Class','Value of content is not defined.');
				return false;
			}
			
	
			$_xml .= '<' . INTERNATIONAL_API_NAME . 'Request%20USERID="' . urlencode($this->_userID) . '"%20PASSWORD="' . urlencode($this->_password) . '">';
			
			if ( $this->_revision != '' )
				$_xml .= '<Revision>' . $this->_revision . '</Revision>';
			else
				$_xml .= '<Revision/>';
			
			$_xml .= '<Package%20ID="0">';
			
			$_xml .= '<Pounds>' . $this->_pounds . '</Pounds>';	
			$_xml .= '<Ounces>' . $this->_ounces . '</Ounces>';	

			if ( $this->_machinable )
				$_xml .= '<Machinable>true</Machinable>';
			else
				$_xml .= '<Machinable>false</Machinable>';

			$_xml .= '<MailType>' . $this->_mailType . '</MailType>';	
			
			$_xml .= '<ValueOfContents>' . $this->_valueOfContent . '</ValueOfContents>';
			$_xml .= '<Country>' . $this->_country . '</Country>';			
				
			if ( $this->_container != '' )
				$_xml .= '<Container>' . $this->_container . '</Container>';	
			else
				$_xml .= '<Container/>';	
			
			$_xml .= '<Size>' . $this->_size . '</Size>';
			
			$_xml .= '<Width>' 	. $this->_width  . '</Width>';
			$_xml .= '<Length>' . $this->_length . '</Length>';
			$_xml .= '<Height>' . $this->_height . '</Height>';
			$_xml .= '<Girth>' 	. $this->_girth  . '</Girth>';
			
			$_xml .= '</Package>';
			$_xml .= '</' . INTERNATIONAL_API_NAME . 'Request>';
		
		}
		
		$this->_request .= $_xml;
		return true;
	}



	/**
	*	Call USPS Api And Make The Result
	*/
	public function doAction() {
		if ( ! $this->doPrepare() ) {
			return false;
		}
		

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->_request);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_HTTPGET, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SLL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$_xml = curl_exec($ch);
		curl_close($ch); 

        $xmlParser = new xmlparser();
        $this->_response = $xmlParser->GetXMLTree($_xml);

				
		if ( isset($this->_response['ERROR']) ) {
			$this->setError($this->_response['ERROR'][0]['NUMBER'][0]['VALUE'],$this->_response['ERROR'][0]['SOURCE'][0]['VALUE'],$this->_response['ERROR'][0]['DESCRIPTION'][0]['VALUE']);
			return false;
		} else {
			if ( $this->_apiMode == 'Domestic' ) {
				$_postage = $this->_response['RATEV4RESPONSE'][0]['PACKAGE'][0]['POSTAGE'];
				$this->_response = $_postage;
			} else {
				$_postage = $this->_response['INTLRATEV2RESPONSE'][0]['PACKAGE'][0]['SERVICE'];
				$this->_response = $_postage;
			}
			return true;		
		}
		
	}


	/**
	*	Input Parameter Values :
	*		nothing
	*		NAME
	*		PRICE
	*		DESCRIPTION
	*/
	public function getService( $serviceType='' ) {
		$_service = array();
		$serviceType = strtoupper( $serviceType );
		if ( $this->_apiMode == 'Domestic' ) {
			if ( $serviceType == '' ) {
				foreach( $this->_response as $_list ) {
					$_id = $_list['ATTRIBUTES']['CLASSID'];
					$_service[$_id]['id'] 	 = $_list['ATTRIBUTES']['CLASSID'];
					$_service[$_id]['name']  = str_replace('&lt;sup&gt;&amp;reg;&lt;/sup&gt;','',$_list['MAILSERVICE'][0]['VALUE']);
					$_service[$_id]['price'] = $_list['RATE'][0]['VALUE'] + DOMESTIC_EXTRA_COST;
				}
			} elseif ( $serviceType == 'NAME' ) {
				foreach( $this->_response as $_list ) {
					$_id = $_list['ATTRIBUTES']['CLASSID'];
					$_service[$_id] = str_replace('&lt;sup&gt;&amp;reg;&lt;/sup&gt;','',$_list['MAILSERVICE'][0]['VALUE']);
				}
			} elseif ( $serviceType == 'PRICE' ) {
				foreach( $this->_response as $_list ) {
					$_id = $_list['ATTRIBUTES']['CLASSID'];
					$_service[$_id] = $_list['RATE'][0]['VALUE'] + DOMESTIC_EXTRA_COST;
				}
			} elseif ( $serviceType == 'DESCRIPTION' ) {
				foreach( $this->_response as $_list ) {
					$_id = $_list['ATTRIBUTES']['CLASSID'];
					$_name = str_replace('&lt;sup&gt;&amp;reg;&lt;/sup&gt;','',$_list['MAILSERVICE'][0]['VALUE']);
					$_price = $_list['RATE'][0]['VALUE'] + DOMESTIC_EXTRA_COST;
					$_service[$_id] = $_price . ' : ' . $_name;
				}
			}
		} else {
			/* International */
			if ( $serviceType == '' ) {
				foreach( $this->_response as $_list ) {
					$_id = $_list['ATTRIBUTES']['ID'];
					$_service[$_id]['id'] 	 = $_list['ATTRIBUTES']['ID'];
					$_service[$_id]['name']  = str_replace('&lt;sup&gt;&amp;reg;&lt;/sup&gt;','',$_list['SVCDESCRIPTION'][0]['VALUE']);
					$_service[$_id]['name']  = str_replace('&lt;sup&gt;&amp;trade;&lt;/sup&gt;','',$_service[$_id]['name']);
					$_service[$_id]['name']  = str_replace('**','',$_service[$_id]['name']);
					$_service[$_id]['price'] = $_list['POSTAGE'][0]['VALUE'] + INTERNATIONAL_EXTRA_COST;
				}
			} elseif ( $serviceType == 'NAME' ) {
				foreach( $this->_response as $_list ) {
					$_id = $_list['ATTRIBUTES']['ID'];
					$_service[$_id]  = str_replace('&lt;sup&gt;&amp;reg;&lt;/sup&gt;','',$_list['SVCDESCRIPTION'][0]['VALUE']);
					$_service[$_id]  = str_replace('&lt;sup&gt;&amp;trade;&lt;/sup&gt;','',$_service[$_id]);
					$_service[$_id]  = str_replace('**','',$_service[$_id]);
				}
			} elseif ( $serviceType == 'PRICE' ) {
				foreach( $this->_response as $_list ) {
					$_id = $_list['ATTRIBUTES']['ID'];
					$_service[$_id] = $_list['POSTAGE'][0]['VALUE'] + INTERNATIONAL_EXTRA_COST;
				}
			} elseif ( $serviceType == 'DESCRIPTION' ) {
				foreach( $this->_response as $_list ) {
					$_id = $_list['ATTRIBUTES']['ID'];
					$_name  = str_replace('&lt;sup&gt;&amp;reg;&lt;/sup&gt;','',$_list['SVCDESCRIPTION'][0]['VALUE']);
					$_name  = str_replace('&lt;sup&gt;&amp;trade;&lt;/sup&gt;','',$_name);
					$_name  = str_replace('**','',$_name);
					$_price = $_list['POSTAGE'][0]['VALUE'] + INTERNATIONAL_EXTRA_COST;
					$_service[$_id] = $_price . ' : ' . $_name;
				}
			}
			
		}

		return $_service;
	}


}


?>