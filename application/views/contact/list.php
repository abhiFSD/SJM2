
 <script type="text/javascript" charset="utf-8">
	$(document).ready(function() {
		$('#items').dataTable({ "lengthMenu": [ 25, 50, 100, 200 ], "pageLength": 200});
	} );


	 var filter = function ()
	 {
		    var url = "<?php echo site_url('licensor/all'); ?>/" + document.getElementById('status').value;

		    document.location.href= url;
	 }


</script>
<div class="row">
<h3 class="page-header">Contacts <span class="pull-right">&nbsp;&nbsp;<a href="<?php echo site_url('contact/manage'); ?>" class="btn btn-primary">Add Contact</a>&nbsp;</span></h3>

							<?php
	if ($this->session->flashdata('msg') != "") {
		echo '<div class="alert alert-warning">'. $this->session->flashdata('msg') .'</div>';
	}
?>
<?php

if($contacts->num_rows() > 0 ) { ?>

 <table class="table table-striped" id="items">
        <thead>
            <tr>
                <!-- <th>ID</th> -->
                <th>First Name</th>
                <th>Last Name</th>
              <!--  <th>Organisation</th> -->
                <th>Licensor ID</th>
              <!--   <th>Licensor</th> -->
                <th>Edit</th>

            </tr>
        </thead>
        <tbody>
        <?php

        	$results = $contacts->result_object();
        	foreach($results as $contact) {

			?>
            <tr>
                 <!-- <td><?php echo $contact->contact_id; ?></td> -->
                <td><?php echo $contact->first_name; ?></td>
                <td><?php echo $contact->last_name; ?></td>
           <!--     <td><?php // echo $contact->organisation; ?></td>   -->
                <td><?php echo $contact->licensor_id; ?></td>
                <td><a href="<?php echo site_url('contact/manage/'. $contact->id); ?>" title="Edit Contact"><i class="fa fa-edit"></i></a>&nbsp;&nbsp;&nbsp;&nbsp;
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
