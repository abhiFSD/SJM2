<?php //print_r($agreement);exit;
$this->load->view('common/licensor_form',$license_id) ;?>
<div id="dialog-help1" class="dialogdiv" title="Hint">
	<p>Please use following naming standard:<br/>
 1. insert licensor name if agreement at licensor level or <br/>2. insert site name if license at site or location level<br/>3. insert location name if license at location level<br/>4. insert YYYY-MM being the month the license commenced
</p>
</div>
<div id="dialog-help2" title="Hint">
	<p>Commission rate paid on revenue earned above this amount (will often be $0)
</p>
</div>
<div class="row">
<?php if(isset($agreement)) {
		$action = "Edit";
	} else {
		$action = "Add";
	}
?>

<h3 class="page-header"><?php echo $action; ?> Licensor Agreement <span class="pull-right"><a href="<?php echo site_url('agreement/all'); ?>" class="btn btn-primary">Back</a>&nbsp;</span></h3>
<?php if (!empty($msg)): ?>
    <div class="alert alert-warning "><?php print $msg; ?></div>
<?php endif; ?>
<form method="post" id="form_agreement" class="form" onsubmit="return checkRules();return false;">
 
	<div class="col-xs-12 col-md-12">


			<div class="form-group">
			  <div class="col-sm-8 col-md-6">
				<label>Licensor:</label>

				<select name="licensor_id" class="form-control" value="<?php echo (isset($agreement)?$agreement->licensor_id:'');?>" required>
				<option value="">Select</option>
				<?php foreach ($licensors as $licensor) {
						$selected = isset($agreement)? ($agreement->licensor_id==$licensor->id? 'selected="selected"':''):'';
					?>
					<option value="<?php echo $licensor->id; ?>" data-name="<?php echo $licensor->display_name; ?>" <?php echo $selected; ?>> <?php echo $licensor->display_name; ?></option>
	<?php } ?>
				</select>
			</div>
			<div class="col-sm-4 col-md-6">
					<a class="btn btn-primary btnright licenseAdd" id="agreement_add">Add New Licensor</a>
					<!--href="<?php echo site_url('licensor/manage'); ?>"-->
			</div>
		</div>

		</div>
<!-- </div> -->
	<!-- </div> -->
	<div class="col-xs-12 col-md-12">

			<div class="form-group">
				<div class="col-xs-10 col-sm-8 col-md-6">
					<label>Agreement Name:</label>
					<input type="text" name="name" class="form-control" value="<?php echo (isset($agreement)?$agreement->name:'');?>" required/>
        </div>
				<div class="col-xs-2 col-sm-4 col-md-6"> <!--Help section-->
					<a class="help-icon help_id" data-html="true"  data-toggle="popover" title="Hint" data-content="
<p>Please use following naming standard:<br/> 
 1. insert licensor name if agreement at licensor level or <br/>2. insert site name if license at site or location level<br/>3. insert location name if license at location level<br/>4. insert YYYY-MM being the month the license commenced
</p>
				"  ><i class="fa fa-question-circle" aria-hidden="true"></i></a>
				</div>
			</div>


		</div>
	<div class="col-xs-12 col-md-12">
		<div class="col-md-6">
			<div class="form-group">
				<label>Fixed Component:</label>
				<div class="input-group">
					<span class="input-group-addon">$</span>
					<input type="text" name="fixed" class="form-control" id="fixed" value="<?php echo (isset($agreement)?$agreement->fixed_fee_exGST:'');?>" haveFixedOrCommission="true"/>
					<span class="input-group-addon">per month</span>
			</div>

			</div>

			<div class="form-group">
				<label>Commission 1 Rate:</label>
				<div class="input-group">
					<input type="text" name="commission1" id="commission1" class="form-control" value="<?php echo (isset($agreement)?($agreement->commission_1_rate * 100):'');?>" />
					<span class="input-group-addon">%</span>
				</div>
			</div>
		</div>
	</div>
 <div class="col-xs-12 col-md-12">
	<div class="form-group">
		<div class="col-xs-10 col-sm-8 col-md-6">
			<div class="form-group">
				<label>Commission 1 Threshold:</label>
				<div class="input-group">
					<span class="input-group-addon">$</span>
					<input type="text" name="commission1_threshold" id="commission1_threshold" class="form-control" value="<?php echo (isset($agreement)?$agreement->commission_1_threshold:'');?>" />
					<span class="input-group-addon">per month</span>
				</div>
			</div>
		</div>
		<div class="col-xs-2 col-sm-4 col-md-6">
			<a class=" help-icon help-icon2" data-html="true"  data-toggle="popover" title="Hint" data-content="
<p>Commission rate paid on revenue earned above this amount (will often be $0)
</p>
				" 
			><i class="fa fa-question-circle" aria-hidden="true"></i></a>
		</div>
	</div>
