<?php $now = new DateTime();
 $now_date = $now->format('Y-m-d')."T".$now->format('H:i:s').".000";
 //print_R($models);exit; ?>



<style>
select, textarea, input {
    font-size: 14px;
}
</style>



<style type="text/css">
    
    #filter_section .col-md-4 input[type="text"] {
     margin-left:0px;  
    }
 body {
    overflow-x: hidden;
 }

</style>
 <div id="wrapper">


  <div id="sidebar-wrapper">
<form method="post" name="batch_change_form">       <ul class="sidebar-nav" style="margin-left:0;">
 
     <li class="sidebar-brand" style="">
                           
                            <a href="#menu-toggle"  id="menu-toggle" style="float:right;" > <i class="fa fa-filter" style="font-size:20px !Important;" aria-hidden="true" aria-hidden="true"></i> 
                            </a>
                            <h4>Filters</h4>
      </li>


 

          <li>
                  
          <div class="form-group kiosk_name_filter">
          <label>Select Kiosk Attribute:</label>
            <select data-placeholder="" name ="filter_config_name" id="filter_config_name" class="singleselect filter_crit multisel"  >
              
              <?php if(isset($configItems)){
                foreach($configItems as $configItem){ ?>
                  <option value = "<?php echo $configItem->id; ?>"><?php echo $configItem->name; ?></option>
                <?php }

              } ?>
            </select>
          </div>

           </li>
                
                <li>
                    <div class="form-group">
                 <label>Select Attribute Value(s): </label>
      
      <select data-placeholder="Attribute Value(s):" name ="filter_config_value" id="filter_config_value" class="filter_crit multiselect" multiple >
       
      </select>
                    </div>
                 </li>
                <li>

            <li>
                    <div class="form-group">
                 <label>Select Kiosk No(s): </label>
 
                        <select data-placeholder="Kiosk No(s):" name ="filter_kiosk" class="filter_crit multiselect"  multiple>
                           <!-- <option>Select</option> -->
                        <?php if(isset($kiosks)){
                            foreach($kiosks as $kiosk){ ?>
                              <option value = "<?php echo $kiosk->number; ?>" selected><?php echo $kiosk->number; ?></option>
                          <?php  }
                          }
                         ?>
                      </select>
                    </div>
                 </li>
                <li>

             <li>
                    <div class="form-group">
               
                         <label>Kiosk Model(s):</label>
                      <select data-placeholder="Kiosk Model(s):" name ="filter_model" class="filter_crit multiselect" multiple data-id = "model_name" >
                        <!-- <option value='' selected>Select</option> -->
                        <?php if(isset($models)){
                          foreach($models as $model){ ?>
                            <option value= "<?php echo $model->id;?>" selected><?php echo $model->name.'-'.$model->make;?></option>
                        <?php  }
                        } ?>
                      </select>
                    </div>
                 </li>
                <li>
             <li>
                    <div class="form-group">
               
   <label>Site Category(s):</label>
      <select data-placeholder="Site Category(s):" name ="filter_siteCategory" class="filter_crit multiselect"  multiple>
        <!-- <option value='' selected>Select</option> -->
      <?php if(isset($site_categories)){
          foreach($site_categories as $site_category){ ?>
            <option value = "<?php echo $site_category->category;?>" selected><?php echo $site_category->category;?></option>
        <?php  }
      } ?>
      </select>
                    </div>
                 </li>
                <li>
                    <div class="form-group">
               
 <label>State(s):</label>
  
      <select data-placholder="State(s):" name ="filter_state" class="filter_crit multiselect"  id="state" data-id="state" multiple>
        <!-- <option value='' selected>Select</option> -->
        <?php if(isset($states)){
          foreach($states as $state){ ?>
            <option value = "<?php echo $state->state;?>" selected><?php echo $state->state;?></option>
        <?php  }
         } ?>
      </select>
                    </div>
                 </li>
 
                <li>
                 
                      
                  <div id="form-group" style="clear: both;margin-top: 60px;">
                       <input type="button" name="filter" id="go_filter" class="btn btn-primary filter_but pull-right" value="Go" />
                     
                    </div>
                    
              
                </li>
                <li style="height: 250px"> </li>
   
            </ul>

            </form>
        </div>
   

  <script>
    $("#menu-toggle").click(function(e) {
        e.preventDefault();
        $("#wrapper").toggleClass("toggled");
    });
    </script>


