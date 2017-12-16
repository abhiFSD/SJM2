
<script type="text/javascript" charset="utf-8">

  var itemTable = '';
			$(document).ready(function() {
			  itemTable =	$('#items').DataTable(

          { 

            "lengthMenu": [ 25, 50, 100, 200 ],
             "pageLength": 200,
                fixedHeader: true,

           }


          );


			} );
			 var filter = function ()
			 {
				    var url = "<?php echo site_url('product/all'); ?>/" + document.getElementById('status').value;
				    
				    document.location.href= url;
			 }
			 
		</script>



 
<div class="row">
<h3 class="page-header">Items <span class="pull-right">

    <label><h5>Item Type</h5> </label>

    <select name="type[]" id="pdttype" onchange="changeType()" multiple >

        <option value="Product" >Product</option>
        <option value="Equipment">Equipment</option>
        <option value="Part">Part</option>
        <option value="Material">Material</option>
    </select>



        <a href="<?php echo site_url('products/manage'); ?>" class="btn btn-primary add-url">Add New Item</a>&nbsp;</span></h3>
<?php if(count($products) > 0 ) { ?>
<?php
	if ($this->session->flashdata('msg') != "") {
		echo '<div class="alert alert-warning">'. $this->session->flashdata('msg') .'</div>';
	}
?>
 <table class="table table-striped" id="items">
        <thead>
            <tr>
            	<th>Name</th>
                <th>SKU</th>
                <th>Type</th>
                <th>Category</th>
                
                <th class="desktop-only">Barcode</th>
                <th>Status</th>
                
                <th>Edit</th>
                
            </tr>
        </thead>
        <tbody>
        <?php 
        	
        	//$results = $products->result_object();
 

        	foreach($products as $product) {
				
			?>
            <tr>
                
                <td  ><?php echo $product->product_name; ?></td>
                
                <td><?php echo $product->sku_value; ?></td>
                <td><?php echo ucfirst($product->product_type); ?></td>
                <td><?php  echo $product->category_name; ?></td>
                <td class="desktop-only"><?php echo $product->barcode; ?></td>
                <td ><?php echo $product->status; ?></td>
                <td><a href="<?php echo site_url('products/manage/'. $product->id); ?>" title="Edit Item"><i class="fa fa-edit"></i></a>&nbsp;&nbsp;&nbsp;&nbsp;
               </td>
                
                
            </tr>
           <?php } ?>
	 </tbody>
 </table>
 <?php // echo $pages; ?>
 <?php } else  { ?>
 <div class="alert alert-danger">No items found currently</div>
  <?php } ?>
 </div>
 <script>
/*

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
}*/
function getPathFromUrl(url) {
  return url.split("?")[0];
}

 $(document.body).on('click', '.item_type', function () {
           
          var type = $(this).data('type');

          $('.filter-text').html($(this).html())

          console.log(type);

          var url = $('.add-url').attr('href')
          $('.add-url').attr('href',getPathFromUrl(url)+'?type='+type)

         // alert(type)

           itemTable.columns(2).search(type); 

          // PickTable.columns(2).search($("#state_filter").val());

          // PickTable.columns(3).search($("#site_filter").val());


          itemTable.draw();
            
  });

function changeType()
{

    var filterValue = "";
    var pdtType = $('#pdttype').val();
    if (pdtType) {
        filterValue = pdtType.join("|");
        console.log(filterValue)

        var url = $('.add-url').attr('href')
        $('.add-url').attr('href',getPathFromUrl(url)+'?type='+filterValue)

        // alert(type)

        itemTable.columns(2).search(filterValue, true);

        // PickTable.columns(2).search($("#state_filter").val());

        // PickTable.columns(3).search($("#site_filter").val());


        itemTable.draw();
    }
}

$('#pdttype').multiselect();
</script>