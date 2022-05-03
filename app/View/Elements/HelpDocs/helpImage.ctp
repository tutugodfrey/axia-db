<?php
	$defaultClass = ' img-thumbnail center-block';
	if(isset($class)) {
		$defaultClass = $class .= $defaultClass;
	}

	echo $this->Html->image("/img/help/$imageFile", ['class' => $defaultClass]);
?>