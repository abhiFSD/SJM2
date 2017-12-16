<div class="row">
<?php if(isset($attribute)) { 
		$action = "Edit"; 
	} else {
		$action = "Add";
	}
?>
<h3 class="page-header"><?php echo $action; ?> Item Attribute Option</h3>
<?php
	if (isset($msg) && $msg != "") {
		echo '<div class="alert alert-warning">'. $msg .'</div>';
	}
?>
<form method="post" id="form">
	<div class="col-md-12">
		<div class="col-md-6">

			<div class="form-group">
				<label>Attribute:</label>
				<?php echo isset( $attribute->name )?$attribute->name:"" ?>
			</div>

			<div class="form-group">
				<label>Name:</label>
				<input type="text" name="name" class="form-control" value="" required />
			</div>
			<div class="form-group">
				<label>SKU Suffix (if Required):</label>
				<input type="text" name="sku_suffix" class="form-control" value=""  />
			</div>

				<div class="form-group">
            <input type="submit" class="btn btn-primary" value="Submit" name="save" />&nbsp;<a href="<?php echo site_url('stocktakenew/listall'); ?>" class="btn ">Cancel</a>
         </div>
		</div>

	</div>
	<?php 
		if (isset($attribute)) { ?>
			<input type="hidden" name="attribute_id" value="<?php echo $attribute->id; ?>" />
	<?php } ?>
	
</form>
</div>

<script>    
	$("#form").validate();
</script>