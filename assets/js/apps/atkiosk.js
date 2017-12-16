var AT_KIOSK_DT_REMOVE =null;
var AT_KIOSK_DT_CHECK_PRICE = null;
var AT_KIOSK_DT_STOCK = null;

$(document).ready(function(){
    $('#atKioskList #dodelete').on("click",function(){
        var transfer_id = $(this).data('id');
        destructive_swal('Are you sure?', 'This action can not be undone', 'Yes, Delete', function() {
            App.PickDelete(transfer_id);
        })
    });

    $('#atKioskList #check-price').click(function(){
        var kiosk_id = $('#atKioskId').val();
        $.ajax({
            url : appPath + '/picks/getpricetable',
            type : 'POST',
            data : {'kiosk_id' : kiosk_id},
            success : function(response){
                $('#check-price-data').html(response);
                console.log(response)
                if(!response){
                    $("#table-check-price").hide();
                    alert("No price issues found!");
                }
                else{
                    alert("Price issue found!");
                    $("#table-check-price").show();
                }
            }
        });
    });

    $(document).on("click", '#atKioskList .quickfill-commit', function(){
        App.PickNPackCommit($(this).data('id'), $(this).data('currentid'),  Utils.dateTime());
        $(this).prev().show();
        $(this).next().next().hide();
        $(this).hide(); 
    });

    $(document).on("click",'#atKioskList .quickfill-commit-3',function(){
        $(this).parent().find('.quickfill-reset').show();
        $(this).prev().show();
        $(this).next().next().hide();
        $(this).hide(); 
    });

    $(document).on("click", '#atKioskList .quickfill-commit-queued-no', function(){
        $(this).parent().find('.quickfill-reset').show();
        $(this).prev().show();
        $(this).next().next().hide();
        $(this).hide(); 
    });

    $("#atKioskList .quickfill").on("click",function(){
        $(this).hide();
        var value = $(this).data('value');
        $(this).parent().next().show();
        var input =  $(this).parent().parent().find('input.controls');
        input.val(value);
    });

    $("#atKioskList form#atkiosk-pick .dosubmit").on("click",function(){
        var hidden = $('div.soh-boxes:hidden').length;
        var planagram_not_done = $('.planagram .label-success:hidden').length;
        var button = $(this);

        if(hidden > 0 || planagram_not_done > 0)
        {
            alert("Please tick all the items.");
        }
        else
        {
            var price_changes = $('#atKioskPriceChanges').val();
            if(Utils.isOnline())
            {
                if(price_changes>0)
                {
                    console.log(price_changes)
                    swal({
                        title: "Price issue detected",
                        text: "Continue to press ignore or fix the issue.",
                        type: "info",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Ignore",
                        cancelButtonText: "Fix Issue",
                        closeOnConfirm: false,
                        closeOnCancel: true
                    },
                    function(isConfirm){
                        if (isConfirm) {
                            destructive_swal('Are you sure?', 'This can not be undone', 'Yes', function() {
                                button.prop('disabled', true).val('Processing...');
                                POW.msg.rc('post', button.data('url'), $("form#atkiosk-pick").serialize());
                            })
                        } else {
                            $(window).scrollTop(4000);
                        }
                    });
                }else{
                    button.prop('disabled', true).val('Processing...');

                    var transfer_id = $('input[name=transfer_id]').val();

                    // clear drafts
                    localStorage.setItem('atkiosk-replenish-open'+transfer_id, '');
                    localStorage.setItem('atkiosk-replenish-soh'+transfer_id, '');
                    localStorage.setItem('atkiosk-planagram-open'+transfer_id, '');
                    localStorage.setItem('atkiosk-planagram-closed'+transfer_id, '');

                    button.prop('disabled', true).val('Processing...');
                    POW.msg.rc('post', button.data('url'), $("form#atkiosk-pick").serialize());
                }    
            }
            else
            {
                swal({
                    title: "Price issue detected",
                    text: "Continue to press ignore or fix the issue.....",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Ignore",
                    cancelButtonText: "Fix issue",
                    closeOnConfirm: false,
                    closeOnCancel: false
                },
                function(isConfirm){
                    if (isConfirm) {
                        swal("You are offline!", "Please click on Save Draft button", "warning");
                    } else {
                        swal("Cancelled", "", "error");
                    }
                });
            }
        }
    });

    $("#atKioskList .quickfill-stock").on("click",function(){
        var button = $(this);
        var tr = button.closest('tr');
        var new_soh = +button.data('value') + +button.data('pick');
        var id = tr.data('id');

        if (tr.find('.swap-stock').is('input')) {
            new_soh += +tr.find('.swap-stock').val();
        }

        button.hide();
        tr.find('.soh-boxes').show();
        tr.find('input.soh-item').val(new_soh);
    });

    AT_KIOSK_DT_REMOVE = $('#atKioskList #table-remove-items').DataTable({
        responsive:false,
        "ordering": true,
        "searching": false,
        "info": false,
        "dom": '<"top"lf>t<"bottom"pi><"clear">',
        "autoWidth": false,  "paging": false
    });

    AT_KIOSK_DT_STOCK = $('#atKioskList #table-stock-kiosk').DataTable({
        responsive:false,
        "ordering": true,
        "searching": false,
        "info": false,
        fixedHeader: true,
        "dom": '<"top"lf>t<"bottom"pi><"clear">',
        "autoWidth": false,  "paging": false,
        "columnDefs": [{"orderable": false, "targets": 5 }]
    });

    $('#atKioskList #sorting_stock_kiosk').change( function() {
        var col = $(this).val();
        var item =  col.split("|");
        col = item[0];
        var dir = item[1];

        AT_KIOSK_DT_STOCK
        .column( col )
        .order( dir )
        .draw();
    });

    $('#atKioskList #sorting_remove_items').change( function() {
        var col = $(this).val();
        var item =  col.split("|");
        col = item[0];
        var dir = item[1];

        DT_REMOVE
        .column( col )
        .order( dir )
        .draw();
    });

    $('#atKioskList form#atkiosk-pick').submit(function(){
        alert($(this["options"]).val());
        return false;
    });

    atkiosk_tick_items();

    $('.dosave').on("click", atkiosk_local_draft);
});

