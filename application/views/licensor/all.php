




 <script type="text/javascript" charset="utf-8">
	$(document).ready(function() {
		$('#items').dataTable({ "lengthMenu": [ 25, 50, 100, 200 ], "pageLength": 200, fixedHeader: true});
	} );


	 var filter = function ()
	 {
		    var url = "<?php echo site_url('licensor/all'); ?>/" + document.getElementById('status').value;

		    document.location.href= url;
	 }


</script>
<div class="row">
<h3 class="page-header">Licensors <span class="pull-right"><small><label>Status:</label>&nbsp;&nbsp;&nbsp;<select name="status" id="status" onchange="filter()" >
					<option value="">Select</option>
					<option value="All" <?php echo $status == 'All'? 'selected="selected"':''; ?> >All</option>
					<option value="Active" <?php echo $status == 'Active'? 'selected="selected"':''; ?> >Active</option>
					<option value="Inactive" <?php echo $status == 'Inactive'? 'selected="selected"':''; ?>>Inactive</option>
				</select></small>&nbsp;&nbsp;<a href="<?php echo site_url('licensor/manage'); ?>" class="btn btn-primary">Add Licensor</a>&nbsp;</span></h3>

							<?php
	if ($this->session->flashdata('msg') != "") {
		echo '<div class="alert alert-warning">'. $this->session->flashdata('msg') .'</div>';
	}
?>
<?php if($licensors->num_rows() > 0 ) { ?>

 <table class="table table-striped" id="items">
        <thead>
            <tr>                 
                <th>Licensor Name</th>                
                <th>Edit</th>
            </tr>
        </thead>
        <tbody>
        <?php

        	$results = $licensors->result_object();
        	foreach($results as $licensor) {

			?>
            <tr>
                <td><?php echo $licensor->licensor_name; ?></td>
                <td><a href="<?php echo site_url('licensor/manage/'. $licensor->licensor_id); ?>" title="Edit Licensor"><i class="fa fa-edit"></i></a>
                </td>
            </tr>
           <?php } ?>
	 </tbody>
 </table>
 <?php // echo $pages; ?>
 <?php } else  { ?>
 <div class="alert alert-danger">No licensors found currently</div>
  <?php } ?>
 </div>
