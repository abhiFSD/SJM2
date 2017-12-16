
<script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js"></script>
<script src="//cdn.datatables.net/plug-ins/1.10.12/sorting/datetime-moment.js"></script>


 <script type="text/javascript" charset="utf-8">
	$(document).ready(function() {
    $.fn.dataTable.moment( 'd-MM-YYYY' );
		$('#items').dataTable(
      { "lengthMenu": [ 25, 50, 100, 200 ], "pageLength": 200, fixedHeader: true,
      "aoColumns": [
              null,
              null,
              null,
              null,
              null,
              null,
              null,
              null,
              { "sType": "date-uk" },
              { "sType": "date-uk" },
              null,
              null


          ]});

	} );
  if ($.fn.dataTableExt !== undefined){
    jQuery.extend( jQuery.fn.dataTableExt.oSort, {
    "date-uk-pre": function ( a ) {
        var ukDatea = a.split('-');
        return (ukDatea[2] + ukDatea[1] + ukDatea[0]) * 1;
    },

    "date-uk-asc": function ( a, b ) {
        return ((a < b) ? -1 : ((a > b) ? 1 : 0));
    },

    "date-uk-desc": function ( a, b ) {
        return ((a < b) ? 1 : ((a > b) ? -1 : 0));
    }
    } );
  }


	 var filter = function ()
	 {
		    var url = "<?php echo site_url('agreement/all'); ?>/" + document.getElementById('status').value;

		    document.location.href= url;
	 }


</script>

<div class="row">
<h3 class="page-header">License Agreements <span class="pull-right"><small><label>Status:</label>&nbsp;&nbsp;&nbsp;<select name="status" id="status" onchange="filter()" >
					<option value="">Select</option>
					<option value="All" <?php echo $status == 'All'? 'selected="selected"':''; ?> >All</option>
					<option value="Active" <?php echo $status == 'Active'? 'selected="selected"':''; ?> >Active</option>
					<option value="Inactive" <?php echo $status == 'Inactive'? 'selected="selected"':''; ?>>Inactive</option>
				</select></small>
				&nbsp;&nbsp;<a href="<?php echo site_url('agreement/manage'); ?>" class="btn btn-primary">Add Agreement</a>&nbsp;</span></h3>

						<?php
	if ($this->session->flashdata('msg') != "") {
		echo '<div class="alert alert-warning">'. $this->session->flashdata('msg') .'</div>';
	}
?>
</div>

<div class="row">
<?php if($agreements->num_rows() > 0 ) { ?>

 <table class="table table-striped" id="items">
        <thead>
            <tr>
                <!-- <th>Agreement ID</th> -->
                <th>Name</th>
                <th>Licensor</th>
                <th>Day Due</th>
                <th>Fixed</th>
                <th>Rate1</th>
                <th>Threshold 1</th>
                <th>Rate2</th>
                <th>Threshold 2</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Status </th>
                <th>Edit</th>
            </tr>
        </thead>
        <tbody>
        <?php

        	$results = $agreements->result_object();

        	foreach($results as $agreement) {

                $startDate = new DateTime($agreement->start_date);
                $endDate = new DateTime($agreement->end_date);

			?>
            <tr>
                <!-- <td><?php echo $agreement->agreement_id; ?></td> -->
                <td><?php echo $agreement->name; ?></td>
                <td><?php echo $agreement->display_name; ?></td>

                <td><?php echo $agreement->day_due;  ?></td>

                   <td><?php echo '$'.number_format($agreement->fixed_fee_exGST); ?></td>
                <td><?php echo ($agreement->commission_1_rate * 100) . '%'; ?></td>
                 <td><?php echo '$'.number_format($agreement->commission_1_threshold); ?></td>
                <td><?php echo ($agreement->commission_2_rate * 100) . '%'; ?></td>
                <td><?php echo '$'.number_format($agreement->commission_2_threshold); ?></td>
                <td><?php echo  $startDate->format('d-m-Y'); ?></td>
                <td><?php echo $endDate->format('d-m-Y'); ?></td>
                <td><?php echo $agreement->status; ?></td>


                <td><a href="<?php echo site_url('agreement/manage/'. $agreement->id); ?>" title="Edit Agreement"><i class="fa fa-edit"></i></a></td>
            </tr>
           <?php } ?>
	 </tbody>
 </table>
 <?php // echo $pages; ?>
 <?php } else  { ?>
 <div class="alert alert-danger">No Agreements found currently</div>
  <?php } ?>
 </div>
