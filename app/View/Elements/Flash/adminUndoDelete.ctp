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
	<h5><strong><?php echo h($message); ?>&nbsp;&nbsp;</strong>
		<?php
			echo $this->Html->link('<span class="btn btn-xs btn-primary">UNDO <img src="/img/undoLg.png" style="height:20px"></span>', ['controller' => $controller, 'action' => $action, $id, $model], ['escape' => false]);
		?>
	 </h5>
</div>
