<div class="row page-header">
	<div class="col-sm-9">
		<h3 class="margin-0"><?php echo $action; ?> Kiosk Location</h3>
	</div>
	<div class="col-sm-3 text-right">
		<a href="<?php echo site_url('kiosklocation/all'); ?>" class="btn btn-primary">Back</a>
	</div>
</div>

<form method="post" enctype="multipart/form-data" id="form">
	<div class="col-md-12">
		<div class="col-md-3">
	        <div class="form-group">
				<label>Site</label>
				<?php print role_form_dropdown(2, 'site_id', $site_options, $kiosk_location->site_id, 'class="form-control" required'); ?>
			</div>
			<div class="form-group">
				<label>Location Name</label>
				<?php print role_form_input(2, 'name', $kiosk_location->name, 'class="form-control" required'); ?>
			</div>
			<div class="form-group">
				<label>Location Description</label>
				<?php print role_form_textarea(2, ['name' => 'location_within_site', 'rows' => 3], $kiosk_location->location_within_site, 'class="form-control" required'); ?>
			</div>
			<div class="form-group">
				<label>Latitude</label>
				<?php print role_form_input(2, 'lat', $kiosk_location->lat, 'class="form-control" readonly', $hidden_input = true); ?>
			</div>
			<div class="form-group">
				<label>Longitude</label>
				<?php print role_form_input(2, 'lng', $kiosk_location->lng, 'class="form-control" readonly', $hidden_input = true); ?>
			</div>

			<div class="form-group">
				<label>Sales Multiplier</label>
				<?php print role_form_input(2, 'sales_multiplier', $kiosk_location->sales_multiplier, 'class="form-control" required'); ?>
			</div>
			<?php if ($this->session->userdata('role_id') <= 2): ?>
				<div class="form-group">
					<label>Photo</label>
					<input type="file" name="photo" class="form-control" />
				</div>
			<?php endif; ?>
			<div class="form-group">
				<label>Loading Dock/Parking</label>
				<?php print role_form_textarea(2, ['name' => 'nearest_loading_dock_parking', 'rows' => 3], $kiosk_location->nearest_loading_dock_parking, 'class="form-control" required'); ?>
			</div>
		    <div class="form-group">
				<label>Warehouse</label>
				<?php print role_form_dropdown(2, 'warehouse_id', $warehouse_options, $kiosk_location->warehouse_id, 'class="form-control" required'); ?>
			</div>
			<div class="form-group">
				<label>Status</label>
				<?php print role_form_dropdown(2, 'status', $status_options, $kiosk_location->status, 'class="form-control" required'); ?>
			</div>
			<?php if ($this->session->userdata('role_id') <= 2): ?>
				<div class="form-group">
	            	<input type="submit" class="btn btn-primary" value="Submit" name="save" />&nbsp;
	            	<a href="<?php echo site_url('kiosklocation/all'); ?>" class="btn ">Cancel</a>
	        	</div>
	        <?php endif; ?>
		</div>
		<div class="col-md-9">
        	<div id="map" style="height: 500px; width: 100%"></div>
		</div>
	</div>

	<?php print form_hidden('draggable', $draggable); ?>
	<?php print form_hidden('kiosk_location_id', $kiosk_location->id); ?>

</form>
</div>
