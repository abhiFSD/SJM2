<div id="commit-buttons" style="display: none">
    <div class="btn-group pull-left change_buttons" role="group" aria-label="...">
        <button role="group" type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"
                aria-haspopup="true" aria-expanded="false">
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
            <th> <input type="checkbox" data-select-all name="head_chkbox1" id="head_chkbox1"  class="all-check kiosk-checkbox-all kiosk-checkbox"/></th>
            <th>Kiosk No.</th>
            <th>Current Location</th>
            <th>Attribute</th>
            <th>Configuration Value</th>
            <th>UoM</th>
            <th>Status</th>
            <th>Start Date Time</th>
            <th>End Date Time</th>
            <th>Last Updated</th>
        </tr>
        </thead>
        <tbody>
        <?php if ($history){ ?>
        <?php
        $i = 0;
        foreach ($history as $activekiosk)
        {
            ?>
            <tr>
                <td><input type="checkbox" name="chkbox" id="chkbox<?php echo $i . '-' . $activekiosk->id; ?>"
                           data-id="<?php echo $activekiosk->config_item_id; ?>" class="all-check kiosk-checkbox"/></td>
                <td><?php echo $activekiosk->number; ?></td>
                <td><?php echo $activekiosk->location_name; ?></td>
                <td><?php echo $activekiosk->configuration_name; ?></td>
                <td>

                    <label
                        style="display:block;font-size:14px;" <?php echo ($activekiosk->configuration_status == "Queued") ? 'id="label_queued"' : ''; ?>> <?php echo $activekiosk->value; ?>
                        <label>
                </td>
                <td><?php echo $activekiosk->uom; ?></td>
                <td><?php echo $activekiosk->configuration_status; ?></td>
                <td><?php echo $activekiosk->startdate != '0000-00-00 00:00:00' ? $activekiosk->startdate : ''; ?></td>
                <td><?php echo $activekiosk->enddate != '0000-00-00 00:00:00' ? $activekiosk->enddate : ''; ?></td>
                <td><?php echo $activekiosk->last_updated != '0000-00-00 00:00:00' ? date("d-m-Y H:i:s", strtotime($activekiosk->last_updated)) : ''; ?></td>
            </tr>

            <?php
            $i ++;
        }
        ?>
        </tbody>
    </table>
</div>
<?php } else
{ ?>
    <tr>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    </tbody>
    </table>

<?php
}


?>
