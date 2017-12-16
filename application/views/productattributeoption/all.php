
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
<h3 class="page-header">Item Attributes <span class="pull-right"><a href="<?php echo site_url('products/manage'); ?>" class="btn btn-primary">Add New Attribute</a>&nbsp;</span></h3>
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

                
                <th>Edit</th>
                
            </tr>
        </thead>
        <tbody>
        <?php 
        	
        	//$results = $products->result_object();
        	foreach($attributes as $attribute) {
				
			?>
            <tr>
                
                <td contenteditable="true"><?php echo $attribute->name; ?></td>
                <td><?php echo $attribute->unit; ?></td>

                <td><a href="<?php echo site_url('productsattribute/manage/'. $attribute->id); ?>" title="Edit Attribute"><i class="fa fa-edit"></i></a>&nbsp;&nbsp;&nbsp;&nbsp;
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
 <script>
var editable = document.getElementByClass('editable');

addEvent(editable, 'blur', function () {
  // lame that we're hooking the blur event
  localStorage.setItem('contenteditable', this.innerHTML);
  document.designMode = 'off';
});

addEvent(editable, 'focus', function () {
  document.designMode = 'on';
});

addEvent(document.getElementById('clear'), 'click', function () {
  localStorage.clear();
  window.location = window.location; // refresh
});

if (localStorage.getItem('contenteditable')) {
  editable.innerHTML = localStorage.getItem('contenteditable');
}

</script>