<script type="text/javascript" charset="utf-8">
 $(document).ready(function(){
   $('#batch_config').DataTable({
     paging : false,
     searching : false,
     "pageLength": 100,
     bInfo : false
   });
 });
</script>
<?php
$today = new DateTime();
 $today_date = $today->format('Y-m-d')."T".$today->format('H:i:s').".000"; ?>
<!-- Dialog to add date and time of commit --->
<div id="dialog-commit" name="dialog-commit" title= "Date & Time of Commit">
  <form method="post" id="commit-form" >
<!--action="<?php //echo site_url('kiosk/commitKiosks/');?>"-->
	   <div class="form-group">
					<input type="datetime-local" name="commit_time"  id="commit_time" class="form-control" value= "<?php echo $today_date; ?>"  required/>
          <input type="hidden" id="queued_hidden" name="queued_hidden" />
    </div>
	</form>
</div>
<!-- dialog box ends here -->
<div class="row">

 <div class="col-md-12">
<h3 class="page-header">Multi Kiosk Attribute Change <span><a href="<?php echo site_url('configitem/batchHistory'); ?>" class="btn btn-primary responsive-btn" style="float: right;">Back</a>&nbsp;</span></h3>

<!-- <span class="pull-right"><a href="<?php echo site_url('configitem/manage/'); ?>"  class="btn btn-primary">New Configuration Field</a>&nbsp;</h3> -->
<div id="filter_section">  

 

    <div class="clearFloat"></div>

</div>

</div>
</div>

 
<div class="row">

    <form method="post" enctype="multipart/form-data" id="config_form">

  	   <div class="col-md-12">
  	 
             <div id="kiosk_selection">
               <table class="table table-striped dataTable" id="batch_config">
                 <thead>
                   <tr>
                    <th><input type="checkbox" name="head_chkbox2" id="head_chkbox2" /></th>
                     <th>Kiosk No.</th>
                     <th>Current Location</th>
                     <th>Attribute</th>
                     <th>Current Value</th>
                     <th>Queued Value</th>
                   </tr>
                 </thead>
                 <tbody>
                   <tr>
                     <td><input type="checkbox" name="chkbox2" /></td> 
                     <td></td>
                     <td></td>
                     <td></td>
                     <td></td>
                     <td></td>
                   </tr>
                 </tbody>
               </table>
             </div>

          <input type="hidden" name="filter_hidden" id="filter_hidden" />
          <input type="hidden" name="config_name_hidden" id="config_name_hidden" />
          <input type="hidden" name="selection_hidden" id="selection_hidden" />
          <div id="blank"></div>
        </div>
      </div>
    </form>
</div>

<script>
$('#filter_config_name').on("change",function(){
  var item_name = $('#filter_config_name option:selected').val();
  $('#config_name_hidden').val(item_name);
  $.ajax({
    url : appPath + '/configitem/getValues?itemName='+item_name,
    type : 'GET',
    success :function(response){
      $("#filter_config_value").html(response);
      //$('#queued_value').html(response);
    }
  });
});
</script>
<script> // Javascript function to select all checkboxes once the header is clicked
$('#head_chkbox1').click(function(){
  $('#select_kiosk :checkbox').prop('checked', this.checked);
  });
  $('#head_chkbox2').click(function(){
    $('#batch_config :checkbox').prop('checked', this.checked);
    });
