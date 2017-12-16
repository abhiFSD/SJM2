<?php
$roles = array (
    "1"     => "Super Admin",
    "2"    => "Company Admin",
    "3"     => "User"
);
?>

<script type="text/javascript" charset="utf-8">
$(document).ready(function() {
    $('#items').dataTable({ "lengthMenu": [ 25, 50, 100, 200 ], "pageLength": 200, 
        "columns": [
            { "orderable": true },
            { "orderable": true },
            { "orderable": true },
            { "orderable": true },
            { "orderable": false },
        ]
    });
} );
var filter = function ()
{
    var url = "<?php echo site_url('user/all'); ?>/" + document.getElementById('status').value;
    document.location.href= url;
}
</script>

<div class="row">
    <h3 class="page-header">Users <span class="pull-right">
        &nbsp;&nbsp;<a href="<?php echo site_url('user/edit/0/0'); ?>" class="btn btn-primary">Add New User</a>&nbsp;</span>
    </h3>
    <?php if ($this->session->flashdata('msg') != "") {
        echo '<div class="alert alert-warning">'. $this->session->flashdata('msg') .'</div>';
    } ?>
</div>
<div class="row">
    <?php if(count($users) > 0 ): ?>
        <table class="table table-striped" id="items">
            <thead>
                <tr>
                    <th>Display Name</th>
                    <th>Email Address</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Edit</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($users as $user): ?>
                    <tr>
                        <td><?php echo $user->display_name; ?></td>
                        <td><?php echo $user->email; ?></td>
                        <td><?php echo $user->role_name; ?></td>
                        <td><?php echo $user->status; ?></td>
                        <td><a href="<?php echo site_url('user/edit/'.$user->id.'/'.$user->user_id); ?>" title="Edit User"><i class="fa fa-edit"></i></a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="alert alert-danger">No users found currently</div>
    <?php endif; ?>
</div>
