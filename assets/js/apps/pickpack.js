var DT = null;
$(document).ready(function() {
    $('#pick-pack-form #dodelete').on("click", function() {
        var transfer_id = $(this).data('id');
        destructive_swal('Are you sure?', "This action can not be undone", 'Yes, Delete', function() {
            App.PickDelete(transfer_id)
        })
    });

    $('#pick-pack-form .dosubmit').on("click", function() {
        var hidden = $('div.soh-boxes:hidden').length;
        if (hidden != 0) {
            alert("Please tick all the items.");
        } else {
            info_swal("This can not be undone. Are you sure you want to proceed?", '', 'Yes', function() {
                $('#pick-type').val("Fully Picked");
                App.PickNPack($("#pick-pack-form"), Utils.dateTime())
            });
        }
    });

    $('#pick-pack-form .dosave').on("click", function() {
        $('#pick-type').val("Partially picked");
        App.PickNPack($("#pick-pack-form"), Utils.dateTime());
    });

    $('#pick-pack-form .quickfill-commit').on("click", function() {
        //App.PickNPackCommit($(this).data('id'), $(this).data('currentid'),  Utils.dateTime());
        $(this).prev().show();
        $(this).next().next().hide();
        $(this).hide();
        $(this).parent().next().find(".collapsed-value").val($(this).parent().next().find(".collapsed-value").data('value'));
        $(this).parent().find(".quickfill-reset").show();
    });

    $('#pick-pack-form .multiselect').multiselect({
        maxHeight: 300,
        buttonWidth: '190',
    });

    $("#pick-pack-form .quickfill").on("click", function() {
        $(this).parent().hide();
        var value = $(this).data('value');
        $(this).parent().next().show();
        var input = $(this).parent().parent().find('input.controls');
        input.val(value);
    });

    //Picktable
    DT = $('#pick-pack-form #table-product-1').DataTable({
        responsive: false,
        "ordering": true,
        "searching": false,
        "autoWidth": false,
        "paging": false,
        "columnDefs": [{"orderable": false, "targets": 5 }]
    });

    $('#pick-pack-form #sorting').change(function() {
        var col = $(this).val();
        var item = col.split("|");
        col = item[0];
        var dir = item[1];
        console.log(dir)
        DT
            .column(col)
            .order(dir)
            .draw();
    });

});
