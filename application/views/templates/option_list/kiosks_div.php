<?php foreach ($kiosks as $kiosk): ?>
    <div>
        <label class="control-label not-bold">
            <input type="checkbox" name="to_kiosk_id[]" required value="<?php print $kiosk->id; ?>">
            <?php print $kiosk->number.' - '.$kiosk->location_name; ?>
        </label>
    </div>
<?php endforeach; ?>
