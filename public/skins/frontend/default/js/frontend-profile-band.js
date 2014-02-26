
        
$(document).ready(function(){
      //tab selector
        $('#edit-information-tab-id a').click(function (e) {
            e.preventDefault();
            $(this).tab('show');
        })
        
        
        $('.select2').select2({placeholder : ''});
    $("#band-edit-btn-id").click(function(){
        $.post($projectExecPath+'/profile/get-user-info', 
        function(resp){
            if(resp.success == true){
                $.each(resp.data, function(s, item){
                    if(s == 'vGenre_ids'){
                        $("#pre_vGenre_ids").val(item).select2();

                    }else{
                    $("#pre_"+s).val(item) 
                    }
                })
                $("#alert-login-faild").html('');
                $("#form-edit-band-id").slideDown(500);
                
            }
            
        }, 'json')
        
    });
    
//    $("#more-info-form-close").click(function(){
//        $("#alert-login-faild").html('');
//        $("#form-edit-band-id").slideUp(500);
//        
//    });
//    $("#more-info-form-close-tab2").click(function(){
//        $("#alert-login-faild").html('');
//        $("#form-edit-band-id").slideUp(500);
//        
//    });
//    $("#more-info-form-close-tab3").click(function(){
//        $("#alert-login-faild").html('');
//        $("#form-edit-band-id").slideUp(500);
//        
//    });
//    $("#more-info-form-close-tab4").click(function(){
//        $("#alert-login-faild").html('');
//        $("#form-edit-band-id").slideUp(500);
//        
//    });
    $("[data-close='close']").click(function(){
        $("#alert-login-faild").html('');
        $("#form-edit-band-id").slideUp(500);
    })
    
var urlBandEdit = $("form#form-band-edit-id").attr("action")+'/save-band-info'
$("#more-info-form-save").click(function(){
   var data_band = {};
   $("input").each(function(){
       data_band[$(this).attr("name")] = $(this).val();
   })
   data_band[$("#pre_iMarket_id").attr("name")] =$("#pre_iMarket_id").val()
   data_band[$("#pre_vGenre_ids").attr("name")] =$("#pre_vGenre_ids").val()
   data_band[$("#pre_iState_id").attr("name")] =$("#pre_iState_id").val()
   $.post(urlBandEdit, data_band, 
   function(resp){
            if(resp['errorMessage']){
                var allError = []
                var email = [];
                if(resp['errorMessage']['vEmail']){
                    $.each(resp['errorMessage']['vEmail'], function(i, item) {

                            email.push('<li>'+item+'</li>');

                     });
                     var ulEmail = '<ul>Email Errors'+email.join('')+'</ul>'
                     allError.push(ulEmail)
                }
                
                var state = [];
                
                if(resp['errorMessage']['iState_id']){
                    $.each(resp['errorMessage']['iState_id'], function(i, item) {

                            state.push('<li>'+item+'</li>');

                     });
                     var ulState = '<ul>State Errors'+state.join('')+'</ul>'
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
                
                 $("#alert-login-faild").html("<div id='error' class='alert alert-error'>"+allError.join('')+"</div>")
            }
            else if(resp['success'] == true){
                
                $("#alert-login-faild").html("<div id='error' class='alert alert-success'>change info successfuly</div>")
            }else{
                $("#alert-login-faild").html("<div id='error' class='alert alert-error'>change info faild</div>")
            }
   }, 'json')
})


var urlBandOtherInfo = $("form#form-band-edit-id-tab2").attr("action")+'/save-band-other-info'
    $("#more-info-form-save-tab2").click(function(){
   var data_band_tab2 = {};
   $("input[data-tab2='data']").each(function(){
       data_band_tab2[$(this).attr("name")] = $(this).val();
   })
   $.post(urlBandOtherInfo, data_band_tab2, 
   function(resp){
       if(resp['success'] == true){
                
                $("#alert-login-faild").html("<div id='error' class='alert alert-success'>change info successfuly</div>")
            }else{
                $("#alert-login-faild").html("<div id='error' class='alert alert-error'>change info faild</div>")
            }
   }, 'json')
    })



//var urlChangePassword = $("form#change-password-form").attr("action")+'/change-password';
$("#more-info-form-save-tab3").click(function(){
    var dataChangePass = {};
    $("input[data-tab='password']").each(function(){
        dataChangePass[$(this).attr("name")] = $(this).val();
    })
    $.post($projectExecPath+'/profile/change-password', dataChangePass, function(resp){
        $("#alert-login-faild").find("#error").remove();
            if(resp['errorMessage']){
                if(resp['errorMessage'] == 'faild1'){
                    $("#alert-login-faild").html("<div id='error' class='alert alert-error'>you don't have permission to this page </div>")
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
                     $("#alert-login-faild").html("<div id='error' class='alert alert-error'>"+allItemsError.join('')+"</div>")
                }
                

            }
            else{
                
                $("#alert-login-faild").html("<div id='error' class='alert alert-success'>"+resp['SuccessMessage']+"...</div>")
                $("input[data-tab='password']").each(function(){
                    $(this).val('');
                })
//                 var delay = 3000; //Your delay in milliseconds
//
//                setTimeout(function(){ window.location = $projectExecPath+'/login'; }, delay);
           
            }
        
    }, 'json')
})

//    $.backstretch("img/background.jpg");
    
//    $('#btn_add_vote_id').mouseover(function(){
//        var vote= 'add';
//        var url= $projectExecPath+'/frontend/profile/band-load-vote';
//        $.ajax({
//            url: url,
//            data: {
//                vote: vote
//            },
//            method: 'POST',
//            dataType: 'json',
//            success: function(responce){
//                var content= '';
//                if(responce.success == false){
//                   content= 'faild';
//                }else{
//                    content= 'success';
//                }
//                console.log(content);
//                $('#btn_add_vote_id').popover({
//                        placement : 'right',
//                        content : content
//                    });
//            }
//        });
//    });
//    $('div[data-login]').attr('data-login');
     $('a[data-img]').click(function(){
        $(this).attr('href', '#modal_zoom_id');
        $(this).attr('data-toggle', 'modal');
        var img= $(this).attr('data-img');
        var src= $projectExecImage+'/'+img;
        var desc= $(this).attr('data-desc');
        var dateCreate= $(this).attr('data-dateCreate');
        var width= $(this).attr('data-width');
        var height= $(this).attr('data-height');
//        console.log(height);
        if(height>410){
            $('#modal_body_id').children('img').attr('style', 'height: 410px;');
        }else{
            $('#modal_body_id').children('img').removeAttr('style');
        }
        if(width>550){
            $('#modal_body_id').children('img').attr('style', 'width: 410px;');
        }else{
            $('#modal_body_id').children('img').removeAttr('style');
        }
        $('#modal_body_id').children('img').attr('src', src);
        $("#modal_footer_id").children('h6').html('Created at '+dateCreate);
        $("#modal_footer_id").children('p').html(desc);
    });
    var notPhoto= $('div[data-emptyPhoto]').attr('data-emptyPhoto');
    if(notPhoto == 'notPhoto'){
        $("div[data-title='photo']").addClass('hide');
    }
    
    var notVideo= $('div[data-emptyVideo]').attr('data-emptyVideo');
    if(notVideo == 'notVideo'){
        $("div[data-title='video']").addClass('hide');
    }
//    console.log(notPhoto);
    $('img[data-picName]').click(function(){
        var picName= $(this).attr('data-picName');
        var content= '<img src="'+$projectExecImage+'/'+picName+'" />';
        $('div.modal-body').html(content);
    });
    $('img[data-videosName]').click(function(){
        var idVideo= $(this).attr('id');
        $.ajax({
            url: $projectExecPath+'/frontend/profile/load-video-profile',
            data: {
                id: idVideo
            },
            type: 'POST',
            success: function(response){
                var result= $.parseJSON(response);
                $('div.modal-body').html('');
                $('div.modal-body').html(result.data.tVideo_embed_code);
            }
        });
    });
    var idProfile= $('div[data-idProfile]').attr('id');



    var login= $('div[data-login]').attr('data-login');
//    console.log(login);
    if(login == 'notLogin'){
        $("div[data-title='information']").addClass('hide');
        $("div[data-title='video']").addClass('hide');
        $("div[data-title='photo']").addClass('hide');
        $("div[data-title='players']").addClass('hide');
        $("#btn_add_vote_id").remove();
//        console.log($("#btn_add_vote_id"));
//        $("#btn_add_vote_id").hide();
        $("#btn_add_vote_notlogin_id").show();
        $('#btn_add_vote_notlogin_id').popover({content: 'Please login to vote!'});
        $('div#alert_block_login_id').slideUp(0);
        $('div#hidden_block_login_id').removeClass('hide');
        $('div#alert_block_login_id').slideDown(1000, function(){
            setTimeout(hideTrue, 2000);
        });
        function hideTrue(){
            $('div#hidden_block_login_id').addClass('hide');
        }
    }
    
    
    if(idProfile == 'notVote') {
        $('#btn_add_vote_id').attr('disabled', 'disabled');
        $('#btn_add_vote_id').hide();
    } else{
        $('#btn_add_vote_id').removeAttr('disabled');
        $('#btn_add_vote_id').show();
    }
    $('#btn_add_vote_id').click(function(){
//        $("form").submit(function(event){
//        event.preventDefault();
        var vote= 'add';
        var url= $projectExecPath+'/frontend/profile/band-save-vote';
        $.ajax({
            url: url,
            data: {
                vote: vote,
                idProfile: idProfile
            },
            type: 'POST',
//            dataType: 'json',
            success: function(response){
                var result= $.parseJSON(response);
                if(result.success == false){
                    $('div#alert_block_vote_id').slideUp(0);
                    $('div#hidden_block_vote_id').removeClass('hide');
                    $('div#hidden_success_vote_id').addClass('hide');
                    $('div#alert_block_vote_id').slideDown(1000, function(){
                        setTimeout(hideFalse, 2000);
                    });
                    function hideFalse(){
                        $('div#hidden_block_vote_id').addClass('hide');
                    }
                    $('#btn_add_vote_id').addClass('btn-warning');
                    $('#btn_add_vote_id').addClass('disabled');
                    $('#btn_add_vote_id').attr('disabled', 'disabled');
                    $('#btn_add_vote_id').removeClass('btn-primary');
                    $('#btn_add_vote_id').removeClass('btn-success');
                }else{
                    $('div#alert_success_vote_id').slideUp(0);
                    $('div#hidden_success_vote_id').removeClass('hide');
                    $('div#hidden_block_vote_id').addClass('hide');
                    $('div#alert_success_vote_id').slideDown(1000, function(){
                        setTimeout(hideTrue, 2000);
                    });
                    function hideTrue(){
                        $('div#hidden_success_vote_id').addClass('hide');
                    }
                    $('span#icon_vote_id').removeClass('icon-white');
                    $('#btn_add_vote_id').addClass('disabled');
                    $('#btn_add_vote_id').attr('disabled', 'disabled');
                    $('#btn_add_vote_id').removeClass('btn-warning');
                    $('#btn_add_vote_id').removeClass('btn-primary');
                }
            }
        });

//    });
    });
    
//       $('#btn_add_vote_id').popover({
//    placement : 'right',
////    selector: false,
////    animation: true,
//    trigger: 'focus',
//    title : title,
//    content : 'rwergfwsgs'
//});

loadBandsMember();


    $('#btn_add_id').click(function(){
        $('form#form_member_id').resetForm();
        $('input[name="instrument"]').val('');
        $('input[name="name"]').val('');
        $('input[name="instrument"]').removeAttr('value');
        $('input[name="name"]').removeAttr('value');
        $('#sub_btn_id').attr('value', 'add');
        $('#div_form_id').show();
    });
    $('button[name="reset"]').click(function(){
        $('#div_form_id').hide();
        $('input[name="instrument"]').removeAttr('value');
        $('input[name="name"]').removeAttr('value');
    });
    var options= {
        dataType: 'json',
        target: '#output1',
//        beforeSubmit: function(){
//            $('#sub_btn_id').attr('value', 'submit');
//        },
        success: function(response){
            if(response.success == true){
//                $('input[name="instrument"]').removeAttr('value');
//                $('input[name="name"]').removeAttr('value');
                $('#div_form_id').hide();
                $('thead').show();
                $('form#form_member_id').resetForm();
//                $('#modal_id').modal('hide');
                if(response.mode == 'add'){
                    var html= '<tr data-instrument="'+response.data.vInstrument+'" data-name="'+response.data.vName_lastname+'" data-id="'+response.id+'"><td><span id="name_lastname_'+response.id+'" class="mouseC">'+response.data.vName_lastname+'</span></td><td><span id="vInstrument_'+response.id+'" class="mouseC">'+response.data.vInstrument+'</td><td> <span onclick="deleteMember('+response.id+')" class="icon-remove mouseC"></span></td></tr>';
                    $('tbody#members-band-id').prepend(html);
                    $('#name_lastname_'+response.id).editable({
                        type: 'text',
                        pk: response.id,
                        url: $projectExecPath+"/frontend/bands-member/edit-line",
                        title: 'Enter username',
                        name: 'vName_lastname',
                        validate: function(value) {
                            if($.trim(value) == '') {
                            return 'This field is required';
                            }
                        }
                    });
                    $('#vInstrument_'+response.id).editable({
                        type: 'text',
                        pk: response.id,
                        url: $projectExecPath+"/frontend/bands-member/edit-line",
                        title: 'Enter username',
                        name: 'vInstrument',
                        validate: function(value) {
                            if($.trim(value) == '') {
                            return 'This field is required';
                            }
                        }
                    });
                }
//                else if(response.mode == 'edit'){
//                    $('tr[data-id='+response.id+']').attr('data-instrument', response.data.vInstrument);
//                    $('tr[data-id='+response.id+']').attr('data-name', response.data.vName_lastname);
//                    $('tr[data-id='+response.id+'] td:nth-child(1) > span').html(response.data.vName_lastname);
//                    $('tr[data-id='+response.id+'] td:nth-child(2) > span').html(response.data.vInstrument);
//                }
                $('span[data-delete]').click(function(){
                    deleteMember();
                });
            }else{
            }
        }
    };
    $('#form_member_id').ajaxForm(options);


});


function loadBandsMember(){
    $.ajax({
        type: 'json',
        url: $projectExecPath+"/frontend/bands-member/load-member",
        success: function(response){
            var result= $.parseJSON(response);
//            console.log(result.data);
            if(result.data == ''){
                $('thead').hide();
            }else{
                $('thead').show();
            }
            $.each(result.data, function(index, data){
                var html= '<tr data-instrument="'+data.vInstrument+'" data-name="'+data.vName_lastname+'" data-id="'+data.iId+'"><td><span id="name_lastname_'+data.iId+'" class="mouseC">'+data.vName_lastname+'</span></td><td><span id="vInstrument_'+data.iId+'" class="mouseC">'+data.vInstrument+'</span></td><td><span onclick="deleteMember('+data.iId+')" class="icon-remove mouseC"></span></td></tr>';
                $('tbody#members-band-id').prepend(html);
                $('#name_lastname_'+data.iId).editable({
                    type: 'text',
                    pk: data.iId,
                    url: $projectExecPath+"/frontend/bands-member/edit-line",
                    title: 'Enter username',
                    name: 'vName_lastname',
                    validate: function(value) {
                        if($.trim(value) == '') {
                           return 'This field is required';
                        }
                    }
                });
                $('#vInstrument_'+data.iId).editable({
                    type: 'text',
                    pk: data.iId,
                    url: $projectExecPath+"/frontend/bands-member/edit-line",
                    title: 'Enter username',
                    name: 'vInstrument',
                    validate: function(value) {
                        if($.trim(value) == '') {
                           return 'This field is required';
                        }
                    }
                });
            }); 
        }
    });
}


function deleteMember(id){
        $.ajax({
            type: 'POST',
            url: $projectExecPath+"/frontend/bands-member/delete-member",
            data: {
                id: id
            },
            success: function(response){
                var result= $.parseJSON(response);
                $('tr[data-id='+result.id+']').remove();
                if($('tr').length == '1'){
                    $('thead').hide();
                }
            }
        });
}


function play_audio(id){
    var n = $("[data-play='play']").attr("id")
    if(n!=null){
        var x = $("[data-play='play']").attr("id").replace("playeBtn_","")
        $("[data-play='play']")
                        .attr("data-play","")
                        .attr("onclick","play_audio("+x+")")
                        .attr("src",$projectExecPath+"/public/skins/frontend/default/images/img/white_arrow.png");
                        console.log("playeBtn_"+x)
                        document.getElementById("player_"+x).pause();
                        document.getElementById("player_"+x).currentTime = 0;
        
    }
    document.getElementById('player_'+id).play()
    $("#playeBtn_"+id)
                    .attr("data-play","play")
                    .attr("onclick","pause_audio("+id+")")
                    .attr("src",$projectExecPath+"/public/skins/frontend/default/images/img/white_arrow_puase.png");
    //SET TIME FOR ADD CLICK IN DB
    if(typeof(localStorage) == 'undefined') {
        //Check if the browser has local storage. 
//        return false; 
    }
    else {
                    var myDate = new Date();
                    var dateNew = myDate.getMinutes();
                    if(localStorage.getItem(id)){
                        var itemsOld = localStorage.getItem(id); 
                        var timeMin = dateNew - itemsOld
                        if(timeMin >=3 || timeMin<0){
                            localStorage.removeItem(id); 
                            var myArrayOld = []
                            myArrayOld.push(id)
                            localStorage.setItem(id+'_old', myArrayOld.join(";"))
                            $.post($projectExecPath+'/profile/click-update', {
                               id : id
                           }, 
                           function(resp){
                               if(resp.success == true){
                                   $("#iClick_"+id).text(resp.count)
                               }
                           }, 'json')
                        }else{
                        }
                    }else{
                        if(localStorage.getItem(id+'_old')== id){
                            localStorage.removeItem(id+'_old');
                            var myArray = []
                            myArray.push(dateNew)
                            localStorage.setItem(id, myArray.join(";"))
                        }else{
                            myArray = []
                            myArray.push(dateNew)
                            localStorage.setItem(id, myArray.join(";"))
                            var items = localStorage.getItem(id);
                             $.post($projectExecPath+'/profile/click-update', {
                                   id : id
                               }, 
                               function(resp){
                                   if(resp.success == true){
                                       $("#iClick_"+id).text(resp.count)
                                   }
                               }, 'json')
                        }
                    }
    }
}

function pause_audio(id){
    $("[data-play='play']")
                        .attr("data-play","")
                        .attr("onclick","play_audio("+id+")")
                        .attr("src",$projectExecPath+"/public/skins/frontend/default/images/img/white_arrow.png");
    document.getElementById('player_'+id).pause();
    document.getElementById("player_"+id).currentTime = 0;
}

function show_movie(id){
    $("#movie-title-modal-id").html('')
    $("#movie-embed-modal-id").html('')
    $.post($projectExecPath+'/profile/show-movie', 
    {
        id_movie : id
    }, 
    function(resp){
        $("#movie-title-modal-id").text(resp.data.vVideo_title)
        $("#movie-embed-modal-id").append(resp.data.tVideo_embed_code)
        $("#movie-image-modal-id").attr("src",resp.data.tVideo_image)
        $("#movie-show").modal()
    }, 'json')
}