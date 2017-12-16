/**
 * Prerequisite shows child options in the postrequisite
 */

$(function() {
    $('select[data-prerequisite]').each(function() {
        var el = $(this);
        var prereq = $(el.data('prerequisite'));

        if (!prereq.val() || !prereq.val().length) {
            el.prop('disabled', true);
        }
        else {
            el.prop('disabled', false);
        }
    });

    $(document).on('change', 'select[data-postrequisite]', function() {
        update_select_postrequisite($(this));
    });

    $('select[data-postrequisite]').each(function() {
        update_select_postrequisite($(this));
    });
});

function update_select_postrequisite(prereq)
{
    var postreq = $(prereq.data('postrequisite'));
    var shadow = $('#' + postreq.attr('id') + '_shadow');
    var selected = false;

    postreq.find('option').each(function() {
        var option = $(this);
        if (option.attr('value')) option.remove();
    });

    if (!prereq.val() || prereq.val().length) {
        var filter = new RegExp(prereq.val(), 'i');

        shadow.find('option').each(function() {
            var option = $(this);
            if (option.data('filter').match(filter)) {
                var clone = option.clone();
                if (clone.attr('value') == postreq.data('value')) {
                    clone.prop('selected', true);
                    selected = true;
                }
                clone.appendTo(postreq);
            }
        });
    }
    else {
        shadow.find('option').each(function() {
            var option = $(this);
            var clone = option.clone();
            
            if (clone.attr('value') == postreq.data('value')) {
                clone.prop('selected', true);
                selected = true;
            }
            clone.appendTo(postreq);
        });
    }

    // set to default option
    if (!selected) {
        postreq.find('option:nth-child(1)').prop('selected', true);
    }

    postreq.prop('disabled', false);
}
