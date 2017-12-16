<div  class="row">
<?php if(isset($machine)) {
		$action = "Edit";
	} else {
		$action = "Add";
	}
?>
<h3 class="page-header"><?php echo $action; ?> Kiosk <span class="pull-right"><a href="<?php echo site_url('machine/all'); ?>" class="btn btn-primary">View All Machines</a>&nbsp;</span></h3>
<?php
	if (isset($msg) && $msg != "") {
		echo '<div class="alert alert-warning">'. $msg .'</div>';
	}
?>
<form method="post" id="form">
	<div class="col-md-12">
		<div class="col-md-6">
			<div class="form-group">
				<label>Kiosk ID:</label>
			<?php

		        if (isset($machine_id)) {
                    $machineId = $machine_id;
                } else {
                    $machineId = $machine->number;
                }
            ?>
        			<input type="text" name="id" value="<?php echo $machineId; ?>"  class="form-control" onblur="checkMachineNumber(this)" required/><br />
					<span id="suggestion"></span>

			</div>

			<div class="form-group">
				<label>Model:</label>
				<select name="model_id" class="form-control" required>
				    <option value="">Select</option>
				    <?php
				        foreach ($models->result() as $model) {
                            $selected = "";
                            if (isset($machine)) {
                                $selected  = $machine->model_id == $model->model_id? 'selected="selected"':'';
                            }

				    ?>
				        <option value="<?php echo $model->model_id; ?>" <?php echo $selected; ?> ><?php echo $model->name; ?></option>
				    <?php } ?>
				</select>

			</div>
			<div class="form-group">
				<label>Phone number:</label>
				<input type="text" name="phone_number" class="form-control" value="<?php echo (isset($machine)?$machine->phone_number:'');?>" required/>
			</div>
			<div class="form-group">
				<label>Distributor:</label>
				<input type="text" name="distributor" class="form-control" value="<?php echo (isset($machine)?$machine->distributor:'');?>" required/>
			</div>
			<div class="form-group">
				<label>Warranty - Labour:</label>
				<input type="text" name="labour_warranty" class="form-control" value="<?php echo (isset($machine)?$machine->labour_warranty:'');?>" required/>
			</div>
			<div class="form-group">
				<label>Warranty - Parts:</label>
				<input type="text" name="part_warranty" class="form-control" value="<?php echo (isset($machine)?$machine->part_warranty:'');?>" required/>
			</div>
			<div class="form-group">
            <input type="submit" class="btn btn-primary" value="Submit" name="save" />&nbsp;<a href="<?php echo site_url('licensor/all'); ?>" class="btn ">Cancel</a>
         </div>
		</div>
		<div class="col-md-6">

		</div>
	</div>
	<?php
		if (isset($machine_id)) { ?>
			<input type="hidden" name="id" id="new_machine_id" value="<?php echo $machine_id; ?>" />
	<?php } ?>
	<?php
		if (isset($machine)) { ?>
			<input type="hidden" name="machine_id" value="<?php echo $machine->id; ?>" />
	<?php } ?>

</form>
</div>

<script>
	$("#form").validate();

	var checkMachineNumber = function (el)
	{
		var regex = /^MPP\d{3}$/;

		// \d{3}

		if (el.value != "") {

			if (regex.test((el.value).trim())) {
				$('#suggestion').html('');
				checkForUnique(el);

			} else {
				$('#suggestion').html("<small class='error'> Invalid Kiosk Number. Kiosk number should be starting with 'MPP' followed by 3 digit number.<br/>ex: MPP001</small>");

				el.value = '';
				el.focus();
				return false;
			}
		}
	};

	var checkForUnique = function (el)
	{
		$.get(appPath + '/machine/isunique/'+ el.value, function (data) {
			if (data != 1) {
				$('#suggestion').html("<small class='error'>"+ el.value+ ' is already used. Suggested value is: <strong>'+ data +'</strong></small>');

				el.value = '';
				el.focus();
			} else {
				$('#new_machine_id').val(el.value);
			}
		});
	}
</script>