function atkiosk_tick_items()
{
    var transfer_id = $('input[name=transfer_id]').val();

    var open = localStorage.getItem('atkiosk-replenish-open'+transfer_id);
    open = open ? open.split('|') : [];
    var soh = localStorage.getItem('atkiosk-replenish-soh'+transfer_id);
    soh = soh ? soh.split('|') : [];

    $('tr.replenish').each(function() {
        var tr = $(this);
        var id = tr.data('position');
        var index = open.indexOf(id.toString());

        if (-1 < index)
        {
            tr.find('.quickfill-stock').trigger('click');
            tr.find('input.soh-item').val(soh[index]);
        }
    });

    var open = localStorage.getItem('atkiosk-planagram-open'+transfer_id);
    open = open ? open.split('|') : [];
    var closed = localStorage.getItem('atkiosk-planagram-closed'+transfer_id);
    closed = closed ? closed.split('|') : [];

    $('tr.planagram').each(function() {
        var tr = $(this);
        var id = tr.data('position') + '-' + tr.data('attribute-id');

        if (-1 < open.indexOf(id))
        {
            tr.find('.quickfill-commit-3').trigger('click');
        }
        else if (-1 < closed.indexOf(id))
        {
            tr.find('.quickfill-remove').trigger('click');
        }
    });
}

function atkiosk_local_draft() 
{
    var data = $("form#atkiosk-pick").serialize();
    var transfer_id = $('input[name=transfer_id]').val();

    var open = [];
    var soh = [];

    $('tr.replenish').each(function() {
        var tr = $(this);

        if (!tr.find('input.soh-item:visible').is('input')) return;

        var id = tr.data('position');
        var value = tr.find('input.soh-item').val();

        open.push(id);
        soh.push(value);
    });

    localStorage.setItem('atkiosk-replenish-open'+transfer_id, open.join('|'));
    localStorage.setItem('atkiosk-replenish-soh'+transfer_id, soh.join('|'));

    var open = [];
    var closed = [];

    $('tr.planagram').each(function() {
        var tr = $(this);
        var id = tr.data('position') + '-' + tr.data('attribute-id');

        if (tr.find('.label-success:visible').length)
        {
            open.push(id);
        }

        if (tr.find('.label-default:visible').length)
        {
            closed.push(id);
        }
    });

    localStorage.setItem('atkiosk-planagram-open'+transfer_id, open.join('|'));
    localStorage.setItem('atkiosk-planagram-closed'+transfer_id, closed.join('|'));

    if(Utils.isOnline()==false)
    {
        swal("Success!", "Changes have been saved", "success");
    }

    var button = $(this);
    POW.msg.rc('post', button.data('url'), $("form#atkiosk-pick").serialize());
}
