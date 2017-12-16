<input type="hidden" class="kiosk-attributes-item" name="json" value='<?php echo base64_decode($json); ?>'>
<input type="hidden" class="kiosk-attributes-item" name="cols_sort" value="<?php echo $sort; ?>">
<input type="hidden" class="kiosk-attributes-item" name="status" value="<?php echo $status ; ?>">
<input type="hidden" class="kiosk-attributes-item" name="config" value="<?php echo $config ; ?>">
<input type="hidden" class="kiosk-attributes-item" name="value" value="<?php echo $value ; ?>">
<input type="hidden" class="kiosk-attributes-item" name="php_timestamp" value="<?php echo date('Y-m-d H:i:s'); ?>">

<div id="dialog-commit" title="Date & Time of Commit" style="display:none;">
    <form method="post" id="commit-form" action="<?php print site_url('kiosk/commitKiosks'); ?>">
        <div class="form-group">
            <div class="alert alert-danger" id="alert"></div>
            <input type="datetime-local" name="commit_time"  id="commit_time" class="form-control" value= "<?php echo $today_date; ?>"  required/>
            <input type="hidden" id="queued_hidden" name="queued_hidden" />
        </div>
    </form>
</div>

<div id="wrapper">
    <div id="sidebar-wrapper" class="mobile">
        <form method="post" enctype="multipart/form-data" id="history_mainform" name="history_mainform" class="filter">
            <ul class="sidebar-nav" style="margin-left:0;">
                <li class="sidebar-brand" style="">
                    <a href="#menu-toggle"  id="menu-toggle" style="float:right;" > <i class="fa fa-filter" style="font-size:20px !Important;" aria-hidden="true" aria-hidden="true"></i>
                    </a>
                    <h4>Filters</h4>
                </li>
                <?php $this->load->view('configitem/sections/filters'); ?>
                <li style="height: 250px"> </li>
            </ul>
        </form>
    </div>

    <h3 class="page-header">Kiosk Attributes
      <span class="pull-right" >
          <a href="<?php echo site_url('configitem/singleChange/'); ?>"  class="btn btn-primary response-btn">Single Kiosk Attribute Change</a>
          <a href="<?php echo site_url('configitem/batchChange/'); ?>"  class="btn btn-primary response-btn">Multi Kiosk Attribute Change</a>&nbsp;
      </span>
    </h3>
    <form method="post" class="desktop filter" enctype="multipart/form-data" id="history_mainform_desktop" name="history_mainform_desktop">
        <ul class="sidebar-nav-desktop" style="margin-left:0;">
            <?php $this->load->view('configitem/sections/filters'); ?>
        </ul>
    </form>

    <div class="row top30"  >
        <div class="col-md-12">
            <div id="kiosk_history">

                <div id="commit-buttons" style="display: none">
                    <div class="btn-group pull-left change_buttons" role="group" aria-label="...">
                        <button role="group" type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Bulk Actions <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a href="#" id="commit">Commit Selected</a></li>
                            <li><a href="#" id="unqueue">Unqueue Selected</a></li>
                        </ul>

                    </div>
                </div>

                <div id="kiosk_selection_table">
                    <table class="table table-striped dataTable" id="history_config">
                        <thead>
                        <tr>
                            <th> <input type="checkbox" name="head_chkbox1" data-select-all id="head_chkbox1"  class="all-check kiosk-checkbox-all kiosk-checkbox"/></th>
                            <th>Kiosk No.</th>
                            <th>Current Location</th>
                            <th>Attribute Name</th>
                            <th>Attribute Value</th>
                            <th>UoM</th>
                            <th>Status</th>
                            <th>Start Date Time</th>
                            <th>End Date Time</th>
                            <th>Last Updated</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if ($kiosk_active):
                                 $i = 0;
                                foreach ($kiosk_active as $key => $activekiosk):

                                    $start_date = '';
                                    $end_date = '';
                                    $last_updated = '';
                                    if($startdate_timestamp = strtotime($activekiosk->startdate))
                                    {
                                        $start_date = date("d-m-y H:i:s", $startdate_timestamp);
                                    }
                                    if($enddate_timestamp = strtotime($activekiosk->enddate))
                                    {
                                        $end_date = date("d-m-y H:i:s", $enddate_timestamp);
                                    }
                                    if($last_updated_timestamp = strtotime($activekiosk->last_updated))
                                    {
                                        $last_updated = date("d-m-y H:i:s",$last_updated_timestamp);
                                    }
                            ?>
                                    <tr>
                                        <td> <input type="checkbox" name="chkbox" id="chkbox<?php echo $i.'-'. $activekiosk->id; ?>" data-id ="<?php echo $activekiosk->config_item_id; ?>" class="all-check kiosk-checkbox"/></td>
                                        <td><?php echo $activekiosk->number; ?></td>
                                        <td><?php echo $activekiosk->location_name;?></td>
                                        <td><?php echo $activekiosk->configuration_name; ?></td>
                                        <td>
                                            <label style="display:block;font-size:14px;" <?php echo ($activekiosk->configuration_status == "Queued") ? 'id="label_queued"' : '' ;?>> <?php echo $activekiosk->value ;?>
                                            </label>
                                        </td>
                                        <td><?php echo $activekiosk->uom ; ?></td>
                                        <td><?php echo $activekiosk->configuration_status ;?></td>
                                        <td data-order="<?php echo $startdate_timestamp?>"><?php echo $start_date;?></td>
                                        <td data-order="<?php echo $enddate_timestamp?>"><?php echo $end_date;?></td>
                                        <td data-order="<?php echo $last_updated_timestamp?>"><?php echo $last_updated;?></td>
                                    </tr>
                                <?php $i++;
                                endforeach;
                            endif; ?>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>