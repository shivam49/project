$(document).ready(function(){
    marketLit();
    
    var urlMarket = $("form#form-market-id").attr("action")+'/save-market';
    $("#submit-title-market-id").click(function(){
        $("#alert-error-success-id").html('')
        if($("#market-title-id").val()!=''){
            $.post(urlMarket, 
            {
                vTitle : $("#market-title-id").val()
            }, 
            function(resp){
                if(resp.success==true){
                    $("#market-title-id").val('')
                    $("#alert-error-success-id").append('<div class="alert alert-success" >save amount successfully</div>')
                    $("#table-market-list-id").prepend('<tr data-id='+resp.data['iId']+' ><td  id="vTitle_'+resp.data['iId']+'"><i style="text-decoration: none" id="vTitle_name_'+resp.data['iId']+'"  data-toggle="tooltip" title="CLICK TO EDIT THIS MARKET">'+resp.data['vTitle']+'</i></td>'+
                    '<td><button class= "btn btn-small btn-danger" onclick=deleteMarket('+resp.data['iId']+')>DELETE <i class="icon-trash  icon-white"></i></button></td></tr>')
                    $("#vTitle_name_"+resp.data['iId']).tooltip('hide')
                    $('#vTitle_'+resp.data['iId']).editable({
                            type: 'text',
                            pk: resp.data['iId'],
                            url: $projectExecPath+"/backend/markets/edit-market",
                            title: 'Enter username',
                            name: 'vTitle',
                            validate: function(value) {
                                if($.trim(value) == '') {
                                return 'This field is required';
                                }
                            }
                        });
                        setTimeout(function(){
                            $("#alert-error-success-id").html('');
                        },4000)
                }else{
                        $("#alert-error-success-id").append('<div class="alert alert-error" >There is a similar amount to the amount entered</div>')
                        setTimeout(function(){
                            $("#alert-error-success-id").html('');
                        },4000)
                    }

            }, 'json')
        }else{
            $("#market-title-id").attr("required","required")
        }
    })
    
})


function marketLit(){
    $.post('markets/market-list', 
    function(resp){
        if(resp.success == true){
            $.each(resp.data,function(s,item){
                $("#table-market-list-id").append('<tr data-id='+item['iId']+' ><td  id="vTitle_'+item['iId']+'"><i style="text-decoration: none" id="vTitle_name_'+item['iId']+'"  data-toggle="tooltip" title="CLICK TO EDIT THIS MARKET">'+item['vTitle']+'</i></td>'+
                '<td><button class= "btn btn-small btn-danger" onclick=deleteMarket('+item['iId']+')>DELETE <i class="icon-trash  icon-white"></i></button></td></tr>')
                $("#vTitle_name_"+item['iId']).tooltip('hide')
                $('#vTitle_'+item['iId']).editable({
                        type: 'text',
                        pk: item['iId'],
                        url: $projectExecPath+"/backend/markets/edit-market",
                        title: 'Enter username',
                        name: 'vTitle',
                        validate: function(value) {
                            if($.trim(value) == '') {
                            return 'This field is required';
                            }
                        }
                    });
            })
            
            
        }
    }, 'json')
}



function deleteMarket(id){
    $("#deleteMarket").modal();
    $("#delete-market-yes").click(function(){
        $.post('markets/delete-market', 
        {
            id:id
        }, 
        function(resp){
            if(resp.success==true){
                $("[data-id='"+id+"']").remove()
                $("#deleteMarket").modal('hide');
                $("#delete-market-yes").unbind('click');    
            }
        }, 'json');
    });
    
}