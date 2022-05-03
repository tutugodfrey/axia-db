<!DOCTYPE html">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<link href="/css/bootstrap.min.css" rel="stylesheet" media="all"></link>
		<title>
			<?php
			echo __('Data Warehouse') . ': ';
			echo $this->fetch('title');
			?>
		</title>
	</head>
	<body>
		<div id="container">
			<div id="header" style="margin-top: 6%"/>
			<div class="content">
				<?php echo $this->Html->image("/img/AxiaMedHDnoLoop.gif", array('class' => 'center-block', 'style' => 'width: 300px; margin-bottom: 2%;')); ?>
				<?php echo $this->fetch('content'); 
				?>

			</div>
		</div>
	</body>
</html>
