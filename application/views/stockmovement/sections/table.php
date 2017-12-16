<table class="table table-striped datatable" id="items">
    <thead>
        <tr>
            <th>Movement Date</th>
            <th>Location</th>
            <th>SKU</th>
            <th>SKU Name</th>
            <th>Amount</th>
            <th>SOH</th>
            <th>Movement Type</th>
            <th>Description <br>/ Position</th>
            <th>Movement By</th>
            <th>Date Entered</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!count($items)): ?>
            <tr><td colspan="10">No data.</td></tr>
        <?php endif; ?>
    </tbody>
</table>
<script>
    var table_data = <?php print count($items) ? json_encode($items) : '[]'; ?>;
</script>