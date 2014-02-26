$(document).ready(function(){
   $('a#not_login_vote_id').popover({content: 'please Login'});
});


function play_audio(id){
    var n = $("[data-play='play']").attr("id")
    var n_up = $("[data-play-fu='play']").attr("id")
     if(n_up!=null){
        var x = $("[data-play-fu='play']").attr("id").replace("playeBtnFu_","")
        $("[data-play-fu='play']")
                        .attr("data-play-fu","")
                        .attr("onclick","play_audioFu("+x+")")
                        .attr("src",$projectExecPath+"/library/home/assets/img/fu-play.png");
                        document.getElementById("playerFu_"+x).pause();
                        document.getElementById("playerFu_"+x).currentTime = 0;
        
    }
    if(n!=null){
        var x = $("[data-play='play']").attr("id").replace("playeBtn_","")
        $("[data-play='play']")
                        .attr("data-play","")
                        .attr("onclick","play_audio("+x+")")
                        .attr("src",$projectExecPath+"/public/skins/frontend/default/images/img/white_arrow.png");
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
                            $.post($projectExecPath+'/index/click-update', {
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
                             $.post($projectExecPath+'/index/click-update', {
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
//                        console.log('ttttttttttttttttttttttttttttttt');
//                        console.log(document.getElementById('player_'+id).pause())
    document.getElementById('player_'+id).pause();
    document.getElementById("player_"+id).currentTime = 0;
}



function play_audioFu(id){
    var n_up = $("[data-play='play']").attr("id")
      if(n_up!=null){
        var x = $("[data-play='play']").attr("id").replace("playeBtn_","")
        $("[data-play='play']")
                        .attr("data-play","")
                        .attr("onclick","play_audio("+x+")")
                        .attr("src",$projectExecPath+"/public/skins/frontend/default/images/img/white_arrow.png");
                        document.getElementById("player_"+x).pause();
                        document.getElementById("player_"+x).currentTime = 0;
        
    }
    var n = $("[data-play-fu='play']").attr("id")
    if(n!=null){
        var x = $("[data-play-fu='play']").attr("id").replace("playeBtnFu_","")
        $("[data-play-fu='play']")
                        .attr("data-play-fu","")
                        .attr("onclick","play_audioFu("+x+")")
                        .attr("src",$projectExecPath+"/library/home/assets/img/fu-play.png");
                        document.getElementById("playerFu_"+x).pause();
                        document.getElementById("playerFu_"+x).currentTime = 0;
        
    }
    document.getElementById('playerFu_'+id).play()
    $("#playeBtnFu_"+id)
                    .attr("data-play-fu","play")
                    .attr("onclick","pause_audioFu("+id+")")
                    .attr("src",$projectExecPath+"/library/home/assets/img/fu-pause.png");
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
                            $.post($projectExecPath+'/index/click-update', {
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
                             $.post($projectExecPath+'/index/click-update', {
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

function pause_audioFu(id){
    
    $("[data-play-fu='play']")
                        .attr("data-play-fu","")
                        .attr("onclick","play_audioFu("+id+")")
                        .attr("src",$projectExecPath+"/library/home/assets/img/fu-play.png");
//                        console.log(document.getElementById('playerFu_'+id))
    document.getElementById('playerFu_'+id).pause();
    document.getElementById("playerFu_"+id).currentTime = 0;
}



