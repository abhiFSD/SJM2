POW.batchhistory = {};

$(function() {

    if (!$('#kiosk-attributes').is('body')) return;

    $('.kiosk-attributes-item').each(function( index ){
        POW.batchhistory[$( this ).attr('name')] = $( this ).val();
    });

    $('select[name="filter_historyItem[]"]').on("change",POW.batchhistory.fill_kiosk_value);
    $('button.attribute-filter').on("click",POW.batchhistory.get_history);
    $(document.body).on('click','#unqueue',POW.batchhistory.unqueue);
    $('#menu-toggle').on('click',POW.batchhistory.toggle_menu);
    $(document.body).on('click','#commit',POW.batchhistory.data_commit);

    POW.batchhistory.prepare_filters();
    POW.batchhistory.prepare_table();

});

POW.batchhistory.toggle_menu = function(e) {
     e.preventDefault();
    $("#wrapper").toggleClass("toggled");
}

POW.batchhistory.prepare_table = function(){
    POW.batchhistory.history_table_config = {
        responsive:true,
        "autoWidth": false ,
        fixedHeader: true,
        "pageLength" :100,
        columnDefs: [
            {"orderable": false, "targets":[0] }
        ],
        "fnDrawCallback": function () {
            $('#history_config_length .change_buttons').remove();
            $('#history_config_length').append($('#commit-buttons').html());
        }
    }
    if(POW.batchhistory.col_sort == 5){
        POW.batchhistory.history_table_config.order = [[ POW.batchhistory.col_sort, "desc" ]];
    }
    else{
        POW.batchhistory.history_table_config.order = [[ 1, "asc" ]];
    }
    $('#history_config').DataTable(POW.batchhistory.history_table_config);
}

POW.batchhistory.prepare_filters = function(){
    var status = POW.batchhistory.status;
    var json = POW.batchhistory.json;
    if(status!=""){
        if(status=="Queued" && json !=""){

            $('select[name="filter_historystatus[]"] option').prop('selected', false)
            $('select[name="filter_historystatus[]"] option[value="'+status+'"]').prop('selected', true).change();
            $('select[name="filter_kiosk[]"] option').prop('selected', false)
            $('select[name="filter_historystate[]"] option').prop('selected', false)

            $.each($.parseJSON(json), function(i,v) {
                $('select[name="filter_historyItem[]"] option[value="'+v[1]+'"]').prop('selected', 'selected');
                $('select[name="filter_kiosk[]"] option[data-id="'+v[2]+'"]').prop('selected', 'selected')
            });

            $('select[name="filter_historystatus[]"]').multiselect("refresh");
            $('select[name="filter_historyItem[]"]').multiselect("refresh");
            $('select[name="filter_kiosk[]"]').multiselect("refresh");
            POW.batchhistory.trigger_filter_click();
        }
    }
    else{
        $('select[name="filter_historystatus[]"]').multiselect("refresh");
        $('select[name="filter_historyItem[]"]').multiselect("refresh");
        $('select[name="filter_kiosk[]"]').multiselect("refresh");
    }
}

POW.batchhistory.trigger_filter_click = function(){
    $('button.attribute-filter:visible').click();
}

POW.batchhistory.fill_kiosk_value = function(){
    var el = $(this);
    var form = el.closest('form');
    var item_name = el.find('option:selected').val();
    var url  = appPath + '/configitem/getValues';
    var value = POW.batchhistory.value;
    var status = POW.batchhistory.status;
    var json = POW.batchhistory.json;
    var data = {itemName:item_name,value:value};

    POW.msg.rc('get', url, data, function(response){
        var historyitemvalue = form.find('select[name="filter_historyitemvalue[]"]');

        historyitemvalue.html("");
        historyitemvalue.html(response);

        if(status!="" && json ==""){
            form.find('select[name="filter_historystatus[]"] option').prop('selected', false);
            form.find('select[name="filter_historystatus[]"] option[value="'+status+'"]').prop('selected', true).change();
            POW.batchhistory.trigger_filter_click();
        }

        if(el.find('option:checked').length ==1){
            historyitemvalue.multiselect('enable');
            historyitemvalue.multiselect('rebuild');
        }
        else{
            historyitemvalue.html("");
            historyitemvalue.multiselect('rebuild');
            historyitemvalue.multiselect('disable');
        }
    });

};

