



<script type="text/javascript" charset="utf-8">
$(document).ready(function() {
    $('#items').dataTable({ "lengthMenu": [ 25, 50, 100, 200 ], "pageLength": 200, fixedHeader: true});
} );
var filter = function ()
{
    var url = "<?php echo site_url('site/all'); ?>/" + document.getElementById('status').value;
    document.location.href= url;
}
</script>

<div class="row">
    <h3 class="page-header">Sites <span class="pull-right">
        <small><label>Status:</label>&nbsp;&nbsp;&nbsp;
            <select name="status" id="status" onchange="filter()" >
                <option value="">Select</option>
                <option value="All" <?php echo $status == 'All'? 'selected="selected"':''; ?> >All</option>
                <option value="Active" <?php echo $status == 'Active'? 'selected="selected"':''; ?> >Active</option>
                <option value="Inactive" <?php echo $status == 'Inactive'? 'selected="selected"':''; ?>>Inactive</option>
            </select>
        </small>
        &nbsp;&nbsp;
        <a href="<?php echo site_url('site/manage'); ?>" class="btn btn-primary">Add Site</a>&nbsp;</span>
</h3>

<?php
    if ($this->session->flashdata('msg') != "") {
        echo '<div class="alert alert-warning">'. $this->session->flashdata('msg') .'</div>';
    }
?>
</div>

<div class="row">
    <?php if (!empty($sites)): ?>
        <table class="table table-striped" id="items">
            <thead>
                <tr>
                    <!-- <th>ID</th> -->
                    <th>Site Name</th>
                    <th>Address</th>
                    <th>Licensor</th>
                    <th>Category</th>
                    <th>Days Per Week</th>
                    <th>Edit</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($sites as $site): ?>
                    <?php
                        $address = "";
                        $address .= ($site->address == ''?'':  $site->address );
                        $address .= ($site->city == ''?'': "<br />". $site->city );
                        $address .= ($site->state == ''?'': " ". $site->state );
                        $address .= ($site->postcode == ''?'': " - ". $site->postcode );
                    ?>
                    <tr>
                        <td><?php echo $site->name; ?></td>
                        <td><?php echo $address ; ?></td>
                        <td><?php echo $site->licensor ; ?></td>
                        <td><?php echo $site->category; ?></td>
                        <td><?php echo $site->days_per_week; ?></td>
                        <td><a href="<?php echo site_url('site/manage/'. $site->id); ?>" title="Edit Site"><i class="fa fa-edit"></i></a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="alert alert-danger">No site found currently</div>
    <?php endif; ?>
</div>