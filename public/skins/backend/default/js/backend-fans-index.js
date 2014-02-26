$(document).ready(function(){
    getFans()
    
    $().dateSelectBoxes($('#pre_birthMonth'),$('#pre_birthDay'),$('#pre_birthYear'));
    
    $("input[data-close='close']").click(function(){
        $("#editFan").modal('hide');
    })
    
    $('#editFanTab a').click(function (e) {
        e.preventDefault();
        $(this).tab('show');
    })
    
    
    
    $("#search-fan-name").click(function(){
        searchFans()
    })
    
    
    $("#reset_search_data").click(function(){
        getFans()
        $("#reset_search_data").attr("data-search","")
        $("#reset_search_data").slideUp()
        $("#search-data-btn").slideDown()
    })
    
    
    
    var url = $("form#form-fan-edit-id").attr("action")+'/save-fan-basic-info'
$("#more-info-form-save").click(function(){
   var data = {};
   $("input[data-tab1='data']").each(function(){
       data[$(this).attr("name")] = $(this).val();
   })
   data[$("#pre_vName_title").attr("name")] =$("#pre_vName_title").val()
   data['iId'] =$("#pre_iId").val()
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
            }
            
        },'json');
    })
    
    
    
    var urlMoreInfo = $("form#form-fan-edit-id-tab2").attr("action")+'/save-fan-more-info'
   $("#more-info-form-save-tab2").click(function(){
   var data = {};
   $("input[data-tab2='data']").each(function(){
       data[$(this).attr("name")] = $(this).val();
   })
   
   var dBirth_date = $('#pre_birthYear').val()+'-'+$('#pre_birthMonth').val()+'-'+$('#pre_birthDay').val();
   data['dBirth_date'] = dBirth_date
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
            }
            
        },'json');
    });

})

function getFans(page){
    $("#ul-pagination-id").html('')
    $("#tBody_table_td").html('')
    $("#tHead_table_td").html('')
    $.post('fans/get-fans',{
        
     page : page
     }, function(resp){
         var i = 1
        while( i <= resp.count ) {
            $("#ul-pagination-id").append('<li id="id_page_li_'+i+'" class="" onclick=getFans('+i+')><a style="" id="id_page_'+i+'">'+i+'</a></li>')
            if(page == i){
                $("#id_page_li_"+i).addClass("active")
            }
            i++;
            
        }
        if(page > 0){
            
        }else{
            $("#id_page_li_1").addClass("active")
        }
        $("#tHead_table_td").append('<tr><th>Fans Name</th><th>Fans Email</th></tr>')
        $.each(resp.data, function(s,item){
            $("#tBody_table_td").append('<tr data-id="'+item['fanId']+'">'+
                    '<td><div>'+item['fanNametitle']+' '+item['fanName']+' '+item['fanFamily']+'<div><div><h6 style="margin:0">Signup Date : '+item['memberUsersignupDate']+'</h6></div></td>'+
                    '<td><h5>'+item['memberEmail']+'</h5></td>'+
                    '<td style="width:160px">'+
                        '<button class="btn btn-small btn-warning" onclick=editFan('+item['fanId']+')>EDIT <i class="icon-edit"></i></button>&nbsp'+
                        '<button class="btn btn-small btn-danger" onclick=deleteFan('+item['fanId']+')>DELETE <i class="icon-trash"></i></button>'+
                    '</td>'+
                '</tr>');
            
        })
     },'json')
//})
        
}


function searchFans(page){
    $.post('fans/search', 
    {
        vName: $("#search-data-name").val(),
        vLastname : $("#search-data-lastname").val(),
        vEmail : $("#search-data-email").val(),
        page : page
    }, 
    function(resp){
        $("#ul-pagination-id").html('')
        $("#tBody_table_td").html('')
        $("#tHead_table_td").html('')
        if(resp.success==true){
            var i = 1
            while( i <= resp.count ) {
                $("#ul-pagination-id").append('<li id="id_page_li_'+i+'" class="" onclick=searchFans('+i+')><a style="" id="id_page_'+i+'">'+i+'</a></li>')
                if(page == i){
                    $("#id_page_li_"+i).addClass("active")
                }
                i++;

            }
            if(page > 0){

            }else{
                $("#id_page_li_1").addClass("active")
            }
            $("#tHead_table_td").append('<tr><th>Fans Name</th><th>Fans Email</th></tr>')
            $.each(resp.data, function(s,item){
                $("#tBody_table_td").append('<tr data-id="'+item['fanId']+'">'+
                        '<td><div>'+item['fanNametitle']+' '+item['fanName']+' '+item['fanFamily']+'<div><div><h6 style="margin:0">Signup Date : '+item['memberUsersignupDate']+'</h6></div></td>'+
                        '<td><h5>'+item['memberEmail']+'</h5></td>'+
                        '<td style="width:160px">'+
                            '<button class="btn btn-small btn-warning" onclick=editFan('+item['fanId']+')>EDIT <i class="icon-edit"></i></button>&nbsp'+
                            '<button class="btn btn-small btn-danger" onclick=deleteFan('+item['fanId']+')>DELETE <i class="icon-trash"></i></button>'+
                        '</td>'+
                    '</tr>');

            })
        }else{
            $("#tHead_table_td").html('No Result')
        }
        $('#myModal').modal('hide');
        $("#search-data").val("");
        $("#search-data-btn").slideUp()
        $("#reset_search_data").slideDown()
      
    }, 'json')
}


function editFan(id){
    
    $.post($projectExecPath+'/backend/fans/get-fan-info',
    {
        id : id
    },
        function(resp){
            if(resp.success == true){
                $("#alert-login-faild").html('')
                $.each(resp.data, function(s, item){
                    $("#pre_"+s).val(item)
                })
                
                
                $("#title-fan-edit").html('EDIT <i>'+resp.data['vName']+' '+resp.data['vLastname']+'</i>');
                $("#editFan").modal()
            }
            
        }, 'json')
}

function deleteFan(id){
    $("#deleteFanMember").modal();
    $("#delete-fan-member-yes").click(function(){
        $.post('fans/delete-fan-member', 
        {
            id : id
        }, 
        function(resp){
            if(resp.success == true){
                $("#deleteFanMember").modal('hide');
//                $("[data-id='"+id+"']").remove()
                getFans();
                $("#delete-fan-member-yes").unbind('click');  
            }
        }, 'json')
    })
}