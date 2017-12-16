<div class="row page-header">
    <div class="col-xs-6">
        <h3 class="margin-0">Current Jobs</h3>
    </div>
    <div class="col-xs-6 text-right">
        Filters &nbsp;
        <input type="checkbox" data-toggle="toggle" data-checked-show=".row.filters" data-not-checked-hide=".row.filters" data-size="small">
    </div>
</div>
<div class="row filters margin-bottom-20-px">
    <div class="col-sm-12">
        <?php print $filters; ?>
    </div>
</div>
<div id="jobs" class="datatable" data-sort-column="1" data-hide-length="1">
    <?php $this->load->view('stockmovement/sections/jobs', ['transfers' => $transfers]); ?>
</div>
<div class="action-buttons" style="display: none;">
    <div class="row">
        <div class="col-sm-6 col-md-5">
            <div class="btn-group bulk-actions" role="group">
                <button role="group" type="button" class="btn btn-primary dropdown-toggle" disabled data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Bulk Actions <span class="caret"></span>
                </button>
                <ul class="dropdown-menu">
                    <li data-url="<?php print site_url('picks/generate/par'); ?>">
                        <a href="#">Generate Pick To PAR</a>
                    </li>
                    <li data-url="<?php print site_url('picks/generate/capacity'); ?>">
                        <a href="#">Generate Pick To Capacity</a>
                    </li>
                    <li class="transfer-job" data-url="<?php print site_url('stockmovement/transfer'); ?>">
                        <a href="#">Transfer Selected</a>
                    </li>
                    <?php if ($this->session->userdata('role_id') <= 2): ?>
                        <li data-url="<?php print site_url('stockmovement/delete_transfers'); ?>" destructive>
                            <a href="#">Delete Selected</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>

        <div class="col-sm-6 col-md-5">
            <div class="btn-group">
                <button class="btn btn-primary" data-modal="create-job2"
                        data-url="<?php print site_url('stockmovement/create'); ?>"
                        data-backdrop="static"
                        data-keyboard=false>
                    Create Stock Movement
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    window.onload = function (ev) {
        $.fn.modal.prototype.constructor.Constructor.DEFAULTS.keyboard = false;
        $.fn.modal.prototype.constructor.Constructor.DEFAULTS.backdrop = 'static'
    };
</script>