$(function() {
    var planagram = $('#planagram');

    if (!planagram.is('body')) return;

    // add batch actions dropdown to floating bar
    setInterval(function() {
        var floating = $('body > .fixedHeader-floating');

        if (floating.length) {
            floating.each(function() {
                if (!$(this).find('tr.float').length) {
                    $(this).find('thead').prepend('<tr class="float" role="row"><td colspan="14">' + $('#bulk-buttons .btn-group').prop('outerHTML') + '</td></tr>');
                }
            });
        }
        else if (!floating.length) {
            $('#config_items').find('tr.float').remove();
        }
        floating.css('table-layout','auto');
    }, 250);

    $(document).on('click', '#planagram form.filter button[type=submit]', function(e) {
        e.preventDefault();

        var el = $(this),
            form = el.closest('form'),
            kiosk_name = form.find('select[name=kiosk_name]');

        //currently multiple forms are using this event so we need to check the class attr that needs validation and continue to submit otherwise
        if (!kiosk_name.val() && kiosk_name.hasClass('kiosk_name_filter'))
        {
            //moving this inside the if statement cause it bypasses the validation if the length is no longer empty
            if(!$('.planagram-filter-message').length)
                $('#wrapper').prepend('<p class="planagram-filter-message text-center"><span class="label label-warning">At least 1 kiosk is required.</span></p>');
        }
        else
        {
            $('.planagram-filter-message').remove();
            form.submit();
        }
    });

    $("#searchFormDesktop, #searchForm").submit(function( event ) {

        App.initLoader( $( "#wrapper" ));
        // Stop form from submitting normally
        event.preventDefault();

        // Get some values from elements on the page:
        var $form = $( this ),
            url = $form.attr( "action" );
        var data = { 
            kiosk_name: $form.find("select[name=kiosk_name]").val(),
            kiosk_model: $form.find("select[name=kiosk_model]").val(),
            site_category: $form.find("select[name=site_category]").val(),
            product: $form.find("select[name=product]").val(),
            item_category: $form.find("select[name=item_category]").val(),
            state: $form.find("select[name=state]").val(),
            position: $form.find("select[name=position]").val(),
            par: $form.find("select[name=par]").val(),
            capacity: $form.find("select[name=capacity]").val(),
            status: $form.find("select[name=status]").val(),
            price_issue: $form.find("select[name=price_issue]").val(),
            min_price: $form.find("input[name=min_price]").val(),
            max_price: $form.find("input[name=max_price]").val(),
            item_type: $form.find("select[name=item_type]").val(),
            commit_type: $form.find("select[name=commit_type]").val(),
        };

        // Send the data using post
        var posting = $.post( url, data );

        // Put the results in a div
        posting.done(function( data ) {
            $('#filter_section').hide();
            $("#sidebar-wrapper").scrollTop(0);
            updateTable(data);
            App.destroy(".widget-box-layer");
            params = [];
            allocation = [];
            newPosition = false;
            kioskId = false;
        });
    });

    $("#menu-toggle").click(function(e) {
        e.preventDefault();
        $("#wrapper").toggleClass("toggled");
    });

    var allocation = [];
    var params = [];
    var status = [];
    var newPosition = false;
    var kioskId = false;

    dialog = $( "#confirm" ).dialog({
        autoOpen: false,
        height: 200,
        width: 350,
        modal: true,
        title: 'Commit date and time',
        buttons: {
            //"Commit": saveAllocation('commit'),
            Commit: function() {
                // dialog.dialog( "close" );
                date = document.getElementById('commit_time').value;
                if (date) {
                    if (isFutureDate(date,date)) {
                        params['date'] = getTimeJS(date);
                        console.log(params['date'] );
                        saveAllocation('commit');

                        dialog.dialog( 'close' );
                    } else {
                        alert('You cannot commit for future dates');
                    }
                }
            }
        },
    });

    dialogposition = $( "#position-dialog" ).dialog({
        autoOpen: false,
        height: 350,
        width: 350,
        modal: true,
        title: 'Add Position',
        buttons: {
            Save: function() {
                if (($('#position_kiosk_name').val() == "") || ($('#position-confirm').val() == "") )  {
                    $( "#popup-msg" ).empty().append( "All fields are mandatory" );
                    $( "#popup-msg" ).addClass('alert alert-danger');
                } else {
                    kioskId = $('#position_kiosk_name').val();
                    newPosition  = $('#position-confirm').val();

                    $('#changes').show();
                    dialogposition.dialog('close');
                }
            }
        },
    });

    var selectAllocation = function (el)
    {
        if  (el.checked) {
            allocation.push(el.value);
        } else {
            index = allocation.indexOf(el.value);
            allocation.splice(index, 1);
        }

        if (allocation.length > 0) {
            $('#changes').show();
        } else {
            $('#changes').hide();
        }
    }

    var saveAllocation = function (action)
    {
        var attributes =[];
        var values = [];
        var status = [];
        price = document.getElementById('price').value
        if (price)  {
            attributes.push(3);
            values.push(price)
            status.push($("input[name='price_status']:checked").val());
        }

        capacity = document.getElementById('capacity').value;

        if (capacity) {
            attributes.push(5);
            values.push(capacity);
            status.push($("input[name='capacity_status']:checked").val());
        }

        par = document.getElementById('par').value;

        if (par) {
            attributes.push(6);
            values.push(par);
            status.push($("input[name='par_status']:checked").val());
        }

        product = document.getElementById('pdt_filter').value;

        if (product) {
            attributes.push(1);
            values.push(product);
            status.push($("input[name='product_status']:checked").val());
        }

        width = document.getElementById('spaces').value;

        if (width) {
            attributes.push(8);
            values.push(width);
            status.push($("input[name='spaces_status']:checked").val());
        }

        params['values'] = values;
        params['attributes'] = attributes;
        params['allocation'] = allocation;
        params['status'] =  status;
        params['action'] = action;
        params['position'] = newPosition;
        if (kioskId) {
            params['kioskId'] = kioskId;
        }

        if (newPosition != false && params['attributes'].length < 5) {
            alert('All properties are mandatory when you add new position');
            return false;
        }

        $.post(appPath +"/batchofferingchange/save", Object.assign({},params)).done( function (data) {
            if (data == '1') {
                $('#searchForm').submit();
                $('#changes').hide();
                if (action == "commit") {
                    msg = "Data queued has been commited successfully";
                } else if(action == "queued") {
                    msg = "Data  has been queued successfully";
                } else{
                    msg = "Data  has been unqueued successfully";
                }

                bootstrap_alert('success', msg);
            }
        });
    }

    function switchFilter()
    {
        if ($('#switchfilter').is(":visible")) {
            $('#switchfilter').hide();
        } else {
            $('#switchfilter').show();
        }
    }

    var validateValues = function()
    {
    }

    var planagram_filters = $('#filter-template').detach().html();
    $('#planagrams-mobile-filters').after(planagram_filters);
    $('#planagrams-desktop-filters').append(planagram_filters);

    $('[data-toggle="popover"]').popover({ trigger: "hover",placement: 'bottom' });
    $('.multiselect').each(function() {
        $(this).multiselect({
            allSelectedText: 'All',
            maxHeight: 300,
            numberDisplayed: 0,
            buttonWidth: '175',
            includeSelectAllOption: true,
            enableCaseInsensitiveFiltering: $(this).hasClass('enable-filtering'),
        });
    });

    $(document.body).on('click','#add-position',function(){
        $('#AddPositionModal').modal('show')
    });

    $('.unqueue-delete-tick').on('click',function(){
        var kiosk_id = $(this).data('id');
        var position = $(this).data('position');
        swal({
                title: "Are you sure?",
                text: "This will commit queued item",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes, commit selected!",
                cancelButtonText: "No, cancel!",
                closeOnConfirm: true,
                closeOnCancel: true
            },
            function(isConfirm){
                if (isConfirm) {
                    App.CommitQueuedOfferingItem(kiosk_id,position);
                } else {
                }
            });
    });

    $(document.body).on('click','#remove-selected',function(){
        var selected_ids = [];
        $('.offering-checkboxes').each(function(){
            if($(this).prop('checked')){
                selected_ids.push( [$(this).data('id'),$(this).data('position')]);
            }
        })

        if(selected_ids.length==0)
        {
            alert("please select kiosk")
        }
        else
        {
            swal({
                    title: "Remove Selection(s)",
                    text: "This will queue the selected position(s) to be removed",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes, remove selected!",
                    cancelButtonText: "No, cancel!",
                    closeOnConfirm: true,
                    closeOnCancel: true
                },
                function(isConfirm){
                    if (isConfirm) {
                        App.deleteOfferingQueue(selected_ids);
                        console.log(selected_ids)
                    }
                });
        }
    });


    $(document.body).on('click','#commit-position',function(){
        var selected_ids = [];
        $('.offering-checkboxes').each(function(){

            if($(this).prop('checked')){
                selected_ids.push( [$(this).data('id'),$(this).data('position')]);
            }
        })

        if(selected_ids.length==0)
        {
            alert("please select kiosk")
        }
        else
        {
            swal({
                    title: "Commit Selected",
                    text: "This will commit the selected position(s)",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes, commit selected!",
                    cancelButtonText: "No, cancel!",
                    closeOnConfirm: true,
                    closeOnCancel: true
                },
                function(isConfirm){
                    if (isConfirm) {
                        App.processingButton();
                        App.CommitSelectPosition(selected_ids);
                        console.log(selected_ids)
                    }
                });
        }
    });

    $(document.body).on('click','#unqueue-position',function(){
        var selected_ids = [];
        $('.offering-checkboxes').each(function(){
            if($(this).prop('checked')){
                selected_ids.push( [$(this).data('id'),$(this).data('position')]);
            }
        })

        if(selected_ids.length==0)
        {
            alert("Please select kiosk")
        }
        else
        {
            swal({
                    title: "Unqueue Selected",
                    text: "This will unqueue the selected position(s)",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes, Unqueue selected!",
                    cancelButtonText: "No, cancel!",
                    closeOnConfirm: true,
                    closeOnCancel: true
                },
                function(isConfirm){
                    if (isConfirm) {
                        App.BatchUnqueuPosition(selected_ids);
                        console.log(selected_ids)
                    }
                });
        }
    });

    $(document.body).on('click','#batch-modify',function(){
        var selected_ids = [];
        $('.offering-checkboxes').each(function(){

            if($(this).prop('checked')){
                // console.log(  $(this).data('id') );
                selected_ids.push( [$(this).data('id'),$(this).data('position')]);

            }

        })

        if(selected_ids.length==0)
        {
            alert("Please select kiosk")
        }
        else
        {
            $('#BatchModifyModal').modal('show')
        }
    });

    $('#position-number').keyup(function( event ) {
        App.getPositionKiosk($(this).val());
    });

    function toggle(source) {
        checkboxes = document.getElementsByClassName('allocation_check');
        for(var i=0, n=checkboxes.length;i<n;i++) {
            checkboxes[i].checked = source.checked;
        }
    }

    var planagram_datatables = [];
    function updateTable(data) {

        if (planagram_datatables.length) {
            $.each(planagram_datatables, function(i) {
                console.log(i);
                planagram_datatables[i].fnDestroy();
                delete planagram_datatables[i];
            });

            planagram_datatables = [];
        }

        $("#result").empty().append(data);

        $('#config_items').each(function() {
            var t = $(this).dataTable({
                "paging":   true,
                "order": [[ 1, 'asc'], [3, 'asc']],
                responsive: false,
                "info":     false,
                "pageLength": 100,
                "autoWidth": false,
                fixedHeader: true,
                "columns": [
                    { "orderable": false },
                    { "orderable": true },
                    { "orderable": true },
                    { "orderable": true },
                    { "orderable": true },
                    { "orderable": true },
                    { "orderable": true },
                    { "orderable": true },
                    { "orderable": true },
                    { "orderable": true } ,
                    { "orderable": true },
                    { "orderable": true },
                    { "orderable": true } ,
                    { "orderable": false }
                ],
                "lengthMenu": [[10, 25, 50,100, 200, -1], [10, 25, 50,100,200, "All"]],
                fnDrawCallback: function() {
                }
            });
            planagram_datatables.push(t);
        });
        //need to move this out from the fnDrawCallback as it creates multiple buttons when the table is redrawn
        $('#config_items_length').prepend($('#bulk-buttons').html());
    }

    var filter = function ()
    {
        var values = new Array;
        var url = appPath +"/configitem/all/?";
        $("select[class='filter_crit'] option:selected").each(function() { // this function is to select all the filter criteria
            if($(this).val() != ''){
                values.push({
                    key:$(this).attr('data-id'),
                    value :$(this).val()
                });
            }
        });

        for(var i=0; i < values.length ; i++){
            url = url + values[i]['key']+'='+values[i]['value'] +'&&'; //url passing key value pair to the controller
        }

        document.location.href= url;
    }

    if ($('#planagram form.filter select[name=kiosk_name]:visible').val()) {
        $('#planagram form.filter:visible').submit();
    }
});
