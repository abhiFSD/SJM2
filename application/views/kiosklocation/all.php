<div class="row page-header">
    <div class="col-sm-6">
        <h3 class="margin-0">Kiosk Locations</h3>
    </div>
    <div class="col-sm-6 text-right">
        <form method="post" action="<?php print site_url('kiosklocation/all'); ?>" get-html interactive class="form-inline" data-target="#kiosklocations">
            <input type="hidden" name="_thisisapost" value="thisisapost">
            <div class="form-group">
                <label>Status &nbsp;</label>
                <?php print form_dropdown('status', $options, $status, 'class="form-control"'); ?>
                &nbsp;
                <?php if ($this->session->userdata('role_id') <= 2): ?>
                    <a href="<?php echo site_url('kiosklocation/manage'); ?>" class="btn btn-primary">Add Location</a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<?php if (!count($kiosks)): ?>
    <div class="alert alert-danger hidden">No kiosk location found currently</div>
<?php endif; ?>

<div id="kiosklocations" class="datatable" data-sort="0">
    <?php $this->load->view('kiosklocation/sections/table', ['kiosks' => $kiosks]); ?>
</div>