POW.batchhistory.get_history = function(){
    var form = $(this).closest('form');
    var data = {'conditions' : form.serialize()};
    var url = appPath + '/kiosk/getConfigHistory';

    POW.msg.rc('post', url,  data, function(response){
        $('#kiosk_history').html(response);
        $('#history_config').DataTable(POW.batchhistory.history_table_config);
    });
}

POW.batchhistory.unqueue = function(e){
    e.preventDefault();

    var config = $('#config_name_hidden').val();
    var conditions = $('#filter_hidden').val();
    var selects = [];
    var i=0;

    $('#history_config input[name="chkbox"]:checkbox:checked').each(function() {

        var inputs = [];
        //for each checked checkbox, iterate through its parent's siblings and get all the input values
        var inputs = $(this).parent().siblings().find('#label_queued').map(function() {
            return $(this).val().trim();
        }).get();

        if(inputs.length > 0){
            i++;
        }
        inputs.push($(this).attr('data-id'));

        var id_value = $(this).attr('id')
        var res = id_value.split('-');
        inputs.push(res[1]);
        selects.push(inputs);
    });

    if(selects.length > 0){
        if(i > 0){
            swal({
                    title: "Are you sure?",
                    text: "You will not be able to revert this action",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes, unqueue it!",
                    cancelButtonText: "No, cancel please!",
                    closeOnConfirm: true,
                    closeOnCancel: true
                },
                function(isConfirm){
                    if (isConfirm) {
                        var url = appPath + '/kiosk/unQueueKiosks';
                        var data = {
                            'kiosks':JSON.stringify(selects),
                            'conds':conditions,
                            'config':config,
                            'rc':1
                        };
                        POW.msg.rc('get', url,  data, function(response){
                            POW.batchhistory.trigger_filter_click();
                            bootstrap_alert("info", "Changes have been Unqueued");
                        });
                    }
                });
        } else{
            bootstrap_alert('danger', 'Please select a kiosk with queued changes');
        }
    } else{
        bootstrap_alert('danger', "Please select atleast one kiosk with queued change to continue");
    }
}

POW.batchhistory.time_dialog = function(){
    var el = $('#dialog-commit');

    return el.dialog({
        autoOpen: false,
        resizable: true,
        width:300,
        position: { my: "center", at: "center", of: '#kiosk_selection_table' },
        modal: true,
        buttons:{
            "Submit":function(){
                var data = {
                    'selected' : POW.batchhistory.queued_hidden,
                    'date_unapplied' : $('#commit_time').val(),
                    'rc':1
                };
                var form = el.find('form');

                if(isFutureDate(POW.batchhistory.php_timestamp, data.date_unapplied)){
                    POW.msg.rc(form.attr('method'), form.attr('action'),  data, function(response){
                        POW.batchhistory.trigger_filter_click();
                        bootstrap_alert('success', "Request successfully completed.");
                    });
                    $(this).dialog("close");
                }
                else{
                    bootstrap_alert('danger', "Date should be today or past date.");
                }
            },
            "Cancel":function(){
                $(this).dialog("close");
            }
        }
    })
}

POW.batchhistory.data_commit = function(e){
    POW.batchhistory.queued_hidden = '';
    e.preventDefault();
    var elems = [];
    var tmp =[];
    var i=0;
    $('#history_config input[name="chkbox"]:checkbox:checked').each(function() {
        var array = [];
        //for each checked checkbox, iterate through its parent's siblings and get all the input values
        var array = $(this).parent().siblings().find('#label_queued').map(function() {
            return $(this).text();
        }).get();
        if (array.length > 0){
            i++;
        }

        array.push($(this).attr('data-id'));
        var id_value = $(this).attr('id')
        var res = id_value.split('-');
        array.push(res[1]);
        elems.push(array);
    });

    POW.batchhistory.queued_hidden = JSON.stringify(elems);

    if(elems.length > 0){
        if(i > 0){
            POW.batchhistory.time_dialog().dialog('open');
        } else{
            bootstrap_alert('danger', "Please select kiosks with queued data to commit");
        }
    } else{
        bootstrap_alert('danger', "Please select at least one kiosk with queued change to continue");
    }
}