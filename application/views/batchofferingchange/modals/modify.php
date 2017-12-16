<div class="modal fade" id="BatchModifyModal" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Batch Modify</h4>
            </div>
            <form name="batch-modify-position-form" id="batch-modify-position-form">
                <div class="modal-body">
                    <div class="form-group row">
                        <div class="col-sm-7">
                            <select   name="attribute[1]" id="batch_product_filter" class="filter_crit select-value form-control">
                                <option value="">Select Item</option>
                                <?php
                                foreach($all_products as $product) { ?>
                                <option value= "<?php echo $product->id; ?>"><?php echo $product->name.' - '.$product->sku_value;?></option>
                                <?php  } ?>
                            </select>
                        </div>
                        <div class="col-sm-5 pull-right">
                            <select name="attributestatus[1]"   class="filter_crit form-control">
                                <option value="1">Permanent</option>
                                <option value="0">Temporary</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <span class="col-sm-7">
                            <input id="batch_capacity" type="text" placeholder="Enter capacity" name="attribute[5]"   class="form-control numeric-field" value=""  >
                        </span>
                        <span class="col-sm-5 pull-right">
                            <select name="attributestatus[5]" class="filter_crit form-control">
                                <option value="1">Permanent</option>
                                <option value="0">Temporary</option>
                            </select>
                        </span>
                    </div>
                    <div class="form-group row">
                        <span class="col-sm-7">
                            <input id="batch_par" placeholder="Enter PAR" type="text" name="attribute[6]" class="form-control numeric-field" value="" />
                        </span>
                        <span class="col-sm-5 pull-right">
                            <select name="attributestatus[6]" class="filter_crit form-control">
                                <option value="1">Permanent</option>
                                <option value="0">Temporary</option>
                            </select>
                        </span>
                    </div>
                    <div class="form-group row">
                        <span class="col-sm-7">
                            <input id="batch_soh" type="text"  placeholder="Enter stock on hand" name="attribute[4]" class="form-control numeric-field" value="" />
                        </span>
                        <span class="col-sm-5 pull-right">
                            <select name="attributestatus[4]" class="filter_crit form-control">
                                <option value="1">Permanent</option>
                                <option value="0">Temporary</option>
                            </select>
                        </span>
                    </div>
                    <div class="form-group row">
                        <span class="col-sm-7">
                            <div class="input-group">
                                <span class="input-group-addon">$</span>
                                <input  placeholder="Price" type="text"  name="attribute[2]" class="form-control price-attr" value="" />
                            </div>
                        </span>
                        <span class="col-sm-5 pull-right">
                            <select name="attributestatus[2]" class="filter_crit form-control">
                                <option value="1">Permanent</option>
                                <option value="0">Temporary</option>
                            </select>
                        </span>
                    </div>
                    <div class="form-group row">
                        <span class="col-sm-7">
                            <select  name="attribute[9]" id="batch_coil" class="filter_crit select-value form-control">
                                <option value="">Select Coil</option>
                                <?php   foreach(POW\Sku::with_category_id(28) as $coil) { ?>
                                <option value= "<?php echo $coil->id;?>"><?php echo $coil->sku_value.'-'.$coil->name;?></option>
                                <?php  } ?>
                            </select>
                        </span>
                        <span class="col-sm-5 pull-right">
                            <select name="attributestatus[9]" class="filter_crit form-control">
                                <option value="1">Permanent</option>
                                <option value="0">Temporary</option>
                            </select>
                        </span>
                    </div>
                    <div class="form-group row">
                        <span class="col-sm-7">
                            <select name="attribute[11]" id="batch_stabiliser" class="filter_crit select-value form-control">
                                <option value="">Select Stabiliser</option>
                                <?php   foreach($stabiliser as $s) { ?>
                                <option value= "<?php echo $s['id'];?>"><?php echo $s['sku_name'].'-'.$s['name'];?></option>
                                <?php  } ?>
                            </select>
                        </span>
                        <span class="col-sm-5 pull-right">
                            <select name="attributestatus[11]"   class="filter_crit form-control">
                                <option value="1">Permanent</option>
                                <option value="0">Temporary</option>
                            </select>
                        </span>
                    </div>
                    <div class="form-group row">
                        <span class="col-sm-7">
                            <select name="attribute[10]" id="batch_pusher" class="filter_crit select-value form-control">
                                <option value="">Select Pusher</option>
                                <?php   foreach($pusher as $p) { ?>
                                <option value= "<?php echo $p['id'];?>"><?php echo $p['sku_name'].'-'.$p['name'];?></option>
                                <?php  } ?>
                            </select>
                        </span>
                        <span class="col-sm-5 pull-right">
                            <select name="attributestatus[10]"   class="filter_crit form-control">
                                <option value="1">Permanent</option>
                                <option value="0">Temporary</option>
                            </select>
                        </span>
                    </div>
                    <div class="form-group row">
                        <span class="col-sm-7">
                            <select   name="attribute[12]" id="batch_platform" class="filter_crit select-value form-control">
                                <option value="">Select Platform</option>
                                <?php   foreach($platform as $pt) { ?>
                                <option value= "<?php echo $pt['id'];?>"><?php echo $pt['sku_name'].'-'.$pt['name'];?></option>
                                <?php  } ?>
                            </select>
                        </span>
                        <span class="col-sm-5 pull-right">
                            <select name="attributestatus[12]"   class="filter_crit form-control">
                                <option value="1">Permanent</option>
                                <option value="0">Temporary</option>
                            </select>
                        </span>
                    </div>
                    <div class="form-group row">
                        <span class="col-sm-7">
                            <select   name="attribute[13]" id="batch_label" class="filter_crit select-value form-control">
                                <option value="">Select Label</option>
                                <option value="1">New Label Required</option>
                                <option value="0">New Label Not Required</option>
                            </select>
                        </span>
                        <span class="col-sm-5 pull-right">
                            <select name="attributestatus[13]"   class="filter_crit form-control">
                                <option value="1">Permanent</option>
                                <option value="0">Temporary</option>
                            </select>
                        </span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" id="batch-modify-position-button" class="btn btn-primary">Queue</button>
                    <span class="btn btn-default" data-dismiss="modal">Cancel</span>
                </div>
            </form>
        </div>
    </div>
</div>