</script>
<script>
$('.filter_but').click(function(){

  var filters = [];
  var states = [];
  var models = [];
  var siteCategories = [];
  var kiosks = [];
  var config_id;
  var config_values = [];
  $('select[name="filter_state"]').change(function(){

    $('select[name="filter_state"] option:selected').each(function(i){
      states[i] = $(this).text();
    });
  })
  .trigger("change");
  //console.log(states);
  if(states.length > 0){
    filters.push({
      //name : 'state',
      state : states
    });
  }

  $('select[name="filter_model"]').change(function(){

    $('select[name="filter_model"] option:selected').each(function(i){
      models[i] = $(this).val();
    });
  })
  .trigger("change");

  if(models.length > 0){
    filters.push({
      //name : 'model_name',
      model_name : models
    });
  }
  //console.log(models);
  $('select[name="filter_siteCategory"]').change(function(){

    $('select[name="filter_siteCategory"] option:selected').each(function(i){
    siteCategories[i] = $(this).val();
    });
  })
  .trigger("change");
  if(siteCategories.length > 0){
    filters.push({
    //ame : 'site_category',
      site_category : siteCategories
    });
  }
//console.log(siteCategories);
  $('select[name="filter_kiosk"]').change(function(){

    $('select[name="filter_kiosk"] option:selected').each(function(i){
    kiosks[i] = $(this).val();
    });
  })
  .trigger("change");
  if(kiosks.length > 0){
    filters.push({
      //name : 'number',
      kiosk_number : kiosks
    });
  }


    config_id = $('#filter_config_name option:selected').val();

    $('select[name="filter_config_value"]').change(function(){

      $('select[name="filter_config_value"] option:selected').each(function(i){
      config_values[i] = $(this).val().trim();
      });
    })
    .trigger("change");
    if(config_values.length > 0){
      filters.push({
        //name : 'number',
        configuration_value : config_values
      });
    }

    //config_value = $('#filter_config_value option:selected').val().trim();

  if(config_id){
    filters.push({
      config_item_id : config_id

    });
  }
    // if( config_value){
    //   filters.push({
    //     configuration_value : config_value
    //   });
    // }
    $('input[type="checkbox"]').click(function(){
      if($('#current').is(':checked')){
        var status = $('#current').val()
        }
    });
    $('#filter_hidden').val(JSON.stringify(filters));

var config = $('#config_name_hidden').val();
if(config_id){
  $.ajax({
    url : appPath + '/kiosk/findKiosk',
    type : 'GET',
    data : {'json':JSON.stringify(filters),'config':$('#filter_config_name').val(), 'item_name': $('#filter_config_name option:selected').html()},
    dataType :'html',
    success : function(response){
      //$('#kiosk_selection').html('');
      $('#kiosk_selection').html(response);
      
    }

  });
} else{
  alert("Please select a Kiosk Attribute");
}


});
</script>
<script>
//Function to queue changes when Queue button is clicked
$(document.body).on('click','#queue',function(){
  var elems = [];
  var queue = ($('#queued_value').val()).trim();
  var filters = $('#filter_hidden').val();
  console.log(filters);
  var config = $('#filter_config_name').val();



  //get all the checked checboxes
  $('#batch_config input[name="chkbox"]:checkbox:checked').each(function() {
    var array = [];
    //for each checked checkbox, iterate through its parent's siblings and get all the input values
    // var array = $(this).parent().siblings().find('#queued_value').map(function() {
    //
    //      return $(this).val().trim();
    //
    // }).get();
    array.push(queue);
    array.push($('#filter_config_name').val());
    var id_value = $(this).attr('id')
    var res = id_value.split('-');

    array.push(res[1]);

    //array.push();

    console.log(array);
    elems.push(array);


  });
  //console.log(elems);
  //$('#queued_hidden').val(JSON.stringify(elems));
  if(elems.length > 0){

    if($('#queued_value').val() !== ''){


                var today = new Date(); var date = today.getFullYear()+'-'+(today.getMonth()+1)+'-'+today.getDate();
                var time = today.getHours() + ":" + today.getMinutes() + ":" + today.getSeconds();
                var dateTime = date+' '+time;


      $.ajax({
          url : appPath + '/kiosk/deleteQueued',
          data : {'json': JSON.stringify(elems),'filters' : filters,'config':$('#filter_config_name').val(),'selection':queue, 'item_name': $('#filter_config_name option:selected').html(), 'datetime': dateTime},
          type : 'GET',
          dataType : 'text',
          success : function(response){
            if(response){
          //  $('.value_rows').html(response);
              location.href=appPath +'/configitem/batchHistory?config='+config+'&status=Queued&'+'value='+queue;
              $('#kiosk_selection').html(response);
            }
          }
        });
      } else{
        alert("Please enter a new value for the selected kiosk to queue changes");
      }
    } else{
        alert("Please make kiosk selections to queue");
    }
});
</script>

