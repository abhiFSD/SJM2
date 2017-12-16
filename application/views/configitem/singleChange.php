

<style>
select, textarea, input {
    font-size: 14px;
    border: 1px solid #ccc;
}
#filter_section select
{
  height: 35px;
}
#single_go_filter {
  margin: 0 0 0 10px;
}

.filter-section-single{
  margin: 5px 0;
}

.single-kiosk-table{

  margin: 30px 0;
}

</style>
 
 
<?php
$today = new DateTime();
 $today_date = $today->format('Y-m-d')."T".$today->format('H:i:s').".000"; ?>
 
<!-- dialog box ends here -->
<div class="row">

  <form method="post" enctype="multipart/form-data" id="single_config_form">
 <div class="col-md-12">
      <h3 class="page-header">Single Kiosk Attribute Change <span><a href="<?php echo site_url('configitem/batchHistory'); ?>" class="btn btn-primary response-btn" style="float: right;">Back</a>&nbsp;</span></h3>
      <div id="filter_section">
        <div class="filter-row">
          <div class= "filter-section-single">
            <label>Kiosk To Change:</label>
            <select required="" name="change_kiosk" id="change_kiosk" class="filter_crit">
               <option value="" selected>Select Kiosk </option> 
                <?php if(isset($kiosks)){
                foreach($kiosks as $kiosk){ ?>
                  <option value = "<?php echo $kiosk->id; ?>" ><?php echo $kiosk->number; ?></option>
              <?php  }
              }
             ?>
            </select>
          </div>
        </div>
          <div class="filter-row">
          <div class= "filter-section-single single-kiost-copy" style="display: none">
            <label>Copy From Kiosk:</label>
            <select name="copy_kiosk" id="copy_kiosk" class="filter_crit">
               <option value="" selected>Select Kiosk to copy</option> 
                <?php if(isset($kiosks)){
                foreach($kiosks as $kiosk){ ?>
                  <option value = "<?php echo $kiosk->id; ?>" ><?php echo $kiosk->number; ?></option>
              <?php  }
              }
             ?>
            </select> 
          </div>
        </div>

        <div class="filter-row" style="padding-top: 35px;">
          <input type="button" name="filter" id="single_queue_filter" class="btn btn-primary single_queue_filter" value="Queue Change"/>
          <a href="<?php echo site_url('configitem/batchHistory'); ?>" class="btn ">Cancel</a>
          <div id="blank"></div>
        </div>


          <div class="clearFloat"></div>
      </div>
 </div>    
   
 <div class="col-md-12">
  	 
             <div id="kiosk_selection" class="single-kiosk-table">
               <table class="table table-striped dataTable" id="batch_config">
                 <thead>
                   <tr>
        
                     <th>Attribute</th>
                     <th>Current Value</th>
                     <th>Queued Value</th>
                     <th>New Value to Queue</th>        
                   </tr>
                 </thead>
                 <tbody id="attribute-table-content">

                  <?php
                  foreach ($configItems as $key => $config)
                   { ?>
                   <tr class="value_rows" role="row">
                     <td><?php echo $config->name?></td>
                      <td></td>
                     <td></td>
                     <td></td>   
                   </tr>
                  <?php 
                    }
                   ?>
                 </tbody>
               </table>
             </div>
         
         <div class="filter-row" style="padding-top: 5px;">
          <input type="button" name="filter" id="single_queue_filter" class="btn btn-primary single_queue_filter" value="Queue Change"/>
          <a href="<?php echo site_url('configitem/batchHistory'); ?>" class="btn ">Cancel</a>
          <div id="blank"></div>
        </div>

        </div>
      
    </form>
</div>

<script type="text/javascript">
$(document).ready(function(){

 $('.single-kiost-copy').hide();

 $('#change_kiosk').on("change",function(){

  var kioskID = $('#change_kiosk option:selected').val();
    App.FetchAttributes(kioskID); 
  });

  $('#copy_kiosk').on("change",function(){

  var kioskID = $('#copy_kiosk option:selected').val();
    App.CopyAttributes(kioskID); 
  });

  $('.single_queue_filter').on("click",function(){

      console.log();

      App.QueueAttributes($('#single_config_form')); 
  });

  
});
 
</script>