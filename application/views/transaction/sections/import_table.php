<table class="table table-striped">
	<thead>
		<tr>
			<th>ID</th>
			<th>Date Authorised</th>
			<th>Kiosk</th>
			<th>Position</th>
			<th>SKU</th>
			<th>Amount</th>
			<th>Currency</th>
			<th>Payment Method</th>
			<th>Card Method</th>
			<th>Card Type</th>
			<th>First 4 Digits</th>
			<th>Last 4 Digits</th>
			<th>Confirmation Number</th>
			<th>Remarks</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($rows as $row): ?>
			<tr>
				<td><?php print $row['id']; ?></td>
				<td><?php print $row['date_authorised']; ?></td>
				<td><?php print $row['kiosk_number']; ?></td>
				<td><?php print $row['position']; ?></td>
				<td><?php print $row['sku_value']; ?></td>
				<td><?php print $row['amount']; ?></td>
				<td><?php print $row['currency']; ?></td>
				<td><?php print $row['payment_method']; ?></td>
				<td><?php print $row['card_method']; ?></td>
				<td><?php print $row['card_type']; ?></td>
				<td>Hidden<?php //print $row['first_4_digits']; ?></td>
				<td>Hidden<?php //print $row['last_4_digits']; ?></td>
				<td><?php print $row['confirmation_number']; ?></td>
				<td><?php print $row['remarks']; ?></td>
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>
