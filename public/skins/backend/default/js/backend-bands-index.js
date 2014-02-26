$(document).ready(function(){
    $('.select2').select2({placeholder : ''});
    $('#editBandTab a').click(function (e) {
        e.preventDefault();
        $(this).tab('show');
    })
    
    $("input[data-close='close']").click(function(){
        $("#editBand").modal('hide');
    })
    
    var urlBandMember = $("form#band-members-form-id").attr("action")+'/save-band-member';
    $("#save-band-member-id").click(function(){
        if($("#band-member-instrument-id").val()!= '' && $("#band-member-name-id").val()!= ''){
            var data_band_member = {}
            data_band_member['vName_lastname'] = $("#band-member-instrument-id").val();
            data_band_member['vInstrument'] = $("#band-member-name-id").val();
            data_band_member['iBand_id'] = $("#band-member-hidden-id").val();
            $.post(urlBandMember, data_band_member, function(resp){
                if(resp.success == true){
                    $("#band-member-instrument-id").val('');
                    $("#band-member-name-id").val('');
                    $("#band-members-table-id").prepend(
                    '<tr data-id="'+resp.data['iId']+'">'+
                            '<td style="width: 250px" id="name_lastname_'+resp.data['iId']+'">'+resp.data['vName_lastname']+'</td>'+
                            '<td style="width: 150px" id="vInstrument_'+resp.data['iId']+'">'+resp.data['vInstrument']+'</td>'+
                            '<td><button class= "btn btn-small btn-danger" onclick=deleteBandMember('+resp.data['iId']+')>DELETE <i class="icon-trash"></i></button></td>'+
                        '</tr>'
                )
                    $('#name_lastname_'+resp.data['iId']).editable({
                        type: 'text',
                        pk: resp.data['iId'],
                        url: $projectExecPath+"/backend/bands/edit-line",
                        title: 'Enter username',
                        name: 'vName_lastname',
                        validate: function(value) {
                            if($.trim(value) == '') {
                            return 'This field is required';
                            }
                        }
                    });
                    $('#vInstrument_'+resp.data['iId']).editable({
                        type: 'text',
                        pk: resp.data['iId'],
                        url: $projectExecPath+"/backend/bands/edit-line",
                        title: 'Enter username',
                        name: 'vInstrument',
                        validate: function(value) {
                            if($.trim(value) == '') {
                            return 'This field is required';
                            }
                        }
                    });
                }

            }, 'json')
        }
        
        
    })
    
    
    $("#sort_by_top_featured").click(function(){
        featuredFunc()
    })

    getBands();
    $("#search-band-name").click(function(){
        
        getSearchBand();

    })

$("#reset_search_data").click(function(){
    getBands()
    $("#reset_search_data").attr("data-search","")
    $("#reset_search_data").slideUp()
    $("#search-data-btn").slideDown()
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
   data_band[$("pre_iId").attr("name")] =$("#pre_iId").val()
   data_band[$("#pre_iMember_id").attr("name")] =$("#pre_iMember_id").val()
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
   data_band_tab2[$("#pre_iId").attr("name")] =$("#pre_iId").val()
   data_band_tab2[$("#pre_iMember_id").attr("name")] =$("#pre_iMember_id").val()
   $.post(urlBandOtherInfo, data_band_tab2, 
   function(resp){
       if(resp['success'] == true){
                
                $("#alert-login-faild").html("<div id='error' class='alert alert-success'>change info successfuly</div>")
            }else{
                $("#alert-login-faild").html("<div id='error' class='alert alert-error'>change info faild</div>")
            }
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
        $("#tHead_table_td").html('')
        $("#tBody_table_td").html('')
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
    if(data!=null){
        $("#tHead_table_td").append('<th>Image &  Band Title</th><th>Count Votes</th><th>Count Fans</th><th>Count Song Plays</th>')
          $.each(data,function(s,item){
            if(item['totalTrackPlay'] == null){
                var traks = '0 Plays'
            }else{
                traks = item['totalTrackPlay']+' Plays'
            }
            if(item['totalVote'] == null){
                var count_vote = '0 Votes'
            }else{
                count_vote = item['totalVote']+' Votes'
            }
            if(item['totalFan'] == null){
                var count_fans = '0 Fans'
            }else{
                count_fans = item['totalFan']+' Fans'
            }
            
            if(item['featured'] == 'Yes'){
                var featured = '<div id="fuchered_star_'+item['bandId']+'" onclick=unsetFuchered('+item['bandId']+')><i id="featured-id_'+item['bandId']+'" data-placement="right" data-toggle="tooltip" title="Featured"  class = "icon-star"></i></div>'
            }else{
                featured = '<div id="fuchered_star_'+item['bandId']+'" onclick=setFuchered('+item['bandId']+')><i id="un-featured-id_'+item['bandId']+'" data-placement="right" data-toggle="tooltip" title="un Featured" class = "icon-star-empty"></i></div>'
            }
            if(item['bandImage']!=null){
                var image = '<img width = "50px" src="'+$projectExecPath+'/public/uploaded_resource/frontend/band/image/'+item['bandImage']+'"/>'
            }else{
                image = '<img width = "50px" src="'+$projectExecPath+'/public/uploaded_resource/frontend/band/image/images-defaults-profile.jpg"/>'
            }
            
            
            $("#tBody_table_td").append('<tr>'+
                                            '<td>'+
                                                '<div  class="pull-left" style="margin-right:5px">'+
                                                    image+
//                                                    '<img width = "50px" src="'+$projectExecPath+'/public/uploaded_resource/frontend/member/profile-image/'+item['bandImage']+'"/>'+
                                                '</div>'+
                                                '<div class="pull-left">'+
                                                    '<div style="cursor:pointer" onclick = "getbandInfo('+item['bandId']+')">'+item['bandTitle']+'</div>'+
                                                    '<div style="font-size:10px;color:#999">'+item['city']+' '+item['stateLittle']+'</div>'+
                                                '</div>'+
                                            '</td>'+
                                            '<td data-position = "center">'+
                                                count_vote+
                                            '</td>'+
                                            '<td data-position = "center">'+
                                                count_fans+
                                            '</td>'+
                                            '<td data-position = "center">'+
                                                traks+
                                            '</td>'+
                                            '<td data-position = "center">'+
                                                 featured+
                                            '</td>'+
                                            '<td><i style="cursor:pointer" onclick="memberBand('+item['bandId']+')" class="icon-user"></i></td>'+
                                            '<td style="width:150px">'+
                                                '<button class= "btn btn-small btn-warning" onclick=editBand('+item['bandId']+')>EDIT <i class="icon-edit"></i></button>&nbsp'+
                                                '<button class= "btn btn-small btn-danger" onclick=deleteBand('+item['bandId']+')>DELETE <i class="icon-trash"></i></button>'+
                                            '</td>'+
                     
                                        '</tr>')
            $("#featured-id_"+item['bandId']).tooltip('hide')
            $("#un-featured-id_"+item['bandId']).tooltip('hide')
        })
    }else{
        $("#tBody_table_td").append('<h5>No Result</h5>')
    }
         
        
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
}



function getBands(page){
    $("#tBody_table_td tr").remove()
    $("#tHead_table_td").html('')
    $("#ul-pagination-id").html('');
    $("#tBody_table_td").html('')
    $("#sort_by_top_featured.btn.btn-info").removeClass("btn-info").addClass("btn-inverse")
    $("#sort_by_top_songs > i").removeClass()
    $("#sort_by_top_songs.btn.btn-primary").removeClass("btn-primary").addClass("btn-inverse")
    $("#sort_by_name_band > i").removeClass()
    $("#sort_by_name_band.btn.btn-primary").removeClass("btn-primary").addClass("btn-inverse")
    $("#sort_by_most_fans > i").removeClass()
    $("#sort_by_most_fans.btn.btn-primary").removeClass("btn-primary").addClass("btn-inverse")
    
     $.post('bands/get-bands',{
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
        $("#tHead_table_td").append('<th>Image &  Band Title</th><th>Count Votes</th><th>Count Fans</th><th>Count Song Plays</th>')
        $.each(resp.data,function(s,item){
            if(item['totalTrackPlay'] == null){
                var traks = '0 Plays'
            }else{
                traks = item['totalTrackPlay']+' Plays'
            }
            if(item['totalVote'] == null){
                var count_vote = '0 Votes'
            }else{
                count_vote = item['totalVote']+' Votes'
            }
            if(item['totalFan'] == null){
                var count_fans = '0 Fans'
            }else{
                count_fans = item['totalFan']+' Fans'
            }
            
            if(item['featured'] == 'Yes'){
                var featured = '<div id="fuchered_star_'+item['bandId']+'" onclick=unsetFuchered('+item['bandId']+')><i id="featured-id_'+item['bandId']+'" data-placement="right" data-toggle="tooltip" title="Featured"  class = "icon-star"></i></div>'
            }else{
                featured = '<div id="fuchered_star_'+item['bandId']+'" onclick=setFuchered('+item['bandId']+')><i id="un-featured-id_'+item['bandId']+'" data-placement="right" data-toggle="tooltip" title="un Featured" class = "icon-star-empty"></i></div>'
            }
            if(item['bandImage']!=null){
                var image = '<img width = "50px" src="'+$projectExecPath+'/public/uploaded_resource/frontend/band/image/'+item['bandImage']+'"/>'
            }else{
                image = '<img width = "50px" src="'+$projectExecPath+'/public/uploaded_resource/frontend/band/image/images-defaults-profile.jpg"/>'
            }
            
            $("#tBody_table_td").append('<tr>'+
                                            '<td>'+
                                                '<div  class="pull-left" style="margin-right:5px">'+
                                                    image+
//                                                    '<img width = "50px" src="'+$projectExecPath+'/public/uploaded_resource/frontend/member/profile-image/'+item['bandImage']+'"/>'+
                                                '</div>'+
                                                '<div class="pull-left">'+
                                                    '<div style="cursor:pointer" onclick = "getbandInfo('+item['bandId']+')">'+item['bandTitle']+'</div>'+
                                                    '<div style="font-size:10px;color:#999">'+item['city']+' '+item['stateLittle']+'</div>'+
                                                '</div>'+
                                            '</td>'+
                                            '<td data-position = "center">'+
                                                count_vote+
                                            '</td>'+
                                            '<td data-position = "center">'+
                                                count_fans+
                                            '</td>'+
                                            '<td data-position = "center">'+
                                                traks+
                                            '</td>'+
                                            '<td data-position = "center">'+
                                                featured+
                                            '</td>'+
                                            '<td><i style="cursor:pointer" onclick="memberBand('+item['bandId']+')" class="icon-user"></i></td>'+
                                            '<td style="width:150px">'+
                                                '<button class= "btn btn-small btn-warning" onclick=editBand('+item['bandId']+')>EDIT <i class="icon-edit"></i></button>&nbsp'+
                                                '<button class= "btn btn-small btn-danger" onclick=deleteBand('+item['bandId']+')>DELETE <i class="icon-trash"></i></button>'+
                                            '</td>'+
                                      
                                        '</tr>')
                                    
                                    
                $("#featured-id_"+item['bandId']).tooltip('hide')
                $("#un-featured-id_"+item['bandId']).tooltip('hide')
                $("#sort_by_most_vote").attr("data-sort",'SORT_ASC');
                $("#sort_by_most_vote.btn.btn-inverse").removeClass("btn-inverse").addClass("btn-primary")
                $("#sort_by_most_vote > i").removeClass("icon-sort-down")
                $("#sort_by_most_vote > i").addClass("icon-sort-up")
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
        if(page>0){
            data_sort['data-sort'] = '';
        }else{
            data_sort['data-sort'] = $("#sort_by_most_vote").attr("data-sort");
        }
        data_sort['page'] = page;
        data_sort['sortType'] = 'sort_by_most_vote';
        
        $.post('bands/sort-list', data_sort, function(resp){
            if(page>0){
                
            }else{
                $("#sort_by_most_vote").attr("data-sort",resp.sort_new);
                $("#sort_by_top_songs > i").removeClass()
                $("#sort_by_top_songs.btn.btn-primary").removeClass("btn-primary").addClass("btn-inverse")
                $("#sort_by_name_band > i").removeClass()
                $("#sort_by_name_band.btn.btn-primary").removeClass("btn-primary").addClass("btn-inverse")
                $("#sort_by_most_fans > i").removeClass()
                $("#sort_by_most_fans.btn.btn-primary").removeClass("btn-primary").addClass("btn-inverse")
            
                if(resp.sort_new == 'SORT_DESC'){
                    $("#sort_by_most_vote.btn.btn-inverse").removeClass("btn-inverse").addClass("btn-primary")
                    $("#sort_by_most_vote > i").removeClass("icon-sort-up")
                    $("#sort_by_most_vote > i").addClass("icon-sort-down")
                }
                if(resp.sort_new == 'SORT_ASC'){
                    $("#sort_by_most_vote.btn.btn-inverse").removeClass("btn-inverse").addClass("btn-primary")
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
        if(page>0){
            data_sort['data-sort'] = '';
        }else{
            data_sort['data-sort'] = $("#sort_by_top_songs").attr("data-sort");
        }
        data_sort['page'] = page;
        data_sort['sortType'] = 'sort_by_top_songs';
        
        $.post('bands/sort-list', data_sort, function(resp){
            if(page>0){
                
            }else{
                $("#sort_by_top_songs").attr("data-sort",resp.sort_new);
                $("#sort_by_name_band > i").removeClass()
                $("#sort_by_name_band.btn.btn-primary").removeClass("btn-primary").addClass("btn-inverse")
                $("#sort_by_most_vote > i").removeClass()
                $("#sort_by_most_vote.btn.btn-primary").removeClass("btn-primary").addClass("btn-inverse")
                $("#sort_by_most_fans > i").removeClass()
                $("#sort_by_most_fans.btn.btn-primary").removeClass("btn-primary").addClass("btn-inverse")
            
                if(resp.sort_new == 'SORT_DESC'){
                    $("#sort_by_top_songs.btn.btn-inverse").removeClass("btn-inverse").addClass("btn-primary")
                    $("#sort_by_top_songs > i").removeClass("icon-sort-up")
                    $("#sort_by_top_songs > i").addClass("icon-sort-down")
                }

                if(resp.sort_new == 'SORT_ASC'){
                    $("#sort_by_top_songs.btn.btn-inverse").removeClass("btn-inverse").addClass("btn-primary")
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
        
        $.post('bands/sort-list', data_sort, function(resp){
            if(page>0){
                
            }else{
                $("#sort_by_most_fans").attr("data-sort",resp.sort_new);
                $("#sort_by_name_band > i").removeClass()
                $("#sort_by_name_band.btn.btn-primary").removeClass("btn-primary").addClass("btn-inverse")
                $("#sort_by_most_vote > i").removeClass()
                $("#sort_by_most_vote.btn.btn-primary").removeClass("btn-primary").addClass("btn-inverse")
                $("#sort_by_top_songs > i").removeClass()
                $("#sort_by_top_songs.btn.btn-primary").removeClass("btn-primary").addClass("btn-inverse")
            
               if(resp.sort_new == 'SORT_DESC'){
                    $("#sort_by_most_fans.btn.btn-inverse").removeClass("btn-inverse").addClass("btn-primary")
                    $("#sort_by_most_fans > i").removeClass("icon-sort-up")
                    $("#sort_by_most_fans > i").addClass("icon-sort-down")
                }
                if(resp.sort_new == 'SORT_ASC'){
                    $("#sort_by_most_fans.btn.btn-inverse").removeClass("btn-inverse").addClass("btn-primary")
                    $("#sort_by_most_fans").addClass("signup")
                    $("#sort_by_most_fans > i").removeClass("icon-sort-down")
                    $("#sort_by_most_fans > i").addClass("icon-sort-up")
                } 
            }
            
            sortResetData(resp,'',page,'sortFansFunc')
        }, 'json')
}



function featuredFunc(page){
    var data_faetured = {}
        data_faetured['page'] = page;
        if($("#sort_by_top_featured_yes").attr("data-sort")== 'Yes'){
            data_faetured['featureband'] = 'Yes'
        }else{
            data_faetured['featureband'] = 'No'
        }
        $.post('bands/sort-list', data_faetured, function(resp){
                sortResetData(resp,'',page,'featuredFunc')
        }, 'json')
}


function featuredFuncNo(page){
    var data_faetured = {}
        data_faetured['page'] = page;
        if($("#sort_by_top_featured_no").attr("data-sort")== 'No'){
            data_faetured['featureband'] = 'No'
        }else{
            data_faetured['featureband'] = 'No'
        }
        $.post('bands/sort-list', data_faetured, function(resp){
                sortResetData(resp,'',page,'featuredFuncNo')
        }, 'json')
}



function setFuchered(id){
    $.post('bands/featured',{
        id : id,
        type : 'set'
    },function(resp){
        if(resp.success){
            $("#fuchered_star_"+id).attr("onclick","unsetFuchered("+id+")")
            $("#fuchered_star_"+id+" > i.icon-star-empty").removeClass("icon-star-empty").addClass("icon-star")
        }
        
    },'json')
}


function unsetFuchered(id){
    $.post('bands/featured',{
        id : id
    },function(resp){
        if(resp.success){
            $("#fuchered_star_"+id).attr("onclick","setFuchered("+id+")")
            $("#fuchered_star_"+id+" > i.icon-star").removeClass("icon-star").addClass("icon-star-empty")
        }
        
    },'json')
}

function getbandInfo(id){
    $.post('bands/band-info',
    {
        id:id
    },
    function(resp)
    {
        
        if(resp.success == true){
            if(resp.data['bandImage']!=null){
                var src=$projectExecPath+'/public/uploaded_resource/frontend/band/image/'+resp.data['bandImage']
            }else{
                src=$projectExecPath+'/public/uploaded_resource/frontend/band/image/images-defaults-profile.jpg'
            }
            
            $("#infoBand").modal()
            $("#image-info-bands-id").attr("src",src)
            $("#title-info-bands-id").html(resp.data['bandTitle'])
            $("#email-info-bands-id").html(resp.data['memberEmail'])
            $("#city-info-bands-id").html(resp.data['city']+' '+resp.data['stateLittle'])
        }
        
    }, 'json')

}


function editBand(id){
    $.post($projectExecPath+'/backend/bands/get-user-info',{
        id : id
    } ,
    function(resp){
            if(resp.success == true){
                $("#band-title-header-modal-id").html('EDIT <i>'+resp.data['vTitle']+'</i>')
                $.each(resp.data, function(s, item){
                    if(s == 'vGenre_ids'){
                        $("#pre_vGenre_ids").val(item).select2();

                    }else{
                    $("#pre_"+s).val(item) 
                    }
                })
                $("#alert-login-faild").html('');
                $("#editBand").modal()
                
            }
            
        }, 'json')
}



function deleteBand(id){
    $("#deleteBand").modal();
    $("#delete-band-yes").click(function(){
        $.post('bands/delete-band', {
            id:id
        }, function(resp){
            if(resp.success == true){
                getBands();
                $("#deleteBand").modal('hide');
                $("#delete-band-yes").unbind('click');
            }
        }, 'json');
    });
    
    
}



function memberBand(id){
$("#band-member-hidden-id").val(id)
$("#band-members-table-id").html('')
$("#memberBand").modal()
    $.post('bands/member-band', 
    {
        id : id
    },
    function(resp)
    {
        $.each(resp.data,function(s,item){
            $("#band-members-table-id").append(
                '<tr data-id="'+item['iId']+'">'+
                        '<td style="width: 250px" id="name_lastname_'+item['iId']+'">'+item['vName_lastname']+'</td>'+
                        '<td style="width: 150px" id="vInstrument_'+item['iId']+'">'+item['vInstrument']+'</td>'+
                        '<td><button class= "btn btn-small btn-danger" onclick=deleteBandMember('+item['iId']+')>DELETE <i class="icon-trash"></i></button></td>'+
                    '</tr>'
            )
                 $('#name_lastname_'+item['iId']).editable({
                    type: 'text',
                    pk: item['iId'],
                    url: $projectExecPath+"/backend/bands/edit-line",
                    title: 'Enter username',
                    name: 'vName_lastname',
                    validate: function(value) {
                        if($.trim(value) == '') {
                           return 'This field is required';
                        }
                    }
                });
                $('#vInstrument_'+item['iId']).editable({
                    type: 'text',
                    pk: item['iId'],
                    url: $projectExecPath+"/backend/bands/edit-line",
                    title: 'Enter username',
                    name: 'vInstrument',
                    validate: function(value) {
                        if($.trim(value) == '') {
                           return 'This field is required';
                        }
                    }
                });
        })
    }, 'json')
}


function deleteBandMember(id){
    $("#deleteBandMember").modal();
    $("#delete-band-member-yes").click(function(){
        $.post('bands/delete-band-member', 
        {
            id : id
        }, 
        function(resp){
            if(resp.success == true){
                $("#deleteBandMember").modal('hide');
                $("[data-id='"+id+"']").remove()
                $("#delete-band-member-yes").unbind('click');
            }
        }, 'json')
    })
    
}