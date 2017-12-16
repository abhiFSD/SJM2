$(document).ready(function(){
    var formState = 0;
    $('#indexNewpdttype').multiselect();
});
var indexNewChangeLocation = function () {

    formdata = $( "#stock-adjustment-form #form" ).serialize();

    value = $('#location').val();
    adjustment = $('#adjustment').val();

    if (value != "") {
        $('#productresults').html("<i class=\"fa fa-refresh fa-spin\"></i>&nbsp;&nbsp;Loading...Please wait.");
        $.post(appPath +"/stockadjustmentnew/products/" + value,
            formdata,
            function (data) {
                $('#productresults').html(data);
            }
        ).done(function( data ) {
                $('#stock-adjustment-table').DataTable({
                    "columnDefs": [
                        { "orderable": false, "targets": [3] }
                    ],
                    "paging": false,
                    "info": false
                });
            });
        $('#typefilter').show();
    } else {
        $('#productresults').html('');
    }
};

var indexNewvalidateForm = function () {

    dateValue = $('#date').val();
    locationValue = $('#location').val();
    adjustment = $('#adjustment').val();
    var checkDate = $('#indexNewIsFutureDate').val();

    if (adjustment != "" && adjustment != 4 && adjustment != 10 && adjustment != 11) {
        pick = $('#field_' + adjustment).val();
        pickFlag = true;

    } else if (adjustment > 0) {
        pickFlag = false;
        pick = "";
    }

    if (dateValue == "" || locationValue == "" || adjustment == "" || (pickFlag && pick == "")) {
        bootstrap_alert('danger', 'Missing mandatory fields.');
        return false;
    } else if (!isFutureDate(checkDate, dateValue)) {
        bootstrap_alert('danger', 'Date should be today or past date.');
        return false;
    }

    if (indexNewconfirmation()) {

        var formValues = $('#form').serialize();
        console.log(formValues);
        if (typeof(Storage) !== "undefined") {
            localStorage.formValues = formValues;
        }

        if (!navigator.onLine) {
            alert("You're currently offline, preventing the data submission");
            event.preventDefault();
            return false;
        }
        return true;
    }
};

var indexNewupdateStock = function (id) {
    formState = 1;
    adjustment = $('#adjustment').val();

    value = $('#product_' + id).val();
    amount = $('#amount_' + id).val();

    if (amount < 0) {
        alert("Amount cannot be negative");
        indexNewchangeValue(id, "");
        $('#amount_' + id).val(null);
        return false;
    } else if (amount.trim() === "") {
        indexNewchangeValue(id, "");
        $('#amount_' + id).val(null);
        return false;
    }

    if (adjustment == 4 || adjustment == 9 || adjustment == 10 || adjustment == 12 || adjustment == 13 || adjustment == 14) {
        newvalue = parseInt(value) - parseInt(amount);
    } else {
        newvalue = parseInt(value) + parseInt(amount);
    }

    if (newvalue >= 0) {
        indexNewchangeValue(id, newvalue);
    } else if (newvalue < 0) {
        alert("New stock on hand cannot be negative");
        indexNewchangeValue(id, "");
        $('#amount_' + id).val(null);
    }
};

var indexNewchangeValue = function (id, newvalue) {
    $('#stock_count_' + id).val(newvalue);
    $('#stock_display_' + id).text(newvalue);
};

var indexNewchangeAdjustment = function () {

    var label = new Array();
    label[1] = 'Date & Time Order Received ';
    label[2] = 'Date & Time at Kiosk';
    label[3] = 'Date & Time at Kiosk';
    label[4] = 'Date & Time of Pick';
    label[6] = 'Date & Time of Pick';
    label[8] = 'Date & Time of Kiosk';
    label[7] = 'Date & Time';
    label[9] = 'Date & Time';

    label[10] = 'Date & Time Sent';
    label[11] = 'Date & Time Received';
    label[12] = 'Date Picked';
    label[13] = 'Date Picked';
    label[14] = 'Date Picked';


    $('.adjustment_fields').hide();
    value = $('#adjustment').val();
    if (value) {
        $('.field_' + value).show();
        $('#date-label').html(label[value]);
    }
};

var indexNewconfirmation = function () {
    var location = $("#location option:selected").text();
    var dateval = $("#date").val();

    dateStr = (dateval.split('T'));
    date = dateStr[0].split('-');

    if (confirm('This adjustment date is set to ' + date[2] + '/' + date[1] + '/' + date[0] + ' at ' + location + '. Do you want to continue?')) {
        return 1;
    } else {
        return 0;
    }
};