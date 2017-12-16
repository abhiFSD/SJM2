$(function() {
    $(document).on('submit', "#planagram-history form", function(event) {
        event.preventDefault();

        var form = $(this);

        $("#result").empty().append( "Loading...please wait." );

        $.ajax({
            url: form.attr('action'),
            data: form.serialize(),
            type: 'post',
            success: function(data) {
                $('#result')
                    .empty()
                    .append(data)
                    .find('table')
                    .dataTable({
                        pageLength: 100,
                        fixedHeader: true,
                        order: [[0, 'asc'], [1, 'asc']]
                    });
            }
        })
    });

});
