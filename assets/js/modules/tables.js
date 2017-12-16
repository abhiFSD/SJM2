POW.tables = {};

$(function() {
    prep_datatables();

    $(document).on('html-update', prep_datatables);
    $(window).on('resize', POW.tables.pow_responsive);

    POW.tables.pow_responsive();
});

function prep_datatables()
{
    $('div.datatable,div.datatable-json').each(function() {
        var target = $(this);
        var options = {
            pageLength: 100,
            fixedHeader: true
        };
        if (target.is('.datatable-json')) {
            options.aaData = table_data;
        }
        if (target.data('hide-length')) {
            options.bLengthChange = false;
        }
        if (target.is('[data-sort-column]')) {
            options.order = [[+target.data('sort-column'), target.data('sort-direction') ? target.data('sort-direction') : 'asc']];
        }
        if (target.is('[data-no-filter-column]')) {
            options.columnDefs = [{"searchable": false, "targets": JSON.parse("[" + target.data('no-filter-column') + "]")}];
        }
        target.find('table').dataTable(options);
    });
}

POW.tables.pow_responsive = function() 
{
    var table = $('table.pow-responsive');

    if (window.innerWidth < 960 && table.is('table') && table.is(':not(.pow-processed)')) {
        var ths = table.find('thead th');

        if (table.is('.table-bordered')) {
            table
                .removeClass('table-bordered')
                .addClass('was-bordered');
        }

        table.addClass('pow-processed');

        table.find('tbody tr').each(function() {
            var tr = $(this);

            tr.find('td').reverse().each(function(i) {
                var td = $(this);
                if (!td.html().trim()) {
                    td.html('&nbsp;');
                }
                td.before('<th class="mobile-only">' + ths.eq(ths.length - i - 1).text() + '</th>');
            })
        })
    }
    else if (window.innerWidth >= 960 && table.is('table') && table.is('.pow-processed')) {
        $('tbody tr th.mobile-only').remove();

        if (table.is('.was-bordered')) {
            table.addClass('table-bordered');
        }

        table.removeClass('pow-processed');
    }
};
