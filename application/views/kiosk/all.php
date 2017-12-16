<div class="row page-header">
    <div class="col-sm-9">
        <h3 class="margin-0">Kiosks</h3>
    </div>
    <?php if ($this->session->userdata('role_id') <= 2): ?>
        <div class="col-sm-3 text-right">
            <a href="<?php echo site_url('kiosk/manage'); ?>" class="btn btn-primary">Add Kiosk</a>
        </div>
    <?php endif; ?>
</div>

<div class="datatable" data-sort-column="0">
    <table class="table table-striped dataTable">
        <thead>
            <tr>
                <th>Kiosk / Kiosk Number</th>
                <th>Model</th>
                <th>Status</th>
                <th width="1%">Edit</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($kiosks as $kiosk): ?>
                <tr>
                    <td><?php echo $kiosk->number; ?></td>
                    <td><?php echo $kiosk->model_name; ?></td>
                    <td><?php echo $kiosk->status; ?></td>
                    <td><a href="<?php echo site_url('kiosk/manage/'. $kiosk->id); ?>" title="Edit Configuration"><i class="fa fa-edit"></i></a>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
