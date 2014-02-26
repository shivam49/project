$(document).ready(function(){
    var $signUpModalBoxElm = $('#signUpModalBox');
    if ( $signUpModalBoxElm ) {

    	$signUpModalBoxElm.click(function(){
    		
		    $signUpModalBoxElm.modal({
			    keyboard: false
		    });

    	});
    }
});
