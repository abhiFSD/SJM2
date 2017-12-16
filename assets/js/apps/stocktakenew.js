var stockTakeNewChangeWarehouse = function () {
    var filterValue = "";
    var pdtType = $('#stockTakeNewpdttype').val();
    if (pdtType) {
        filterValue = pdtType.join();
    }

    formdata = "";
    if(localStorage.formValues) {
        formdata = localStorage.formValues;
    }
    value = document.getElementById('warehouse').value;
    document.getElementById('productresults').innerHTML = "<i class=\"fa fa-refresh fa-spin\"></i>&nbsp;&nbsp;Loading...Please wait.";
    if (value!="") {
        document.getElementById('productresults').innerHTML = "<i class=\"fa fa-refresh fa-spin\"></i>&nbsp;&nbsp;Loading...Please wait.";
        $.post(appPath +"/stocktakenew/products/"+ value +"/"+ filterValue , formdata, function(data) {
            document.getElementById('productresults').innerHTML = data;
        }).done(function( data ) {

            $('#product-table').DataTable({
                "columnDefs": [
                    { "orderable": false, "targets": [3,4,5] },
                    { "type": "natural", "targets": 0}
                ],
                "paging": false,
                "info": false
            });
        });
        $('#typefilter').removeClass('hidden').addClass('shown');
    } else {
        document.getElementById('productresults').innerHTML = "";
    }
};

var stockTakeNewValidateForm = function ()
{
    dateValue = document.getElementById('date').value;
    locationValue = document.getElementById('warehouse').value;
    var checkDate = $('#stockTakeNewIsFutureDate').val();

    if (dateValue ==   ""  ) {
        bootstrap_alert('danger', 'Missing mandatory fields.');
        return false;
    } else if (!isFutureDate(checkDate, dateValue)) {
        bootstrap_alert('danger', 'Date should be today or past date.');
        return false;
    }

    if (stockTakeNewConfirmation()) {
        // Gather values
        var formValues = $('#stockTakeNewForm').serialize();
        //  var url = $(this).attr('action');
        // postForm(url, formValues);
        if(typeof(Storage) !== "undefined") {
            //   console.log("Storage supported");
            localStorage.formValues = formValues;
            // console.log(localStorage.formValues);
        }

        if (!navigator.onLine) {
            alert ("You're currently offline, preventing the data submission");
            event.preventDefault();
            return false;
        }
        return true;
    }

    return false;
};

var stockTakeNewUpdateStock = function(id) {
    value = $('#product_'+id).val();
    if (value.trim() == "")
        value = 0;

    amount = $('#amount_'+id).val();
    if (amount != "" && amount >= 0) {
        $('#stock_count_'+id).val(parseInt(amount)-parseInt(value));
        $('#stock_display_'+id).text(parseInt(amount)-parseInt(value));

    } else if(amount < 0) {
        alert("Value cannot be negative");
        $('#amount_'+id).val(0);

    } else if (amount.trim() === "") {
        $('#stock_count_'+id).val(null);
        $('#stock_display_'+id).text('');

        $('#amount_'+id).val(null);
        return false;

    }
};

var stockTakeNewConfirmation = function ()
{
    var location = $("#warehouse option:selected").text();
    var dateval = $("#date").val();

    dateStr = (dateval.split('T'));
    date = dateStr[0].split('-');

    if (confirm ('This adjustment date is set to '+ date[2] +'/'+ date[1] + '/'+ date[0] + ' at '+ location + '. Do you want to continue?')) {
        return 1;
    } else {
        return 0;
    }
};

$(document).ready(function(){
    $('#stockTakeNewpdttype').multiselect();

    $("#stockTakeNewForm").submit(function(event){
        if(!stockTakeNewValidateForm ()) {
            event.preventDefault();
        }
    });
});