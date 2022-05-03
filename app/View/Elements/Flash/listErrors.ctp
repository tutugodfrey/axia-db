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
 	<?php 
 		if(!empty($message)) :
 			echo $this->Html->tag('h5', 'Error(s):', array('class' => 'contentModuleTitle'));
	 		foreach($message as $errStr) :
	?>
				<span class="glyphicon glyphicon-triangle-right"></span> <strong><?php echo h($errStr); ?></strong><br/>
	<?php 	endforeach; ?>
	<?php endif; ?>
</div>
