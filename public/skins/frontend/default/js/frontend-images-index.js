$(document).ready(function(){
var loginDiv= $('#data-login-id').attr('data-login');
//if(loginDiv != 'notLogin'){
//console.log($userId,'jjjjjjjjjjjjjjjjjjjj');
console.log(loginDiv);
        $('#file_upload').uploadify({
            'swf': $projectExecLibrary+'/plugin/uploadify/uploadify.swf',
            'uploader': $projectExecPath+'/frontend/images/save-img',
            'auto': true,
            'fileObjName': 'the_files',
            'fileSizeLimit': '2048KB',
            'fileTypeDesc': 'Image Files',
            'width': 575,
            'height': 45,
    //        'buttonImage' : $projectExecLibrary+'/plugin/uploadify/addimages.png',
            'buttonClass' : 'addphotos',
            'buttonText' : 'Add Photos',
        // 'buttonCursor' : 'arrow',
            'fileTypeExts': '*.gif; *.jpg; *.png;',
    //        'debug' : true,
            'method': 'post',
            'formData': {'iMember_id' : $userId},
            'onUploadStart': function(file) {
                $("#file_upload").uploadify("settings", "someOtherKey", 2);
            },
            'onUploadSuccess': function(file, data, response) {
                var result= $.parseJSON(data);
    //            console.log(result);
                if(result.success == true){
                    $('div.allimages').append(
                        '<div data-iId="'+result.data.iId+'" class="allimages1">'+
                            '<div class="imagedel mouseCursore" data-iId="'+result.data.iId+'" onclick="deletePic('+result.data.iId+')"></div>'+
                            '<li style="list-style: none;" data-dateEdit="'+result.data.dDate_modify+'" data-dateCreate="'+result.data.dDate_create+'" data-iId="'+result.data.iId+'" dataDesc="'+result.data.tDesc+'" dataNameImg="'+result.data.vImg+'">'+
                                '<div style="width: 213px; height: 157px;" >'+
                                    '<a>'+
                                        '<img class="mouseC brd" href="#modal_pic_id" data-toggle="modal" src="'+$projectExecImage+'/'+result.data.vImg+'" />'+
                                    '</a>'+
                                '</div>'+
                            '</li>'+
                        '</div>'
                );
    //                $('#ul_view_pic_id').append('<li class="span3 mouseC"><a class="thumbnail"><img src="'+$projectExecImage+'/'+result.data+'" /></a></li>');
    //                $('#ul_view_pic_id').append('<li data-dateEdit="'+result.data.dDate_modify+'" data-dateCreate="'+result.data.dDate_create+'" data-iId="'+result.data.iId+'" dataDesc="'+result.data.tDesc+'" dataNameImg="'+result.data.vImg+'" class="span3"><div class="thumbnail"><a class="thumbnail"><span data-iId="'+result.data.iId+'" onclick="deletePic('+result.data.iId+')" class="icon-remove-sign mouseCursore"></span><hr /><img class="mouseC" href="#modal_pic_id" data-toggle="modal" src="'+$projectExecImage+'/'+result.data.vImg+'" /></a><p class="pDesc" id="'+result.data.vImg+'"></p></div></li>');
    //           console.log(result);
            clickLi(result);
        }
            }  
        });

    $.ajax({
        type: 'json',
        url: $projectExecPath+"/frontend/images/load-img",
        success: function(response){
            var result= $.parseJSON(response);
            $.each(result.data, function(index, data){
                if(loginDiv == 'notLogin'){
                    var htmlLi= '<div data-iId="'+data.iId+'" class="allimages1">'+
                                    //'<div class="imagedel mouseCursore" data-iId="'+data.iId+'" onclick="deletePic('+data.iId+')"></div>'+
                                    '<li style="list-style: none;" data-dateEdit="'+data.dDate_modify+'" data-dateCreate="'+data.dDate_create+'" data-iId="'+data.iId+'" dataDesc="'+data.tDesc+'" dataNameImg="'+data.vImg+'">'+
                                        '<div style="width: 213px; height: 157px;" >'+
                                            '<a>'+
                                                '<img class="mouseC brd" href="#modal_pic_id" data-toggle="modal" src="'+$projectExecImage+'/'+data.vImg+'" />'+
                                            '</a>'+
                                        '</div>'+
                                    '</li>'+
                                '</div>';
                }else{
                    var htmlLi= '<div data-iId="'+data.iId+'" class="allimages1">'+
                                    '<div class="imagedel mouseCursore" data-iId="'+data.iId+'" onclick="deletePic('+data.iId+')"></div>'+
                                    '<li style="list-style: none;" data-dateEdit="'+data.dDate_modify+'" data-dateCreate="'+data.dDate_create+'" data-iId="'+data.iId+'" dataDesc="'+data.tDesc+'" dataNameImg="'+data.vImg+'">'+
                                        '<div style="width: 213px; height: 157px;" >'+
                                            '<a>'+
                                                '<img class="mouseC brd" href="#modal_pic_id" data-toggle="modal" src="'+$projectExecImage+'/'+data.vImg+'" />'+
                                            '</a>'+
                                        '</div>'+
                                    '</li>'+
                                '</div>';
                }
                
//                if(data.tDesc){
//                    var htmlLi= '<li data-dateEdit="'+data.dDate_modify+'" data-dateCreate="'+data.dDate_create+'" data-iId="'+data.iId+'" dataDesc="'+data.tDesc+'" dataNameImg="'+data.vImg+'" class="span3"><div class="thumbnail"><a class="thumbnail"><span data-iId="'+data.iId+'" onclick="deletePic('+data.iId+')" class="icon-remove-sign mouseCursore"></span><hr /><img class="mouseC" href="#modal_pic_id" data-toggle="modal" src="'+$projectExecImage+'/'+data.vImg+'" /></a><p class="pDesc" id="'+data.vImg+'">'+data.tDesc+'</p></div></li>';
//                } else {
//                    var htmlLi= '<li data-dateEdit="'+data.dDate_modify+'" data-dateCreate="'+data.dDate_create+'" data-iId="'+data.iId+'" dataDesc="'+data.tDesc+'" dataNameImg="'+data.vImg+'" class="span3"><div class="thumbnail"><a class="thumbnail"><span data-iId="'+data.iId+'" onclick="deletePic('+data.iId+')" class="icon-remove-sign mouseCursore"></span><hr /><img class="mouseC" href="#modal_pic_id" data-toggle="modal" src="'+$projectExecImage+'/'+data.vImg+'" /></a><p class="pDesc" id="'+data.vImg+'"></p></div></li>';
//                }
                $('div.allimages').append(htmlLi);
            });
            clickLi(result);       
        }
    });
//    }
});

