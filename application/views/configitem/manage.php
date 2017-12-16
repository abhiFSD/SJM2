<?php
$this->load->view('common/uom_form'); ?>
<div class="row">
<?php
  if(isset($configitem)) {
		$action = "Edit ";
	} else {
		$action = "Add ";
	}
?>
<h3 class="page-header"><?php echo $action; ?> Attribute Field</h3>
<?php
	if (isset($msg) && $msg != "") {
		echo '<div class="alert alert-warning">'. $msg .'</div>';
	}
?>
<div id="hint-dialog" class="dialogdiv" title="Hint">
  <p>Use semi-colon (;) to separate options</p>
</div>

<form method="post" enctype="multipart/form-data" id="config_form">
	<div class="col-md-12">
		<div class="col-md-6">

	        <?php  ?>
	        <div class="form-group">
				<label>Attribute Field Name:</label>
				<input type="text" name="name" class="form-control" value="<?php  echo (isset($configitem)? $configitem->name :''); ?>"  />
			</div>


       <div class="form-group">
         <label>Attribute Category:</label>
         <select name="category_id" class="form-control" required>
            <option value="">Select</option>
            

              <?php

               if(count($config_item_category->result_object())>0)
               foreach($config_item_category->result_object() as $category) {
                
               ?>

            <option value="<?php echo $category->id?>" <?php echo (isset($configitem)? ($configitem->category_id == $category->id? 'selected="selected"':''):''); ?> ><?php echo $category->name?></option>

            <?php } ?>
        
         </select>
        </div>

    <div class="form-group">
        <label>Field Type:</label>
              <select name="fieldType" class="form-control" value="<?php echo (isset($configitem)? $configitem->field_type :'');?>" required>
				        <option value="">Select</option>
				          <option value = "Dropdown – Single Select" <?php echo (isset($configitem)? ($configitem->field_type == 'Dropdown – Single Select'? 'selected="selected"':''):''); ?>>Dropdown – Single Select</option>
               <!--   <option value = "Dropdown - Multi Select" <?php echo (isset($configitem)? ($configitem->field_type == 'Dropdown – Multi Select'? 'selected="selected"':''):''); ?>>Dropdown – Multi Select</option> -->
                  <option value = "Text Field" <?php echo (isset($configitem)? ($configitem->field_type == 'Text Field'? 'selected="selected"':''):''); ?>>Text Field</option>
                  <option value = "Numeric Field" <?php echo (isset($configitem)? ($configitem->field_type == 'Numeric Field'? 'selected="selected"':''):''); ?>>Numeric Field</option>
                  <option value = "Date Field" <?php echo (isset($configitem)? ($configitem->field_type == 'Date Field'? 'selected="selected"':''):''); ?>>Date Field</option>
				      </select>
    </div>
  </div>
</div>
  <div class="col-xs-12 col-md-12">
    <div class="form-group">
        <div class="col-xs-10 col-sm-8 col-md-6">
        <label>Field Options:</label>
        <input type="text" name="valueOptions" class="form-control" value="<?php  echo (isset($configitem)? $configitem->value_options :''); ?>"  />
      </div>
      <div class="col-xs-2 col-sm-4 col-md-6"> <!--Help section-->
        <a class="help-icon help_id" id="hint-ids" data-html="false"  data-toggle="popover" title="Hint" data-content="Use semi-colon (;) to separate options."><i class="fa fa-question-circle" aria-hidden="true"></i></a>
     </div>
    </div>
  </div>
   <div class="col-xs-12 col-md-12">
     <div class="form-group">
         <div class="col-sm-8 col-md-6">
           <label>Unit of Measure:</label>
           <select name="uom" id="uom" class="form-control" value="<?php echo (isset($configitem)? $configitem->uom :'');?>" >
             <option value="">Select</option>
             <?php foreach($measurementunits->result_object() as $measurementunit) {
               $selected = isset($configitem)? ($configitem->uom == $measurementunit->unit_of_measure ? 'selected="selected"':''):'';
               ?>
             <option value="<?php echo $measurementunit->unit_of_measure; ?>"  <?php echo $selected; ?>> <?php echo $measurementunit->unit_of_measure; ?></option>

          <?php }?>
          </select>
        </div>
        <div class="col-sm-4 col-md-6">
  					<a class="btn btn-primary btnright licenseAdd" id="unit_add" >Add New Unit of Measure</a>
  					<!--href="<?php echo site_url('licensor/manage'); ?>"-->
  			</div>
      </div>
    </div>
    <div class="col-xs-12 col-md-12">
      <div class="col-md-6">
       <div class="form-group">
          <label>Apply To:</label>
          <select name="applyTo" class="form-control" value="<?php echo (isset($configitem)? $configitem->apply_to :'');?>" required>
            <option value="">Select</option>
            <option value = "Kiosk" <?php echo (isset($configitem)? ($configitem->apply_to == 'Kiosk'? 'selected="selected"':''):''); ?>>Kiosk</option>
            <option value = "Kiosk Model" <?php echo (isset($configitem)? ($configitem->apply_to == 'Kiosk Model'? 'selected="selected"':''):''); ?>>Kiosk Model</option>
          </select>
        </div>
       <div class="form-group">
         <label>Status:</label>
         <select name="status" class="form-control" value="<?php echo (isset($configitem)? $configitem->status :'');?>" required>
            <option value="">Select</option>
            <option value = "Active" <?php echo (isset($configitem)? ($configitem->status == 'Active'? 'selected="selected"':''):''); ?>>Active</option>
            <option value = "Inactive" <?php echo (isset($configitem)? ($configitem->status == 'Inactive'? 'selected="selected"':''):''); ?>>Inactive</option>
         </select>
        </div>
        <div class="form-group">
          <input type="submit" class="btn btn-primary" value="Submit" name="save" />&nbsp;<a href="<?php echo site_url('configitem/all'); ?>" class="btn">Cancel</a>
        </div>
        <input type="hidden"  name="configId" id="configId" value= "<?php echo (isset($configitem)? $configitem->id :'');?>">
				</div>
    </div>
    </form>
  </div>

  <script type="text/javascript">
    
     $('[data-toggle="popover"]').popover({ trigger: "hover",placement: 'bottom' }); 
  </script>
