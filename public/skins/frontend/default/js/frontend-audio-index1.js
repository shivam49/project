$(document).ready(function(){
//    $("#refresh-icon-id").tooltip('hide');
    //edit tracks
    $("#done-edit-track-id").click(function(){
        $.post('audio/update-track', 
        {
            'vTitle' : $("#track-title-id").val(),
            'dDate_release' : $("#date-release-track-id").val(),
            'iId' : $("#track-id").val()
            
        }, function(resp){
//            console.log(resp)
            if(resp.success ==true){
                $('#demo').jstree('refresh',-1);
                $("#show-edit-track-field").slideUp()
                $("#show-audio-details-id").slideDown()
            }
        }, 'json')
    })
    
    
    //done change cover album
    $("#done-change-cover-album-id").click(function(){
        $("#change-cover-modal").slideUp()
        $("#edit-album").slideDown()
    })
    
    //load image for cover from images table
    $("#image-album-cover-id").click(function(){
        $("#tab-cover2 *").remove();
        $.post('audio/load-image',function(resp){
            $.each(resp.data, function(s,item){
                $("#tab-cover2").append('<div id="cover_'+item['iId']+'" onclick = setCover('+item['iId']+') class="list-image-cover" data-cover="cover" class="icon-edit">'+
                        '<img id="img_'+item['iId']+'" data-placement="bottom" data-toggle="tooltip" title="'+item['vImg']+'"  src="'+$projectExecImage+'/'+item['vImg']+'" class="image-new-up" />'+
                   '</div>')
               $("#img_"+item['iId']).tooltip('hide')
            })
        },'json')
        $("#change-cover-modal").slideDown()
        $("#edit-album").slideUp()
    })
    
    //update album when click done buttom
    $("#done-edit-album-id").click(function(){
        $.post('audio/update-album', 
        {
            "vTitle":$("#album-title-id").val(),
            "release":$("#date-release-album-id").val(),
            "desc":$("#description-album-edit-id").val()
        },
        function(resp){
            if(resp.success == true){
                $('#demo').jstree('refresh',-1);
                $('#msg').removeClass('alert-success').html('');
                $('#msg').removeClass('alert-error').html('');
                $("#edit-album").slideUp()
                $("#show-album-details-id").slideDown()
            }
        }, 'json')
    })


    //function for create tree of albums and tracks
    $(function () {
        $("#demo")
            .bind("before.jstree", function (e, data) {
                    $("#alog").append(data.func + "<br />");
                               if(data.func == 'is_selected'){
                                   $("#show-album-details-id *").remove()
                                    $("#show-audio-player-id div").remove()
                                    $("#show-audio-player-id *").remove()
                                    $("#show-audio-details-id div").remove()
                                    $("#fullScreen-player").remove()
                                    $("#show-edit-track-field").slideUp()
                                    $("#edit-album").slideUp()
                                    $("#change-cover-modal").slideUp()
                                    //run when click a node in tree
                                   if(data.args[0][0].id.replace("node_","")!= 0){
                                       if(data.args[0][0].attributes[1].nodeValue == 'folder'){
                                           //if folder node
                                            $.post('audio/get-info', 
                                            {
                                                "id":data.args[0][0].id.replace("node_",""),
                                                "type":data.args[0][0].attributes[1].nodeValue
                                            }, 
                                            function(r){
                                                create_detaile_album(r)
                                            }, 'json')
                                       }else{
                                           //if file node
                                           $("#show-album-details-id div").remove()
                                           plyeAudio(data.args[0][0].id.replace("node_",""))
                                       }

                                   }

                                }
            })
            .jstree({ 
                    // List of active plugins
                    "plugins" : [ 
                            "themes",
                            "json_data","ui","crrm","cookies","dnd","search","types","hotkeys"
                    ],

                    // I usually configure the plugin that handles the data first
                    // This example uses JSON as it is most common
                    "json_data" : { 
                            // This tree is ajax enabled - as this is most common, and maybe a bit more complex
                            // All the options are almost the same as jQuery's AJAX (read the docs)
                            "ajax" : {
                                    // the URL to fetch the data
                                    "url" : "audio/get-list-album",
                                    // the `data` function is executed in the instance's scope
                                    // the parameter is the node being loaded 
                                    // (may be -1, 0, or undefined when loading the root nodes)
                                    "data" : function (n) { 
                                            // the result is fed to the AJAX request `data` option
                                            return { 
                                                    "operation" : "get_children", 
                                                    "id" : n.attr ? n.attr("id").replace("node_","") : -1 
                                            }; 
                                    }
                            }
                    },
                    // Configuring the search plugin
                    "search" : {
                            // As this has been a common question - async search
                            // Same as above - the `ajax` config option is actually jQuery's AJAX object
                            "ajax" : {
                                    "url" : "./server.php",
                                    // You get the search string as a parameter
                                    "data" : function (str) {
                                            return { 
                                                    "operation" : "search", 
                                                    "search_str" : str 
                                            }; 
                                    }
                            }
                    },
                    // Using types - most of the time this is an overkill
                    // read the docs carefully to decide whether you need types
                    "types" : {
                            // I set both options to -2, as I do not need depth and children count checking
                            // Those two checks may slow jstree a lot, so use only when needed
                            "max_depth" : 3,
                            "max_children" : -2,
                            // I want only `drive` nodes to be root nodes 
                            // This will prevent moving or creating any other type as a root node
                            "valid_children" : [ "drive" ],
                            "types" : {
                                    // The default type
                                    "default" : {
                                            // I want this type to have no children (so only leaf nodes)
                                            // In my case - those are files
                                            "valid_children" : "none",
    //                                        "valid_children" : [ "defult", "folder" ],
                                            // If we specify an icon for the default type it WILL OVERRIDE the theme icons
                                            "icon" : {
                                                    "image" : $projectExecPath+"/library/plugin/jstree-v.pre1.0/_demo/file.png"
                                            }
                                    },
                                    // The `folder` type
                                    "folder" : {
                                            // can have files and other folders inside of it, but NOT `drive` nodes
                                            "valid_children" : [ "default", "folder" ],
                                            "icon" : {
                                                    "image" : $projectExecPath+"/library/plugin/jstree-v.pre1.0/_demo/folder.png"
                                            }
                                    },
                                    // The `drive` nodes 
                                    "drive" : {
                                            // can have files and folders inside, but NOT other `drive` nodes
                                            "valid_children" : [ "default", "folder" ],
                                            "icon" : {
                                                    "image" : "./root.png"
                                            },
                                            // those prevent the functions with the same name to be used on `drive` nodes
                                            // internally the `before` event is used
                                            "start_drag" : false,
                                            "move_node" : false,
                                            "delete_node" : false,
                                            "remove" : false
                                    }
                            }
                    },
                    // UI & core - the nodes to initially select and open will be overwritten by the cookie plugin
                    // the UI plugin - it handles selecting/deselecting/hovering nodes
                    "ui" : {
                            // this makes the node with ID node_4 selected onload
                    },
                    // the core plugin - not many options here
                    "core" : { 
                            // just open those two nodes up
                            // as this is an AJAX enabled tree, both will be downloaded from the server
                    }
            })
            .bind("create.jstree", function (e, data) {
                    $.post(
                            "audio/create-album", 
                            { 
                                    "operation" : "create_node", 
                                    "id" : data.rslt.parent.attr("id").replace("node_",""), 
                                    "position" : data.rslt.position,
                                    "title" : data.rslt.name,
                                    "type" : data.rslt.obj.attr("rel")
                            }, 
                            function (r) {
                                var r= $.parseJSON(r);
                                    if(r.status) {
                                            $(data.rslt.obj).attr("id", "node_" + r.id);
                                            $('#demo').jstree('refresh',-1);
                                    }
                                    else {
                                            $.jstree.rollback(data.rlbk);
                                    }
                            }
                    );
            })
            .bind("remove.jstree", function (e, data) {
                if($("#demo").jstree("types")._get_node().length != 0){
                    data.rslt.obj.each(function () {
                            $.ajax({
                                    async : false,
                                    type: 'POST',
                                    url: "audio/remove",
                                    data : { 
                                            "operation" : "remove_node", 
                                            "id" : this.id.replace("node_",""),
                                            "type" : data.rslt.obj.attr("rel")
                                    }, 
                                    success : function (r) {
                                        var r= $.parseJSON(r);
                                            if(!r.status) {
                                                    data.inst.refresh();
                                            }else{
                                                $("#delete-album-track-id").modal('hide');
                                                $("#show-album-details-id *").remove()
                                                $("#show-audio-player-id div").remove()
                                                $("#show-audio-player-id *").remove()
                                                $("#show-audio-details-id div").remove()
                                                $("#fullScreen-player").remove()
                                            }
                                    }
                            });
                    });
                }

            })
            .bind("rename.jstree", function (e, data) {
                    $.post(
                            "audio/rename", 
                            { 
                                    "operation" : "rename_node", 
                                    "id" : data.rslt.obj.attr("id").replace("node_",""),
                                    "title" : data.rslt.new_name,
                                    "type" : data.rslt.obj.attr("rel")
                            }, 
                            function (r) {
                                var r= $.parseJSON(r);
                                    if(!r.status) {
                                            $.jstree.rollback(data.rlbk);
                                    }else{
                                         $('#demo').jstree('refresh',-1);
                                    }
                            }
                    );
            })
            .bind("move_node.jstree", function (e, data) {
                    data.rslt.o.each(function (i) {
                            $.ajax({
                                    async : false,
                                    type: 'POST',
                                    url: "audio/move-music",
                                    data : { 
                                            "operation" : "move_node", 
                                            "id" : $(this).attr("id").replace("node_",""), 
                                            "ref" : data.rslt.cr === -1 ? 1 : data.rslt.np.attr("id").replace("node_",""), 
                                            "position" : data.rslt.cp + i,
                                            "title" : data.rslt.name,
                                            "copy" : data.rslt.cy ? 1 : 0
                                    },
                                    success : function (r) {
                                        var r= $.parseJSON(r);
                                            if(!r.status) {
                                                    $.jstree.rollback(data.rlbk);
                                            }
                                            else {
                                                    $(data.rslt.oc).attr("id", "node_" + r.id);
                                                    if(data.rslt.cy && $(data.rslt.oc).children("UL").length) {
                                                            data.inst.refresh(data.inst._get_parent(data.rslt.oc));
                                                    }
                                            }
                                            $("#analyze").click();
                                    }
                            });
                    });
            });
    });

    //function for buttom of tree when click
    $(function () { 
	$("#mmenu button").click(function () {
		switch(this.id) {
			case "add_default":
                                    $("#demo").jstree("create");
				break;
			case "add_folder":
                                if($("#demo").jstree("types")._get_parent()==-1){
                                    $("#demo").jstree("create", null, "last", { "attr" : { "rel" : this.id.toString().replace("add_", "") } });
                                }else{
                                    $("#notifier-errors").append('<div class="alert">'+
                                    '<button type="button" class="close" data-dismiss="alert">&times;</button>'+
                                    '<strong>Warning!</strong> You will not be able to add folder in this folder    .'+
                                    '</div>')
                                    $("#notifier-errors").slideDown()
                                    $("#notifier-errors").fadeOut(4000,function(transform){
                                        $("#notifier-errors > div").remove()
                                    });
                                }
				
				break;
                        case "remove":
                                if($("#demo").jstree("types")._get_parent()!=-1){
//                                    $("#notifier-errors > div").remove()
//                                    $("#notifier-errors").append(
//                                        '<div class="alert alert-block alert-error fade in">'+
//                                            '<button type="button" class="close" data-dismiss="alert">&times;</button>'+
//                                                '<h4 class="alert-heading">Warning!</h4>'+
//                                            '<p>Are you sure to delete this place?</p>'+
//                                            '<p>'+
//                                              '<a class="btn btn-danger" href="#" id="take-this-action">Take this action</a>'+
//                                            '</p>'+
//                                          '</div>')
//                                      $("#notifier-errors").slideDown()
//                                      $("#take-this-action").click(function(){
//                                          $("#notifier-errors").fadeOut(function(){
//                                              $("#notifier-errors > div").remove()
//                                          })
//                                          $("#demo").jstree("remove");
//                                          
//                                      })
                                      $("#delete-album-track-id").modal();
                                      $("#delete-tree-done-id").click(function(){
                                          $("#demo").jstree("remove");
                                          
                                      })
//                                    
                                }else{
                                    $("#notifier-errors").append('<div class="alert">'+
                                    '<button type="button" class="close" data-dismiss="alert">&times;</button>'+
                                    '<strong>Warning!</strong> You will not be able to remove the main folder.'+
                                    '</div>')
                                    $("#notifier-errors").slideDown()
                                    $("#notifier-errors").fadeOut(4000,function(transform){
                                        $("#notifier-errors > div").remove()
                                    });
                                }
				
				break;
                        case "rename":
                                if($("#demo").jstree("types")._get_parent()!=-1){
                                    $("#demo").jstree("rename");
                                }else{
                                    
                                    $("#notifier-errors").append('<div class="alert">'+
                                    '<button type="button" class="close" data-dismiss="alert">&times;</button>'+
                                    '<strong>Warning!</strong> You will not be able to rename the main folder.'+
                                    '</div>')
                                    $("#notifier-errors").slideDown()
                                    $("#notifier-errors").fadeOut(4000,function(transform){
                                        $("#notifier-errors > div").remove()
                                    });
                                }
				
				break;
			case "search":
				$("#demo").jstree("search", document.getElementById("text").value);
				break;
			case "text": break;
			default:
				$("#demo").jstree(this.id);
				break;
                }
            });
        });
    
    
    //function for upload image for cover from system
    $('#file_upload_cover').uploadify({
        'swf': $projectExecPath+'/library/plugin/uploadify/uploadify.swf',
        'uploader': $projectExecPath+'/frontend/audio/upload-cover',
        'checkScript'  :$projectExecPath+'/frontend/audio/check',
        'fileObjName': 'the_files',
        'fileSizeLimit': '2048KB',
        'multi'    : false,
        'fileTypeExts': '*.gif; *.jpg; *.png;',
//        'debug' : true,
        'method': 'post',
        'formData': {'iMember_id' : $userId,'id':$album_id}, 
        'onSelectError' : function() {
            alert('The file ' + file.name + ' returned an error and was not added to the queue.');
        },
        'onUploadSuccess' : function(file, data, response) {
            var result= $.parseJSON(data);
            if(result.success==true){
                $("#img-show-up-cover-id").attr("src", $projectExecImage+'/'+result.cover)
                $("#image-album-cover-id").attr("src", $projectExecImage+'/'+result.cover)
                $("#show-small-cover-album-id").attr("src", $projectExecImage+'/'+result.cover)
            }
        },
        'onUploadError' : function(file, errorCode, errorMsg, errorString) {
            alert('The file ' + file.name + ' could not be uploaded: ' + errorString);
        }
    });
    
    
    //function for upload audio mp3 from system     
    $('#file_upload').uploadify({
            'swf': $projectExecPath+'/library/plugin/uploadify/uploadify.swf',
            'uploader': $projectExecPath+'/frontend/audio/upload',
            'checkScript'  :$projectExecPath+'/frontend/audio/check',
            'fileTypeExts' : '*.mp3',
            'fileObjName': 'the_files',
            'method': 'post',
            'formData': {'iMember_id' : $userId}, 
            'onSelectError' : function() {
                alert('The file ' + file.name + ' returned an error and was not added to the queue.');
            },
            'onUploadSuccess' : function(file, data, response) {
//                console.log('ssss')
//                console.log(data)
                var result= $.parseJSON(data);
                if(result.success==true){
                    $.get('audio/get-list-album', function () { $('#demo').jstree('refresh',-1); })
                }
            },
            'onUploadError' : function(file, errorCode, errorMsg, errorString) {
                alert('The file ' + file.name + ' could not be uploaded: ' + errorString);
            }
        });
        
     //function for upload audio ogg from system     
    $('#file_upload_ogg').uploadify({
            'swf': $projectExecPath+'/library/plugin/uploadify/uploadify.swf',
            'uploader': $projectExecPath+'/frontend/audio/upload-ogg',
            'checkScript'  :$projectExecPath+'/frontend/audio/check',
            'fileTypeExts' : '*.ogg',
            'fileObjName': 'the_files',
            'method': 'post',
            'multi':false,
            'formData': {'iMember_id' : $userId}, 
            'onSelectError' : function() {
                alert('The file ' + file.name + ' returned an error and was not added to the queue.');
            },
            'onUploadStart' : function(file) {
                    $("#file_upload_ogg").uploadify("settings", 'formData', {'iTrack_id' : $("#track-id").val()});
            },
            'onUploadSuccess' : function(file, data, response) {
                var result= $.parseJSON(data);
                if(result.success==true){
                    $("#show-error-upload-ogg div").remove();
                    $("#show-error-upload-ogg")
                        .append('<div class="alert alert-success">Upload ogg file Success</div>')
                        setTimeout(function(){
                            $("#show-error-upload-ogg div").remove();
                        },3000)
                        
                        //set new data when upload ogg file
                        $("#set-name-file-ogg").slideDown();
                        $("#music-name-ogg-id").text(result.trackName);
                        $("#music-href-ogg-id").attr("href",$projectExecPath+'/public/uploaded_resource/frontend/band-'+result.band_id+'/audio/'+result.trackName)
                }else{
                    $("#show-error-upload-ogg div").remove();
                    $("#show-error-upload-ogg")
                        .append('<div class="alert alert-error">Upload ogg file Faild</div>')
                        setTimeout(function(){
                            $("#show-error-upload-ogg div").remove();
                        },3000)
                }
            },
            'onUploadError' : function(file, errorCode, errorMsg, errorString) {
                alert('The file ' + file.name + ' could not be uploaded: ' + errorString);
            }
        });
        
         //function for upload audio ogg from system     
    $('#file_upload_mp3').uploadify({
            'swf': $projectExecPath+'/library/plugin/uploadify/uploadify.swf',
            'uploader': $projectExecPath+'/frontend/audio/upload-mp3',
            'checkScript'  :$projectExecPath+'/frontend/audio/check',
            'fileTypeExts' : '*.mp3',
            'fileObjName': 'the_files',
            'method': 'post',
            'multi':false,
            //set data from layout
            'formData': {'iMember_id' : $userId}, 
            //set data in js
            'onUploadStart' : function(file) {
                    $("#file_upload_mp3").uploadify("settings", 'formData', {'iTrack_id' : $("#track-id").val()});
            },
            'onSelectError' : function() {
                alert('The file ' + file.name + ' returned an error and was not added to the queue.');
            },
            
            'onUploadSuccess' : function(file, data, response) {
                var result= $.parseJSON(data);
                if(result.success==true){
                    $("#show-error-upload-mp3 div").remove();
                    $("#show-error-upload-mp3")
                        .append('<div class="alert alert-success">Upload mp3 file Success</div>')
                        setTimeout(function(){
                            $("#show-error-upload-mp3 div").remove();
                        },3000)
                        
                        //set new mp3 file when upload
                        $("#set-name-file-mp3").slideDown();
                        $("#music-name-mp3-id").text(result.trackName)
                        $("#music-href-mp3-id").attr("href",$projectExecPath+'/public/uploaded_resource/frontend/band-'+result.band_id+'/audio/'+result.trackName)
                }else{
                    $("#show-error-upload-mp3 div").remove();
                    $("#show-error-upload-mp3")
                        .append('<div class="alert alert-error">Upload mp3 file Faild</div>')
                        setTimeout(function(){
                            $("#show-error-upload-mp3 div").remove();
                        },3000)
                }
            }
        });

    })
    
    
    
    

    // function for create detail album and load player for album
    function create_detaile_album(r){
        if(r.type == 'folder'){
            $("#tab-cover2").find(".list-image-cover-selected").removeClass("list-image-cover-selected");
            $("#show-album-details-id > div").remove();
            $("#show-audio-details-id > div").remove();
            $("#show-edit-track-field").slideUp();
            $("#show-album-details-id").slideDown();
            $("#edit-album").slideUp()
            $("#change-cover-modal").slideUp()
            $("#show-audio-player-id").slideDown()
            if(r.data.dDate_release != null){
                var release = '<h6>Date Release : '+r.data.dDate_release+'</h6>'
            }else{
                release = '';
            }
            if(r.data.dDate_modify != null){
                var modify = '<h6>Date Modify : '+r.data.dDate_modify+'</h6>'
            }else{
                modify = ''
            }
            if(r.data.iClick != null){
                var click = '<h6>Click Count : '+r.data.iClick+'</h6>'
            }else{
                click = ''
            }

            //create album detail
            $("#show-album-details-id").append(
                '<div class="span12 show-audio-details">'+
                    '<div class="span12">'+
                        '<div class="span4"  >'+
                            '<img class="image-album-cover-cls" src="'+$projectExecPath+'/public/uploaded_resource/frontend/registers.jpg ?>" style="width: 100%;height: 150px" />'+
                        '</div>'+
                        '<div class="span8">'+
                            '<h5>'+r.data.vTitle+'</h5>'+
                            '<h6>Date Create : '+r.data.dDate_create+'</h6>'+
                            release+
                            modify+
                            click+
                        '</div>'+
                        '<div class="btn btn-small btn-primary botton-change-album text-right" onclick = modal_show()>EDIT <i  id="edit-icon-cls" data-placement="top" data-toggle="tooltip" title="Click To Edit" class="icon-edit icon-white"></i></div>'+
                        '</div>'+
                        '<div class="span12" style="margin-left: 0" id="show-track-list">'+
                        '</div>'+
                '</div>'
            );
//                $("#edit-icon-cls").tooltip('hide')
                $("#image-album-cover-id").tooltip('hide')
            //set image  
            if(r.data.vImg_cover == null){
                $(".image-album-cover-cls").attr("src",$projectExecPath+'/public/uploaded_resource/frontend/band/image/default-album-cover.jpg')
                $("#img-show-up-cover-id").attr("src",$projectExecPath+'/public/uploaded_resource/frontend/band/image/default-album-cover.jpg')
                $("#show-small-cover-album-id").attr("src", $projectExecImage+'/default-album-cover.jpg')
            }else{
                $(".image-album-cover-cls").attr("src",$projectExecPath+'/public/uploaded_resource/frontend/band/image/'+r.data.vImg_cover)
                $("#img-show-up-cover-id").attr("src",$projectExecPath+'/public/uploaded_resource/frontend/band/image/'+r.data.vImg_cover)
                $("#show-small-cover-album-id").attr("src", $projectExecImage+'/'+r.data.vImg_cover)
            }

            $("#album-title-id").val(r.data.vTitle)
             $("#tab2 *").remove()
            $("#tab2").append('<textarea id="description-album-edit-id" class="textarea" data-value="" placeholder="Enter text ..." style="width: 98%; height: 200px"></textarea>')
            $('.textarea').wysihtml5();
            $('.textarea').val(r.data.tDesc);

            //for load date piker
            $(function() {
            $('#datetimepicker1').datetimepicker({
                language: 'pt-BR',
                 pickTime: false
              });
            });

            if(r.data.dDate_release == null){
                $("#date-release-album-id").val('-')
            }else{
               $("#date-release-album-id").val(r.data.dDate_release)
            }

            //load jpalayer for album
            jplayerAlbum(r.data)
        }
    }

    //playe audio when click in tree
    function plyeAudio(id){
        $("#about-music-id").remove();
        $("#show-audio-player-id div").remove()
        var dataAudio = {}
        dataAudio['id'] =id
        $.post('audio/play-audio', dataAudio, function(resp){
            if(resp.data['dDate_release']==null){
                var date = '<h6>Date Create : '+resp.data['dDate_create']+'</h6>'
            }else{
                date = '<h6>Date Create : '+resp.data['dDate_release']+'</h6>'
            }
            if(resp['success'] == true){
//                console.log(resp.data.iId)
                track_ss = resp.data.iId;
                jplayer(resp)
            }
        }, 'json')
    }
    
    //jpalayer for albums
    function jplayerAlbum(resp){
        $("#show-audio-player-id div").remove()
        if(resp.tracks[0] != null ){
        $("#fullScreen-player").remove()
        $("#fullScreen-player-id").append('<button class="btn btn-small btn-inverse" id="fullScreen-player">Hide and Show Player <i class="icon-fullscreen icon-white"  data-placement="right" data-toggle="tooltip" title="Show and Hide Player" ></i></button>')
//        $("#fullScreen-player").tooltip('hide')
        $("#fullScreen-player").click(function(){
            $("#show-audio-player-id").toggle(300)
        })
        
        //jplayer html
         $("#show-audio-player-id").append(
                    '<div id="jp_container_1" class="jp-video jp-video-270p">'+
                            '<div class="jp-type-playlist">'+
                                    '<div id="jquery_jplayer_1" class="jp-jplayer"></div>'+
                                    '<div class="jp-gui">'+
                                            '<div class="jp-video-play">'+
                                                    '<a href="javascript:;" class="jp-video-play-icon" tabindex="1">play</a>'+
                                            '</div>'+
                                            '<div class="jp-interface">'+
                                                    '<div class="jp-progress">'+
                                                            '<div class="jp-seek-bar">'+
                                                                    '<div class="jp-play-bar"></div>'+
                                                            '</div>'+
                                                    '</div>'+
                                                    '<div class="jp-current-time"></div>'+
                                                    '<div class="jp-duration"></div>'+
                                                    '<div class="jp-controls-holder">'+
                                                            '<ul class="jp-controls">'+
                                                                    '<li><a href="javascript:;" class="jp-previous" tabindex="1">previous</a></li>'+
                                                                    '<li><a href="javascript:;" class="jp-play" tabindex="1">play</a></li>'+
                                                                    '<li><a href="javascript:;" class="jp-pause" tabindex="1">pause</a></li>'+
                                                                    '<li><a href="javascript:;" class="jp-next" tabindex="1">next</a></li>'+
                                                                    '<li><a href="javascript:;" class="jp-stop" tabindex="1">stop</a></li>'+
                                                                    '<li><a href="javascript:;" class="jp-mute" tabindex="1" title="mute">mute</a></li>'+
                                                                    '<li><a href="javascript:;" class="jp-unmute" tabindex="1" title="unmute">unmute</a></li>'+
                                                                    '<li><a href="javascript:;" class="jp-volume-max" tabindex="1" title="max volume">max volume</a></li>'+
                                                            '</ul>'+
                                                            '<div class="jp-volume-bar">'+
                                                                    '<div class="jp-volume-bar-value"></div>'+
                                                            '</div>'+
                                                            '<ul class="jp-toggles">'+
                                                                    '<li><a href="javascript:;" class="jp-full-screen" tabindex="1" title="full screen">full screen</a></li>'+
                                                                    '<li><a href="javascript:;" class="jp-restore-screen" tabindex="1" title="restore screen">restore screen</a></li>'+
                                                                    '<li><a href="javascript:;" class="jp-shuffle" tabindex="1" title="shuffle">shuffle</a></li>'+
                                                                    '<li><a href="javascript:;" class="jp-shuffle-off" tabindex="1" title="shuffle off">shuffle off</a></li>'+
                                                                    '<li><a href="javascript:;" class="jp-repeat" tabindex="1" title="repeat">repeat</a></li>'+
                                                                    '<li><a href="javascript:;" class="jp-repeat-off" tabindex="1" title="repeat off">repeat off</a></li>'+
                                                            '</ul>'+
                                                    '</div>'+
                                                    '<div class="jp-title">'+
                                                            '<ul>'+
                                                                    '<li></li>'+
                                                            '</ul>'+
                                                    '</div>'+
                                            '</div>'+
                                    '</div>'+
                                    '<div class="jp-playlist">'+
                                            '<ul>'+
                                                    '<!-- The method Playlist.displayPlaylist() uses this unordered list -->'+
                                                    '<li></li>'+
                                            '</ul>'+
                                    '</div>'+
                                    '<div class="jp-no-solution">'+
                                            '<span>Update Required</span>'+
                                            'To play the media you will need to either update your browser to a recent version or update your <a href="http://get.adobe.com/flashplayer/" target="_blank">Flash plugin</a>.'+
                                    '</div>'+
                            '</div>'+
                    '</div>')

                var data = []
            //create each for generate array of track for show in jplayer
            $.each(resp.tracks,function(s,item){
                if(resp.vImg_cover == null){
                    var imageCover = $projectExecImage+'/default-album-cover.jpg';
                }else{
                    imageCover = $projectExecImage+'/'+resp.vImg_cover;
                }
                
                if(item['vFile_mp3'] && item['vFile_ogg']){
                    var format = "Yes"
                    data.push({
                        title:item['vTitle'],
    //                            artist : "The Stark Palace",
                        mp3 : $projectExecPath+'/public/uploaded_resource/frontend/band-'+item['iMember_id']+'/audio/'+item['vFile_mp3'],
                        oga : $projectExecPath+'/public/uploaded_resource/frontend/band-'+item['iMember_id']+'/audio/'+item['vFile_ogg'],
                        poster :imageCover
                    })
                }else{
                    format = "No";
                    data.push({
                        title:item['vTitle'],
    //                            artist : "The Stark Palace",
                        mp3 : $projectExecPath+'/public/uploaded_resource/frontend/band-'+item['iMember_id']+'/audio/'+item['vFile_mp3'],
//                        oga : $projectExecPath+'/public/uploaded_resource/frontend/band-'+item['iMember_id']+'/audio/'+item['vFile_ogg'],
                        poster :imageCover
                    })
                }
                
            })
            
            //jplayer js
            new jPlayerPlaylist({
                jPlayer: "#jquery_jplayer_1",
                cssSelectorAncestor: "#jp_container_1"
            },
            //array of data for show track list in jplayer
            data
            , {
//                    swfPath: "js",
                    swfPath: $projectExecPath+"/library/plugin/jQuery.jPlayer.2.3.0.demos/js",
                    supplied: "webmv, ogv, m4v,mp3",
                    smoothPlayBar: true,
                    keyEnabled: true,
                    wmode: "window",
                    audioFullScreen: true,
                    canplay: function() {
                    $("#jquery_jplayer_1").jPlayer("play");
                    }
            });
        }
    }


    function jplayer(resp){
        $("#show-album-details-id > div").remove()
        $("#show-audio-details-id > div").remove()
        $("#show-audio-player-id div").remove();
        $("#show-audio-details-id").slideDown();
        $("#show-audio-player-id").slideDown()
        $("#edit-album").slideUp()
        $("#change-cover-modal").slideUp()
        $("#fullScreen-player").remove()
        
        //append icon for hide and show player
        $("#fullScreen-player-id").append('<button class="btn btn-small btn-inverse" id="fullScreen-player">Hide and Show Player <i class="icon-fullscreen icon-white"  data-placement="right" data-toggle="tooltip" title="Show and Hide Player" ></i></button>')
//        $("#fullScreen-player").tooltip('hide')
        $("#fullScreen-player").click(function(){
            $("#show-audio-player-id").toggle(300)
        })
        
        //jplayer html
        $("#show-audio-player-id").append('<div id="jquery_jplayer_1" class="jp-jplayer span12"></div>'+
                    '<div id="jp_container_1" class="jp-audio">'+
                            '<div class="jp-type-single">'+
                                    '<div class="jp-gui jp-interface">'+
                                            '<ul class="jp-controls">'+
                                                    '<li><a href="javascript:;" class="jp-play" tabindex="1">play</a></li>'+
                                                    '<li><a href="javascript:;" class="jp-pause" tabindex="1">pause</a></li>'+
                                                    '<li><a href="javascript:;" class="jp-stop" tabindex="1">stop</a></li>'+
                                                    '<li><a href="javascript:;" class="jp-mute" tabindex="1" title="mute">mute</a></li>'+
                                                    '<li><a href="javascript:;" class="jp-unmute" tabindex="1" title="unmute">unmute</a></li>'+
                                                    '<li><a href="javascript:;" class="jp-volume-max" tabindex="1" title="max volume">max volume</a></li>'+
                                            '</ul>'+
                                            '<div class="jp-progress">'+
                                                    '<div class="jp-seek-bar">'+
                                                            '<div class="jp-play-bar"></div>'+
                                                    '</div>'+
                                            '</div>'+
                                            '<div class="jp-volume-bar">'+
                                                    '<div class="jp-volume-bar-value"></div>'+
                                            '</div>'+
                                            '<div class="jp-time-holder">'+
                                                    '<div class="jp-current-time"></div>'+
                                                    '<div class="jp-duration"></div>'+

                                                    '<ul class="jp-toggles">'+
                                                            '<li><a href="javascript:;" class="jp-repeat" tabindex="1" title="repeat">repeat</a></li>'+
                                                            '<li><a href="javascript:;" class="jp-repeat-off" tabindex="1" title="repeat off">repeat off</a></li>'+
                                                    '</ul>'+
                                            '</div>'+
                                    '</div>'+
                                    '<div class="jp-title">'+
                                            '<ul>'+
                                                    '<li id = "title-music-show">Cro Magnon Man</li>'+
                                            '</ul>'+
                                    '</div>'+
                                    '<div class="jp-no-solution">'+
                                            '<span>Update Required</span>'+
                                            'To play the media you will need to either update your browser to a recent version or update your <a href="http://get.adobe.com/flashplayer/" target="_blank">Flash plugin</a>.'+
                                    '</div>'+
                            '</div>'+
                    '</div>')
        //set data in track edit form
        $("#title-music-show").text(resp.data['vTitle']);
        $("#track-title-id").val(resp.data['vTitle'])
        $("#track-id").val(resp.data['iId']);
        if(resp.data['vFile_ogg'] != ''){
            $("#set-name-file-ogg").slideDown();
            $("#music-name-ogg-id").text(resp.data['vFile_ogg'])
            $("#music-href-ogg-id").attr("href",$projectExecPath+'/public/uploaded_resource/frontend/band-'+resp.data['iBand_id']+'/audio/'+resp.data['vFile_ogg'])
        }else{
            $("#set-name-file-ogg").slideUp();
        }
        if(resp.data['vFile_mp3'] != ''){
            $("#set-name-file-mp3").slideDown();
            $("#music-name-mp3-id").text(resp.data['vFile_mp3'])
            $("#music-href-mp3-id").attr("href",$projectExecPath+'/public/uploaded_resource/frontend/band-'+resp.data['iBand_id']+'/audio/'+resp.data['vFile_mp3'])
        }else{
            $("#set-name-file-mp3").slideUp();
        }
        
        //function for load date piker
        $(function() {
            $('#datetimepicker2').datetimepicker({
            language: 'pt-BR',
             pickTime: false
          });
        });
        //array for create jplayer contain oga and mp3
        var data_tracks= [];
        
        if(resp.data['dDate_release']== null){
            $("#date-release-track-id").val('-')
        }else{
           $("#date-release-track-id").val(resp.data['dDate_release'])
           
        }
        if(resp.data['dDate_release'] == null ){
            var release = '-'
        }else{
            release = resp.data['dDate_release']
        }
        if(resp.data['iClick'] != null){
            var click = resp.data['iClick']
        }else{
            click = ''
        }
        if(resp.data['eShow_in_profile_playlist'] == 'Yes'){
            var flag = '<div id="botton-default-audio-id"  onclick="set_undefult('+resp.data['iId']+')" class="btn btn-small btn-success botton-default-audio text-right">Show In Profile <i id="default-icon-cls-track"  class="icon-flag icon-white"></i></div>'
        }else{
            flag = '<div id="botton-undefault-audio-id" class="btn btn-small btn-primary botton-default-audio text-right" onclick="set_defult('+resp.data['iId']+')" style="cursor: pointer">Hide From Profile <i id="unDefault-icon-cls-track" data-placement="top" data-toggle="tooltip" data-original-title="Set Default Track" class="icon-ok icon-white" ></i></div>'
        }
        
        //append track details
        $("#show-audio-details-id").append(
            '<div class="span12 show-track-list"  style="margin-left: 0" id="">'+
               '<div class="span12" style="margin-top: 5px">'+
                   '<div class="span7">'+
                       '<h5><i class="icon-music"></i> '+resp.data['vTitle']+'</h5>'+
                   '</div>'+
               '</div>'+
               '<div class="">'+
                       '<h6 style="margin:0;font-size:10px;color:#999">Date Create :'+ resp.data['dDate_create']+'</h6>'+
               '</div>'+
               '<div class="">'+
                       '<h6 style="margin:0;font-size:10px;color:#999">Date Release :'+release+'</h6>'+
               '</div>'+
               '<div class="">'+
                       '<h6 style="margin:0;font-size:10px;color:#999">Click Count :'+click+'</h6>'+
               '</div>'+
               '<div class="btn btn-small btn-primary botton-change-audio text-right" onclick="show_track_edit()">EDIT <i id="edit-icon-cls-track" data-placement="top" data-toggle="tooltip" title="Click To Edit" class="icon-edit icon-white"></i></div>'+
               '<div id="flag-status">'+
               flag+
               '</div>'+
          '</div>'
        )
          
//        $("#edit-icon-cls-track").tooltip('hide')
//        $("#default-icon-cls-track").tooltip('hide')
//        console.log(resp.data['vFile_mp3'])
//        console.log($projectExecPath+"/library/plugin/jQuery.jPlayer.2.3.0.demos/js/Jplayer.swf")
        
        if(resp.data['vFile_mp3'] && resp.data['vFile_ogg']){
            $("#jquery_jplayer_1").jPlayer({
                    ready: function () {
                            $(this).jPlayer("setMedia", {
                                mp3 : $projectExecPath+'/public/uploaded_resource/frontend/band-'+resp.data['iMember_id']+'/audio/'+resp.data['vFile_mp3'],
                                oga : $projectExecPath+'/public/uploaded_resource/frontend/band-'+resp.data['iMember_id']+'/audio/'+resp.data['vFile_ogg']
                            });
                    },
                    canplay: function() {
                    $("#jquery_jplayer_1").jPlayer("play");

                    },
    //                swfPath: "js",
                    swfPath: $projectExecPath+"/library/plugin/jQuery.jPlayer.2.3.0.demos/js",
                    supplied: "mp3, oga",
                    wmode: "window",
                    solution: 'html, flash',
                    smoothPlayBar: true,
                    keyEnabled: true,
                    errorAlerts: true,
                    warningAlerts: false
            });
        }else{
            $("#jquery_jplayer_1").jPlayer({
                    ready: function () {
                            $(this).jPlayer("setMedia", {
                                mp3 : $projectExecPath+'/public/uploaded_resource/frontend/band-'+resp.data['iMember_id']+'/audio/'+resp.data['vFile_mp3'],
//                                oga : $projectExecPath+'/public/uploaded_resource/frontend/band-'+resp.data['iMember_id']+'/audio/'+resp.data['vFile_ogg']
                            });
                    },
                    canplay: function() {
                    $("#jquery_jplayer_1").jPlayer("play");

                    },
    //                swfPath: "js",
                    swfPath: $projectExecPath+"/library/plugin/jQuery.jPlayer.2.3.0.demos/js",
                    supplied: "mp3",
                    wmode: "window",
                    solution: 'html, flash',
                    smoothPlayBar: true,
                    keyEnabled: true,
                    errorAlerts: true,
                    warningAlerts: false
            });
        }
        
    }

    //show and hide album details
    function modal_show(){
        $("#edit-album").slideDown()
        $("#show-album-details-id").slideUp()
    }

    //save image for cover from images tabale in album table
    function setCover(id){
        $("#tab-cover2").find(".list-image-cover-selected").removeClass("list-image-cover-selected")
        $("#cover_"+id).addClass("list-image-cover-selected")
        var data_change_cover = {};
        data_change_cover['id'] = id;
        data_change_cover['selected'] = 'true'
        $.post('audio/select-cover', data_change_cover, function(resp){
            $("#img-show-up-cover-id").attr("src", $projectExecImage+'/'+resp.cover)
            $("#image-album-cover-id").attr("src", $projectExecImage+'/'+resp.cover)
            $("#show-small-cover-album-id").attr("src", $projectExecImage+'/'+resp.cover)
        }, 'json')
    }

    //show track edit form
    function show_track_edit(){
        $("#show-edit-track-field").slideDown()
        $("#show-audio-details-id").slideUp()
        $("#edit-album").slideUp()
        $("#change-cover-modal").slideUp()
    }
    
    //set track defult
    function set_defult(id){
        $.post('audio/default-track', 
        {
            id : id
        },
        function(resp){
            if(resp.success == true){
                $("#botton-undefault-audio-id").remove()
                $("#unDefault-icon-cls-track").find(".icon-flag-alt").removeClass("icon-flag-alt")
                $("#flag-status").append('<div id="botton-default-audio-id" onclick="set_undefult('+resp.track_id+')" class="btn btn-small btn-success botton-default-audio text-right">Show In Profile <i id="default-icon-cls-track"  class="icon-flag icon-white"></i></div>')
//                $("#default-icon-cls-track").tooltip('hide')
            }
        }, 'json')
    }
    
    function set_undefult(id){
        $.post('audio/default-track', 
        {
            id : id,
            undefault : 'Yes'
        },
        function(resp){
            if(resp.success == true){
                $("#botton-default-audio-id").remove()
                $("#default-icon-cls-track").find(".icon-flag-alt").removeClass("icon-flag-alt")
                $("#flag-status").append('<div id="botton-undefault-audio-id" class="btn btn-small btn-primary botton-default-audio text-right" onclick="set_defult('+resp.track_id+')" style="cursor: pointer">Hide From Profile <i id="unDefault-icon-cls-track" data-placement="top" data-toggle="tooltip" data-original-title="Set Default Track" class="icon-ok icon-white" ></i></div>')
//                $("#default-icon-cls-track").tooltip('hide')
            }
        }, 'json')
    }