</div>
<div class="col-xs-12 col-md-12">
		<div class="col-md-6">
			<div class="form-group">
				<label>Commission 2 Rate:</label>
				<div class="input-group">
					<input type="text" name="commission2" class="form-control" id="commission2" value="<?php echo (isset($agreement)?($agreement->commission_2_rate * 100):'');?>" />
					<span class="input-group-addon">%</span>
				</div>
			</div>
		</div>
	</div>
  <div class="col-xs-12 col-md-12">
			<div class="form-group">
				<div class="col-xs-10 col-sm-8 col-md-6">
					<label>Commission 2 Threshold:</label>
				  <div class="input-group">
					 <span class="input-group-addon">$</span>
					 <input type="text" name="commission2_threshold" class="form-control" id="commission2_threshold" value="<?php echo (isset($agreement)?$agreement->commission_2_threshold:'');?>" />
					 <span class="input-group-addon">per month</span>
				 </div>
			</div>
			<div class="col-xs-2 col-sm-4 col-md-6">
				<a class="help-icon help-icon2"   data-html="true"  data-toggle="popover" title="Hint" data-content="
<p>Commission rate paid on revenue earned above this amount (will often be $0)
</p>
				" 	><i class="fa fa-question-circle" aria-hidden="true"></i></a>
			</div>
		</div>
	</div>


			<?php
			$svalue = $evalue = "";
			if (isset($agreement->start_date) && $agreement->start_date != NULL) {
				 $svalue = date('d-m-Y',strtotime($agreement->start_date));
			}
			if (isset($agreement->end_date) && $agreement->end_date != NULL) {
        		 $evalue = date('d-m-Y',strtotime($agreement->end_date));
			}
			?>

	<div class="col-xs-12 col-md-12" style="position:relative">
		 <div class="col-md-6">
			 <div class="form-group">
				<label>Start Date:</label>
				<input type="text" name="start_date" class="form-control datepicker" value="<?php echo $svalue;?>" required/>
			</div>
			<div class="form-group">
				<label>End Date:</label>
				<input type="text" id="end-date" name="end_date" class="form-control datepicker" value="<?php echo $evalue?>" required/>
			</div>


			<div class="form-group">
				<label>Status:</label>
				<select name="status"  class="form-control" required>
					<option value="">Select</option>
					<option value="Active" <?php echo isset($agreement)? ($agreement->status=='Active'? 'selected="selected"':''):''; ?>>Active</option>
					<option value="Inactive" <?php echo isset($agreement)? ($agreement->status=='Inactive'? 'selected="selected"':''):''; ?>>Inactive</option>
				</select>
			</div>
			<div class="form-group">
        <input type="submit" class="btn btn-primary" value="Submit" name="save" />&nbsp;<a href="<?php echo site_url('agreement/all'); ?>" class="btn ">Cancel</a></div>
				</div>
				<div class="col-md-6">

				 </div>
			 </div>
		</div>


	</div>

	<?php
		if (isset($agreement)) { ?>
			<input type="hidden" name="agreement_id" value="<?php echo $agreement->id; ?>" />
	<?php } ?>

</form>

</div>

<script>
	$('input').removeAttr('required','');
	$('select').removeAttr('required','');
	function checkRules()
	{

		return ;
		var msg = "";
		console.log($('#fixed').val());
		if ((($('#fixed').val() == "") || ($('#fixed').val() == "0.00"))
			&& (($('#commission1').val() == "" )|| ($('#commission1').val() == "0.0000"))
			) {
			msg = "Fixed Rate or Commission should be provided.";
		}

		if ($('#commission1').val() != "" && $('#commission1').val() !='0.0000' ) {
			// threshold should be present.
			if ($('#commission1_threshold').val() == "" || $('#commission1_threshold').val() =='0.00' ) {
				msg = "Commission 1 Threshold should be provided.";
			}
			if ($('#commission2').val() != "" && $('#commission2').val() !='0.0000'  ) {
				// threshold should be present.
				if ($('#commission2_threshold').val() == "" || $('#commission2_threshold').val() == '0.00') {
					msg = "Commission 2 Threshold should be provided.";
				}
			}


		} else {
			// commission 2 and threshold should be blank.
			if (( $('#commission2').val() != "" &&  $('#commission2').val() !='0.0000' ) ||
				$('#commission2_threshold').val() != "" && $('#commission2_threshold').val() != '0.00') {
				msg = "Commission 2 or threshold should be provided after commission #1.";
			}
		}

		if (msg != "") {
			$('.alert').html(msg);
			$('.alert').show();
			return false;
		} else {
			$("#form_agreement").validate();
		}
	}

$('.datepicker').datepicker({
});

</script>
<script>
$("#licensor_form").validate();
 $('[data-toggle="popover"]').popover({ trigger: "hover" });	
</script>
