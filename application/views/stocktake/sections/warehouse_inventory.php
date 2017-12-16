<?php if(count($items) > 0): ?>
    <table class="table table-striped datatable" id="items">
        <thead>
            <tr>
                <th>Location</th>
                <th>POS</th>
                <th>SKU</th>
                <th>SKU Name</th>
                <th>Available SOH</th>
                <th>Allocated SOH</th>
                <th>Total SOH</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($items as $item): ?>
                <tr>
                    <td><?php echo $item->warehouse_name; ?></td>
                    <td><?php echo $item->position; ?></td>
                    <td><?php echo $item->sku_value; ?></td>
                    <td><?php echo $item->name; ?></td>
                    <td><?php echo $item->available_soh; ?></td>
                    <td><?php echo $item->allocated_soh; ?></td>
                    <td><?php echo $item->total_soh; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <div class="alert">No Items Found For This Location</div>
<?php endif; ?>