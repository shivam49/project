$(document).ready(function(){
    
    //for genres multiple select
    $('.select2').select2({ placeholder : '' });
    
    
    $("#tab_lA").click(function(){
    })
//var urlState = $projectExecPath+'/profile/get-state-band'
//  $("#pre1_vState").keyup(function(x,y){
//      var dataState = {};
//      dataState['vName'] = $("#pre1_vState").val()
//      $.post(urlState,dataState,function(resp){
//            var data_state = $.parseJSON(resp);
//            var dataCu = [];
//            $.each(data_state['data'], function(s,item){
//                dataCu.push(item['vName'])
//            })
//            var state = dataCu.join(' ');
//            $( "#pre1_vState" ).autocomplete( {
//                source: state.split(" ")
//            } );
//      });
//
//  });
$(document).load('get-info' ,function(data){
        
    data_array = $.parseJSON(data);
    $.each(data_array['data'], function(s, item){
        if(item!=null){
            $("#profile_"+s).text(item)
            if(s == 'vWebsite'){
                $("#profile_vWebsite_link").attr("href","http://"+item);
            }
            
             if(s == 'image_band') {
                 var imageAddr = $projectExecImage+'/'+item;
                 console.log(imageAddr)
                 $("#image-band").attr("src", imageAddr)
             } 
        }
    })
     $("#profile1_vCity").text(data_array.data['vCity'])
        
        if(data_array.data['market_title']!=null){
            $("#profile1_iState_id").text(data_array.data['state_name'])
        }else{
            $("#profile1_iState_id").text('-')
        }
        if(data_array.data['market_title']!=null){
            $("#profile1_iMarket_id").text(data_array.data['market_title'])
        }else{
            $("#profile1_iMarket_id").text('-')
        }
//        $("#profile1_iMarket_id").text(data_array.data['market_title'])
        if(data_array.data['genres_title']){
            $("#profile1_vGenre_ids").text(data_array.data['genres_title'])
        }
        
});

 $("#back-info").click(function(){
    $("#item-show").slideDown();
    $("#item-hide").slideUp();
  });
  
$("#back-info1").click(function(){
    $("#item-show1").slideDown();
    $("#item-hide1").slideUp();
  });
    
$("#edit-info").click(function(){
    $.each(data_array['data'], function(s, item){
        $("#pre_"+s).val(item)
    })
    $("#item-show").slideUp();
    $("#item-hide").slideDown();
  });
  
   $("#edit-info1").click(function(){
    $.each(data_array['data'], function(s, item){
        if(s == 'vGenre_ids'){
//            item = ["1","2"]
            $("#pre1_vGenre_ids").val(item).select2();
            
        }else{
           $("#pre1_"+s).val(item) 
        }
    })
    $("#item-show1").slideUp();
    $("#item-hide1").slideDown();
  });

  
  
var url = $("form#basic-info-form").attr("action")+'/save-band-basic-info'
$("#basic-info-form-save").click(function(){
   var data = {};
   $("input").each(function(){
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
                
                $("#alert-login-faild").html("<div id='error' class='alert alert-success'>"+resp['SuccessMessage']+"...</div>")
                loadData()
                $("#item-show").slideDown();
                $("#item-hide").slideUp();
                $("#alert-login-faild").find("#error").remove();
            }
            
        },'json');
});

  
var urlMoreInfo = $("form#more-info-form").attr("action")+'/save-band-more-info'
$("#more-info-form-save").click(function(){
   var data = {};
   $("input").each(function(){
       data[$(this).attr("name")] = $(this).val();
   })
   data[$("#pre1_iMarket_id").attr("name")] =$("#pre1_iMarket_id").val()
   data[$("#pre1_vGenre_ids").attr("name")] =$("#pre1_vGenre_ids").val()
   data[$("#pre1_iState_id").attr("name")] =$("#pre1_iState_id").val()
   $.post(urlMoreInfo,data,function(resp){
            $("#alert-login-faild2").find("#error").remove();
            if(resp['errorMessage']){
                var allError = []
                var state = [];
                if(resp['errorMessage']['iState_id']){
                    $.each(resp['errorMessage']['iState_id'], function(i, item) {

                            state.push('<li>'+item+'</li>');

                     });
                     var ulState = '<ul>Email Errors'+state.join('')+'</ul>'
                     allError.push(ulState)
                }
                
                if(resp['errorMessage']['vCity']){
                    var city =  [];
                    $.each(resp['errorMessage']['vCity'], function(i, item) {

                           city.push('<li>'+item+'</li>');

                    });
                    var ulCity = '<ul>City Errors'+city.join('')+'</ul>'
                    allError.push(ulCity)
                }
                
                if(resp['errorMessage']['iMarket_id']){
                    var market = [];
                    $.each(resp['errorMessage']['iMarket_id'], function(i, item) {

                           market.push('<li>'+item+'</li>');

                    });
                    var ulMarket = '<ul>Market Errors'+market.join('')+'</ul>'
                    allError.push(ulMarket)
                }
                
                if(resp['errorMessage']['vGenre_ids']){
                    var genres = [];
                    $.each(resp['errorMessage']['vGenre_ids'], function(i, item) {

                           genres.push('<li>'+item+'</li>');

                    });
                    var ulGenres = '<ul>Genres Errors'+genres.join('')+'</ul>'
                    allError.push(ulGenres)
                }
                
                
                 $("#alert-login-faild2").html("<div id='error' class='alert alert-error'>"+allError.join('')+"</div>")

            }
            else{
                
                $("#alert-login-faild2").html("<div id='error' class='alert alert-success'>"+resp['SuccessMessage']+"</div>")
         
            
                loadDataMore()
                $("#item-show1").slideDown();
                $("#item-hide1").slideUp();
                $("#alert-login-faild2").find("#error").remove();
            }
            
        },'json');
});

var urlChangePassword = $("form#change-password-form").attr("action")+'/change-password';
$("#change-password-form-save").click(function(){
    var dataChangePass = {};
    $("input").each(function(){
        dataChangePass[$(this).attr("name")] = $(this).val();
    })
    $.post(urlChangePassword, dataChangePass, function(resp){
        $("#alert-change-password").find("#error").remove();
            if(resp['errorMessage']){
                if(resp['errorMessage'] == 'faild1'){
                    $("#alert-change-password").html("<div id='error' class='alert alert-error'>you don't have permission to this page </div>")
                     var delay = 3000; //Your delay in milliseconds
                     setTimeout(function(){ window.location = $projectExecPath+'/login'; }, delay);
                }
                else if(resp['errorMessage'] == 'faild'){
                    $("#alert-change-password").html("<div id='error' class='alert alert-error'>There was a problem with the database .please try again later.</div>")
                }else{
                    var allItemsError = [];
                    if(resp['errorMessage']['CuPassword']){
                        var items = [];
                        $.each(resp['errorMessage']['CuPassword'], function(i, item) {

                                items.push('<li>'+item+'</li>');

                         });
                        allItemsError.push('<ul>Current Password Errors'+items.join('')+'</ul>')
                    }
                    
                    if(resp['errorMessage']['vPassword']){
                        var passItems = [];
                        $.each(resp['errorMessage']['vPassword'], function(i, item) {

                                passItems.push('<li>'+item+'</li>');

                         });
                        allItemsError.push('<ul>Password Errors'+passItems.join('')+'</ul>')
                    }
                    if(resp['errorMessage']['vPasswordConfirm']){
                        var passConfItems = [];
                        $.each(resp['errorMessage']['vPasswordConfirm'], function(i, item) {

                                passConfItems.push('<li>'+item+'</li>');

                         });
                         allItemsError.push('<ul>Password Confirm Errors'+passConfItems.join('')+'</ul>')
                    }
                     $("#alert-change-password").html("<div id='error' class='alert alert-error'>"+allItemsError.join('')+"</div>")
                }
                

            }
            else{
                
                $("#alert-change-password").html("<div id='error' class='alert alert-success'>"+resp['SuccessMessage']+"...</div>")
                $("#change-password-form")[0].reset();
                 var delay = 3000; //Your delay in milliseconds

                setTimeout(function(){ window.location = $projectExecPath+'/login'; }, delay);
           
            }
        
    }, 'json')
})




function loadData(){
    
    $(document).load('get-info' ,function(data){
        data_array = $.parseJSON(data);
        $.each(data_array['data'], function(s, item){
            if(item!=null){
                $("#profile_"+s).text(item)
                if(s == 'vWebsite'){
                    $("#profile_vWebsite_link").attr("href","http://"+item);
                }

            }
        })
    });
}
function loadDataMore(){
    $('#loading')
    $(document).load('get-info' ,function(data){
        data_array = $.parseJSON(data);
        $("#profile1_vCity").text(data_array.data['vCity'])
        if(data_array.data['market_title']!=null){
            $("#profile1_iState_id").text(data_array.data['state_name'])
        }else{
            $("#profile1_iState_id").text('-')
        }
        if(data_array.data['market_title']!=0){
            $("#profile1_iMarket_id").text(data_array.data['market_title'])
        }else{
            $("#profile1_iMarket_id").text('-')
        }
        
        if(data_array.data['genres_title']){
            $("#profile1_vGenre_ids").text(data_array.data['genres_title'])
        }
        
    });

    
    $('#loading').fadeOut();
}

});



