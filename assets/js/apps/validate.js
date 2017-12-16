var isFutureDate = function(today, dateValue)
{
	var currentDate = new Date();

	// Find the current time zone's offset in milliseconds.
	var timezoneOffset = currentDate.getTimezoneOffset() * 60 * 1000;
	todayValue = today;
	today = new Date().valueOf();
	idate = dateValue.split("-");
	selectedValue = new Date(dateValue);
	newSelectedValue = selectedValue.getTime() + timezoneOffset;
	selectedValueAU = new Date(newSelectedValue);
    idateValue = selectedValueAU.valueOf();

    return (idateValue - today ) <= 0 ? true : false;
};

$(document).ready(function(){

    $("#form").submit(function(event){
        var prefix = $(this).data('method-prefix');
        var validateFunc = 'validateForm';

        if (prefix  !== undefined)
            validateFunc = prefix+'validateForm';

        if(!window[validateFunc]()) {
            event.preventDefault();
        }

    });
});

var getTimeJS = function(dateValue)
{
	var currentDate = new Date();

	// Find the current time zone's offset in milliseconds.
	var timezoneOffset = currentDate.getTimezoneOffset() * 60 * 1000;

	today = new Date().valueOf();
	idate = dateValue.split("-");
	selectedValue = new Date(dateValue);
	newSelectedValue = selectedValue.getTime() + timezoneOffset;
	date = new Date(newSelectedValue);
	month = date.getMonth()+1;

	return date.getFullYear() +"-" + (prependValue(month)) +"-" + prependValue(date.getDate()) + " " + prependValue(date.getHours()) + ":"+ prependValue(date.getMinutes()) + ":"+ prependValue(date.getSeconds());
}

function prependValue(value)
{
	if (value < 10) {
		return "0" + value;
	}
	return value;
}