function clickLi(result){
    $('li[dataDesc]').click(function(){
        var loginDiv= $('#data-login-id').attr('data-login');
        if(loginDiv == 'login'){
            $('#modal_pic_id').addClass('modal-pic-login');
            $('#modal_pic_id').removeClass('modal-pic-notLogin');
        }
        var dateCreate= $(this).attr('data-dateCreate');
        var dateEdit= $(this).attr('data-dateEdit');
//        console.log(dateCreate);
//        console.log(dateEdit);
        if(dateEdit != 'null'){
            $('#date_create_edti_id').html('Created at '+dateCreate+', Last modified at '+dateEdit);
        }else{
            $('#date_create_edti_id').html('Created at '+dateCreate);
        }
        $('#title_band_id').html(result.picProfile.vTitle);
        if(result.picProfile.img){
    //        $('#date_create_id').html(result.picProfile.img.dDate_create);
            $('#pic_header_id').attr('src', $projectExecImage+'/'+result.picProfile.img.vImg);
        }else{
            $('#pic_header_id').attr('src', $projectExecImage+'/'+'images-defaults-profile.jpg');
        }
         vImgResult= $(this).attr('dataNameImg');
        $('#img_body_id').attr('src', $projectExecImage+'/'+vImgResult);
        var tDesc= $(this).attr('dataDesc');
        if(tDesc != 'null'){
            $('.edit_area').html(tDesc);
        }else{
            $('.edit_area').html('');
        }
        $('div.edit_area').attr('id',vImgResult );
        selectPTag= $(this).children().children('p');
        editFooterModalLi= $(this);
        $('.edit_area').editable($projectExecPath+"/frontend/images/auto-jeditable-desc", {
            type: 'textarea',
            cancel: 'Cancel',
            submit: 'OK',
            submitdata: {vImgResult: vImgResult},
            type: 'textarea',
            loadurl: $projectExecPath+"/frontend/images/auto-jeditable-desc",
            rows: 4,
            width: '250px',
            callback: function(value, settings) {
                selectPTag.html(value);
                editFooterModalLi.removeAttr('dataDesc');
                editFooterModalLi.attr('dataDesc', value);
                $.ajax({
                    type: 'POST',
                    url: $projectExecPath+"/frontend/images/date-edit",
                    data: {
                        vImgResult: vImgResult
                    },
                    success: function(response){
                        var result= $.parseJSON(response);
                        $('#date_create_edti_id').html('Created at '+result.data.dDate_create+', Last modified at '+result.data.dDate_modify);
                        $('li[data-iId='+result.data.iId+']').removeAttr('data-dateEdit');
                        $('li[data-iId='+result.data.iId+']').attr('data-dateEdit', result.data.dDate_modify);
                    }
                });
            }
        });
        
        var iIdImg= $(this).attr('data-iId');
        $('#btn_set_pic_id').attr('data-iId', iIdImg);
});
}

function setAsProfile(){
    var iId= $('button#btn_set_pic_id').attr('data-iId');
    $.ajax({
        type: 'POST',
        url: $projectExecPath+"/frontend/images/set-profle-pic",
        data: {
            iId: iId
        },
        success: function(response){
            var result= $.parseJSON(response);
            if(result.success == true){
                $('#pic_header_id').removeAttr('src');
                $('#pic_header_id').attr('src', $projectExecImage+'/'+result.data.vImg);
//                $('#date_create_id').html(result.data.dDate_create);
                $('li[dataDesc]').click(function(){
//                    $('#date_create_id').html(result.data.dDate_create);
                    $('#pic_header_id').attr('src', $projectExecImage+'/'+result.data.vImg);
                });
            }
        }
    });
}

function deletePic(iId){
   $.ajax({
       type: 'POST',
       url: $projectExecPath+"/frontend/images/delete-pic",
       data: {
           iId: iId
       },
       success: function(response){
           var result= $.parseJSON(response);
           if(result.success == true){
               $('div[data-iId='+result.data+']').remove();
               if(result.picProfile == 'Yes'){
                   $('li[dataDesc]').click(function(){
//                    $('#date_create_id').html(' ');
                    $('#pic_header_id').attr('src', $projectExecImage+'/'+'images-defaults-profile.jpg');
                });
               }
           }
       }
   });
    
}
