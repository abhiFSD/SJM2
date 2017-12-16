<!DOCTYPE html>
<html>
<head>
	<title>Powerpod Kiosk Locator</title>
	<meta charset="UTF-8">
	<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css">
        <link rel="stylesheet" type="text/css" href="assets/css/bootstrap-example.min.css" />

</head>

<body>

<div class="bh-sl-container container-fluid">
            <div class="jumbotron">
                <div class="container">
                    <h1>Powerpod Kiosk Locator</h1>
                    <p>Following location are where you can find Powepod Kiosks</p>

                    <div class="bh-sl-form-container">
                        <form id="bh-sl-user-location" class="form-inline" method="post" action="#" role="form">
                            <div class="form-input form-group">
                                <label for="bh-sl-address">Enter Address or Zip Code:</label>
                                <input class="form-control" type="text" id="bh-sl-address" name="bh-sl-address" />
                            </div>

                            <button id="bh-sl-submit" class="btn btn-primary" type="submit">Submit</button>
                        </form>
                    </div>
                </div>
            </div>

            <div id="map-container" class="bh-sl-map-container">
                <div class="row">
                    <div id="map-results-container" class="container">
                        <div id="bh-sl-map" class="bh-sl-map col-md-9"></div>
                        <div class="bh-sl-loc-list col-md-3">
                            <ul class="list list-unstyled"></ul>
                        </div>
                    </div>
                </div>
      </div>


<script src="http://code.jquery.com/jquery-1.11.2.min.js"></script>
<script src="assets/js/libs/handlebars.min.js"></script>
<script src="http://maps.google.com/maps/api/js"></script>
<script src="assets/js/plugins/storeLocator/jquery.storelocator.js"></script>
<script>
	$(function() {
		$('#map-container').storeLocator({
			'querystringParams' : true,
			'fullMapStart': true,
			// The following paths are set because this example is in a subdirectory
			'dataType': 'json',
		    'dataLocation' : 'http://nowretail.co/index.php/deployment/locations',
			//'dataLocation' : 'data/locations.json',
			'infowindowTemplatePath': 'assets/js/plugins/storeLocator/templates/infowindow-description.html',
			'listTemplatePath': 'assets/js/plugins/storeLocator/templates/location-list-description.html'
		});
	});
</script>

</body>
</html>