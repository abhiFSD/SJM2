<?php //print_r($kioskModel);exit;?>
<div class="row">
<?php  if(isset($kioskModel)) {

		$action = "Edit ";
	} else {
		$action = "Add ";
	}
 $config = array();
if(isset($kioskConfigs)){
	foreach($kioskConfigs as $kioskConfig){
	 $config[$kioskConfig->configuration_name] =  $kioskConfig->value;

 }
}
	?>
<h3 class="page-header"><?php echo $action; ?> Kiosk Model <span class="pull-right"><!--<a href="<?php echo site_url('kioskModel/all'); ?>" class="btn btn-primary">View All Configurations</a>-->&nbsp;</span></h3>
<?php
	if (isset($msg) && $msg != ""){
		echo '<div class="alert alert-warning">'. $msg .'</div>';
	}
?>

<form method="post" enctype="multipart/form-data" id="kiosk_form">
	<div class="col-md-12">
		<div class="col-md-6">
      <div class="form-group">
         <label>Brand:</label>
         <input type="text" name="model_make" class="form-control" value="<?php  echo (isset($kioskModel)? $kioskModel->make :''); ?>"  />
        </div>
        <div class="form-group">
           <label>Model Name:</label>
           <input type="text" name="model_name" class="form-control" value="<?php  echo (isset($kioskModel)? $kioskModel->name :''); ?>"  />
        </div>
        <div class="form-group">
          <label>Status:</label>
          <select name="status" class="form-control" value="<?php echo (isset($kioskModel)? $kioskModel->status :'');?>" required>
             <option value="">Select</option>
             <option value = "Active" <?php echo (isset($kioskModel)? ($kioskModel->status == 'Active'? 'selected="selected"':''):''); ?>>Active</option>
             <option value = "Inactive" <?php echo (isset($kioskModel)? ($kioskModel->status == 'Inactive'? 'selected="selected"':''):''); ?>>Inactive</option>
          </select>
         </div>
       </div>
     </div>
     <div class="col-xs-12 ">
       <div class= "col-xs-10 col-sm-8 col-md-4">

         <div class="form-group">
            <label class="spl_label">Maximum Shelves:</label>
            <input type="text" name="max_shelves" class="form-control spl_values" value="<?php echo (isset($config['Maximum Shelves'])? $config['Maximum Shelves'] :'') ;  ?>"  />
          </div>
         <div class="form-group">
            <label class="spl_label">Height:</label>
          	<input type="text" name="model_height" class="form-control spl_values" value="<?php echo (isset($config['Height'])? $config['Height'] :'') ;  ?>"  /><span class="unit_space">mm</span>
          </div>
         <div class="form-group">
            <label class="spl_label">Width:</label>
          	<input type="text" name="model_width" class="form-control spl_values" value="<?php echo (isset($config['Width'])? $config['Width'] :'') ;  ?>"  /><span class="unit_space">mm</span>
        </div>
         <div class="form-group">
            <label class="spl_label">Depth:</label>
      			<input type="text" name="model_depth" class="form-control spl_values" value="<?php echo (isset($config['Depth'])? $config['Depth'] :'') ;  ?>" /><span class="unit_space">mm</span>
        </div>
         <div class="form-group">
            <label class="spl_label">Weight:</label>
          	<input type="text" name="model_weight" class="form-control spl_values" value="<?php echo (isset($config['Weight'])? $config['Weight'] :'') ;  ?>"  /><span class="unit_space">kg</span>
						<input type="hidden" name="kiosk_model_id" value="<?php echo(isset($kioskModel) ? $kioskModel->id : '');?>" />
				 </div>

         <div class="form-group">
           <input type="submit" class="btn btn-primary" value="Submit" name="save" />&nbsp;<a href="<?php echo site_url('kioskmodel/all'); ?>" class="btn">Cancel</a>
         </div>
      </div>
    </div>
  </form>
</div>
