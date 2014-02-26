$(document).ready(function(){
    $().ready(function() {
		//alert('Jquery loaded');
		$.backstretch("img/background.jpg");
	});
    getBands();
//    $("#myModal").modal({keyboard:false,backdrop:'static'});
//    $('#myModal').modal('hide');



    $("#search-band-name").click(function(){
        
        getSearchBand();

    })


$("#reset_search_data").click(function(){
    getBands()
    $("#reset_search_data").attr("data-search","")
    $("#reset_search_data").slideUp()
    $("#search-data-btn").slideDown()
    
//    $("#reset_search_data").css("display", "none");
//    $("#search-data-btn").css("display", "block")
})
   
    
    var data_sort = {};
     $("#sort_by_name_band").click(function(){
        if($("#reset_search_data").attr("data-search")!= ''){
            data_sort['sortBySearch'] = $("#reset_search_data").attr("data-search")
        }else{
            data_sort['sortBySearch'] = '';
        }
        data_sort['sortType'] = 'sort_by_name_band';
        data_sort['data-sort'] = $("#sort_by_name_band").attr("data-sort");
        $.post('browse/sort-list', data_sort, function(resp){
            $("#sort_by_name_band").attr("data-sort",resp.sort_new);
            
            $("#sort_by_most_vote > i").removeClass()
            $("#sort_by_top_songs > i").removeClass()
            $("#sort_by_most_fans > i").removeClass()
            if(resp.sort_new == 'SORT_DESC'){
                $("#sort_by_most_vote").find(".pillLink .signin").removeClass("signup")
                $("#sort_by_name_band > i").removeClass("icon-sort-up")
                $("#sort_by_name_band > i").addClass("icon-sort-down")
            }
            if(resp.sort_new == 'SORT_ASC'){
                $("#sort_by_name_band > i").removeClass("icon-sort-down")
                $("#sort_by_name_band > i").addClass("icon-sort-up")
            }
            sortResetData(resp.data)
        }, 'json')
    })
    
    $("#sort_by_most_vote").click(function(){
        sortVoteFunc()
    })
    
    $("#sort_by_top_songs").click(function(){
       sortSongFunc()
    })
    
    $("#sort_by_most_fans").click(function(){
        sortFansFunc()
    })
})


