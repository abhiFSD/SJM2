<?php foreach ($inventory_locations as $inventory_location): ?>
    <option value="<?php print $inventory_location->id; ?>"><?php print $inventory_location->name; ?>
<?php endforeach; ?>
