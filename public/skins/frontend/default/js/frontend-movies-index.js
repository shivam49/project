$(document).ready(function(){
    $().ready(function() {
		$.backstretch("img/background.jpg");
	})
    loadAllMoviePic()
    $("#botton-add-movie").click(function(){
        $("#tVideo_embed_code").val('');
        $("#tDesc").val('');
        $("#vVideo_title").val('');
        $("#more-info-form-save").attr("value","Save Video")
        $("#movie-iId-edit").val('')
        $("#more-info-form-save").attr("onclick","save_video()")
        $("#form-insert-movie").slideToggle()
    });

    })
    
    function loadInfo(id,type){
var data_load ={};
if(type=='edit'){
        $("#image-movie-"+id).remove()
}
data_load['iId'] = id
    $.post($projectExecPath+'/movies/get-movie',data_load,function(resp){
        data_array = resp
        if(data_array.success == true && data_array.data != 'no result'){
        $.each(data_array['data'],function(s,item){
                if(item['dDate_modify']!=null){
                    var data_modify = '<p style="float:left; font-size:16px; margin:5px 0 0 5px; font-weight:bold;">Date modify : '+item['dDate_modify']+'</p>';
                }else{
                    data_modify = ''
                }
                if(item['iId'] == id){
                    $("#thumbnail-show-image-youtube").append(
                                            '<div id="image-movie-'+item['iId']+'">'+
                                            '<div class="clear"></div>'+
                                                '<div class="clear"></div>'+
                                                    '<div class="smallvideo" >'+
                                                                '<div class="">'+
                                                                    '<img src="'+item['tVideo_image']+'"  class="smallicon" style="cursor: pointer;" onclick="getId('+item['iId']+')" >'+
                                                                '</div>'+
                                                    '<div class="videodesc">'+
                                                    '<p style="float:left; font-size:16px; margin:5px 0 0 5px; font-weight:bold;cursor: pointer;" id="title-movie-'+item['iId']+'" onclick="getId('+item['iId']+')" >'+item['vVideo_title']+'</p>'+
                                                    '<div class="clear"></div>'+
                                                    '<p style="float:left; font-size:16px; margin:5px 0 0 5px; font-weight:bold;">Date Create : '+item['dDate_create']+'</p>'+
                                                    '<div class="clear"></div>'+
                                                    data_modify+
                                                    '</div>'+
                                                    '<div style="float:left; height:80px; width:13px;">'+
                                                    '<input type="button" class="switchoff" value="" style="cursor: pointer;" id="edit-movie-'+item['iId']+'" onclick="editMovie('+item['iId']+')"/>'+
                                                    '<div class="clear"></div>'+
                                                    '<input type="button" class="deletebutton" value="" id="delete-movie-'+item['iId']+'" style="cursor: pointer;" onclick="removeMovie('+item['iId']+')"/>'+
                                                    '</div>'+
                                            '</div>'+
                                            '</div>'
                    )
                }
            })
        }
        
    },'json')
}

function loadAllMoviePic(){
    $.post($projectExecPath+'/movies/get-movie',function(data){
        data_array = data;
        if(data_array['success'] == true && data_array['data'] != 'no result'){
            $.each(data_array['data'], function(s, item){
                if(item['dDate_modify']!=null){
                    var data_modify = '<p style="float:left; font-size:16px; margin:5px 0 0 5px; font-weight:bold;">Date modify : '+item['dDate_modify']+'</p>';
                }else{
                    data_modify = ''
                }
                if(data_array['show_btn'] == 'Yes'){
                    var btn = '<div style="float:left; height:80px; width:13px;">'+
                                '<input type="button" class="switchoff" value="" style="cursor: pointer;" id="edit-movie-'+item['iId']+'" onclick="editMovie('+item['iId']+')"/>'+
                                '<div class="clear"></div>'+
                                '<input type="button" class="deletebutton" value="" id="delete-movie-'+item['iId']+'" style="cursor: pointer;" onclick="removeMovie('+item['iId']+')"/>'+
                                '</div>'
                }else{
                    btn = '';
                }
                $("#thumbnail-show-image-youtube").append(
                                            '<div id="image-movie-'+item['iId']+'">'+
                                            '<div class="clear"></div>'+
                                                    '<div class="smallvideo">'+
                                                                '<div class="" id="image-movie-'+item['iId']+'" >'+
                                                                    '<img src="'+item['tVideo_image']+'"  class="smallicon" style="cursor: pointer;" onclick="getId('+item['iId']+')" >'+
                                                                '</div>'+
                                                    '<div class="videodesc">'+
                                                    '<p style="float:left; font-size:16px; margin:5px 0 0 5px; font-weight:bold;cursor: pointer;" id="title-movie-'+item['iId']+'" onclick="getId('+item['iId']+')" >'+item['vVideo_title']+'</p>'+
                                                    '<div class="clear"></div>'+
                                                    '<p style="float:left; font-size:16px; margin:5px 0 0 5px; font-weight:bold;">Date create : '+item['dDate_create']+'</p>'+
                                                    '<div class="clear"></div>'+
                                                    data_modify+
                                                    '</div>'+
                                                    btn+
                                                    
                                                    
                                            '</div>'+
                                            '</div>'
                    
                    )
            })
            
            $("div#thumbnail-show-image-youtube > div:first-child img").trigger('click')
        }
    },'json')
}


