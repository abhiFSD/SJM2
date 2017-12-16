<?php $count = count($transfer_statuses); ?>
<?php foreach ($transfer_statuses as $index => $transfer_status): ?>
    <?php $date_created = date("d-m-Y H:i", strtotime($transfer_status->date_created)); ?>
    <?php if ($index == $count - 1 || $transfer_statuses[$index + 1 ]->transfer_id != $transfer_status->transfer_id): ?>
        <?php $status = strtolower($transfer_status->status); ?>
        <li class='picklist'>
            <?php if ($status == 'fully picked' || $status == 'partially complete'): ?>
                <a href="<?php print base_url().'picks/putaway/'.$transfer_status->transfer_id; ?>">
            <?php elseif ($status == 'warehouse returns'): ?>
                <a href="<?php print base_url().'picks/returns/'.$transfer_status->transfer_id; ?>">
            <?php else: ?>
                <a href="<?php print base_url().'picks/pickpack/'.$transfer_status->transfer_id; ?>">
            <?php endif; ?>

                <div>Pick ID <?php print str_pad($transfer_status->transfer_id, 5, '0', STR_PAD_LEFT).' '.$transfer_status->status; ?></div>
                <div><?php print $date_created; ?></div>
            </a>
        </li>
    <?php else: ?>
        <li class='picklist'>
            <div>Pick ID <?php print str_pad($transfer_status->transfer_id, 5, '0', STR_PAD_LEFT).' '.$transfer_status->status; ?></div>
            <div><?php print $date_created; ?></div>
        </li>
    <?php endif; ?>
<?php endforeach; ?>
