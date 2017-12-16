<?php //print_r($kioskModels);exit; ?>

 <script type="text/javascript" charset="utf-8">
	$(document).ready(function() {
		$('#kiosk_model').dataTable({ "lengthMenu": [ 25, 50, 100, 200 ], "pageLength": 200});
	} );

</script>

<div class="row">
<h3 class="page-header">Kiosk Models
<span class="pull-right"><a href="<?php echo site_url('kioskmodel/manage'); ?>"  class="btn btn-primary">Add Kiosk Model</a></span></h3>
<?php
	if ($this->session->flashdata('msg') != "") {
		echo '<div class="alert alert-warning">'. $this->session->flashdata('msg') .'</div>';
	}
?>

<div class="row">

 <table class="table table-striped" id="kiosk_model">
        <thead>
            <tr>
                <th>Model Name</th>
                <th>Supplier</th>
                <th>Status</th>
                <th>Edit</th>
            </tr>
        </thead>
        <tbody>
          <?php if(count($kioskModels) > 0 ) { ?>

          <?php foreach($kioskModels as $kioskModel){ ?>
            <tr>
             <td><?php echo $kioskModel->name; ?></td>
             <td><?php echo $kioskModel->make; ?></td>
             <td><?php echo $kioskModel->status; ?></td>
             <td><a href="<?php echo site_url('kioskmodel/manage/'. $kioskModel->id); ?>" title="Edit Configuration"><i class="fa fa-edit"></i></a>&nbsp;&nbsp;&nbsp;&nbsp;

            </tr>
        <?php  } ?>
           <?php } ?>
	 </tbody>
 </table>
 </div>
