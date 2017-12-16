$(function() {
    $('.btn-bucket').click(function(e){
        e.preventDefault();

        var group = $(this).closest('.input-group')
        var type = $(this).data('type');
        var input = group.find('input.controls');
        var max = input.attr("max");
        var currentVal = parseInt(input.val());
        var available = 0;
        var selector = input.is('.bucket-destination') ? '.bucket-source' : '.bucket-destination';

        $(selector + '[data-skuid='+input.data('skuid')+']').each(function() {
            available += +$(this).val();
        });

        if (!isNaN(currentVal)) {
            if (type == 'minus') {
                if (currentVal>0) {
                    input.val(currentVal - 1);

                    var dec = false;
                    $(selector + '[data-skuid='+input.data('skuid')+']').each(function() {
                        if (dec) return;

                        var el = $(this);
                        if (+el.val() < +el.attr('max')) {
                            el.val(+el.val() + 1);
                            dec = true;
                        }
                    });
                }
            }
            else if (type == 'plus') {
                if (currentVal < max && available > 0) {
                    input.val(currentVal + 1);

                    var inc = false;
                    $(selector + '[data-skuid='+input.data('skuid')+']').reverse().each(function() {
                        if (inc) return;

                        var el = $(this);
                        if (+el.val() > 0) {
                            el.val(el.val() - 1);
                            inc = true;
                        }
                    });
                }
            }
        } else {
            input.val(0);
        }
    });    
});
    