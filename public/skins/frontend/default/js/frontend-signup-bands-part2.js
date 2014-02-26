$(document).ready(function(){
    
     //for genres multiple select
    $('.select2').select2({ placeholder : '' });
    
    //get form part2 info
    var url= $("form#form-sub-signup-part2").attr("action")+'/register-bands'
    $("#signup-part2-form").click(function(){
        var data = {};
        $("input").each(function()
        {
                data[$(this).attr('name')]=$(this).val();
        });
        data[$("#pre1_iMarket_id").attr("name")] =$("#pre1_iMarket_id").val()
        data[$("#pre1_vGenre_ids").attr("name")] =$("#pre1_vGenre_ids").val()
        data[$("#pre1_iState_id").attr("name")] =$("#pre1_iState_id").val()
        $.post(url,data,function(resp){
            if(resp['errorMessage']){
                var allError = []
                var state = [];
                if(resp['errorMessage']['iState_id']){
                    $.each(resp['errorMessage']['iState_id'], function(i, item) {

                            state.push('<li>'+item+'</li>');

                     });
                     var ulState = '<ul>Email Errors'+state.join('')+'</ul>'
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
            else{
                
                $("#alert-login-faild").html("<div id='error' class='alert alert-success'>"+resp['SuccessMessage']+"</div>")
                var delay = 3000; //Your delay in milliseconds

                setTimeout(function(){ window.location = $projectExecPath+'/wellcome'; }, delay);
            }
        },'json')
    });
})