<div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">Job History</h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-sx-12">
                    <?php if (count($transfer_statuses)): ?>
                        <p><strong>Job ID <?php print $transfer_statuses[0]->transfer_id; ?></strong></p>
                    <?php endif; ?>
                    <?php foreach ($transfer_statuses as $transfer_status): ?>
                        <p>
                            <?php print $transfer_status->get_date_created('Y-m-d H:i'); ?> -
                            <?php print $transfer_status->user->display_name ? $transfer_status->user->display_name.':' : ''; ?>
                            
                            <?php if (strtolower($transfer_status->status) == 'transferred' && $transfer_status->location_type == 'Freight Provider'): ?>
                                Transferred to 
                                <?php if ($transfer_status->tracking_link): ?>
                                    <?php print anchor($transfer_status->tracking_link, $transfer_status->object->display_name, ['target' => '_blank']); ?>
                                <?php else: ?>
                                    <?php print $transfer_status->object->display_name; ?>
                                <?php endif; ?>
                            <?php elseif (strtolower($transfer_status->status) == 'transferred' && $transfer_status->location_type == 'Staff'): ?>
                                Transferred to <?php print $transfer_status->object->display_name; ?>
                            <?php elseif (strtolower($transfer_status->status) == 'pick generated'): ?>
                                Pick list created
                            <?php else: ?>
                                <?php print $transfer_status->status; ?>
                            <?php endif; ?>
                        </p>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
    </div>
</div>
