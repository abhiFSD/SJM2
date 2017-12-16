 <script type="text/javascript" charset="utf-8">
	$(document).ready(function() {
		$('#config_items').dataTable({ "lengthMenu": [ 25, 50, 100, 200 ], "pageLength": 200, fixedHeader: true,});
	} );

	 var filter = function ()
	 {
         var values = new Array;
         var url = "<?php echo site_url('configitem/all'); ?>/?";
         $("select[class='filter_crit'] option:selected").each(function() { // this function is to select all the filter criteria
           if($(this).val() != ''){
             values.push({
               key:$(this).attr('data-id'),
               value :$(this).val()
             });
         }
       });


         for(var i=0; i < values.length ; i++){
           url = url + values[i]['key']+'='+values[i]['value'] +'&&'; //url passing key value pair to the controller
         }

		    document.location.href= url;
	 }

</script>

<style type="text/css">
    
    #filter_section .col-md-4 input[type="text"] {
     margin-left:0px;  
    }
 body {
    overflow-x: hidden;
 }

 
button.multiselect{
  text-align: left;
}
#switchfilter{
    display: none;
}
#apply_filters_button {
    margin: 0 32px 0 0;
}

 
#sidebar-wrapper label{
  display: block; 
}
</style>
 <div id="wrapper">

  <div id="sidebar-wrapper">
 
 <ul class="sidebar-nav" style="margin-left:0;">
 
     <li class="sidebar-brand" style="">
                           
                            <a href="#menu-toggle"  id="menu-toggle" style="float:right;" > <i class="fa fa-filter" style="font-size:20px !Important;" aria-hidden="true" aria-hidden="true"></i> 
                            </a>
                            <h4>Filters</h4>
      </li>
      <li>      
          <div class="form-group kiosk_name_filter">
            <label>Applies To:</label>

                <select name="applies_to" id="applies_to" class="filter_crit"  onchange ="filter()" value= "<?php echo (array_key_exists('apply_to',$filters)? $filters['apply_to']:'') ;?>" >
                    <option value="">Select</option>
                  <?php if(isset($applyto_filters)) { ?>
                  <?php    foreach($applyto_filters->result_object() as $filter) {
                  $selected = array_key_exists('apply_to',$filters)? ($filters['apply_to']==$filter->apply_to? 'selected="selected"':''):'';
                    ?>
                  <option value= "<?php echo $filter->apply_to;?>" data-id="apply_to" <?php echo $selected;?>><?php echo $filter->apply_to;?></option>
                  <?php  }
                 } ?>
               </select>
          </div>

          </li>
          <li>
                  <div class="form-group">
                     <label>Unit of Measurement:</label>
                      <select name="uom" id="uom" class="filter_crit" onchange ="filter()" value= "<?php echo (array_key_exists('uom',$filters)? $filters['uom']:'') ;?>">
                        <option value="">Select</option>
                      <?php if(isset($uom_filters)) { ?>
                      <?php    foreach($uom_filters->result_object() as $filter) {
                        $selected = array_key_exists('uom',$filters)? ($filters['uom']== $filter->unit_of_measure? 'selected="selected"':''):'';
                        ?>
                      <option value= "<?php echo $filter->unit_of_measure;?>"  data-id="uom" <?php echo $selected;?>><?php echo $filter->unit_of_measure;?></option>
                      <?php  }
                     } ?>
                   </select>
                </div>

            </li>
                        
            <li>

                  <div class="form-group">
                   <label>Field Type:</label>
                      <select name="field_type" id="field_type" class="filter_crit"  onchange ="filter()" value= "<?php echo (array_key_exists('field_type',$filters)? $filters['field_type']:'') ;?>">
                        <option value="">Select</option>
                      <?php if(isset($type_filters)) { ?>
                      <?php    foreach($type_filters->result_object() as $filter) {
                $selected = array_key_exists('field_type',$filters)? ($filters['field_type']== $filter->field_type? 'selected="selected"':''):'';
                         ?>
                      <option value= "<?php echo $filter->field_type;?>" data-id="field_type" <?php echo $selected;?>><?php echo $filter->field_type;?></option>
                      <?php  }
                     } ?>
                      </select>
                </div>
              </li>
          
                <li>

                  <div class="form-group">
                  <label>Status:</label>
                  <select name="status" id="status" class="filter_crit"  onchange="filter()" value= "<?php echo (isset($filters)? $filters['status']:'') ;?>" >
                    <option value="">Select</option>

                    <!-- <option value="All" data-id="status" <?php echo(array_key_exists('status',$filters)? ($filters['status']== 'All'? 'selected="selected"':''):'');?>>All</option> -->
                    <option value="Active" data-id="status"   <?php echo(array_key_exists('status',$filters)? ($filters['status']== "Active"? 'selected="selected"':''):'');?>>Active</option>
                    <option value="Inactive" data-id="status"   <?php echo(array_key_exists('status',$filters)? ($filters['status']== "Inactive"? 'selected="selected"':''):'');?>>Inactive</option>
                  </select>
                </div>
              </li>


 
                <li>
                                    
                  <div id="filter_button_box">
                      
                   <!--  <button id="apply_filters_button" type="submit" class="btn btn-primary"><i class="fa fa-filter" aria-hidden="true"></i> Filter</button> -->
                    </div>
                    
              
                </li>
                <li style="height: 250px"> </li>
   
            </ul>
      
        </div>

 
 
 

<?php
	if ($this->session->flashdata('msg') != "") {
		echo '<div class="alert alert-warning">'. $this->session->flashdata('msg') .'</div>';
	}
?>
<div class="row">
<h3 class="page-header">Kiosk Attribute Fields
<span class="pull-right"><a href="<?php echo site_url('configitem/manage/'); ?>"  class="btn btn-primary">New Attribute Field</a>&nbsp;</h3>
<div class="col-md-12">

 <table class="table table-striped" id="config_items">
        <thead>
            <tr>
                <th>Field Name</th>
                <th>Category</th>
                <th>Applies To</th>
                <th>Field Type</th>
                <th>Field Options</th>
                <th>UoM</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
          <?php if($configs->num_rows() > 0 ) { ?>

          <?php foreach($configs->result_object() as $config){ ?>
            <tr>
             <td><?php echo $config->name; ?></td>
             <td><?php echo $config->category_name; ?></td>
             <td><?php echo $config->apply_to; ?></td>
             <td><?php echo $config->field_type; ?></td>
             <td><?php echo $config->value_options; ?></td>
             <td><?php echo $config->uom; ?></td>
             <td><?php echo $config->status; ?></td>
             <td><a href="<?php echo site_url('configitem/manage/'. $config->id); ?>" title="Edit Configuration"><i class="fa fa-edit"></i></a> 

            </tr>
        <?php  } ?>
           <?php } ?>
	 </tbody>
 </table>
 </div>

 </div>
 </div>
<script>
 $(document).ready(function(){
  
      $("#menu-toggle").click(function(e) {
          e.preventDefault();
          $("#wrapper").toggleClass("toggled");
      });
   });
</script>
