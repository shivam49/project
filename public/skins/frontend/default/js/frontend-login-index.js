$(document).ready(function(){
//       $(window).load(function(){
// $("#myModal").modal({keyboard:false,backdrop:'static'});
//        $('#myModal').modal('show');
       
        
//    });
    
    var url= $("form#form-sub").attr("action")+'/login'
    $("input:button").click(function(){
        var data = {};
        $("input").each(function()
        {
                data[$(this).attr('name')]=$(this).val();
            
            
        });
        $.post(url,data,function(resp){
            $("#alert-login-faild").find("#error").remove();
            if(resp['errorMessage']){
                if(resp['errorMessage'] == 'username or password is wrong'){
                    $("#alert-login-faild").html("<div id='error' class='alert alert-error'>Password is wrong. if you have forgotten your password click <a href = "+$projectExecPath+"/forget-password>here</a></div>")
                }else{
                    var items = [];
                    if(resp['errorMessage']['vEmail']){
                        $.each(resp['errorMessage']['vEmail'], function(i, item) {

                                items.push('<li>'+item+'</li>');

                         });
                         var ulEmail = '<ul>Email Errors'+items.join('')+'</ul>'
                    }
                    
                    if(resp['errorMessage']['vPassword']){
                        var passItems = [];
                        $.each(resp['errorMessage']['vPassword'], function(i, item) {

                                passItems.push('<li>'+item+'</li>');

                         });
                         var ulPass = '<ul>Password Errors'+passItems.join('')+'</ul>'
                    }
                    var AllLI = ''
                    if( ulPass && ulEmail){
                        AllLI = ulEmail+ulPass;
                    }
                    if(ulPass && !ulEmail){
                        AllLI = ulPass;
                    }
                    if(ulEmail && !ulPass){
                        AllLI = ulEmail;
                    }
                    
                     $("#alert-login-faild").html("<div id='error' class='alert alert-error'>"+AllLI+"</div>")
                }
                

            }
            else if(resp['SuccessMessage']){
                
                $("#alert-login-faild").html("<div id='error' class='alert alert-success'>"+resp['SuccessMessage']+"...</div>")
                var delay = 3000; //Your delay in milliseconds

                setTimeout(function(){ window.location = $projectExecPath+'/wellcome'; }, delay);
            }else{
                setTimeout(function(){ window.location = $projectExecPath+'/signup/bands-part2'; });
            }
            
        },'json')
    });
    
    
    $('#fbloginlink').click(function(){
		 dofblogin();
		 return false;
    }); 
});

window.fbAsyncInit = function() {
  FB.init({
            appId  : fbappid, // App ID
            cookie : true, // enable cookies to allow the server to access the session
//            channelUrl : '//WWW.YOUR_DOMAIN.COM/channel.html', // Channel File
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
    			 $("#alert-login-faild").remove();
    			$('#alert-login-faild').html("<div id='error' class='alert alert-error'>Facebook Login Failed. Try again.</div>")
		   	return;
		   }
		   var fburl = $("a#fbloginlink").attr("href")+'/fblogin';
		   var jqxhr = $.post(fburl, 
                          function (data) {
	                         var response2 = jQuery.parseJSON(data);
	 		   	  if( (typeof response2.success != "undefined") && response2.success == true) {
	    				$('#alert-login-faild').html("<div id='error' class='alert alert-error'>No record matching this account was not found. Click <a href="+$projectExecPath+"/signup>here</a> to register </div>")
//					$("#facebookid").val(response.id);	// set the facebook id
//		   			$("#user_email").val(response.email);  // set the facebook email
//		   			$("#password").val(response.email);  // not used but ned this here
		   			//$("#password_again").val(response.email);
					//$("#bg-img").hide();
					//$("#getstarted").show();
                                        
//                                        $("#ff-register-submit").trigger('click');
                                        
			   	  } else{
				        if(( typeof response2.error != "undefined") )
	    		   			$('#alert-login-faild').html("<div id='error' class='alert alert-success'>"+response2.error+"</div>")
					else
	    		   			 $('#alert-login-faild').html("<div id='error' class='alert alert-error'>Facebook Login Not found.</div>")
				        if(( typeof response2.redirectUrl != "undefined") )
						location.href  = response2.redirectUrl;
		   			$("#user_email").val(""); 
		   			$("#password").val("");  
		   			//$("#password_again").val("");  
				  } 
                          } );  // post to fblogin
		   $('#alert-login-faild').html("<div id='error' class='alert alert-alert-info'>Checking Facebook Login....</div>")
		   return;
		   if( response.success == false) {
                        $('#alert-login-faild').html("<div id='error' class='alert alert-error'>Facebook not logged in. Try again.</div>")
		   	return;		      
		   }
		   $("#facebookid").val(response.id);	// set the facebook id
		   $("#user_email").val(response.email);// set the facebook email
		   $("#password").val(response.email);	
		   //$("#password_again").val(response.email);
		   //$("#shop_user_email").val(response.email);			   
    		   $('#alert-login-faild').html();html("<div id='error' class='alert alert-success'>Successful login by:"  + response.email+"</div>")
		   //$("#bg-img").hide();
		   //$("#getstarted").show();		
		}); // FB.api
            } else {
    		$('#alert-login-faild').html("<div id='error' class='alert alert-error'>Facebook not logged in. Try again.</div>")
		$("#user_email").val(""); 
		$("#password").val("");  
		//$("#password_again").val("");  
	    }
            // handle the response
        }, {
            scope: 'email'
        });
    }