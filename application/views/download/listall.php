

<div class="row">
<div class="col-md-12">
<h2>Downloads <span class="pull-right desktop-only"></h2>
<hr />

 
<div class="table" >
<?php if(count($downloads) > 0) { ?>
        
        <table class="table table-striped" id="items">
            <thead>
                 <tr>
                 	<th>
                        Requested Date
                    </th>
                     <th>
                         Status
                     </th>
                    <th>
                        Download File
                    </th>                   


                </tr>
            </thead>
            
            <tbody>
        <?php 
                $i = 0;
                foreach($downloads->result() as $download) {
                    $date = $download->requested_date;
                    $val = new DateTime($date);
            ?>
                <tr>
                    <td>
                         <?php echo $val->format('m-d-Y'); ?>
                    </td>
                    <td>
                       <?php  
                        if($download->completed == 1) {
                            echo "Completed";
                        } else {
                            echo "Queued";
                        }
                       ?>
                    </td>
                   
                    <td>
                         <?php
                         if($download->file) {
                             echo '<a href="'. base_url("/downloads/". $download->file) .'" class="btn btn-primary"><i class="fa fa-cloud-download" aria-hidden="true"></i>&nbsp;Download</a>';
                         }
                        ?>
                    </td>

                </tr>
        
        <?php 
                $i++;
                } 
            ?>
         
            </tbody>
        </table>
       

<?php } else { ?>
<div class="alert">No downloads requested</div>
<?php } ?>
</div>
</div>

 
 
 
 
 