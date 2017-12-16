<?php
$roles = [
    1 => 'Super Administrator',
    2 => 'Company Administrator',
    3 => 'User',
];
$status = [
    1 => 'Active',
    0 => 'Inactive',
];
?>
<div class="row">
    <h3 class="page-header"><?php echo $action; ?> User <span class="pull-right"><a
                href="<?php echo site_url('user/all'); ?>" class="btn btn-primary">View All Users</a>&nbsp;</span></h3>
    <?php
    if (!empty($msg)) {
        echo '<div>' . $msg . '</div>';
    }
    ?>
    <?php echo validation_errors(); ?>
    <form method="post" id="form" autocomplete="off">
        <div class="col-md-12">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Display Name:</label>
                    <input type="text" name="display_name" class="form-control"
                           value="<?php echo !empty($party->display_name) ? $party->display_name : $user->display_name; ?>"
                           required>
                </div>
                <div class="form-group">
                    <label>Email Address:</label>
                    <input type="text" name="email_address" class="form-control"
                           value="<?php echo empty($party->email) ? $user->email_address : $party->email; ?>" required
                           autocomplete="off">
                </div>
                <?php if ($action == 'Add'): ?>
                    <div class="form-group">
                        <label>Password:</label>
                        <input type="password" name="password" class="form-control" value="<?php echo $password; ?>"
                               required autocomplete="off">
                        <strong>Password: </strong><?php echo $password; ?>
                    </div>
                <?php else: ?>
                    <div class="form-group">
                        <label>Password:</label>
                        <input type="password" name="password" class="form-control" value="" autocomplete="off">
                        <small>Type a new password to reset password</small>
                    </div>
                <?php endif; ?>
                <div class="form-group">
                    <label>Role:</label>
                    <?php print form_dropdown('role_id', $roles, $role_id, 'required class="form-control"'); ?>
                </div>
                <!-- added warehouse -->
                <div class="form-group">
                        <label>Default Warehouse:</label>
                        <select name="inventory_location_id" required class=" form-control">
                                <option value="">Select a warehouse</option>
                                <?php
                                    foreach ($locations as $location) {
                                            $selected = "";
                                        if ($location->id == $inventory_location_id) {
                                                    $selected = "selected";
                                            }
                                        echo '<option value="'.$location->id .'"  '.$selected.'>'.$location->name.'</option>';
                                    }
 					?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Status:</label>
                    <?php print form_dropdown('party_status', $status, $party_status, 'required class="form-control"'); ?>
                </div>
                <div class="form-group">
                    <input type="submit" class="btn btn-primary" value="Save" name="action"/>&nbsp;<a
                        href="<?php echo site_url('user/all'); ?>" class="btn ">Cancel</a>
                </div>
            </div>
            <div class="col-md-6">
           </div>
        </div>
    </form>
</div>
