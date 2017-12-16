
 <script type="text/javascript" charset="utf-8">
			$(document).ready(function() {
				$('#items').dataTable({ "lengthMenu": [ 25, 50, 100, 200 ], "pageLength": 200});
			} );
			 var filter = function ()
			 {
				    var url = "<?php echo site_url('product/all'); ?>/" + document.getElementById('status').value;
				    
				    document.location.href= url;
			 }
			 
		</script>
<div class="row">
<h3 class="page-header">Item Attribute Fields<span class="pull-right"><a href="<?php echo site_url('productattribute/manage'); ?>" class="btn btn-primary">Add New Attribute Field</a>&nbsp;</span></h3>
<?php if(count($attributes) > 0 ) { ?>
<?php
	if ($this->session->flashdata('msg') != "") {
		echo '<div class="alert alert-warning">'. $this->session->flashdata('msg') .'</div>';
	}
?>
 <table class="table table-striped" id="items">
        <thead>
            <tr>
            	<th>Attribute Name</th>
                <th>Unit Of Measure</th>
                <th>Options</th>
                
                <th>Edit</th>
                
            </tr>
        </thead>
        <tbody>
        <?php 
        	
        	//$results = $products->result_object();
        	foreach($attributes as $attribute) {
			?>
            <tr>
                
                <td><?php echo $attribute['name']; ?></td>
                <td><?php echo $attribute['unit_of_measure_id']; ?></td>
                 <td><?php echo $attribute['options']; ?></td>
                <td><a href="<?php echo site_url('productattribute/manage/'. $attribute['id']); ?>" title="Edit Attribute"><i class="fa fa-edit"></i></a>&nbsp;&nbsp;&nbsp;&nbsp;
               </td>
                
                
            </tr>
           <?php } ?>
	 </tbody>
 </table>
 <?php // echo $pages; ?>
 <?php } else  { ?>
 <div class="alert alert-danger">No Attributes found currently</div>
  <?php } ?>
 </div>
 