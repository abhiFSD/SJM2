<?php $this->load->view('common/licensor_form') ;?>
<div class="row">
	<?php if(isset($site)) {
			$action = "Edit";
		} else {
			$action = "Add";
		}
	?>
	<h3 class="page-header"><?php echo $action; ?> Site <span class="pull-right"><a href="<?php echo site_url('site/all'); ?>" class="btn btn-primary">Back</a>&nbsp;</span></h3>
	<?php
		if (isset($msg) && $msg != "") {
			echo '<div class="alert alert-warning">'. $msg .'</div>';
		}
	?>
	<form method="post" class="form" id="siteform">
		<div class="col-md-12">
			<div class="col-md-6">
			</div>
		</div>
		<div class="col-xs-12 col-md-12">
			<div class="col-md-6">
				<div class="form-group">
					<label>Licensor:</label>
					<select name="licensor_id" class="form-control" value="<?php echo (isset($site)?$site->licensor_id:'');?>" required>
						<option value="">Select</option>
						<?php foreach ($licensors as $licensor) {
							$selected = isset($site)? ($site->licensor_id==$licensor->id? 'selected="selected"':''):'';
						?>
						<option value="<?php echo $licensor->id; ?>" data-name="<?php echo $licensor->display_name; ?>" <?php echo $selected; ?>><?php echo $licensor->display_name; ?></option>
						<?php } ?>
					</select>
				</div>
				<div class="form-group">
					<label>Site Name:</label>
					<input type="text" name="name" class="form-control" value="<?php echo (isset($site)?$site->name:'');?>" required/>
				</div>
				<div class="form-group">
					<label>Address:</label>
					<input type="text" name="address" class="form-control" value="<?php echo (isset($site)?$site->address:'');?>" required/>
				</div>
				<div class="form-group">
					<label>City:</label>
					<input type="text" name="city" class="form-control" value="<?php echo (isset($site)?$site->city:'');?>" required/>
				</div>
				<div class="form-group">
					<label>State:</label>
					<input type="text" name="state" class="form-control" value="<?php echo (isset($site)?$site->state:'');?>" required/>
				</div>
				<div class="form-group">
					<label>Postcode:</label>
					<input type="text" name="postcode" class="form-control" value="<?php echo (isset($site)?$site->postcode:'');?>" required/>
				</div>
				<div class="form-group">
					<label>Security Phone Number:</label>
					<input type="text" name="security_phone_number" class="form-control" value="<?php echo (isset($site)?$site->security_phone_number:'');?>" />
				</div>
				<div class="form-group">
					<label>Concierge Phone Number:</label>
					<input type="text" name="concierge_phone" class="form-control" value="<?php echo (isset($site)?$site->concierge_phone:'');?>" />
				</div>
				<div class="form-group">
					<label>Days Per Week:</label>
					<input type="text" name="day_per_week" class="form-control" value="<?php echo (isset($site)?$site->days_per_week:'');?>" required/>
				</div>
				<div class="form-group">
					<label>Category:</label>
					<select name="category"  class="form-control" value="<?php echo (isset($site)?$site->category:'');?>" required>
						<option value="">Select</option>
						<option value="Shopping Centre" <?php echo isset($site)? ($site->category=='Shopping Centre'? 'selected="selected"':''):''; ?> >Shopping Centre</option>
						<option value="Transport Hub" <?php echo isset($site)? ($site->category=='Transport Hub'? 'selected="selected"':''):''; ?>>Transport Hub</option>
						<option value="Convention Centre" <?php echo isset($site)? ($site->category=='Exhibition and Convention Centre'? 'selected="selected"':''):''; ?>>Exhibition and Convention Centre</option>
						<option value="Education" <?php echo isset($site)? ($site->category=='Education'? 'selected="selected"':''):''; ?>>Education</option>
						<option value="Warehouse" <?php echo isset($site)? ($site->category=='Warehouse'? 'selected="selected"':''):''; ?>>Warehouse</option>
						<option value="Sports & Entertainment" <?php echo isset($site)? ($site->category=='Sports & Entertainment'? 'selected="selected"':''):''; ?>>Sports & Entertainment</option>
					</select>
				</div>
				<div class="form-group">
					<label>Status:</label>
					<select name="status"  class="form-control" required>
						<option value="">Select</option>
						<option value="Active" <?php echo isset($site)? ($site->status=='Active'? 'selected="selected"':''):''; ?>>Active</option>
						<option value="Inactive" <?php echo isset($site)? ($site->status=='Inactive'? 'selected="selected"':''):''; ?>>Inactive</option>
					</select>
				</div>
				<div class="form-group">
					<input type="submit" class="btn btn-primary" value="Submit" name="save" />&nbsp;<a href="<?php echo site_url('site/all'); ?>" class="btn ">Cancel</a>
				</div>
			</div>
		</div>
		<?php if (isset($site)) { ?>
		<input type="hidden" name="site_id" value="<?php echo $site->id; ?>" />
		<?php } ?>
	</form>
</div>

<script>
	$("#form").validate();
</script>