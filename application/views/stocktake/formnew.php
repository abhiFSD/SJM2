<div class="row">
    <div class="col-md-12">
        <form action="" method="post" id="stockTakeNewForm" novalidate>
            <div class="form-group">
                <h2>Stocktake Form</h2>
                <div class="row">
                    <div class="col-md-6">
                        <label>Location: *</label>
                        <select id="warehouse" name="warehouse" onchange="stockTakeNewChangeWarehouse()" class="form-control" required="true">
                            <option value="">Select Warehouse</option>
                            <?php foreach ($warehouses as $warehouse)
                            { ?>
                                <option value="<?php echo $warehouse['id'] ?>"><?php echo $warehouse['name'] ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label>Date of Stocktake: *</label>
                        <?php
                        $date = date("Y-m-d");
                        $time = date("H:i:s");
                        $time .= ".000";
                        ?>
                        <input type="datetime-local" name="date" id="date" class="form-control" required="true" value="<?php echo $date . "T" . $time; ?>" step="1"/>
                    </div>
                </div>
                <div class="m-tb-20-px">
                    <div id="typefilter" class="hidden">
                        <label>Filter Items </label>
                        <select name="type[]" id="stockTakeNewpdttype" onchange="stockTakeNewChangeWarehouse()" multiple>
                            <option value="Product">Product</option>
                            <option value="Equipment">Equipment</option>
                            <option value="Part">Part</option>
                            <option value="Material">Material</option>
                        </select>
                    </div>
                    <div id="productresults">
                    </div>
                </div>
            </div>
        </form>
        <input type="hidden" name="chkDate" id="stockTakeNewIsFutureDate" value="<?php echo date('Y-m-d H:i:s'); ?>">
    </div>
</div>