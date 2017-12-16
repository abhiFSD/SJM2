<table class="table table-striped" id="items">
    <thead>
        <tr>
            <th>Site Name</th>
            <th>Location Name</th>
            <th>Location Description</th>
            <th>Sales Multiplier</th>
            <th width="1%">Edit</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($kiosks as $kiosk): ?>
            <tr>
                <td><?php echo $kiosk->sitename; ?></td>
                <td><?php echo $kiosk->name; ?></td>
                <td><?php echo $kiosk->location_within_site; ?></td>
                <td class="text-right"><?php echo $kiosk->sales_multiplier; ?></td>
                <td class="text-right">
                    <a href="<?php echo site_url('kiosklocation/manage/'. $kiosk->id); ?>" title="Edit Kiosk"><i class="fa fa-edit"></i></a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
