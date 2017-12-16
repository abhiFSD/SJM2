<div class="row page-header">
    <div class="col-xs-6">
        <h3 class="margin-0">Job History</h3>
    </div>
    <div class="col-xs-6 text-right">
        Filters &nbsp;
        <input type="checkbox" checked data-toggle="toggle" data-checked-show=".row.filters" data-not-checked-hide=".row.filters" data-size="small">
    </div>
</div>
<div class="row filters margin-bottom-20-px">
    <div class="col-sm-12">
        <?php print $filters; ?>
    </div>
</div>
<div id="jobs" class="datatable" data-sort-column="0" data-sort-direction="desc" data-hide-length="1">
    <?php $this->load->view('stockmovement/sections/jobs', ['transfers' => $transfers, 'completed_jobs_only' => true]); ?>
</div>

