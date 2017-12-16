<form>
    <table class="table actions hide-sorting pow-responsive" id="jobs_table">
        <thead>
            <tr>
                <?php if (empty($completed_jobs_only)): ?>
                    <th class="tight"></th>
                <?php endif; ?>
                <th class="tight">JOB</th>
                <th class="tight padding-right-0">P&P</th>
                <th class="loose">From</th>
                <th class="tight padding-right-0 text-center">PUT</th>
                <th class="loose">To</th>
                <th class="loose">Current Status</th>
                <th class="loose">Notes</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($transfers as $transfer): ?>
                <?php $last_transfer_status = $transfer->last_transfer_status; ?>
                <?php $lowercase_status = strtolower($last_transfer_status->status); ?>
                <tr>
                    <?php if (empty($completed_jobs_only)): ?>
                        <td class="text-left"><input type="checkbox" name="transfer_ids[]" value="<?php print $transfer->id; ?>"></td>
                    <?php endif; ?>
                    <td class="text-left"><?php printf('%03d', $transfer->id); ?></td>
                    <td class="text-left padding-right-0">
                        <?php if ($transfer->has_transfer_items()): ?>
                            <a href="<?php print site_url('picks/pickpack/'.$transfer->id); ?>">
                                <span class="glyphicon glyphicon-list-alt"></span>
                            </a>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ('inventory_location' == $transfer->location_from_type): ?>
                            <?php print inventory_location_map_link($transfer->location_from); ?>
                            <?php print inventory_location_page_link($transfer->location_from); ?>
                        <?php elseif ('kiosk' == $transfer->location_from_type): ?>
                            <?php print kiosk_map_link($transfer->location_from); ?>
                            <?php print kiosk_page_link($transfer->location_from); ?>
                        <?php endif; ?>
                    </td>
                    <td class="text-left padding-right-0">
                        <?php if ($transfer->has_status('fully picked')): ?>
                            <a href="<?php print site_url('picks/putaway/'.$transfer->id); ?>">
                                <span class="glyphicon glyphicon-list-alt"></span>
                            </a>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ('inventory_location' == $transfer->location_to_type): ?>
                            <?php print inventory_location_map_link($transfer->location_to); ?>
                            <?php print inventory_location_page_link($transfer->location_to); ?>
                        <?php elseif ('kiosk' == $transfer->location_to_type): ?>
                            <?php print kiosk_map_link($transfer->location_to); ?>
                            <?php print kiosk_page_link($transfer->location_to); ?>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php print $last_transfer_status->get_date_created('Y-m-d H:i'); ?> &nbsp;

                        <?php if (in_array($lowercase_status, ['partially picked', 'fully picked', 'partially allotted', 'partially complete', 'fully allotted'])): ?>
                            <?php print ucfirst($lowercase_status); ?>
                        <?php elseif ($lowercase_status == 'transferred' && $last_transfer_status->location_type == 'Freight Provider'): ?>
                            Transferred to 
                            <?php if ($last_transfer_status->tracking_link): ?>
                                <?php print anchor($last_transfer_status->tracking_link, $last_transfer_status->object->display_name, ['target' => '_blank']); ?>
                            <?php else: ?>
                                <?php print $last_transfer_status->object->display_name; ?>
                            <?php endif; ?>
                        <?php elseif ($lowercase_status == 'transferred' && $last_transfer_status->location_type == 'Staff'): ?>
                            Transferred to <?php print $last_transfer_status->object->display_name; ?>
                        <?php else: ?>
                            <?php print $last_transfer_status->status; ?>
                        <?php endif; ?>

                        <?php if ($last_transfer_status->note): ?>
                            <?php print 'Note: '.$last_transfer_status->note; ?>
                        <?php endif; ?>
                        <br>
                        <a href="<?php print site_url('stockmovement/history/'.$transfer->id); ?>" data-modal>Status History</a>
                    </td>
                    <td><?php print $transfer->notes; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</form>
