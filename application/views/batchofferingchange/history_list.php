<table class="table table-striped tight-fit" id="config_items">
    <thead>
        <tr>
            <th>Kiosk</th>
            <th>Position</th>
            <th>Change Type</th>
            <th>Value</th>
            <th>Status</th>
            <th>Queued</th>
            <th>Committed</th>
            <th>Replaced</th>
            <th>User</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($data as $allocation): ?>
            <tr>
                <td><?php echo $allocation->number . " - " .$allocation->location; ?></td>
                <td><?php echo $allocation->position; ?></td>
                <td><?php echo $allocation->attribute_name;  ?></td>
                <td><?php echo $allocation->offering_attribute_id == 1 ? $allocation->sku_name : $allocation->value; ?></td>
                <td><?php echo $allocation->status; ?></td>
                <td><?php echo $allocation->date_queued; ?></td>
                <td><?php echo $allocation->date_applied; ?></td>
                <td><?php echo $allocation->date_unapplied; ?></td>
                <td><?php echo $allocation->user_applied; ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