<script>
$(document).ready(function(){


  var dialogTime = $('#dialog-commit').dialog({
      autoOpen: false,
      resizable: true,
      width:300,
      position: { my: "center", at: "center", of: '#kiosk_selection' },
      modal: true,
      buttons:{
        "Submit":function(){
          var form_data = {
              'selected' : $('#queued_hidden').val(),
              'date_unapplied' : $('#commit_time').val()
          };

          if(checkDate()){

              $.ajax({
                url : appPath + '/kiosk/commitKiosks',
                data : form_data,
                method : "POST",
              //  dataType: 'json',
              //contentType: "application/json; charset=utf-8",
                success : function(response){
                  if(response){
                    //$('#blank').html(response);
                     window.location.href = appPath +'/configitem/batchHistory/?order=5';
                  }

                }
              });

              $(this).dialog("close");
          }
        },
        "Cancel":function(){
          $(this).dialog("close");
        }
      }
  });

  $(document.body).on('click','#commit',function(e){ //Ajax request when commit changes button is clicked
    e.preventDefault();
    var elems = [];
    var tmp =[];
    var i=0;
    $('#batch_config input[name="chkbox"]:checkbox:checked').each(function() {
      var array = [];

      //for each checked checkbox, iterate through its parent's siblings and get all the input values
      var array = $(this).parent().siblings().find('#label_queued').map(function() {

           return $(this).text();

      }).get();
     if (array.length > 0){
       i++;
     }

      //array.push($('#queued_value').val().trim());
      array.push($(this).attr('data-id'));
      var id_value = $(this).attr('id')
      var res = id_value.split('-');
      array.push(res[1]);
      elems.push(array);


    });
    $('#queued_hidden').val(JSON.stringify(elems));
    if(elems.length > 0){
     if(i > 0){

      // dialogTime.dialog('open');
     } else{
       alert("Please select kiosks with queued data to commit");
     }

    } else{
      alert("Please select atleast one kiosk with queued change to commit");
    }

  });

});
function checkDate(){
   var date = $('#commit_time').val();
  if(!isFutureDate('<?php echo date('Y-m-d H:i:s'); ?>', date)){
    // do something here
    bootstrap_alert('danger', "Date should be today or past date.");
    return false;
  } else{
    return true;
  }
}
 
$(document.body).on('click','#unqueue',function(e){
  e.preventDefault();
  var config = $('#config_name_hidden').val();
  var selects = [];
  var i=0;
  $('#batch_config input[name="chkbox"]:checkbox:checked').each(function() {
    var inputs = [];

     //for each checked checkbox, iterate through its parent's siblings and get all the input values
    var inputs = $(this).parent().siblings().find('#label_queued').map(function() {

         return $(this).val().trim();

    }).get();
    
    console.log(inputs);

    if(inputs.length > 0){
      i++;
    }
     inputs.push($(this).attr('data-id'));
     var id_value = $(this).attr('id')
     var res = id_value.split('-');

    

     inputs.push(res[1]);

    selects.push(inputs);

  });


  var conditions = $('#filter_hidden').val();
  if(selects.length > 110){
    if(i > 0){
      $.ajax({
        url : appPath + '/kiosk/unQueueKiosks',
        data: {'kiosks':JSON.stringify(selects),'conds':conditions,'config':config},
        type : 'GET',
        dataType : 'html',
        success : function(response){
        $('#kiosk_selection').html(response);
        alert("Changes have been Unqueued");
        }
      });
    } else{
      alert("Please select a kiosk with queued changes");
    }
  } else{
    alert("Please select atleast one kiosk with queued change to continue");
  }


});
</script>
 


 <script type="text/javascript">
    $(document).ready(function() {
     
        $('.multiselect').multiselect({
          allSelectedText: 'All',

          maxHeight: 300,
          numberDisplayed: 0,
          buttonWidth: '175',
          includeSelectAllOption: true
        });
        
            $('.singleselect').multiselect();
            $('.multiselect-container li a label input[value=""]').hide()

            
    });
</script>
