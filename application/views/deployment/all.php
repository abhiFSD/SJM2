<script type="text/javascript" charset="utf-8">
$(document).ready(function() {
    $('#items').dataTable({ "lengthMenu": [ 25, 50, 100, 200 ], "pageLength": 200, fixedHeader: true});
} );
var filter = function ()
{
    var url = "<?php echo site_url('deployment/all'); ?>/" + document.getElementById('status').value;
    document.location.href= url;
}
</script>

<div class="row form-inline">
    <div class="col-sm-4">
        <h3 class="page-header">
            Deployments 
        </h3>
    </div>
    <div class="col-sm-8 text-right">
        <label>
            Status
            &nbsp;&nbsp;&nbsp;
        </label>
        <select name="status" id="status" onchange="filter()" class="form-control">
            <option value="All" <?php echo $status == 'All'? 'selected="selected"':''; ?> >All</option>
            <option value="Installed" <?php echo $status == 'Installed'? 'selected="selected"':''; ?> >Installed & Scheduled</option>
            <option value="Removed" <?php echo $status == 'Removed'? 'selected="selected"':''; ?>>Removed</option>
        </select>
        <a href="<?php echo site_url('deployment/manage'); ?>" class="btn btn-primary">Add Deployment</a>&nbsp
    </div>
</div>

    <?php
    if ($this->session->flashdata('msg') != "") {
        echo '<div class="alert alert-warning">'. $this->session->flashdata('msg') .'</div>';
    }
    ?>

    <?php if (count($deployments)): ?>
        <table class="table table-striped" id="items">
            <thead>
                <tr>
                    
                    <th>Kiosk</th>
                    <th>Site Name</th>
                    <th>Location Name</th>
                    <th>Status</th>
                    <th>Start Date & time</th>
                    <?php   if ($status == 'All' || $status == 'Removed') { ?>
                        <th>End Date & time</th>
                    <?php } ?>
                    <th>Photo</th>
                    <th>Edit</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($deployments as $deployment): ?>
                    <?php
                        $date = new DateTime($deployment->installed_date);
                        $endDate = "";
                        if ($status == 'All' || $status == 'Removed') {
                            if ($deployment->uninstalled_date != '0000-00-00 00:00:00') {
                                $endDate = new DateTime($deployment->uninstalled_date);
                            }
                        }
                    ?>
                    <tr>
                        <td><?php echo $deployment->kiosk->number; ?></td>
                        <td><?php echo $deployment->kiosk_location->site->name; ?></td>
                        <td><?php echo $deployment->kiosk_location->name; ?></td>
                        <td><?php echo $deployment->status; ?></td>
                        <td><span class="hide"><?php echo $date->format('Y-m-d'); ?></span><?php echo $date->format('d-m-Y h:i A'); ?></td>
                        <?php if ($status == 'All' || $status == 'Removed') { ?>
                            <td><span class="hide"><?php echo ($endDate != ""? $endDate->format('Y-m-d'):""); ?></span><?php echo $endDate != ""? $endDate->format('d-m-Y h:i A'):""; ?></td>
                        <?php } ?>
                        <td>
                            <?php if ($deployment->photo !="") { ?>
                            <a href="<?php echo base_url("/uploads/". $deployment->photo); ?>" target="_blank"><img src="" />View</a>
                            <?php } ?>
                        </td>
                        <td><a href="<?php echo site_url('deployment/manage/'. $deployment->id); ?>" title="Edit Deployment"><i class="fa fa-edit"></i></a>&nbsp;&nbsp;&nbsp;&nbsp;
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="alert alert-danger">No deployment found currently</div>
    <?php endif; ?>
</div>