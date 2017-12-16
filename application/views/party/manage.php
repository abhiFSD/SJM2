<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<div class="row">

<h2 class="page-header">Add Organisation or Person</h2>

	<div class="row">
		<form method="post" id="form" enctype="multipart/form-data">
			<div class="col-md-12">
				<div class="col-md-6">

					<div class="form-group  ">
						<label>Form: *</label>&nbsp;&nbsp;&nbsp;
						<input type="radio" name="form" class="" value="Organisation" checked onchange="formSwitch('organisation');"/>&nbsp;Organization &nbsp;
						<input type="radio" name="form" class="" value="Person" onchange="formSwitch('person');"/>&nbsp;Person &nbsp;

					</div>
					<div class="form-group">
						<label>Name: *</label>
						<input type="text" name="name" class="form-control"  required />
					</div>

					<div class="form-group  ">
						<label>Type: *</label>&nbsp;&nbsp;&nbsp;
						<input type="checkbox" name="type[]" class="" value="2"/>&nbsp;&nbsp;Licensor &nbsp;
						<input type="checkbox" name="type[]" class="" value="3"/>&nbsp;&nbsp;Item Supplier &nbsp;
						<input type="checkbox" name="type[]" class="" value="4"/>&nbsp;&nbsp;PEMS Supplier &nbsp;
						<input type="checkbox" name="type[]" class="" value="5" />&nbsp;&nbsp;Org Contact &nbsp;
					</div>
					<div class="form-group  ">
						<label>ABN: *</label>
						<input type="text" name="abn" class="form-control"  required />
					</div>
					<div class="form-group  ">
						<label>Address Line 1: *</label>
						<input type="text" name="address1" class="form-control"  required />
					</div>
					<div class="form-group  ">
						<label>Address Line 2: *</label>
						<input type="text" name="address2" class="form-control"  required />
					</div>
					<div class="form-group">
						<label>Suburb: *</label>
						<input type="text" name="suburb" class="form-control" required />
					</div>
					<div class="form-group">
						<label>State: *</label>
						<select name="state" class="form-control" onchange="changeState(this)" required>
							<option value="">Select State</option>

							<option value="ACT">Australian Capital Territory</option>
							<option value="NSW">New South Wales</option>
							<option value="NT">Northern Territory</option>
							<option value="QLD">Queensland</option>
							<option value="SA">South Australia</option>
							<option value="TAS">Tasmania</option>
							<option value="VIC">Victoria</option>
							<option value="WA">Western Australia</option>
						</select>
					</div>

					<div class="form-group">
						<label>Postcode: *</label>
						<input type="text" name="postcode" class="form-control"  required/>
					</div>
					<div class="form-group  ">
						<label>Country:</label>
						<input type="text" name="country" class="form-control" value="AU" />
					</div>
					<div class="form-group">
						<label>Email Address: *</label>
						<input type="email" name="email_address" id="email" class="form-control"  required  onblur="changeEmail()" />
						<span id="message"></span>
					</div>
					<div class="form-group person-hide">
						<label>Landline:</label>
						<input type="text" name="landline" class="form-control"  />
					</div>
					<div class="form-group person-hide">
						<label>Mobile Phone:</label>
						<input type="text" name="mobile" class="form-control"  />
					</div>

					<div class="form-group person-hide">
						<label>Date of Birth:</label>
						<input type="date" name="dob" class="form-control"  />
					</div>
					<div class="form-group person-hide">
						<label>Gender:</label>
						<?php
						$class = 'class="form-control"';
						$value = "";
						$gender =  array (
							""			=> "Select Gender",
							"Male"		=> "Male",
							"Female"	=> "Female"

						);
						echo form_dropdown('active', $gender, $value , $class);
						?>
					</div>

					<div class="form-group">
						<input type="submit" class="btn btn-primary" value="Save" name="save" />&nbsp;
						<a href="">Cancel</a>
					</div>

				</div>

			</div>


		</form>
	</div>
</div>

<script src="https://code.jquery.com/ui/1.12.0/jquery-ui.js"></script>

<script>
	$("#form").validate();
	$(".person-hide").hide();

	var formSwitch = function (type)
	{
		if (type == 'organisation') {

			$('.person-hide').hide();

		} else {
			$('.person-hide').show();
		}
	}

</script>

