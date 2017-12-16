<?php if ($this->session->flashdata('error_message')): ?>
    <div class="alert alert-danger hidden">
        <a href="#" class="close" data-dismiss="alert">&times;</a>
        <?php print $this->session->flashdata('error_message'); ?>
    </div>
<?php endif; ?>
<?php if ($this->session->flashdata('warning_message')): ?>
    <div class="alert alert-warning hidden">
        <a href="#" class="close" data-dismiss="alert">&times;</a>
        <?php print $this->session->flashdata('warning_message'); ?>
    </div>
<?php endif; ?>
<?php if ($this->session->flashdata('success_message')): ?>
    <div class="alert alert-success hidden">
        <a href="#" class="close" data-dismiss="alert">&times;</a>
        <?php print $this->session->flashdata('success_message'); ?>
    </div>
<?php endif; ?>
