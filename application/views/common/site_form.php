<div id="dialog-form3" title="Add Site">
	<form method="post" id="site_form" class="form" action="site/addNewSite">
		<div class="col-md-12">
			<div class="col-md-12">
				<div class="form-group">
					<label>Licensor:</label>
					<input name="s_licensor_id" id="s_licensor_id" class="form-control" 
						data-id="<?php echo $licensor_id; ?>" 
						value="<?php echo $deployment->kiosk_location->site->licensor->party->display_name; ?>" readonly/>
				</div>
				<div class="form-group">
					<label>Site Name:</label>
					<input type="text" name="s_name" class="form-control" required/>
				</div>
				<div class="form-group">
					<label>Address:</label>
					<input type="text" name="s_address" class="form-control"  required/>
				</div>
				<div class="form-group">
					<label>City:</label>
					<input type="text" name="s_city" class="form-control" value="<?php //echo (isset($site)?$site->city:'');?>" required/>
				</div>
				<div class="form-group">
					<label>State:</label>
					<input type="text" name="s_state" class="form-control" value="<?php //echo (isset($site)?$site->state:'');?>" required/>
				</div>
				<div class="form-group">
					<label>Postcode:</label>
					<input type="text" name="s_postcode" class="form-control" value="<?php //echo (isset($site)?$site->postcode:'');?>" required/>
				</div>
				<div class="form-group">
					<label>Security Phone Number:</label>
					<input type="text" name="s_security_phone_number" class="form-control" value="<?php //echo (isset($site)?$site->security_phone_number:'');?>" required/>
				</div>
				<div class="form-group">
					<label>Concierge Phone Number:</label>
					<input type="text" name="s_concierge_phone" class="form-control" value="<?php //echo (isset($site)?$site->concierge_phone:'');?>" required/>
				</div>
				<div class="form-group">
					<label>Days Per Week:</label>
					<input type="text" name="s_day_per_week" class="form-control" value="<?php //echo (isset($site)?$site->days_per_week:'');?>" required/>
				</div>
				<div class="form-group">
					<label>Category:</label>
					<select name="s_category"  class="form-control" value="<?php //echo (isset($site)?$site->category:'');?>" required>
						<option value="">Select</option>
						<option value="Shopping Centre">Shopping Centre</option>
						<option value="Transport Hub">Transport Hub</option>
						<option value="Convention Centre" >Exhibition and Convention Centre</option>
						<option value="University">Education</option>
						<option value="Warehouse">Warehouse</option>
						<option value="Sports & Entertainment">Sports & Entertainment</option>
					</select>
				</div>
				<input  type="hidden" name="s_status" value = "Active">
			</div>
			<div class="col-md-6">
			</div>
		</div>
	</form>
</div>