<div class="row page-header">
    <div class="col-sm-9">
        <h3 class="margin-0">Inventory Locations</h3>
    </div>
    <div class="col-sm-3 text-right">
        <?php if ($this->session->userdata('role_id') <= 2): ?>
            <a href="<?php echo site_url('inventorylocation/manage'); ?>" class="btn btn-primary">Add New Inventory Location</a>
        <?php endif; ?>
    </div>
</div>

<?php if (!count($inventory_locations)): ?>
    <div class="alert alert-danger hidden">No Inventory Location found currently</div>
<?php endif; ?>

<div class="datatable">
    <table class="table table-striped" id="items">
        <thead>
            <tr>
                <th>Location Name</th>
                <th>Address</th>
                <th>Status</th>
                <th>Edit</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($inventory_locations as $location): ?>
                <?php 
                $address = "";
                $address .= ($location->street_address_1 == ''?'':  $location->street_address_1 );
                $address .= ($location->street_address_2 == ''?'': "<br />". $location->street_address_2 );
                $address .= ($location->suburb == ''?'': " ". $location->suburb );
                $address .= ($location->post_code == ''?'': " - ". $location->post_code );
                ?>
                <tr>
                    <td><?php echo $location->name; ?></td>
                    <td><?php echo $address ; ?></td>
                    <td><?php echo $location->active?"Active":"Inactive" ; ?></td>
                    <td><a href="<?php echo site_url('inventorylocation/manage/'. $location->id); ?>" title="Edit Location"><i class="fa fa-edit"></i></a></td>
                </tr>
           <?php endforeach; ?>
         </tbody>
     </table>
 </div>
 