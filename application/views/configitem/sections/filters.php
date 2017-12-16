<li>
    <div class="form-group kiosk_name_filter">
        <label class="product">Item:</label>
        <select name="filter_historyItem[]" multiple="" class="filter_crit multiselect" >
            <?php if(isset($configItems)): ?>
                <?php foreach($configItems as $configItem): ?>
                    <option value = "<?php echo $configItem->id; ?>"><?php echo $configItem->name; ?></option>
                <?php endforeach; ?>
            <?php endif; ?>
        </select>
    </div>
</li>
<li>
    <div class="form-group kiosk_name_filter">
        <label class="">Value:</label>
        <select name="filter_historyitemvalue[]" class="filter_crit7 multiselect" multiple="multiple"/>
        </select>
    </div>
</li>
<li>
    <div class="form-group">
        <label class="">Kiosk No(s):</label>
        <select name="filter_kiosk[]" class="filter_crit multiselect"  multiple="multiple">
            <?php if(isset($kiosks)): ?>
                <?php foreach($kiosks as $kiosk): ?>
                    <option data-id="<?php echo $kiosk->id; ?>" value = "<?php echo $kiosk->number; ?>" selected><?php echo $kiosk->number; ?></option>
                <?php endforeach; ?>
            <?php endif; ?>
        </select>
    </div>
</li>
<li>
    <div class="form-group">
        <label class="">Kiosk Model(s):</label>
        <select name="filter_model[]" class="filter_crit4 multiselect"  multiple="multiple">
            <?php if(isset($models)): ?>
                <?php foreach($models as $model): ?>
                    <option value= "<?php echo $model->id;?>" selected><?php echo $model->name.' - '.$model->make;?></option>
                <?php endforeach; ?>
            <?php endif; ?>
        </select>
    </div>
</li>
<li>
    <div class="form-group">
        <label class="">State(s):</label>
        <select name="filter_historystate[]" class="filter_crit3 multiselect"  multiple='multiple'>
            <?php if(isset($states)): ?>
                <?php foreach($states as $state): ?>
                    <option value = "<?php echo $state->state;?>" selected><?php echo $state->state;?></option>
                <?php endforeach; ?>
            <?php endif; ?>
        </select>
    </div>
</li>
<li>
    <div class="form-group">
        <label class="">Status:</label>
        <select name="filter_historystatus[]" class="filter_crit2 multiselect"  multiple >
            <option value="Active" selected>Active</option>
            <option value ="Queued" selected>Queued</option>
            <option value = "Inactive" selected>Inactive </option>
        </select>
    </div>
</li>
<li>
    <div class="form-group">
        <div id="filter_button_box">
            <button type="button" class="btn btn-primary attribute-filter"><i class="fa fa-filter" aria-hidden="true"></i> Filter</button>
        </div>
    </div>
</li>