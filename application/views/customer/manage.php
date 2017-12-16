<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<div class="row">

<h2 class="page-header">Register Your Item</h2>
<?php
	if (isset($msg) && $msg != "") {
		echo '<div class="alert alert-danger">'. $msg .'</div>';
	}
?>
	<div class="row alert alert-warning">
		<form method="post" id="form" enctype="multipart/form-data">
			<h4 class="page-header">1. Personal Details</h4>
			<div class="col-md-12">
				<div class="col-md-6">
					<div class="form-group">
						<label>Email Address: *</label>
						<input type="email" name="email_address" id="email" class="form-control"  required  onblur="changeEmail()" />
						<span id="message"></span>
					</div>
					<div class="form-group  exclude">
						<label>First Name: *</label>
						<input type="text" name="first_name" class="form-control"  required />


					</div>
					<div class="form-group  exclude">
						<label>Last Name: *</label>
						<input type="text" name="last_name" class="form-control"  required />
					</div>

					<div class="form-group  exclude">
						<label>Date of Birth:</label>
						<input type="date" name="dob" class="form-control"  />
					</div>
					<div class="form-group exclude">
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
					<div class="form-group  exclude">
						<label>Country of Residence:</label>
						<input type="text" name="country" class="form-control"  />
					</div>
					<div class="form-group  exclude">
						<label>Postcode:</label>
						<input type="text" name="postcode" class="form-control"  />
					</div>

				</div>
				<div class="col-md-6">




				</div>
			</div>

			<h4 class="page-header">2. Item Details</h4>
			<div class="col-md-12">
				<div class="col-md-6">
					<div class="form-group">
						<label>Purchase Date:</label>
						<input type="date" name="purchase_date"  id="purchase_date" class="form-control" required />
					</div>
					<div class="form-group">
						<label>Item Name: *</label>
						<input type="text" id="product" name="product_name" class="form-control"  required />


					</div>
					<div class="form-group">
						<label>State of Purchase *</label>
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
						<label>Site of Purchase: *</label>
						<select name="site" id="site" class="form-control" onchange="changeSite(this)" required></select>
					</div>
					<div class="form-group">
						<label>Location of Purchase: *</label>
						<select name="location" id="location" class="form-control" required></select>
					</div>
					<div class="form-group">
						<label>Item Serial Number:</label>
						<input type="text" name="serial" class="form-control"  />
					</div>
					<div class="form-group">
						<label>Photo of the item:</label>
						<input type="file" name="photo" class="form-control"  />
					</div>

				</div>
				<div class="col-md-6">




				</div>
			</div>
			<div class="col-md-12">
				<div class="form-group exclude">
					<input type="checkbox"  value="1" name="promotions" checked />&nbsp;Would you like us to send you Specials and Promotions?
				</div>
				<div class="form-group">
					<input type="submit" class="btn btn-primary" value="Register Item" name="save" />&nbsp;
					<br />
					<small>By clicking the button you are agreeing to the <a href="http://www.powerpod.net/pages/powerpod-privacy-policy" target="_blank">Privacy Policy</a>
				</div>
			</div>
			<input type="hidden" name="customer" id="customer" />

		</form>
	</div>
</div>

<script src="https://code.jquery.com/ui/1.12.0/jquery-ui.js"></script>
<script>

	

	$("#form").validate();
</script>
<script>
	$( function() {
		var products = [
			<?php echo $products; ?>
		];

		$( "#product" ).autocomplete({
			source: products
		});

	} );

	var changeState = function(el) {
		$.get(appPath + '/customer/getallsites/'+ el.value, function(data) {

			$('#site').empty();
			str = '<option value="">Select a Site</option>';
			$('#site').append(str);

			var jsonObj = $.parseJSON(data);
			for (i in jsonObj) {
				str = '<option value="'+ jsonObj[i].id +'">'+ jsonObj[i].name +'</option>';


				$('#site').append(str);
			}


		});
	};

	var changeSite = function(el) {

		if ($('#purchase_date').val() != "") {

			$.get(appPath + '/customer/getalllocations/' + el.value + '/' + $('#purchase_date').val(), function (data) {

				$('#location').empty();

				str = '<option value="">Select a Location</option>';
				$('#location').append(str);
				var jsonObj = $.parseJSON(data);
				for (i in jsonObj) {
					str = '<option value="' + jsonObj[i].id + '">' + jsonObj[i].name + '</option>';


					$('#location').append(str);
				}
			});

		} else if( (el.value).trim() != "") {
			alert('You must enter a valid purchase date');
			el.value = '';
			return false;
		}
	};

	var changeEmail= function () {
		$.post(appPath + '/customer/checkcustomer', {'email': $('#email').val()}, function (data) {
			
			var params = $.parseJSON(data);

			if (params.data == "Success") {
				$("#customer").val(params.id);
				$('.exclude').hide();
				$("#message").html("<small>You're already registered with PowerPod. You may proceed with item details.</small>");
			} else {
				$("#customer").val(0);
				$('.exclude').show();
				$("#message").html("");

			}

		});
	}



</script>