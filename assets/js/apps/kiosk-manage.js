
$(function() {
    if (!$('#kiosk-manage').is('body')) return;

    $(document).on('blur', 'input[name=number]', POW.kiosk_manage_check_number)
})

POW.kiosk_manage_check_number = function(e)
{
    var regex = /^MPP\d{3}$/;
    var el = $(this);

    if (el.val()) {
        if (regex.test(el.val().trim())) {
            $('#suggestions').html('');

            POW.msg.rc('get', el.data('url')+'/'+el.val()+'/'+$('input[name=kiosk_id]').val(), null, function(data) {
                console.log(data);
                if (data != 'true') {
                    $('#suggestions').html("<small class='error'>"+ el.val()+ ' is already used. Suggested value is: <strong>'+ data +'</strong></small>');
                    el.closest('.form-group').addClass('has-error');
                    el.val(data);
                    el.focus();
                }
                else {
                    el.closest('.form-group').removeClass('has-error');
                }
            })

            return;
        } 
        else {
            $('#suggestions').html("<small class='error'> Invalid Kiosk Number. Kiosk number should be starting with 'MPP' followed by 3 digit number.<br/>ex: MPP001</small>");

            el.closest('.form-group').addClass('has-error');
            el.focus();

            return;
        }

        el.closest('.form-group').removeClass('has-error');
    }
};
