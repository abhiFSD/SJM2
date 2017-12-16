

<div class="row">
<div class="col-md-12">
<h2>Stock Transfer Report <span class="pull-right"><a href="<?php echo site_url('stocktransfer/index'); ?>" class="btn btn-primary"><i class="fa fa-add"></i>&nbsp;New Transfer</a>&nbsp; <a href="<?php echo site_url('stocktransfer/listall/true'); ?>" class="btn btn-primary"><i class="fa fa-file-excel-o"></i>&nbsp;Download</a></span></h2>
<hr />
 
 <script type="text/javascript" charset="utf-8">
			$(document).ready(function() {
				$('#items').dataTable({ "lengthMenu": [ 25, 50, 100, 200 ], "pageLength": 200});
			} );
		</script>
 <div class="table-responsive">
<div class="table" >
<?php if(count($items) > 0) { ?>
        
        <table class="table table-striped" id="items">
            <thead>
                 <tr>
                 	<th>
                        From
                    </th>
                    <th>
                        To
                    </th>
                 	<th>
                       Current Location
                    </th>
                    <th>
                        Current Status
                    </th>
                    <th>
                        Last Updated
                    </th>
                    <th >
                       Est. Delivery Date & Time
                    </th>
                    <th >
                       Total Cartons
                    </th> 
                    <th></th>
                </tr>
            </thead>
            <tbody>
        <?php 
                $i = 0;
                foreach($items as $item) {
                    
            ?>
                <tr>
                    <td>
                        <?php echo $item->From; ?>
                    </td>
                    <td>
                       <?php echo $item->To; ?>
                    </td>
                    <td>
                       <?php echo $item->Current; ?> 
                    </td>
                    <td>
                         <?php echo $item->Status; ?>
                    </td>
                    <td>
                         <?php echo $item->DateUpdated; ?>
                    </td>
                     <td>
                 		<?php echo $item->EstimatedDeliveryDate; ?>
                    </td>
                     <td>
                         <?php echo $item->Cartons; ?>
                    </td>
                     <td>
                         <a href="<?php echo site_url('stocktransfer/index/'. $item->ID); ?>" title="View Transfer"><i class="fa fa-edit"></i></a>
                    </td>
                    
                </tr>
        
        <?php 
                $i++;
                } 
            ?>
         
            </tbody>
        </table>
       

<?php } else { ?>
<div class="alert">No Items Found For This Location</div>
<?php } ?>
</div>
</div>
 
 
 
 
 