function sortResetData(resp,type,page,func){
    $("#ul-pagination-id").html('')
    var data = resp.data;
    
    var i = 1
        while( i <= resp.count ) {
            $("#ul-pagination-id").append('<li id="id_page_li_'+i+'" onclick='+func+'('+i+')><a style="" id="id_page_'+i+'">'+i+'</a></li>')
            if(page == i){
                $("#id_page_li_"+i).addClass("active")
            }
            i++;
            
        }
    if(page > 0){
            
        }else{
            $("#id_page_li_1").addClass("active")
        }
    $("#tBody_table_td tr").remove()
           $.each(data,function(s,item){
            if(item['totalTrackPlay'] == null){
                var traks = '0 Play'
            }else{
                traks = item['totalTrackPlay']+' Plays'
            }
            if(item['totalVote'] == null){
                var count_vote = '0 Vote'
            }else{
                count_vote = item['totalVote']+' Votes'
            }
            if(item['totalFan'] == null){
                var count_fans = '0 Fan'
            }else{
                count_fans = item['totalFan']+' Fans'
            }
            
            if( item['bandImage'] == null) {
                $bandImage = 'images-defaults-profile.jpg';
            } else {
                $bandImage = item['bandImage'];
            }
            
            $("#tBody_table_td").append(
                    '<tr>'+
                        '<td><span style="float:left; margin:0 10px 0 0;"><img src="'+$projectExecImage+'/'+$bandImage+'" alt="album" height="52px" width="52px"></span>'+
                            '<h2>'+item['bandTitle']+'</h2>'+
                            '<h4 style="color:#666;">' + item['city'] + ', ' + item['stateLittle']  +'</h4>'+
                        '</td>'+
                        '<td class="maine-data">'+count_fans+'</td>'+
                        '<td class="serrator"><img src="'+$projectExecPath+'/library/home/assets/img/vbar.gif" alt="vbar" /></td>'+
                        '<td class="maine-data">'+count_vote+'</td>'+
                        '<td class="serrator"><img src="'+$projectExecPath+'/library/home/assets/img/vbar.gif" alt="vbar" /></td>'+
                        '<td class="maine-data">'+traks+'</td>'+
                        '<td class="maine-data1"><a href="'+$projectExecPath+'/profile/band/id/'+item['memberId']+'" class="viewprofilebtn">VIEW PROFILE</a></td>'+
                    '</tr>'+
                    '<tr style="height:2px;"></tr>'
            )
                
                
                if(type == 'search'){
                    $("#sort_by_top_songs > i").removeClass()
                    $("#sort_by_top_songs.pillLink.signup").removeClass("signup").addClass("signin")
                    $("#sort_by_name_band > i").removeClass()
                    $("#sort_by_name_band.pillLink.signup").removeClass("signup").addClass("signin")
                    $("#sort_by_most_fans > i").removeClass()
                    $("#sort_by_most_fans.pillLink.signup").removeClass("signup").addClass("signin")

                    $("#sort_by_most_vote").attr("data-sort",'SORT_ASC');
                    $("#sort_by_most_vote.pillLink.signin").removeClass("signin").addClass("signup")
                    $("#sort_by_most_vote > i").removeClass("icon-sort-down")
                    $("#sort_by_most_vote > i").addClass("icon-sort-up")
                }
       
//            $("#tBody_table_td").append('<tr>'+
//                                            '<td>'+
//                                                '<div  class="pull-left">'+
//                                                    '<img width = "50px" src="'+$projectExecImage+'/'+item['vImg']+'"/>'+
//                                                '</div>'+
//                                                '<div class="pull-left">'+
//                                                    '<div>'+item['vTitle']+'</div>'+
//                                                    '<div style="font-size:10px;color:#999">Los Angles</div>'+
//                                                '</div>'+
//                                            '</td>'+
//                                            '<td data-position = "center">'+
//                                                count_vote+
//                                            '</td>'+
//                                            '<td data-position = "center">'+
//                                                count_fans+
//                                            '</td>'+
//                                            '<td data-position = "center">'+
//                                                traks+
//                                            '</td>'+
//                                            '<td data-position = "center">'+
////                                                '<button class="btn btn-small btn-inverse">VIEW PROFILE</button>'+
//                                                   '<a href="'+$projectExecPath+'/profile/band/id/'+item['iBand_id']+'" class="btn btn-small btn-inverse">VIEW PROFILE</a>'+
//                                            '</td>'+
//                                        '</tr>')
        })
}



