<div class="row">
<?php if(isset($licensor)) {
		$action = "Edit";
	} else {
		$action = "Add";
	}
?>
<h3 class="page-header"><?php echo $action; ?> Licensor <span class="pull-right"><a href="<?php echo site_url('licensor/all'); ?>" class="btn btn-primary">Back</a>&nbsp;</span></h3>
<div id="licensorAdd">
<?php
	if (isset($msg) && $msg != "") {
		echo '<div class="alert alert-warning">'. $msg .'</div>';
	}
?>
<form method="post" id="form">
	<div class="col-md-12">
		<div class="col-md-6">

			<div class="form-group">
				<label>Name:</label>
				<input type="text" name="name" class="form-control" value="<?php echo (isset($licensor)?$licensor->licensor_name:'');?>"  required/>
			</div>

 

			<div class="form-group">
				<label>Status:</label>
				<select name="status"  class="form-control"  value="<?php echo (isset($licensor)?$licensor->status:'');?>" required>
					<option value="">Select</option>
					<option value="Active" <?php echo isset($licensor)? ($licensor->status=='Active'? 'selected="selected"':''):''; ?>>Active</option>
					<option value="Inactive" <?php echo isset($licensor)? ($licensor->status=='Inactive'? 'selected="selected"':''):''; ?>>Inactive</option>
				</select>
			</div>



			<div class="form-group">
            <input type="submit" class="btn btn-primary" value="Submit" name="saveLicensor" />&nbsp;<a href="<?php echo site_url('licensor/all'); ?>" class="btn ">Cancel</a>
         </div>
		</div>
		<div class="col-md-6">

		</div>
	</div>	

	<?php 

		if(isset($licensor->party_id))
		{
		?>
		<input type="hidden" name="licensor_id" value="<?php echo $licensor->party_id; ?>" />
	<?php	}

	?>
 
 	
			
	 

</form>
</div>
</div>

<script>
	$("#form").validate();
</script>
