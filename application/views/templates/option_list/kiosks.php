<?php foreach ($kiosks as $kiosk): ?>
    <option value="<?php print $kiosk->id; ?>"><?php print $kiosk->number.' - '.$kiosk->location_name; ?></option>
<?php endforeach; ?>
