<?php $config = array();
$multipleconfig = array();
if(isset($kiosk_config_items)){


foreach($kiosk_config_items as $kioskConfig){

 $config[$kioskConfig->name] =  $kioskConfig->value;
 //$config['id'] = $kioskConfig->config_item_id;
  }
}

if(isset($kiosk_multiple_config_items)){ //gets values of multiple select items
 foreach($kiosk_multiple_config_items as $kioskMultipleConfig){
    $multipleconfig[$kioskMultipleConfig->name][] =  $kioskMultipleConfig->value;

  }

}
?>
<?php foreach($kioskValues as $kioskValue){ ?>
  <div class="form-group">
    <?php //if($kioskValue->value_options != '') {
      if($kioskValue->field_type == 'Dropdown – Single Select') { ?>
        <label><?php echo $kioskValue->name ;?></label>
         <input type="hidden" name="conf_names[]" value ="<?php echo $kioskValue->id;?>" />
        <select name= "conf_values[]" class="form-control single-select" value ="<?php echo (isset($config) ? (array_key_exists($kioskValue->name,$config) ? $config[$kioskValue->name]:''):'');?>" >
          <option value =''>Select</option>
          <?php
                $values = explode('; ',$kioskValue->value_options);
                foreach($values as $value){
                 $selected = isset($config[$kioskValue->name]) ? (($value == $config[$kioskValue->name]) ? 'selected="selected"':''):''; ?>

                <option value = "<?php echo $value;?>" <?php echo $selected;?>><?php echo $value;?></option>
                <?php } ?>

        </select>
      <?php }
      if($kioskValue->field_type == 'Dropdown – Multi Select') { ?>
        <label><?php echo $kioskValue->name ;?></label>
         <input type="hidden" name="conf_multi_names[]" class="multi-input" value ="<?php echo $kioskValue->id;?>" />
        <select name= "conf_multi_values[<?php echo $kioskValue->id; ?>][]" class="form-control multi-select" value ="<?php echo (isset($config) ? (array_key_exists($kioskValue->name,$config) ? $config[$kioskValue->name]:''):'');?>" multiple required>
        <?php if(!isset($config[$kioskValue->name])){ ?>
           <option value="" selected>Select</option>
        <?php } else{ ?>
          <option value="" >Select</option>


          <?php }
                $values = explode('; ',$kioskValue->value_options);
                foreach($values as $value){
                 $selected = isset($multipleconfig[$kioskValue->name]) ? (in_array($value ,$multipleconfig[$kioskValue->name]) ? 'selected="selected"':''):''; ?>

                <option value = "<?php echo $value;?>" <?php echo $selected;?>><?php echo $value;?></option>
                <?php } ?>

        </select>
        <?php }
      //} else {


     if($kioskValue->field_type == 'Date Field') { ?>
   <label><?php echo $kioskValue->name ;?></label>
    <input type="hidden" name="conf_names[]" value ="<?php echo $kioskValue->id;?>" />
   <input type="date" name="conf_values[]" class="form-control" value ="<?php echo (isset($config) ? (array_key_exists($kioskValue->name,$config) ? $config[$kioskValue->name]:''):'');?>" />
<?php  }
if($kioskValue->field_type == 'Numeric Field') { ?>
<label><?php echo $kioskValue->name ;?></label>
<input type="hidden" name="conf_names[]" value ="<?php echo $kioskValue->id;?>" />
<input type="text" name="conf_values[]" class="form-control" value ="<?php echo (isset($config) ? (array_key_exists($kioskValue->name,$config) ? $config[$kioskValue->name]:''):'');?>" />
<?php }
//}  ?>
  </div>
<?php } ?>
