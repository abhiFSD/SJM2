<?php
$this->load->view('common/licensor_form');
$this->load->view('common/site_form');?>
<?php $this->load->view('common/location_form'); ?>

<div id="dialog-helpdiv" class="dialogdiv" title="Hint">
    <p>Locations will not appear in this list if allocated to another deployment with Active status</p>
</div>
<div id="dialog-helpdiv2" class="dialogdiv" title="Hint">
    <p>Kiosks will not appear in this list if allocated to another deployment with Active status</p>
</div>
<div class="row">
    <?php if(isset($deployment)) {
        $action = "Edit";
    } else {
        $action = "Add";
    }
    ?>
    <h3 class="page-header"><?php echo $action; ?> Deployment <span class="pull-right"><a href="<?php echo site_url('deployment/all'); ?>" class="btn btn-primary">Back</a>&nbsp;</span></h3>
    <div class="alert1 alert-warning"></div>
    <?php if (!empty($msg)): ?>
        <div class="alert alert-warning alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <?php print $msg; ?>
        </div>
    <?php endif; ?>
    <form method="post" enctype="multipart/form-data" id="form" class="form" onsubmit="return checkStatus();return false;">
        <div class="col-xs-12 col-md-12">
            <!--Added Licensor-->
            <div class="form-group row">
                <div class="col-sm-8 col-md-6">
                    <label>Licensor:</label>
                    <select name="licensor_id" class="form-control donotsort"  id="d_lid" value="<?php echo $licensor_id; ?>"  required>
                        <option value="">Select</option>
                        <?php foreach ($licensors as $licensor) {
                        $selected = $licensor->id == $licensor_id ? 'selected="selected"' : '';
                        ?>
                        <option value="<?php echo $licensor->id; ?>" data-party_id="<?php print $licensor->party_id; ?>" data-name="<?php echo $licensor->display_name; ?>" <?php echo $selected; ?>> <?php echo $licensor->display_name; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class= "col-md-1 help-div">
                </div>
                <div class="col-sm-4 col-md-4">
                    <a class="btn btn-primary btnright licenseAdd" id="add_license">Add New Licensor</a>
                </div>
            </div>
            <!-- Added Site-->
            <div class="form-group row">
                <div class="col-sm-8 col-md-6">
                    <label>Site:</label>
                    <select name="site_id"  id="site_id" class="form-control" value="<?php echo $site_name = $deployment->kiosk_location->site->name; ?>" required>
                        <option value="">Select</option>
                        <?php foreach ($sites as $site): ?>
                            <?php $selected = $site->name == $site_name ? 'selected="selected"' : ''; ?>
                            <option value="<?php echo $site->name; ?>" data-id="<?php echo $site->id; ?>" <?php echo $selected; ?>> <?php echo $site->name; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-1 help-div">
                </div>
                <div class="col-sm-4 col-md-4">
                    <a class="btn btn-primary btnright" id="add_site">Add New Site</a>
                </div>
            </div>
    
            <div class="form-group row">
                <div class="col-xs-10 col-sm-6 col-md-6">
                    <label>Location:</label>
                    <select name="location_id" id="location_id" class="form-control" value="<?php echo $name = $deployment->kiosk_location->name; ?>" required>
                        <option value="">Select</option>
                        <?php foreach ($locations as $kiosk_location): ?>
                            <?php $selected = $kiosk_location->id == $deployment->kiosk_location->id ? 'selected="selected"' : ''; ?>
                            <option value="<?php echo $kiosk_location->id; ?>" <?php echo $selected; ?>><?php echo $kiosk_location->site->state. ' - '.  $kiosk_location->site->name. ' - '.$kiosk_location->name; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-xs-1 col-sm-2 col-md-1 help-div">
                    <a class="help-icon help-icon2 help_id"><i class="fa fa-question-circle" aria-hidden="true"></i></a>
                </div>
                <div class="col-sm-4 col-md-4">
                    <a class="btn btn-primary btnright" id="add_location">Add New Location</a>
                </div>
            </div>

            <div class="form-group row">
                <div class="col-xs-10 col-sm-6 col-md-6">
                    <label>Kiosk:</label>
                    <select name="machine_id" class="form-control" value="<?php echo $deployment->machine_id; ?>" required>
                        <option value="">Select</option>
                        <?php foreach ($machines as $kiosk): ?>
                            <?php $selected = $kiosk->id == $deployment->machine_id? 'selected="selected"' : ''; ?>
                            <option value="<?php echo $kiosk->id; ?>" <?php  echo $selected; ?>><?php echo $kiosk->number; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-xs-1 col-sm-2 col-md-1 help-div">
                    <a class="help-icon help-icon2 help_id3"><i class="fa fa-question-circle" aria-hidden="true"></i></a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label>Start Date &amp; Time:</label>
                <?php
                $svalue = "";
                if (isset($deployment->installed_date) && $deployment->installed_date != NULL) {
                $sdate = new DateTime($deployment->installed_date);
                $svalue = $sdate->format('Y-m-d')."T".$sdate->format('H:i:s').".000";
                } else {
                $svalue = date('Y-m-d')."T09:00:00.000";
                }
                ?>
                <input type="datetime-local" name="start_date"  id="start_date" class="form-control" value="<?php echo $svalue; ?>" />
            </div>
            <div class="form-group">
                <label>End Date &amp; Time:</label>
                <?php
                $evalue = "";
                if (isset($deployment->uninstalled_date) && $deployment->uninstalled_date != NULL) {
                $edate = new DateTime($deployment->uninstalled_date);
                $evalue = $edate->format('Y-m-d')."T".$edate->format('H:i:s').".000";
                }
                ?>
                <input type="datetime-local" name="end_date" id="end_date" class="form-control" value="<?php echo $evalue; ?>" />
            </div>
            <div class="form-group">
                <label>Photo:</label>
                <input type="file" name="photo" class="form-control" />
            </div>
            <div class="form-group">
                <label>Status:</label>
                <select name="agreement_status" class="form-control" id="agreement_status"  value = "<?php echo (isset($deployment)?$deployment->status:'');?>" required>
                    <option value="">Select One</option>
                    <option value="Installed" <?php echo (isset($deployment)?$deployment->status == 'Installed'?'selected="selected"':'':''); ?>>Installed</option>
                    <option value="Removed" <?php echo (isset($deployment)?$deployment->status == 'Removed'?'selected="selected"':'':''); ?>>Removed</option>
                    <option value="Install Scheduled" <?php echo (isset($deployment)?$deployment->status == 'Install Scheduled'?'selected="selected"':'':''); ?>>Install Scheduled</option>
                    <option value="Removal Scheduled" <?php echo (isset($deployment)?$deployment->status == 'Removal Scheduled'?'selected="selected"':'':''); ?>>Removal Scheduled</option>
                </select>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Submit" name="save" />&nbsp;<a href="<?php echo site_url('deployment/all'); ?>" class="btn ">Cancel</a>
            </div>
        </div>
        <?php if (isset($deployment)) { ?>
            <input type="hidden" name="deployment_id" value="<?php echo $deployment->id; ?>" />
        <?php } ?>
    </form>
</div>


<script>
    $('#d_lid').on("change",function(){
        var el = $('#d_lid option:selected');
        getAgreements(el.val());
        getSites(el.data('party_id'));
    });
    $('#site_id').on('change',function(){
        var site_select = $('#site_id option:selected').attr('data-id');
        getLocations(site_select);
    });
    function getAgreements(opts) //gets all license agreement associated with a particular licensor
    {
        $.ajax({
            url: appPath + '/agreement/getLicenseAgreements/?id='+opts,
            type:'GET',
            success : function(response){

        $('#agreement_id').html(response);

            },
            error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.status + " "+ thrownError)
                    }

        });
    }
    function getSites(opts) //gets all the sites associated with a particular licensor
    {

        $.ajax({
            url: appPath + '/site/getSitesByLicensor/'+opts,
            type:'GET',
            success : function(response){
                $('#site_id').html(response);
            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert(xhr.status + " "+ thrownError)
            }
        });
    }
    function getLocations(opts) //gets all locations associated with a particular site
    {
        $.ajax({
            url: appPath + '/kiosklocation/getLocations/?id='+opts,
            type:'GET',
            success : function(response){

        $('#location_id').html(response);

            },
            error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.status + " "+ thrownError)
                    }

        });
    }

    $('.alert1').hide();
    function checkStatus()
    {
      var msg = "";
        var stat = $('#agreement_status option:selected').val();

        var start = new Date($('#start_date').val());
        var end = Date.parse($('#end_date').val());
        var curr = new Date();
      var diff =  curr - start;

        if(stat == 'Installed'){
            if(diff >= 0){
                if (isNaN(end) == false) {
                    msg = "End date must be blank";
                    }
            } else{
                msg = "Start date/time must be before the current time";
            }
        }
        if(stat == 'Removed'){
                if (isNaN(end) == false) {
                    enddate = new Date($('#end_date').val());
                    if((curr - enddate) >= 0){
                        if(diff >= 0) {
                            if( (enddate - start) < 0){
                                msg = "End date must be after the start date";
                            }
                        } else{
                            msg = "Start date/time must be before the current time";
                        }
                    } else{
                        msg = "End date/time must be before the current time";
                    }

                } else{
                    msg = "There must be a valid end date";
                }
            }

            if(stat == 'Install Scheduled'){
                if(diff <= 0){
                    if(isNaN(end) == false){
                        msg = "End date/time must be left blank";
                    }
                } else{
                    msg= "Start date/time must be after the current time";
                }
            }

            if(stat == "Removal Scheduled") {
                if(diff >= 0){
                        if(isNaN(end) == false){
                                enddate = new Date($('#end_date').val());
                            if((enddate - curr) <= 0){
                                msg = "End date/time must be after the current time";
                            }
                        } else{
                            msg = "There must be  a valid End date";
                        }
                } else{
                    msg = "Start date/time must be before the current time";
                }
            }

                if (msg != "") {
                    $('.alert1').html(msg);
                    $('.alert1').show();
                    return false;
                } else{

                    $("#form").validate();
                }
    }

</script>
