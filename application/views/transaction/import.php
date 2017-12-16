<div class="row">
	<div class="col-md-12">
		<h3>Upload csv file</h3>
		<form method="post" action="<?php print site_url('transaction/import'); ?>" novalidate enctype="multipart/form-data">
			<input type="hidden" name="upload" value="1">
			<input type="file" name="csv">
			<input class="btn btn-primary reactive" type="submit" value="Import">
		</form>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<h3>Result</h3>
		<?php if (!empty($success)): ?>
			<h4>New Transactions <em>(<?php print count($new); ?>)</em></h4>
			<?php $this->load->view('transaction/sections/import_table', ['rows' => $new]); ?>

			<h4>Duplicates <em>(<?php print count($duplicates); ?>)</em></h4>
			<?php $this->load->view('transaction/sections/import_table', ['rows' => $duplicates]); ?>
		<?php elseif (!empty($msg)): ?>
			<p><?php print $msg; ?></p>
		<?php endif; ?>
	</div>
</div>
