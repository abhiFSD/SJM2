<div class="row">
<?php if(isset($contact)) {
		$action = "Edit";
	} else {
		$action = "Add";
	}
?>
<h3 class="page-header"><?php echo $action; ?> Contact <span class="pull-right"><a href="<?php echo site_url('contact'); ?>" class="btn btn-primary">Back</a>&nbsp;</span></h3>
<?php
	if (isset($msg) && $msg != "") {
		echo '<div class="alert alert-warning">'. $msg .'</div>';
	}
?>
<form method="post" id="form">
	<div class="col-md-12">
		<div class="col-md-6">
 
			<div class="form-group">
				<label>First Name:</label>
				<input type="text" name="first_name" class="form-control" value="<?php echo (isset($contact)?$contact->first_name:'');?>"  required/>
			</div>
			<div class="form-group">
				<label>Last Name:</label>
				<input type="text" name="last_name" class="form-control" value="<?php echo (isset($contact)?$contact->last_name:'');?>"  required/>
			</div>

                        <div class="form-group">
				<label>Licensor:</label>
				<select name="licensor_id" class="form-control" value="<?php echo (isset($contact)?$contact->licensor_id:'');?>" required>
				<option value="">Select</option>
				<?php foreach ($licensors as $licensor) {
						$selected = isset($contact)? ($contact->licensor_id==$licensor->id? 'selected="selected"':''):'';
					?>
					<option value="<?php echo $licensor->id; ?>" <?php echo $selected; ?>><?php echo $licensor->display_name; ?></option>
				<?php } ?>
				</select>

			</div>

			<div class="form-group">
            <input type="submit" class="btn btn-primary" value="Submit" name="save" />&nbsp;<a href="<?php echo site_url('contact'); ?>" class="btn ">Cancel</a>
         </div>
		</div>
		<div class="col-md-6">

		</div>
	</div>
	<?php
                if (isset($contact_id)) { ?>
				<input type="hidden" name="contact_id" value="<?php echo $contact_id; ?>" />
		<?php }

		if (isset($contact)) { ?>
			<input type="hidden" name="id" value="<?php echo $contact->id; ?>" />
	<?php } ?>

</form>
</div>

<script>
	$("#form").validate();
</script>
