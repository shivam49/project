$(document).ready(function(){
    



    $().dateSelectBoxes($('#pre1_birthMonth'),$('#pre1_birthDay'),$('#pre1_birthYear'));
    $("#tab_lA").click(function(){
    })
    
$(document).load('get-info' ,function(data){
     
    data_array = $.parseJSON(data);
    $.each(data_array['data'], function(s, item){
        if(item!=null){
            $("#profile_"+s).text(item)
            if(s == 'vWebsite'){
                $("#profile_vWebsite_link").attr("href","http://"+item);
//                $("#profile_"+s).text("http://"+item)
            }
                
        }
    })
});

$("#collapseTwo-toggel").click(function(){

    $.each(data_array['data'], function(s, item){
        if(item!=null){
            
            $("#profile1_"+s).text(item)
     
        }
        
    })
})

$("#collapseTwo-togge2").click(function(){

    $.each(data_array['data'], function(s, item){
        if(item!=null){
            $("#profile2_"+s).text(item)
     
        }
        
    })
})

    
$("#edit-info").click(function(){
    $.each(data_array['data'], function(s, item){
        $("#pre_"+s).val(item)
    })
    $("#item-show").slideUp();
    $("#item-hide").slideDown();
  });
  
  $("#edit-info1").click(function(){
    $.each(data_array['data'], function(s, item){
        $("#pre1_"+s).val(item)
    })
    $("#item-show1").slideUp();
    $("#item-hide1").slideDown();
  });
  
$("#edit-info2").click(function(){
    $.each(data_array['data'], function(s, item){
        $("#pre2_"+s).val(item)
    })
    $("#item-show2").slideUp();
    $("#item-hide2").slideDown();
  });
  
  var urlCountry = $projectExecPath+'/profile/get-country'
  $("#pre1_vCountry").keyup(function(x,y){
      $("#pre1_vState").val("");
      var dataCountry = {};
      dataCountry['vName'] = $("#pre1_vCountry").val()
      $.post(urlCountry,dataCountry,function(resp){
            var data_country = $.parseJSON(resp);
            var dataCu = [];
            $.each(data_country['data'], function(s,item){
                dataCu.push(item['vName'])
            })
            var country = dataCu.join(' ');
            $( "#pre1_vCountry" ).autocomplete( {
                source: country.split(" ")
            } );
      });

  });
  
  $("#pre2_vShip_country").keyup(function(x,y){
      $("#pre2_vShip_state").val("");
      var dataCountry = {};
      dataCountry['vName'] = $("#pre2_vShip_country").val()
      $.post(urlCountry,dataCountry,function(resp){
            var data_country = $.parseJSON(resp);
            var dataCu = [];
            $.each(data_country['data'], function(s,item){
                dataCu.push(item['vName'])
            })
            var country = dataCu.join(' ');
            $( "#pre2_vShip_country" ).autocomplete( {
                source: country.split(" ")
            } );
      });
      

  });
  var urlNameCountry = $projectExecPath+'/profile/session-country'
  $("#pre1_vCountry").blur(function(){
      var dataSessionCoun ={}
      dataSessionCoun['vName'] = $("#pre1_vCountry").val()
      $.post(urlNameCountry,dataSessionCoun,function(){
          
      })
  })
  
    $("#pre2_vShip_country").blur(function(){
      var dataSessionCoun ={}
      dataSessionCoun['vName'] = $("#pre2_vShip_country").val()
      $.post(urlNameCountry,dataSessionCoun,function(){
          
      })
  })
  
  
    var urlState = $projectExecPath+'/profile/get-state'
  $("#pre1_vState").keyup(function(x,y){
      var dataState = {};
      dataState['vName'] = $("#pre1_vState").val()
      $.post(urlState,dataState,function(resp){
            var data_state = $.parseJSON(resp);
            var dataCu = [];
            $.each(data_state['data'], function(s,item){
                dataCu.push(item['vName_little'])
            })
            var state = dataCu.join(' ');
            $( "#pre1_vState" ).autocomplete( {
                source: state.split(" ")
            } );
      });

  });
  
    $("#pre2_vShip_state").keyup(function(x,y){
      var dataState = {};
      dataState['vName'] = $("#pre2_vShip_state").val()
      $.post(urlState,dataState,function(resp){
            var data_state = $.parseJSON(resp);
            var dataCu = [];
            $.each(data_state['data'], function(s,item){
                dataCu.push(item['vName_little'])
            })
            var state = dataCu.join(' ');
            $( "#pre2_vShip_state" ).autocomplete( {
                source: state.split(" ")
            } );
      });

  });
  
  
  
  $("#back-info").click(function(){
    $("#item-show").slideDown();
    $("#item-hide").slideUp();
  });
   $("#back-info1").click(function(){
    $("#item-show1").slideDown();
    $("#item-hide1").slideUp();
  });
  
$("#back-info2").click(function(){
    $("#item-show2").slideDown();
    $("#item-hide2").slideUp();
  });
  
  
  
var url = $("form#basic-info-form").attr("action")+'/save-fan-basic-info'
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




  
  
var urlMoreInfo = $("form#more-info-form").attr("action")+'/save-fan-more-info'
$("#more-info-form-save").click(function(){
   var data = {};
   $("input").each(function(){
       data[$(this).attr("name")] = $(this).val();
   })
   
   var dBirth_date = $('#pre1_birthYear').val()+'-'+$('#pre1_birthMonth').val()+'-'+$('#pre1_birthDay').val();
   data['dBirth_date'] = dBirth_date
//   data[$("#pre_vName_title").attr("name")] =$("#pre_vName_title").val()
   $.post(urlMoreInfo,data,function(resp){
            $("#alert-login-faild2").find("#error").remove();
            if(resp['errorMessage']){
                if(resp['errorMessage'] == 'faild'){
                    $("#alert-login-faild2").html("<div id='error' class='alert alert-error'>Error in save data</div>")
                }
                else if(resp['errorMessage'] == 'faild1'){
                    $("#alert-login-faild2").html("<div id='error' class='alert alert-error'>Please select a date between 1900-01-01 and 2000-12-30</div>")
                }
                
                else{
                    if(resp['errorMessage']['dBirth_date']['dateInvalidDate']){
                        $("#alert-login-faild2").html("<div id='error' class='alert alert-error'>The correct date format is yyyy-MM-dd</div>")
                    }
                     
                }
                

            }
            else{
                
                $("#alert-login-faild2").html("<div id='error' class='alert alert-success'>"+resp['SuccessMessage']+"...</div>")
                loadDataMore()
                $("#item-show1").slideDown();
                $("#item-hide1").slideUp();
                $("#alert-login-faild2").find("#error").remove();
            }
            
        },'json');
});



var urlShipInfo = $("form#ship-info-form").attr("action")+'/save-fan-ship-info'
$("#ship-info-form-save").click(function(){
   var data = {};
   $("input").each(function(){
       data[$(this).attr("name")] = $(this).val();
   })
//   data[$("#pre_vName_title").attr("name")] =$("#pre_vName_title").val()
   $.post(urlShipInfo,data,function(resp){
            $("#alert-login-faild3").find("#error").remove();
            if(resp['errorMessage']){
                if(resp['errorMessage'] == 'faild'){
                    $("#alert-login-faild3").html("<div id='error' class='alert alert-error'>Error in save data</div>")
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
                    
                     $("#alert-login-faild3").html("<div id='error' class='alert alert-error'>"+AllLI+"</div>")
                }
                

            }
            else{
                
                $("#alert-login-faild3").html("<div id='error' class='alert alert-success'>"+resp['SuccessMessage']+"...</div>")
                loadDataShip()
                $("#item-show2").slideDown();
                $("#item-hide2").slideUp();
                $("#alert-login-faild3").find("#error").remove();
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
                     $("#alert-change-password").html("<div id='error' class='alert alert-error'>"+allItemsError.join('')+"</div>")
                }
                

            }
            else{
                
                $("#alert-change-password").html("<div id='error' class='alert alert-success'>"+resp['SuccessMessage']+"...</div>")
                $("#change-password-form")[0].reset();
                 var delay = 3000; //Your delay in milliseconds

                setTimeout(function(){ window.location = $projectExecPath+'/login'; }, delay);
//                setTimeout(function() {
//                    $('#alert-change-password').find("#error").remove();
//                }, 3000); // <-- time in milliseconds
           
            }
        
    }, 'json')
})




function loadData(){
    $('#loading');

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
    $('#loading').fadeOut();
}

function loadDataMore(){
    $('#loading')
    $(document).load('get-info' ,function(data){
        data_array = $.parseJSON(data);
        $.each(data_array['data'], function(s, item){
            if(item!=null){
                $("#profile1_"+s).text(item)
            }
        })
    });
    $('#loading').fadeOut();
}

function loadDataShip(){
    $('#loading')
    $(document).load('get-info' ,function(data){
        data_array = $.parseJSON(data);
        $.each(data_array['data'], function(s, item){
            if(item!=null){
                $("#profile2_"+s).text(item)
            }
        })
    });
    $('#loading').fadeOut();
}
});



