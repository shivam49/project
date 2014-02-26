$(document).ready(function(){
      
    
    
    
    
        
    var urlChangePadd= $("form#form-changepass").attr("action")+'/change-password'
    $("input:button").click(function(){
        var data = {};
        $("input").each(function()
        {
                data[$(this).attr('name')]=$(this).val();
            
            
        });
        $.post(urlChangePadd,data,function(resp){
            console.log(resp)
            $("#alert-login-faild").find("#error").remove();
            if(resp['errorMessage']){
                if(resp['errorMessage'] == 'other'){
                    $("#alert-login-faild").html("<div id='error' class='alert alert-error'>there is a problem with changing your password!</div>")
                }else{
                    
                    
                    if(resp['errorMessage']['vPassword']){
                        var passItems = [];
                        $.each(resp['errorMessage']['vPassword'], function(i, item) {

                                passItems.push('<li>'+item+'</li>');

                         });
                         var ulPass = '<ul>Password Errors'+passItems.join('')+'</ul>'
                    }
                    
                    var items = [];
                    if(resp['errorMessage']['vPasswordConfirm']){
                        $.each(resp['errorMessage']['vPasswordConfirm'], function(i, item) {

                                items.push('<li>'+item+'</li>');

                         });
                         var ulEmail = '<ul>Password Confirm Errors'+items.join('')+'</ul>'
                    }
                    var AllLI = ''
                    if( ulPass && ulEmail){
                        AllLI = ulPass+ulEmail;
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
            else{
                
                $("#alert-login-faild").html("<div id='error' class='alert alert-success'>"+resp['SuccessMessage']+"...</div>")
                var delay = 3000; //Your delay in milliseconds

                setTimeout(function(){ window.location = $projectExecPath; }, delay);
            }
            
        },'json')
    });
    

});