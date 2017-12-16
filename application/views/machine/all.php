
 <script type="text/javascript" charset="utf-8">
	$(document).ready(function() {
		$('#items').dataTable({ "lengthMenu": [ 25, 50, 100, 200 ], "pageLength": 200});
	} );


	
	 

</script>
 
<div class="row">
<h3 class="page-header">Machines <span class="pull-right"><a href="<?php echo site_url('machine/manage'); ?>" class="btn btn-primary">Add New Kiosk</a>&nbsp;</span></h3>
			<?php
	if ($this->session->flashdata('msg') != "") {
		echo '<div class="alert alert-warning">'. $this->session->flashdata('msg') .'</div>';
	}
?>

<?php if($machines->num_rows() > 0 ) { ?>

 <table class="table table-striped" id="items">
        <thead>
            <tr>
                <th>Kiosk ID</th>
                <th>Model</th>
                <th>Distributor</th>
                <th>Labour Warranty</th>
                <th>Parts Warranty</th>
                <th>Edit</th>
                
            </tr>
        </thead>
        <tbody>
        <?php 
        	
        	$results = $machines->result_object();
        	foreach($results as $machine) { 
				
			?>
            <tr>
                <td><?php echo $machine->number; ?></td>
                <td><?php echo $machine->model; ?></td>
                <td><?php echo $machine->distributor; ?></td>
                <td><?php echo $machine->labour_warranty; ?></td>
                <td><?php echo $machine->part_warranty; ?></td>
                
                <td><a href="<?php echo site_url('machine/manage/'. $machine->id); ?>" title="Edit Kiosk"><i class="fa fa-edit"></i></a>&nbsp;&nbsp;&nbsp;&nbsp;
                </a></td>
                
                
            </tr>
           <?php } ?>
	 </tbody>
 </table>
 <?php // echo $pages; ?>
 <?php } else  { ?>
 <div class="alert alert-danger">No machine found currently</div>
  <?php } ?>
 </div>