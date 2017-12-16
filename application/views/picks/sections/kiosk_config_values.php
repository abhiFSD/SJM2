<?php if (!empty($kiosk_config_values)): ?>
    <div class="single-kiosk-table">
        <h4>Kiosk Attribute Checks</h4>
        <table id="table-product-s" class="table table-bordered">
            <thead>
                <tr>
                    <th>Attribute</th>
                    <th>System State</th>
                    <th>New Current State</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($kiosk_config_values as $kiosk_config_value): ?>
                    <tr class="value_rows">
                        <td class="emphasize-small"><?php print $kiosk_config_value->name; ?></td>
                        <td class="emphasize-small"><?php echo $kiosk_config_value->active_value; ?></td>
                        <td class="emphasize-small">
                            <?php if ($kiosk_config_value->field_type = 'Dropdown – Single Select'): ?>
                                <select name="kiosk_config_values[Active|<?php print $kiosk_config_value->active_config_id; ?>|<?php print $kiosk_config_value->id; ?>]">
                                    <option>Select</option>
                                    <?php foreach (explode(';', $kiosk_config_value->value_options) as $option): ?>
                                        <option value="<?php print trim($option); ?>"><?php print $option; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
