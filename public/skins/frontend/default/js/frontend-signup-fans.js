$(document).ready(function(){
        $('#fbloginlink').click(function(){
                    dofblogin();
                    return false;
       });
})
window.fbAsyncInit = function() {
  FB.init({
            appId  : fbappid, // App ID
            cookie : true, // enable cookies to allow the server to access the session
	    status : true, // check the login status upon init?
            xfbml  : true  // parse XFBML
          });
};                        
   // Load the SDK Asynchronously
  (function(d){
     var js, id = 'facebook-jssdk', ref = d.getElementsByTagName('script')[0];
     if (d.getElementById(id)) {return;}
     js = d.createElement('script'); js.id = id; js.async = true;
     js.src = "https://connect.facebook.net/en_US/all.js";
     ref.parentNode.insertBefore(js, ref);
   }(document));


                        
   function dofblogin(fburl) {
 
        FB.login(function (response) {
            if (response.authResponse) {
                FB.api('/me', function (response) {
		   /* is this a valid reponse */
		   if (typeof response.id == "undefined") {
                       $("#alert-signup").remove();
    			$('#alert-signup').html("<div id='error' class='alert alert-error'>Facebook Login Failed. Try again.</div>")
		   	return;
		   }
		   var fburl = $("a#fbloginlink").attr("href")+'/fblogin';
		   var jqxhr = $.post(fburl, 
                          function (data) {
	                         var response2 = jQuery.parseJSON(data);
	 		   	  if( (typeof response2.success != "undefined") && response2.success == true) {
	    				$('#alert-signup').html("<div id='error' class='alert alert-success'>Facebook login Successful.</div>")
                                        $("#vName").val(response.first_name)
                                        $("#vLastname").val(response.last_name)
					$("#facebookid").val(response.id);	// set the facebook id
		   			$("#vEmail").val(response.email);  // set the facebook email
                                        //$("#vEmailConfirm").val(response.email);
		   			$("#vPassword").val(response.email);  // not used but ned this here
                                        //$("#vPasswordConfirm").val(response.email);
		   			//$("#password_again").val(response.email);
					//$("#bg-img").hide();
					//$("#getstarted").show();
                                        
                                        //$("#vRef").attr('checked','checked');
//                                        $("input[type=submit]").removeAttr("disabled");
                                        $("input[type=submit]").trigger('click');
                                        
			   	  } else{
				        if(( typeof response2.error != "undefined") ){
                                            $('#alert-signup').html("<div id='error' class='alert alert-success'>"+response2.error+"</div>");
                                        }else{
                                            $('#alert-signup').html("<div id='error' class='alert alert-error'>Facebook Login Not found.</div>")
                                        }
	    		   			
				        if(( typeof response2.redirectUrl != "undefined") )
						location.href  = response2.redirectUrl;
                                        $("#vName").val("")
                                        $("#vLastname").val("")
		   			$("#vEmail").val(""); 
                                        //$("#vEmailConfirm").val("");
		   			$("#vPassword").val(""); 
                                        //$("#vPasswordConfirm").val("");  
		   			//$("#password_again").val("");  
				  } 
                          } );  // post to fblogin
		   $('#alert-signup').html("<div id='error' class='alert alert-alert-info'>Checking Facebook Login....</div>")
		   return;
		   if( response.success == false) {
    			$('#alert-signup').html("<div id='error' class='alert alert-error'>Facebook not logged in. Try again.</div>")
		   	return;		      
		   }
                   $("#vName").val(response.first_name)
                    $("#vLastname").val(response.last_name)
                    $("#facebookid").val(response.id);	// set the facebook id
                    $("#vEmail").val(response.email);  // set the facebook email
                    //$("#vEmailConfirm").val(response.email);
                    $("#vPassword").val(response.email);  // not used but ned this here
                    //$("#vPasswordConfirm").val(response.email);
		   //$("#password_again").val(response.email);
		   //$("#shop_user_email").val(response.email);	
    		   $('#alert-signup').html();html("<div id='error' class='alert alert-success'>Successful login by:"  + response.email+"</div>")
		   //$("#bg-img").hide();
		   //$("#getstarted").show();		
		}); // FB.api
            } else {
    		$('#alert-signup').html("<div id='error' class='alert alert-error'>Facebook not logged in. Try again.</div>")
               $("#vName").val("")
                $("#vLastname").val("")
		$("#vEmail").val(""); 
                //$("#vEmailConfirm").val("");
                $("#vPassword").val(""); 
                //$("#vPasswordConfirm").val("");  
		//$("#password_again").val("");  
	    }
            // handle the response
        }, {
            scope: 'email'
        });
    }

$(function(){
    $("input").blur(function()
    {
        var formElementId =$(this).parent().prev().find('label').attr('for');
       doValidation(formElementId)
    })
});
function doValidation(id){
    var url = 'validate-form-fans'
        var data = {};
        $("input").each(function()
        {
                data[$(this).attr('name')]=$(this).val();
            
            
        });
        $.post(url,data,function(resp)
        
        {
            //console.log("#"+id)
            $("#"+id).parent().find('.errors').remove();
//            $("input").each(function(){
                    
                $("#"+id).parent().append(getErrorHtml(resp[id],id))
//            })
            
        },'json')
        

}
function getErrorHtml(formErrors,id)
{
    
    var o = '<ul id="errors-'+id+'" class="errors">';
    for(errorKey in formErrors)
        {
            o += '<li>'+formErrors[errorKey] + '</li>';
            
        }
    o +='</ul>';
    return o;
}


//$(function() {
////    $('#save_changes').attr('disabled', 'disabled')
//  $('#vRef').click(function() {
//    var satisfied = $('#vRef:checked').val();
//    if (satisfied != undefined) $('#save_changes').removeAttr('disabled');
//    else $('#save_changes').attr('disabled', 'disabled');
//  });
//});

$(function(){
    var divRol = document.getElementById( 'form-rol-register' );
    $('#save_changes-label').before(divRol)
    $('#form-rol-register').css("display", "block")
})
