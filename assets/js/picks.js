var PickTable = null;

$(function() {
    $('select[name=state]').change(function(){
        localStorage.setItem("state", $(this).val());

        var opts = $(this).val();
        var form = $(this).closest('form');

        $.ajax({
          url: appPath + '/site/getSitesByState/?id='+opts,
          type:'GET',
          success : function(response){

                form.find('select[name=site_filter]').html(response);
                form.find('select[name=site_filter]').multiselect('rebuild');
                form.find('select[name=site_filter]').change();
            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert(xhr.status + " "+ thrownError)
            }
        });

    });

    $('select[name=site_filter]').change(function() {
        var opts = $(this).val();
        var form = $(this).closest('form');

        $.ajax({
            url: appPath + '/picks/getKiosks/?site_id='+opts,
            type:'GET',
            success : function(response){
                form.find('select[name=kiosk_name]').html(response);
                form.find('select[name=kiosk_name]').multiselect('rebuild');
                form.find('select[name=kiosk_name]').change();
            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert(xhr.status + " "+ thrownError)
            }
        });

    });

    $("#menu-toggle").click(function(e) {
        e.preventDefault();
        $("#wrapper").toggleClass("toggled");
    });

    $('.kiosk-checkbox-all').on('click',function(){
        if($('.kiosk-checkbox-all').prop('checked'))
            $('.pick-checkboxes').prop('checked', true);
        else
            $('.pick-checkboxes').prop('checked', false);
    });

    $('.multiselect').multiselect({
        allSelectedText: 'All',
        maxHeight: 300,
        numberDisplayed: 0,
        buttonWidth: '175',
        includeSelectAllOption: true
    });

    $(document).on('click', '.pick-checkboxes', function () {
        var status = $(this).data('status');
        var checkbox = $(this);
        var pickGenerated = 0;

        if(status =="Pick generated")
        {
            pickGenerated = 1;

            swal(
                {
                    title: "Kiosk "+$(this).data('number')+" already has an unpicked picklist",
                    text: "Continue to replace existing pick list or Cancel to keep existing pick list.",
                    type: "info",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Continue",
                    cancelButtonText: "Cancel!",
                    closeOnConfirm: true,
                    closeOnCancel: true
                },
                function(isConfirm){
                    if (isConfirm) {
                    } else {
                        checkbox.prop('checked','');
                    }
                }
            );
        }

    });

    //Picktable
    PickTable = $('#picks-table').DataTable( {
        "processing": true,
        "serverSide": true,
        "autoWidth": false,
        responsive: false,
        "order": [[ 1, "asc" ]],
        paging:false,
        fixedHeader: true,
        "ajax": appPath + "/picks/table",
        "columns": [
            { "orderable": false },
            { "orderable": true },
            { "orderable": true }
        ]
    } );

    $(document.body).on('click', '.apply_filters_button:visible', function (e) {
        e.preventDefault();
        var form = $(this).closest('form');
        PickTable.columns(1).search(form.find('select[name=kiosk_name]').val() + "|" + form.find('select[name=status]').val());
        PickTable.draw();
    });

    $(document.body).on('click','.slelect-type',function(){
        var selected_ids = [];
        $('.pick-checkboxes').each(function(){
            if($(this).prop('checked')){
                console.log(  $(this).data('id') );
                selected_ids.push( [$(this).data('id'),$(this).data('number')]);
            }
        });

        if(selected_ids.length==0)
        {
              alert("please select kiosk")
        }
        else
        {
            var DateTime = Utils.dateTime();
            var type = $(this).data('type');
            App.Pick(selected_ids,type,DateTime);
            console.log(selected_ids,type)
        }
    });

    var state = localStorage.getItem("state");
    var states = state ? state.split(',') : [];
    var state_filter = $('select[name=state]:visible');

    state_filter.find('option').prop("selected","");

    for (var i=0;i<states.length;i++) {
        console.log('state_filter option[value="'+states[i]+'"]')
        state_filter.find('option[value="'+states[i]+'"]').prop('selected','selected');
    }

    state_filter.multiselect("rebuild");

    state_filter.change();

    setTimeout(function(){
        state_filter.closest('form').find('select[name=site_filter]').change();
    },1000);

});