function getId(id){
    $("#edit-movie-full-div").html('');
    $("#remove-movie-full-div").html('');
    $("#discription-div-id").html('');
    $("#botton-discription-movie").html('')
    $("#movie-show-div-id").html('')
    $("#movie-show-title-id").html('')
    $("#movie-show-desc-id").html('')
    $("#movie-show-id").remove()
    var data = {};
    data['iId'] = id
    $.post($projectExecPath+'/movies/show-movie',data,function(resp){
        var movie_array = resp;
        $("#movie-show-div-id").append(movie_array.data.tVideo_embed_code)
        $("#movie-show-title-id").text(movie_array.data.vVideo_title)
        $("#movie-show-desc-id").append(movie_array.data.tDesc)
        $("#movie-show-div-id iframe").attr("width","637px")
        $("#movie-show-div-id iframe").attr("height","366px")

    },'json')
}


function editMovie(id){
    $("#form-insert-movie").slideUp()
    $.each(data_array['data'],function(s,item){
        if(item['iId'] == id){
            $("#tVideo_embed_code").val(item['tVideo_embed_code'])
            $("#tDesc").val(item['tDesc'])
            $("#vVideo_title").val(item['vVideo_title'])
            $("#more-info-form-save").attr("value","Edit Video")
            $("#more-info-form-save").attr("onclick","edit_video()")
            $("#movie-iId-edit").val(item['iId'])
            $("#form-insert-movie").slideDown()
        }
        
    })
    
    
}
function removeMovie(id){
    $("#delete-movie-alert").modal()
    $("#yes-delete-move-id").attr("onclick","delteMovie("+id+")")

}

function delteMovie(id){
    var remove_id = {}
    remove_id['iId'] = id;
    $.post($projectExecPath+'/movies/remove-movie', remove_id, function(resp){
        if(resp.success == true){
            $("#image-movie-"+id).remove()
            $("#movie-show-id").remove();
            $("#discription-div-id").remove();
            $("div#thumbnail-show-image-youtube > div:first-child img").trigger('click')
            $("#delete-movie-alert").modal('hide')
        }else{
            
        }
    }, 'json') 
}
function save_video(){
            var data_movie = {};
            data_movie['tDesc']=$("#tDesc").val()
            data_movie['tVideo_embed_code']=$("#tVideo_embed_code").val()
            data_movie['vVideo_title']=$("#vVideo_title").val()
            $.post($projectExecPath+'/movies/save-movie', data_movie, function(resp){
                if(resp.success == true){
                    $("#tVideo_embed_code").val('');
                    $("#tDesc").val('');
                    $("#vVideo_title").val('')
                    $("#form-insert-movie").slideToggle();
                    loadInfo(resp.data,'add');
                }

            }, 'json')
    
}
function edit_video(){
        var data_edit_movie = {};
            data_edit_movie['tDesc']=$("#tDesc").val()
            data_edit_movie['tVideo_embed_code']=$("#tVideo_embed_code").val()
            data_edit_movie['vVideo_title']=$("#vVideo_title").val()
            data_edit_movie['iId']=$("#movie-iId-edit").val()
            $.post($projectExecPath+'/movies/edit-movie', data_edit_movie, function(resp){
                if(resp.success == true){
                    $("#tVideo_embed_code").val('');
                    $("#tDesc").val('');
                    $("#vVideo_title").val('');
                    $("#more-info-form-save").attr("value","Save Video")
                    $("#movie-iId-edit").val('')
                    $("#more-info-form-save").attr("onclick","save_video()")
                    $("#form-insert-movie").slideUp();
                    loadInfo(resp.data,'edit');
                }

            }, 'json')
}
