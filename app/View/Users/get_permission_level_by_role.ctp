<?php if (empty($permissions)) { ?>
	<option value=""></option>
	<?php
} else {
	foreach ($permissions as $key => $value) {
		?>
		<option value="<?php echo $key; ?>">
			<?php echo $value; ?></option>
		<?php
	}
}