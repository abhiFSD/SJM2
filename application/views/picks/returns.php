<div class="row">
    <div class="col-md-12">
        <h3 class="page-header">Warehouse Returns</h3>
        <h4><?php echo $kiosk['number'].' - '. $kiosk['location_name']?></h4>
        <h4>Pick ID <?php echo $transfer->id; ?></h4>
        <br><br>
    </div>

    <form name="pick-form" id="atkiosk-pick">
        <input type="hidden" name="transfer_id" value="<?php echo $transfer->id ?>">

        <div class="col-md-12">
            <div class="single-kiosk-table">
                <h4>Returned Products</h4>
                <table id="table-stock-kiosk" class="table table-bordered actions hide-sorting dataTable hide-sorting">
                    <thead>
                        <tr>
                            <th>Pos</th>
                            <th>SKU</th>
                            <th>Picked</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $item): ?>
                            <tr id="replenish-<?php print $item->position; ?>" data-position="<?php print $item->position; ?>" class="replenish">
                                <td class="emphasize-small" scope="row"><?php echo $item->position; ?></td>
                                <td class="emphasize-small" scope="row"><div class="fixwidth"><?php echo $item->name; ?> </div></td>
                                <td class="emphasize-small text-right"><?php echo $item->pick_quantity; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="single-kiosk-table">
                <div class="form-group responsive-button">
                    <input type="button" class="btn btn-primary dosubmit" value="Submit" name="save">&nbsp;  
                    <a href="javascript:history.go(-1)" class="btn ">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>
