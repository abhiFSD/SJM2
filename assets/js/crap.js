
// creates a jquery ui pop u dialog to add new licensor
$(document).ready(function() {

    var dialog = $('#dialog-form').dialog({
        autoOpen: false,
        resizable: true,
        width: 300,
        modal: true,
        buttons: {
            "Submit": function() {
                var form_data = {
                    id: $('input[name="l_id"]').val(),
                    name: $('input[name="l_name"]').val(),
                    status: $('input[name="l_status"]').val(),
                    ajax: 1
                };
                if ($("#licensor_form").valid()) {
                    $.ajax({
                        url: appPath + "/party/addNewParty",
                        type: 'POST',
                        data: form_data,
                        success: function(msg) {
                            var html = '<option value="' + msg.licensor_id + '" data-party_id="' + msg.party_id + '" data-name="' + form_data['name'] + '" selected>' + form_data['name'] + "</option>";
                            $('.form select[name="licensor_id"]').append(html);
                            $('#agreement_form input[name="la_licensor_name"]').append(html);
                            $('.form input[name="s_licensor_id"]').append(html);
                            $('#licensor_form')[0].reset();
                            $('#dialog-form').dialog("close");
                        }
                    });
                }
            },
            "Cancel": function() {
                $('#licensor_form')[0].reset();
                $(this).dialog("close");
            }
        }
    });
    // Licensor dialog form ends here

    var dialog1 = $('#dialog-help1').dialog({
        autoOpen: false,
        resizable: true,
        width: 400,
        show: "fade",
        hide: "fade",
        position: {
            my: "left top",
            at: "right middle",
            of: '#help-id'
        }
    });

    var dialog2 = $('#dialog-helpdiv').dialog({
        autoOpen: false,
        resizable: true,
        width: 400,
        show: "fade",
        hide: "fade",
        position: {
            my: "left top",
            at: "right + 20",
            of: '.help_id'
        }

    });

    var dialog3 = $('#dialog-help2').dialog({
        autoOpen: false,
        show: "fade",
        hide: "fade",
        resizable: true,
        width: 400,
        position: {
            my: "left top",
            at: "right middle",
            of: '.help-icon2'
        }
    });

    var dialog4 = $('#dialog-helpdiv2').dialog({
        autoOpen: false,
        resizable: true,
        show: "fade",
        hide: "fade",
        width: 400,
        position: {
            my: "left top",
            at: "right + 10",
            of: '.help-icon3'
        }
    });

    var dialogConfig = $('#hint-dialog').dialog({
        autoOpen: false,
        resizable: true,
        width: 400,
        modal: true,
        buttons: {
            "OK": function() {
                $(this).dialog('close');
            }
        }
    });



    var dialog_site = $('#dialog-form3').dialog({
        autoOpen: false,
        resizable: true,
        width: 500,
        modal: true,
        buttons: {
            "Submit": function() {
                var form_data3 = {
                    "id": $('input[name="s_id"]').val(),
                    "name": $('input[name="s_name"]').val(),
                    "address": $('input[name="s_address"]').val(),
                    "city": $('input[name="s_city"]').val(),
                    "state": $('input[name="s_state"]').val(),
                    "postcode": $('input[name="s_postcode"]').val(),
                    "licensor_id": $('input[name="s_licensor_id"]').attr('data-id'),
                    "days_per_week": $('input[name="s_day_per_week"]').val(),
                    "category": $('select[name="s_category"]').val(),
                    "status": $('input[name="s_status"]').val(),
                    "security_phone_number": $('input[name="s_security_phone_number"]').val(),
                    "concierge_phone": $('input[name="s_concierge_phone"]').val(),
                    "ajax": 1
                };
                if ($('#site_form').valid()) {
                    $.ajax({
                        url: appPath + '/site/addNewSite',
                        type: 'POST',
                        data: form_data3,
                        success: function(msg) {

                            var html = "<option value='" + form_data3['name'] + "' data-id=" + form_data3['id'] + " selected>" + form_data3['name'] + "</option>";
                            $('.form select[name="site_id"]').append(html);
                            $('#site_form')[0].reset();
                            $('#dialog-form3').dialog("close");

                        }
                    });

                }
            },
            "Cancel": function() {
                $('#site_form')[0].reset();
                $(this).dialog("close");
            }
        }
    });

    var dialog_location = $('#dialog-form4').dialog({
        autoOpen: false,
        resizable: true,
        width: 500,
        modal: true,
        buttons: {
            "Submit": function() {
                var form_data4 = {
                    'location_id': $('input[name="location_id"]').val(),
                    "name": $('input[name="k_name"]').val(),
                    "site_id": $('input[name="k_site"]').val(),
                    "sales_multiplier": $('input[name="k_sales_multiplier"]').val(),
                    "location_within_site": $('textarea[name="k_location_within_site"]').val(),
                    "status": $('input[name="k_status"]').val(),
                    "warehouse_id": $('select[name="k_warehouse_id"]').val(),
                    "latitude": $('input[name="k_latitude"]').val(),
                    "longitude": $('input[name="k_longitude"]').val(),
                    "loading_dock": $('textarea[name="k_loading_dock"]').val(),
                    "photo": $('input[name="k_photo"]').val(),
                    "ajax": 1
                };
                if ($('#location_form').valid()) {
                    $.ajax({
                        url: appPath + '/kiosklocation/addNewKioskLocation',
                        type: 'POST',
                        data: form_data4,
                        success: function(msg) {

                            var html = "<option value=" + form_data4['location_id'] + " selected>" + msg.name + "</option>";
                            $('.form select[name="location_id"]').append(html);
                            $('#location_form')[0].reset();
                            $('#dialog-form4').dialog("close");
                        }
                    });

                }
            },
            "Cancel": function() {
                $('#location_form')[0].reset();
                $(this).dialog("close");
            }
        }
    });

    //Add Unit of Measure Pop Up Dialog form
    var dialogUom = $('#dialog-uom').dialog({
        autoOpen: false,
        resizable: true,
        width: 300,
        modal: true,
        buttons: {
            "Submit": function() {
                var name = $('input[name="uom_name"]').val();
                if ($("#uom_form").valid()) {
                    var html = "<option value='" + name + "' selected>" + name + "</option>";
                    $('#config_form select[name="uom"]').append(html);
                    $('#uom_form')[0].reset();
                    $('#dialog-uom').dialog("close");
                }
            },
            "Cancel": function() {
                $('#uom_form')[0].reset();
                $(this).dialog("close");
            }

        }

    });


    /* Buttons for Agreement page */
    $('.licenseAdd').click(function() {
        dialog.dialog('open');
    });

    // Mouse hover effect on clicking help-id button
    $('#help-id').on("mouseenter", function() { //first help icon in License Agreement Page
        dialog1.dialog('open').parent().find('.ui-dialog-titlebar-close').hide(); //this is to close the titlebar on the dialog box
    }).on("mouseleave", function() {
        dialog1.dialog('close');

    });

    $('#help-iconid').hover( //second help icon in License Agreement Page
        function() {
            dialog3.dialog('open').parent().find('.ui-dialog-titlebar-close').hide();
            //console.log("Mouse enter")
        },
        function() {
            dialog3.dialog('close');
            //console.log("Mouse Leave")
        });
    $('#help-iconlast').hover( //third help icon in License Agreement Page
        function() {
            dialog3.dialog('open').parent().find('.ui-dialog-titlebar-close').hide();
        },
        function() {
            dialog3.dialog('close');
        });

    $('.help_id').hover( //First help icon in Deployments page
        function() {
            dialog2.dialog('open').parent().find('.ui-dialog-titlebar-close').hide();
        },
        function() {
            dialog2.dialog('close');
        });

    $('.help-icon3').hover(function() { //Second help icon in Deployments Page
        dialog4.dialog('open').parent().find('.ui-dialog-titlebar-close').hide();
    }, function() {
        dialog4.dialog('close');
    });

    $('#hint-id').mouseover(function() {
        dialogConfig.dialog('open');
    });
    $('#unit_add').click(function() {
        dialogUom.dialog('open');
    });

    /*Buttons for Agreement page ends here */
    /* Buttons for Deployment Page */

    $('#add_site').click(function() { //on clicking the Add agreement button, the dialog box opens as well as es populated with the Licensor
        var la = $('#d_lid option:selected').attr('data-name');
        $('#s_licensor_id').val(la);
        var l_id = $('#d_lid option:selected').val();
        $('#s_licensor_id').attr('data-id', l_id);
        dialog_site.dialog('open');
    });

    $('#add_location').click(function() {
        var k_id = $('#site_id option:selected').val();
        //alert(k_id);
        $('#k_site_id').val(k_id);
        var kname = $('#site_id option:selected').attr('data-id');
        //alert(kname);
        $('#k_site').val(kname);

        dialog_location.dialog('open');
    });

    function checkRules() {
        var msg = "";
        console.log($('#la_fixed').val());
        if ((($('#la_fixed').val() == "") || ($('#la_fixed').val() == "0.00")) &&
            (($('#la_commission1').val() == "") || ($('#la_commission1').val() == "0.0000"))
        ) 
        {
            msg = "Fixed Rate or Commission should be provided.";
        }

        if ($('#la_commission1').val() != "" && $('#la_commission1').val() != '0.0000') {
            // threshold should be present.
            if ($('#la_commission1_threshold').val() == "" || $('#la_commission1_threshold').val() == '0.00') {
                msg = "Commission 1 Threshold should be provided.";
            }
            if ($('#la_commission2').val() != "" && $('#la_commission2').val() != '0.0000') {
                // threshold should be present.
                if ($('#la_commission2_threshold').val() == "" || $('#la_commission2_threshold').val() == '0.00') {
                    msg = "Commission 2 Threshold should be provided.";
                }
            }


        } else {
            // commission 2 and threshold should be blank.
            if (($('#la_commission2').val() != "" && $('#la_commission2').val() != '0.0000') ||
                $('#la_commission2_threshold').val() != "" && $('#la_commission2_threshold').val() != '0.00') {
                msg = "Commission 2 or threshold should be provided after commission #1.";
            }
        }

        if (msg != "") {
            $('.alert').html(msg);
            $('.alert').show();
            msg = "";
            return false;
        }
        return true;

    }
    $("#agreement_form input[type='text']").blur(function() {
        $('.alert').hide();
    })
});