function getBands(page){
    
    $("#tBody_table_td tr").remove()
    $("#ul-pagination-id").html('');
   
    
     $.post('browse/get-bands',{
         page : page
     }, function(resp){
         var i = 1
        while( i <= resp.count ) {
            $("#ul-pagination-id").append('<li id="id_page_li_'+i+'" class="" onclick=getBands('+i+')><a style="" id="id_page_'+i+'">'+i+'</a></li>')
            if(page == i){
                $("#id_page_li_"+i).addClass("active")
            }
            i++;
            
        }
        if(page > 0){
            
        }else{
            $("#id_page_li_1").addClass("active")
        }
        $.each(resp.data,function(s,item){
            
            if(item['totalTrackPlay'] == null){
                var traks = '0 Play'
            }else{
                traks = item['totalTrackPlay']+' Plays'
            }
            if(item['totalVote'] == null){
                var count_vote = '0 Vote'
            }else{
                count_vote = item['totalVote']+' Votes'
            }
            if(item['totalFan'] == null){
                var count_fans = '0 Fan'
            }else{
                count_fans = item['totalFan']+' Fans'
            }
            if( item['bandImage'] == null) {
                $bandImage = 'images-defaults-profile.jpg';
            } else {
                $bandImage = item['bandImage'];
            }
            $("#tBody_table_td").append(
                    '<tr>'+
                        '<td><span style="float:left; margin:0 10px 0 0;"><img src="'+$projectExecImage+'/'+$bandImage+'" alt="album" height="52px" width="52px"></span>'+
                            '<h2>'+item['bandTitle']+'</h2>'+
                            '<h4 style="color:#666;">' + item['city'] + ', ' + item['stateLittle']  +'</h4>'+
                        '</td>'+
                        '<td class="maine-data">'+count_fans+'</td>'+
                        '<td class="serrator"><img src="'+$projectExecPath+'/library/home/assets/img/vbar.gif" alt="vbar" /></td>'+
                        '<td class="maine-data">'+count_vote+'</td>'+
                        '<td class="serrator"><img src="'+$projectExecPath+'/library/home/assets/img/vbar.gif" alt="vbar" /></td>'+
                        '<td class="maine-data">'+traks+'</td>'+
                        '<td class="maine-data1"><a href="'+$projectExecPath+'/profile/band/id/'+item['memberId']+'" class="viewprofilebtn">VIEW PROFILE</a></td>'+
                    '</tr>'+
                    '<tr style="height:2px;"></tr>'
            )
                
                $("#sort_by_most_vote").attr("data-sort",'SORT_ASC');
                $("#sort_by_most_vote.pillLink.signin").removeClass("signin").addClass("signup")
                $("#sort_by_most_vote > i").removeClass("icon-sort-down")
                $("#sort_by_most_vote > i").addClass("icon-sort-up")
//            $("#tBody_table_td").append('<tr>'+
//                                            '<td>'+
//                                                '<div  class="pull-left">'+
//                                                    '<img width = "50px" src="'+$projectExecImage+'/'+item['vImg']+'"/>'+
//                                                '</div>'+
//                                                '<div class="pull-left">'+
//                                                    '<div>'+item['vTitle']+'</div>'+
//                                                    '<div style="font-size:10px;color:#999">Los Angles</div>'+
//                                                '</div>'+
//                                            '</td>'+
//                                            '<td data-position = "center">'+
//                                                count_vote+
//                                            '</td>'+
//                                            '<td data-position = "center">'+
//                                                count_fans+
//                                            '</td>'+
//                                            '<td data-position = "center">'+
//                                                traks+
//                                            '</td>'+
//                                            '<td data-position = "center">'+
//                                                '<a href="'+$projectExecPath+'/profile/band/id/'+item['iBand_id']+'" class="btn btn-small btn-inverse">VIEW PROFILE</a>'+
//                                            '</td>'+
//                                        '</tr>')
        })
        
        
    },'json')
    
    
}


function getSearchBand(page){
    var urlSearch= $("form#form-send-data").attr("action")+'/search'
    var dataSearch = {}
        search_text = $("#search-data").val();
        dataSearch['search'] = $("#search-data").val();

        
         
         dataSearch['page'] = page;
            $.post(urlSearch, dataSearch, function(resp){
                    sortResetData(resp,'search',page,'getSearchBand')
                    $('#myModal').modal('hide');
                    $("#search-data").val("");
                    $("#search-data-btn").slideUp()
                    $("#reset_search_data").slideDown()
                    $("#reset_search_data").attr("data-search",search_text);
            }, 'json')
}


function sortVoteFunc(page){
    var data_sort = {}
    if($("#reset_search_data").attr("data-search")!= ''){
            data_sort['sortBySearch'] = $("#reset_search_data").attr("data-search")
        }else{
            data_sort['sortBySearch'] = '';
        }
        data_sort['page'] = page;
        if(page>0){
            data_sort['data-sort'] = '';
        }else{
            data_sort['data-sort'] = $("#sort_by_most_vote").attr("data-sort");
        }
        data_sort['page'] = page;
        data_sort['sortType'] = 'sort_by_most_vote';
        
        $.post('browse/sort-list', data_sort, function(resp){
            if(page>0){
                
            }else{
                $("#sort_by_most_vote").attr("data-sort",resp.sort_new);
                $("#sort_by_top_songs > i").removeClass()
                $("#sort_by_top_songs.pillLink.signup").removeClass("signup").addClass("signin")
                $("#sort_by_name_band > i").removeClass()
                $("#sort_by_name_band.pillLink.signup").removeClass("signup").addClass("signin")
                $("#sort_by_most_fans > i").removeClass()
                $("#sort_by_most_fans.pillLink.signup").removeClass("signup").addClass("signin")
            
                if(resp.sort_new == 'SORT_DESC'){
                    $("#sort_by_most_vote.pillLink.signin").removeClass("signin").addClass("signup")
                    $("#sort_by_most_vote > i").removeClass("icon-sort-up")
                    $("#sort_by_most_vote > i").addClass("icon-sort-down")
                }
                if(resp.sort_new == 'SORT_ASC'){
                    $("#sort_by_most_vote.pillLink.signin").removeClass("signin").addClass("signup")
                    $("#sort_by_most_vote > i").removeClass("icon-sort-down")
                    $("#sort_by_most_vote > i").addClass("icon-sort-up")
                }
            }
            
            sortResetData(resp,'',page,'sortVoteFunc')
        }, 'json')
}



