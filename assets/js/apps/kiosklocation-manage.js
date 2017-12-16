function kiosk_location_init_map(latValue, lngValue) {
    var lat_input = $('input[name=lat]');
    var lng_input = $('input[name=lng]');
    var draggable = $('input[name=draggable]').val();
    var lat = lat_input.val() ? lat_input.val() : '-27.46715487370691';
    var lng = lng_input.val() ? lng_input.val() : '153.0158616362305';

    var myLatlng = new google.maps.LatLng(lat,lng);
    var mapOptions = {
        zoom: 14,
        center: myLatlng
    };
    var map = new google.maps.Map(document.getElementById("map"), mapOptions);
    var marker = new google.maps.Marker({
        position: myLatlng,
        draggable: draggable == 'true' ? true : false,
    });

    if (draggable == 'true') {
        google.maps.event.addListener(marker, 'dragend', function (event) {
            lat_input.val(this.getPosition().lat());
            lng_input.val(this.getPosition().lng());
        });
    }

    // To add the marker to the map, call setMap();
    marker.setMap(map);
}