// jquery dialogues and actions for add/edit product page starts here
$(document).ready(function() {
    // add new product category dialog starts here
    var dialog_product_category_form = $('#dialog_product_category_form').dialog({
        autoOpen: false,
        resizable: true,
        width: 300,
        modal: true,
        buttons: {
            "Submit": function() {
                var form_data = {
                    name: $('input[name="category_name"]').val(),
                    name_abbreviation: $('input[name="name_abbreviation"]').val(),
                    parent_product_category_id: $('select[name="parent_product_category_id"]').val(),
                    status: $('#category_status').val(),
                };
                if ($("#product_category_form").valid()) {
                    $.ajax({
                        url: appPath + "/productcategory/add_new_product_category",
                        type: 'POST',
                        data: form_data,
                        dataType: 'json',
                        success: function(data) {
                            $('#product_category_form')[0].reset();
                            $('#parent_product_category_id').append('<option value="' + data.inserted_id + '">' + form_data.name + '</option>');
                            $('#category_select').append('<option value="' + data.inserted_id + '" selected>' + form_data.name + '</option>');
                            $('#dialog_product_category_form').dialog("close");
                        }
                    });
                }
            },
            "Cancel": function() {
                $('#product_category_form')[0].reset();
                $(this).dialog("close");
            }
        }
    });
    $('#add_category').click(function() {
        dialog_product_category_form.dialog('open');
    });
    // add new product category dialog ends here

    // upload product images dialog starts here
    var dialog_upload_product_images_form = $('#dialog_upload_product_images_form').dialog({
        autoOpen: false,
        resizable: true,
        width: 650,
        modal: true,
        buttons: {
            "Cancel": function() {
                $(this).dialog("close");
            }
        }
    });
    $('#upload_product_images_button').click(function() {
        dialog_upload_product_images_form.dialog('open');
    });
    // upload product images dialog ends here

    // add new product attribute dialog starts here
    var new_product_attribute_form = $('#dialog_new_product_attribute_form').dialog({
        autoOpen: false,
        resizable: true,
        width: 600,
        modal: true,
        buttons: {
            "Submit": function() {
                var paramObj = $('#new_product_attribute_form').serializeObject();


                if ($("#new_product_attribute_form").valid()) {
                    $.ajax({
                        url: appPath + "/productattribute/save_product_attribute",
                        type: 'POST',
                        data: paramObj,
                        dataType: 'json',
                        success: function(data) {
                            console.log(data)
                            // adding the new option to the new attribute option dropdown for product
                            $('#new_attribute_values').append('<option data-unit="' + data.unit + '" value="' + data.id + '">' + data.name + '</option>');
                            // getting that option selected
                            $('#new_attribute_values').val(data.id);
                            // enabling the add product attribute button
                            $('#add_new_product_attribute_button').prop('disabled', false);
                            $('#dialog_new_product_attribute_form').dialog("close");
                            $('#new_product_attribute_form')[0].reset();

                            new AddProductAttribute(data);
                        }
                    });
                }
            },
            "Cancel": function() {
                $('#new_product_attribute_form')[0].reset();
                $(this).dialog("close");
            }
        }
    });
    $('#new_product_attribute_button').click(function() {
        new_product_attribute_form.dialog('open');
    });
    // add new product attribute dialog ends here

    // add new unit dialog starts here
    var new_unit_form = $('#new_unit_form').dialog({
        autoOpen: false,
        resizable: true,
        width: 300,
        modal: true,
        buttons: {
            "Add Unit": function() {
                var unit_name = $('input[name="unit_name"]').val();
                if ($("#new_unit_form").valid()) {
                    $('#unit_of_measure_id_dropdown').append('<option value="' + unit_name + '" selected>' + unit_name + '</option>');
                    $('#new_unit_form')[0].reset();
                    $(this).dialog("close");
                }
            },
            "Cancel": function() {
                $('#new_unit_form')[0].reset();
                $(this).dialog("close");
            }
        }
    });
    $('#add_new_unit_button').click(function() {
        new_unit_form.dialog('open');
    });
    // add new unit dialog ends here

    // add new product attribute dialog starts here
    var dialog_new_attribute_option_form = $('#new_attribute_option_form').dialog({
        autoOpen: false,
        resizable: true,
        width: 300,
        modal: true,
        buttons: {
            "Submit": function() {
                var form_data = {
                    option_name: $('input[name="option_name"]').val(),
                    sku_suffix: $('input[name="sku_suffix"]').val().toUpperCase(),
                };



                if ($("#new_attribute_option_form").valid()) {
                    $('#products_attribute_options_table tbody').append('<tr class="product_attribute_row"><td><input type="hidden" name="attribute_option_name[]" value="' + form_data.option_name + '" />' + form_data.option_name + '</td><td><input type="hidden" name="attribute_option_suffix[]" value="' + form_data.sku_suffix + '" />' + form_data.sku_suffix + '</td><td><a class="remove-attr-popup btn" onclick="remove_row($(this));"><i class="fa fa-trash-o" aria-hidden="true"></i> </a></td></tr>');
                    $('#new_attribute_option_form')[0].reset();
                    $(this).dialog("close");
                }
            },
            "Cancel": function() {
                $('#new_attribute_option_form')[0].reset();
                $(this).dialog("close");
            }
        }
    });
    $('#add_attribute_option_button').click(function() {
        $('#grey_box_attribute').val($('#new_product_attribute_form input[name="attribute_name"]').val());
        dialog_new_attribute_option_form.dialog('open');
    });
    // add new product attribute dialog ends here
})
// jquery dialogs and actions for add/edit product page ends here

function remove_row(obj) {
    obj.parent().parent().remove();
}

// form data to json
$.fn.serializeObject = function() {
    var o = {};
    var a = this.serializeArray();
    $.each(a, function() {
        if (o[this.name]) {
            if (!o[this.name].push) {
                o[this.name] = [o[this.name]];
            }
            o[this.name].push(this.value || '');
        } else {
            o[this.name] = this.value || '';
        }
    });
    return o;
};
