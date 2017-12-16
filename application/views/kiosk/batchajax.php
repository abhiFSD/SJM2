
<style>
div#select_kiosk.dataTables_filter{display:none !important;}
</style>
<script type="text/javascript" charset="utf-8">
 $(document).ready(function() {
   $('#batch_config').DataTable({
     paging : false,
     searching :false,
      "pageLength": 100,
     responsive:true,
     "autoWidth": false 
   });
 } );
</script>


<div class="">
 <div class=" " >

  <div id="queued">
   <label>New Item Value: </label>



   <?php if($new_attribute_values){
     if(sizeof($new_attribute_values) > 1) { ?>
       <select name ="queued_value" id="queued_value" class="filter_crit singleselect" value="<?php echo isset($select)? $select:''; ?>">
         <option value=''>Select</option>
          <?php
                foreach($new_attribute_values as $value){
                  $selected = isset($select)?(trim($value) == $select ? 'selected="selected"':''):''; ?>

                   <option value ="<?php echo $value ;?>" <?php echo $selected; ?>><?php echo $value ;?></option>

    <?php } ?>

    <?php }  else{ ?>
      <input type="text" name="queued_value" id="queued_value" style="font-size:12px;" value="<?php echo isset($select)? $select:''; ?>"  />
    <?php }
  } ?>
   </select>
 
 </div>

  <div id="queue-btn">
    <input type="button" name="queue" id="queue" class="btn btn-primary action_btns" value="Queue Changes" />

     <a href="<?php echo site_url('configitem/batchHistory'); ?>" class="btn ">Cancel</a>
  </div>
</div>

<table class="table table-striped dataTable" id="batch_config">
  <thead>
    <tr>
    <th><input type="checkbox" name="head_chkbox1" id="head_chkbox1" /></th>
      <th style="width:15%">Kiosk No.</th>
      <th style="width:37%">Current Location</th>
      <th>Attribute</th>
      <th>Current Value</th>
      <th>Queued Value</th>
      
    </tr>
  </thead>
  <tbody>
  <?php $i = 0;

   if(isset($selections) && $selections){



    foreach($selections as $selection){
      ?>
      <tr class="value_rows">
       <?php if(isset($id)){
            $checked = in_array($selection->id,$id) ? 'checked = "checked"':''; ?>
           <td><input type="checkbox" name="chkbox" id="chkbox<?php echo $i.'-'. $selection->id; ?>" data-id ="<?php echo $selection->config_item_id; ?>" <?php echo $checked; ?>/></td>
            <?php } else { ?>
              <td><input type="checkbox" name="chkbox" id="chkbox<?php echo $i.'-'. $selection->id; ?>" data-id ="<?php echo $selection->config_item_id; ?>" /></td>

            <?php } ?>
        <td class="kiosk_number"><a href="<?php echo site_url('kiosk/manage/'.$selection->id); ?>"><input type="text" class="no-show-input" value="<?php echo $selection->number; ?>" readonly/></a></td>
        <td><input type="text" class="no-show-input" value="<?php echo $selection->location_name ; ?>" readonly/></td>
        <td><input type ="text" class="no-show-input" value="<?php echo $selection->configuration_name; ?>" readonly/></td>
        <td><label style="display:block;"><?php echo $selection->value; ?></label></td>


         <td> <div id="values_queued"><?php  if($selection->new_value) { ?><label style="display:block;font-size:14px;color:green;" id="label_queued"><?php echo $selection->new_value; ?><label><?php } ?></div> </td>
       
          </tr>
  <?php  $i++; } ?>
  </tbody>
 </table>

<?php } else{ ?>
  <tr>
   <td><input type="checkbox" name="chkbox" /></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
     <td></td>
  </tr>
</tbody>
</table>
<p>There are no items to show </p>
<?php }
   ?>



<script> // Javascript function to select all checkboxes once the header is clicked
// $('#head_chkbox1').click(function(){
//   $('#select_kiosk :checkbox').prop('checked', this.checked);
//   });
  $('#head_chkbox1').click(function(){
    $('#batch_config :checkbox').prop('checked', this.checked);
    });
 
    $(document).ready(function() {
        
        $('.singleselect').multiselect();

    });
</script>

