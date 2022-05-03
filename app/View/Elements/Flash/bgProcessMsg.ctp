<?php
if (!isset($class)) {
	$class = false;
}
if (!isset($close)) {
	$close = true;
}

?>
<div class="alert<?php echo ($class) ? ' ' . $class : null; ?>">
<?php if ($close): ?>
	<a class="close" data-dismiss="alert" href="#">×</a>
<?php endif; ?>
	<h5><strong><?php echo h($message); ?></strong></h5>
</div>
