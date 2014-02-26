
        
$(document).ready(function(){
    //tab selector
        $('#edit-information-tab-id a').click(function (e) {
            e.preventDefault();
            $(this).tab('show');
        })
     $().ready(function() {
		$.backstretch("img/background.jpg");
	})
        
        
$("input[data-close='close']").click(function(){
        $("#alert-login-faild").html('');
        $("#form-edit-fan-id").slideUp(500);
        
    });
        
        $().dateSelectBoxes($('#pre_birthMonth'),$('#pre_birthDay'),$('#pre_birthYear'));
    $("#tab_lA").click(function(){
    })
        
            $("#band-edit-btn-id").click(function(){
        $.post($projectExecPath+'/profile/get-info', 
        function(resp){
//            if(resp.success == true){
                $.each(resp.data, function(s, item){
                    $("#pre_"+s).val(item)
                })
                $("#alert-login-faild").html('');
                $("#form-edit-fan-id").slideDown(500);
                
//            }
            
        }, 'json')
        
    });
    
    
    
    
    
var url = $("form#form-fan-edit-id").attr("action")+'/save-fan-basic-info'
$("#more-info-form-save").click(function(){
   var data = {};
   $("input[data-tab1='data']").each(function(){
       data[$(this).attr("name")] = $(this).val();
   })
   data[$("#pre_vName_title").attr("name")] =$("#pre_vName_title").val()
   $.post(url,data,function(resp){
            $("#alert-login-faild").find("#error").remove();
            if(resp['errorMessage']){
                if(resp['errorMessage'] == 'faild'){
                    $("#alert-login-faild").html("<div id='error' class='alert alert-error'>Error in save data</div>")
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
            else{
                
                $("#alert-login-faild").html("<div id='error' class='alert alert-success'>change info successfuly</div>")
//                loadData()
//                $("#item-show").slideDown();
//                $("#item-hide").slideUp();
//                $("#alert-login-faild").find("#error").remove();
            }
            
        },'json');
});


  
  
var urlMoreInfo = $("form#form-fan-edit-id-tab2").attr("action")+'/save-fan-more-info'
$("#more-info-form-save-tab2").click(function(){
   var data = {};
   $("input[data-tab2='data']").each(function(){
       data[$(this).attr("name")] = $(this).val();
   })
   
   var dBirth_date = $('#pre_birthYear').val()+'-'+$('#pre_birthMonth').val()+'-'+$('#pre_birthDay').val();
   data['dBirth_date'] = dBirth_date
//   data[$("#pre_vName_title").attr("name")] =$("#pre_vName_title").val()
   $.post(urlMoreInfo,data,function(resp){
            $("#alert-login-faild").find("#error").remove();
            if(resp['errorMessage']){
                if(resp['errorMessage'] == 'faild'){
                    $("#alert-login-faild").html("<div id='error' class='alert alert-error'>Error in save data</div>")
                }
                else if(resp['errorMessage'] == 'faild1'){
                    $("#alert-login-faild").html("<div id='error' class='alert alert-error'>Please select a date between 1900-01-01 and 2000-12-30</div>")
                }
                
                else{
                    if(resp['errorMessage']['dBirth_date']['dateInvalidDate']){
                        $("#alert-login-faild").html("<div id='error' class='alert alert-error'>The correct date format is yyyy-MM-dd</div>")
                    }
                     
                }
                

            }
            else{
                
                $("#alert-login-faild").html("<div id='error' class='alert alert-success'>change info successfuly</div>")
//                loadDataMore()
//                $("#item-show1").slideDown();
//                $("#item-hide1").slideUp();
//                $("#alert-login-faild2").find("#error").remove();
            }
            
        },'json');
});




var urlShipInfo = $("form#form-fan-edit-id-tab3").attr("action")+'/save-fan-ship-info'
$("#ship-info-form-save-tab3").click(function(){
   var data = {};
   $("input[data-tab3='data']").each(function(){
       data[$(this).attr("name")] = $(this).val();
   })
//   data[$("#pre_vName_title").attr("name")] =$("#pre_vName_title").val()
   $.post(urlShipInfo,data,function(resp){
            $("#alert-login-faild").find("#error").remove();
            if(resp['errorMessage']){
                if(resp['errorMessage'] == 'faild'){
                    $("#alert-login-faild").html("<div id='error' class='alert alert-error'>Error in save data</div>")
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
            else{
                
                $("#alert-login-faild").html("<div id='error' class='alert alert-success'>change info successfuly</div>")
//                loadDataShip()
//                $("#item-show2").slideDown();
//                $("#item-hide2").slideUp();
//                $("#alert-login-faild3").find("#error").remove();
            }
            
        },'json');
});


var urlChangePassword = $("form#form-fan-edit-id-tab4").attr("action")+'/change-password';
$("#change-password-form-save-tab4").click(function(){
    var dataChangePass = {};
    $("input[data-tab='password']").each(function(){
        dataChangePass[$(this).attr("name")] = $(this).val();
    })
    $.post(urlChangePassword, dataChangePass, function(resp){
        $("#alert-login-faild").find("#error").remove();
            if(resp['errorMessage']){
                if(resp['errorMessage'] == 'faild1'){
                    $("#alert-login-faild").html("<div id='error' class='alert alert-error'>you don't have permission to this page </div>")
                     var delay = 3000; //Your delay in milliseconds
                     setTimeout(function(){ window.location = $projectExecPath+'/login'; }, delay);
                }
                else if(resp['errorMessage'] == 'faild'){
                    $("#alert-login-faild").html("<div id='error' class='alert alert-error'>There was a problem with the database .please try again later.</div>")
                }else{
                    var allItemsError = [];
                    if(resp['errorMessage']['CuPassword']){
                        var items = [];
                        $.each(resp['errorMessage']['CuPassword'], function(i, item) {

                                items.push('<li>'+item+'</li>');

                         });
//                         var ulCu = '<ul>Current Password Errors'+items.join('')+'</ul>'
                        allItemsError.push('<ul>Current Password Errors'+items.join('')+'</ul>')
                    }
                    
                    if(resp['errorMessage']['vPassword']){
                        var passItems = [];
                        $.each(resp['errorMessage']['vPassword'], function(i, item) {

                                passItems.push('<li>'+item+'</li>');

                         });
//                         var ulPass = '<ul>Password Errors'+passItems.join('')+'</ul>'
                        allItemsError.push('<ul>Password Errors'+passItems.join('')+'</ul>')
                    }
                    if(resp['errorMessage']['vPasswordConfirm']){
                        var passConfItems = [];
                        $.each(resp['errorMessage']['vPasswordConfirm'], function(i, item) {

                                passConfItems.push('<li>'+item+'</li>');

                         });
                         allItemsError.push('<ul>Password Confirm Errors'+passConfItems.join('')+'</ul>')
                    }
                     $("#alert-login-faild").html("<div id='error' class='alert alert-error'>"+allItemsError.join('')+"</div>")
                }
                

            }
            else{
                
                $("#alert-login-faild").html("<div id='error' class='alert alert-success'>"+resp['SuccessMessage']+"...</div>")
                $("input[data-tab='password']").each(function(){
                    $(this).val('');
                })
//                $("#change-password-form")[0].reset();
//                 var delay = 3000; //Your delay in milliseconds
//
//                setTimeout(function(){ window.location = $projectExecPath+'/login'; }, delay);
//                setTimeout(function() {
//                    $('#alert-login-faild').find("#error").remove();
//                }, 3000); // <-- time in milliseconds
           
            }
        
    }, 'json')
})




})