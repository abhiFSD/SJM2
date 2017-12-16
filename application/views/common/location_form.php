<!--Dialog box to add new locations-->
<div id="dialog-form4" title = "Add Location">
	<form method="post" enctype="multipart/form-data" class="form" id="location_form" action="kiosklocation/addNewKioskLocation">
		<div class="col-md-12">
			<div class="col-md-12">
				<div class="form-group">
					<label>Site:</label>
					<input name ="k_site_id" id="k_site_id" class="form-control" value= "<?php echo(isset($location)? $location->sitename :'');?>" readonly/>
					<input type="hidden"  name="k_site" id="k_site" value = "<?php echo(isset($location)? $location->id :'');?>"/>
				</div>
				<div class="form-group">
					<label>Location Name:</label>
					<input type="text" name="k_name" class="form-control" value="<?php //echo (isset($kiosk)?$kiosk->name:'');?>" required />
				</div>
				<div class="form-group">
					<label>Location Description:</label>
					<textarea name="k_location_within_site" class="form-control" required><?php //echo (isset($kiosk)?$kiosk->location_within_site:''); ?></textarea>
				</div>
			</div>
			<div class="col-md-6">
				<div class="form-group">
					<label>Latitude:</label>
					<input type="text" name="k_latitude" id="dialog_latbox" class="form-control" value="-27.46715487370691"  />
				</div>
			</div>
			<div class="col-md-6">
				<div class="form-group">
					<label>Longitude:</label>
					<input type="text" name="k_longitude" id="dialog_lngbox" class="form-control" value='153.0158616362305'  />
				</div>
			</div>
			<div class="col-md-12">
				<div class="form-group">
					<label>Photo:</label>
					<input type="file" name="k_photo" class="form-control" />
				</div>
				<div class="form-group">
					<label>Loading Dock/Parking:</label>
					<textarea name="k_loading_dock" class="form-control" required><?php //echo (isset($kiosk)?$kiosk->nearest_loading_dock_parking:''); ?></textarea>
				</div>
				<div class="form-group">
					<label>Warehouse:</label>
					<select name="k_warehouse_id" class="form-control" value="<?php //echo (isset($kiosk)?$kiosk->warehouse_id:'');?>" required>
						<option value="">Select</option>
						<?php foreach ($warehouses as $warehouse) { ?>
						<option value="<?php echo $warehouse->id; ?>"><?php echo $warehouse->name; ?></option>
						<?php } ?>
					</select>
				</div>
				<input type="hidden" value="Active" name="k_status" />
			</div>
			<div class="col-md-12">
				<div id="map2" style="height:250px; width:100%;overflow:visible;"></div>
			</div>
		</div>
	</form>
</div>

<script type="text/javascript">
function initMap() {
	var myLatlng = new google.maps.LatLng(-27.46715487370691,153.0158616362305);
	var map = null, marker = null;
	var mapOptions = {
		center: myLatlng,
		mapTypeId: google.maps.MapTypeId.ROADMAP,
		zoom:14
	}
	map = new google.maps.Map(document.getElementById("map2"), mapOptions);
	marker = new google.maps.Marker({
		position: map.getCenter(),
		visible:true,
		draggable:true
	});
	google.maps.event.addListener(marker, 'dragend', function (event) {
		document.getElementById("dialog_latbox").value = this.getPosition().lat();
		document.getElementById("dialog_lngbox").value = this.getPosition().lng();
	});
	google.maps.event.addListener(marker, 'click', function() {
		map.setCenter(marker.getPosition());
	});
	// To add the marker to the map, call setMap();
	map.setCenter(new google.maps.LatLng(-27.46715487370691,153.0158616362305));
	marker.setMap(map);
	$(document).ready(function() {
		$(window).click(function() {
			// to add hte marker to the center on clicking the window
			map.setCenter(marker.getPosition());
			google.maps.event.trigger(map, 'resize');
		});
		google.maps.event.trigger(map, 'resize');
	});
}
</script>
<script async defer
	src="https://maps.googleapis.com/maps/api/js?key=AIzaSyD6yFiDcJX1tNT8waZAqHHL99DXteBINKo&callback=initMap">
</script>