$(document).ready(function(){
    loadPage($userId);
    $("#file_upload").uploadify({
        'swf': $projectExecLibrary+'/plugin/uploadify/uploadify.swf',
        'uploader': $projectExecPath+'/frontend/audio/save-music',
        'auto': true,
        'fileObjName': 'the_files',
        'fileSizeLimit': '10240KB',
        'fileTypeDesc': 'MUSIC',
        'buttonClass' : 'addmusicbtn',
        'buttonText' : 'Add Music',
        'fileTypeExts': '*.mp3; *.ogg;',
//        'debug' : true,
        'method': 'post',
        'formData': {'iMember_id' : $userId},
        'onUploadStart': function(file) {
            $("#file_upload").uploadify("settings", "someOtherKey", 2);
        },
        'onUploadSuccess': function(file, data, response) {
            var result= $.parseJSON(data);
            if(result.success == true){
                $("#no-music-id").hide();
                $("table>tbody").prepend(htmlMusic(result.data));
            }
        }
                
    });
    
    $('button[name="reset"]').click(function(){
        $("#manage-hero-unit-id").slideUp("slow");
    });
    
    var options= {
        dataType: 'json',
        target: '#output1',
        success: function(response){
            $('td[dataTd-id='+response.data.iId+']').html(response.data.vTitle);
            $('button[dataBTN-id='+response.data.iId+']').attr('data-vTitle', response.data.vTitle);
            $("#manage-hero-unit-id").slideUp("slow");
//            console.log(response);
        }
    };
    $('#change-title-id').ajaxForm(options);
    
    $( "#tBody-table-id" ).sortable({
            placeholder: "ui-state-highlight",
            opacity: 0.6, 
            cursor: 'move',
            axis: 'y',
            update: function(event, ui) {
                var stringDiv = "";
                $("#tBody-table-id").children().each(function(i) {
                    var li = $(this);
                    stringDiv += ""+li.attr("id")+'='+i+'&';
                });
                $.post('audio/sortable-music',
                    {
                        data: stringDiv
                    }
                );
            }
            
    });
    $( "#tBody-table-id" ).disableSelection();
$('tr[dataTr-id]').mousedown(function(){
        console.log('xxxxxxx');
        $(this).addClass('mouseDown');
    });
});

function loadPage($userId){
    $.ajax({
        type: 'post',
        url: $projectExecPath+"/frontend/audio/load-music",
        data: {
            iId: $userId
        },
        success: function(response){
            var result= $.parseJSON(response);
            if(result.success == true){
                if(result.data == ''){
                    $("table>tbody").append(htmlMusic(result.data));
                }else{
                    $.each(result.data, function(index, data){
                        $("table>tbody").append(htmlMusic(data));
                    });
                  $('tr[dataTr-id]').mouseover(function(){
                        $('tr[dataTr-id]').addClass('mouseClick');
                  });
                }
            }
        }
    });
}

function htmlMusic(data){
//    console.log(data);
    if(data == ''){
        var html=
            '<tr id="no-music-id">'+
                '<td>'+
                    '<span class="fontcolor">no music, please add music</span>'+
                '</td>'+
            '</tr>';
    } else{
        var html= 
            '<tr id="'+data.iId+'"  dataTr-id="'+data.iId+'" dataTr-iSort="'+data.iSort+'">'+
                '<td dataTd-id="'+data.iId+'" class="fontcolor">'+
                    data.vTitle+
                '</td>'+
                '<td>'+
                    '<button data-vTitle="'+data.vTitle+'" dataBTN-id="'+data.iId+'" onclick="editTitle('+data.iId+')" type="button" style="margin: 5px;" class="btn btn-info btn-small">'+
                        'EDIT'+
                    '</button>'+
                    '<button onclick="deleteMusic('+data.iId+')" type="button" style="margin: 5px;" class="btn btn-danger btn-small">'+
                        'DELETE'+
                    '</button>'+
                '</td>'+
            '</tr>';
    }
    return html;
    
}

function editTitle(iId){
    var vTitle= $('button[dataBTN-id='+iId+']').attr('data-vTitle');
    $("#manage-hero-unit-id").slideDown("slow");
    $("input[name='vTitle']").val(vTitle);
    $("input[name='iId']").val(iId);
}

function deleteMusic(iId){
    $("#deleteMusicModal").modal();
    $("#delete-music-yes").click(function(){
        $.post('audio/delete-music',
            {
                iId: iId
            },
            function(resp){
//                console.log(resp);
                $("tr[dataTr-id='"+resp.data+"']").remove()
                $("#deleteMusicModal").modal('hide');
                $("#manage-hero-unit-id").slideUp("slow");
                $("#delete-music-yes").unbind('click');
                if(resp.count == 0){
                    $("#no-music-id").show();
                }
            },
            'json'
            );
    });
}