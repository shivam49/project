$(document).ready(function(){
    var url= $("form#form-sub").attr("action")+'/send-password'
    $("input:button").click(function(){
        var data = {};
        $("input").each(function()
        {
                data[$(this).attr('name')]=$(this).val();
            
            
        });
        $.post(url,data,function(resp){
            if(resp['errorMessage']){
                var items = [];

                $.each(resp['errorMessage']['vEmail'], function(i, item) {

                       items.push('<li>'+item+'</li>');

                });
                 $("#alert-login-faild").html("<div id='error' class='alert alert-error'>"+items.join('')+"</div>")

            }
            else{
                
                $("#alert-login-faild").html("<div id='error' class='alert alert-success'>"+resp['SuccessMessage']+"...</div>")
                var delay = 3000; //Your delay in milliseconds

                setTimeout(function(){ window.location = $projectExecPath; }, delay);
            }
            
        },'json')
    });
    

});