<?php if (empty($users)) { ?>
	<option value=""></option>
	<?php
} else {
	foreach ($users as $key => $value) {
		?>
		<option value="<?php echo $key; ?>">
			<?php echo $value; ?></option>
		<?php
	}
}