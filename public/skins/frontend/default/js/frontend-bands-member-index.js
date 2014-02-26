$(document).ready(function(){
    
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
                    $('tbody').prepend(html);
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
                $('tbody').prepend(html);
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

//function editMember(id){
//    var instrument= $('tr[data-id='+id+']').attr('data-instrument');
//    var name= $('tr[data-id='+id+']').attr('data-name');
//    $('input[name="instrument"]').attr('value', instrument);
//    $('input[name="instrument"]').val(instrument);
//    $('input[name="name"]').attr('value', name);
//    $('input[name="name"]').val(name);
//    $('#sub_btn_id').attr('value', id);
//    $('#div_form_id').show();
////    $('span[data-edit]').attr('href', '#modal_id');
////    $('span[data-edit]').attr('data-toggle', 'modal');
//    
//}

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
//<span data-edit="" onclick="editMember('+response.id+');" class="icon-edit mouseC"></span>
//<span data-edit="" onclick="editMember('+data.iId+');" class="icon-edit mouseC"></span> 
//function pagination(){
//    $('#pagination_id').pagination({
//        items: 10,
//        itemsOnPage: 2,
//        cssStyle: 'compact-theme',
//        onPageClick: function(pageNumber){
//            console.log(pageNumber);
//            var page="#page-"+pageNumber;
//            $('.displayPage').hide();
//            $(page).show();
//        }
//    });
//}