function sortSongFunc(page){
    var data_sort = {}
     if($("#reset_search_data").attr("data-search")!= ''){
            data_sort['sortBySearch'] = $("#reset_search_data").attr("data-search")
        }else{
            data_sort['sortBySearch'] = '';
        }
        data_sort['page'] = page;
        if(page>0){
            data_sort['data-sort'] = '';
        }else{
            data_sort['data-sort'] = $("#sort_by_top_songs").attr("data-sort");
        }
        data_sort['sortType'] = 'sort_by_top_songs';
        
        console.log(data_sort)
        $.post('browse/sort-list', data_sort, function(resp){
            if(page>0){
                
            }else{
                $("#sort_by_top_songs").attr("data-sort",resp.sort_new);
                $("#sort_by_name_band > i").removeClass()
                $("#sort_by_name_band.pillLink.signup").removeClass("signup").addClass("signin")
                $("#sort_by_most_vote > i").removeClass()
                $("#sort_by_most_vote.pillLink.signup").removeClass("signup").addClass("signin")
                $("#sort_by_most_fans > i").removeClass()
                $("#sort_by_most_fans.pillLink.signup").removeClass("signup").addClass("signin")
            
                if(resp.sort_new == 'SORT_DESC'){
                    $("#sort_by_top_songs.pillLink.signin").removeClass("signin").addClass("signup")
                    $("#sort_by_top_songs > i").removeClass("icon-sort-up")
                    $("#sort_by_top_songs > i").addClass("icon-sort-down")
                }

                if(resp.sort_new == 'SORT_ASC'){
                    $("#sort_by_top_songs.pillLink.signin").removeClass("signin").addClass("signup")
                    $("#sort_by_top_songs > i").removeClass("icon-sort-down")
                    $("#sort_by_top_songs > i").addClass("icon-sort-up")
                }
            }
            
            sortResetData(resp,'',page,'sortSongFunc')
        }, 'json')
}


function sortFansFunc(page){
    var data_sort = {}
        if($("#reset_search_data").attr("data-search")!= ''){
            data_sort['sortBySearch'] = $("#reset_search_data").attr("data-search")
        }else{
            data_sort['sortBySearch'] = '';
        }
        if(page>0){
            data_sort['data-sort'] = '';
        }else{
            data_sort['data-sort'] = $("#sort_by_most_fans").attr("data-sort");
        }
        data_sort['page'] = page;
        data_sort['sortType'] = 'sort_by_most_fans';
        
        $.post('browse/sort-list', data_sort, function(resp){
            if(page>0){
                
            }else{
                $("#sort_by_most_fans").attr("data-sort",resp.sort_new);
                $("#sort_by_name_band > i").removeClass()
                $("#sort_by_name_band.pillLink.signup").removeClass("signup").addClass("signin")
                $("#sort_by_most_vote > i").removeClass()
                $("#sort_by_most_vote.pillLink.signup").removeClass("signup").addClass("signin")
                $("#sort_by_top_songs > i").removeClass()
                $("#sort_by_top_songs.pillLink.signup").removeClass("signup").addClass("signin")
            
               if(resp.sort_new == 'SORT_DESC'){
                    $("#sort_by_most_fans.pillLink.signin").removeClass("signin").addClass("signup")
                    $("#sort_by_most_fans > i").removeClass("icon-sort-up")
                    $("#sort_by_most_fans > i").addClass("icon-sort-down")
                }
                if(resp.sort_new == 'SORT_ASC'){
                    $("#sort_by_most_fans.pillLink.signin").removeClass("signin")
                    $("#sort_by_most_fans").addClass("signup")
                    $("#sort_by_most_fans > i").removeClass("icon-sort-down")
                    $("#sort_by_most_fans > i").addClass("icon-sort-up")
                } 
            }
            
            sortResetData(resp,'',page,'sortFansFunc')
        }, 'json')
}