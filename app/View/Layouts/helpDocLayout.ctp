<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<link href="/css/bootstrap.min.css" rel="stylesheet" media="screen"></link>
		<link rel="stylesheet" href="/jquery-ui-1.13.1/jquery-ui.theme.min.css"></link>
		<link rel="stylesheet" type="text/css" href="/jquery-ui-1.13.1/jquery-ui.css"></link>
		<?php
			echo $this->Html->script('/js/jquery-2.2.4.min.js');
			echo $this->Html->script('/jquery-ui-1.13.1/jquery-ui.min.js');
			echo $this->Html->script('/js/bootstrap.min.js');
		?>
		<title>
		<?php
			echo __('Data Warehouse Help');
		?>
		</title>
	</head>
	<body>
		<div id="header">
			<?php echo $this->element('/Layout/quickNavigation'); ?>
		</div>
		<div class="container-fluid">
			<div class="row">
				<?php 
				echo $this->Element('/HelpDocs/helpSideNav'); ?>
				<div class="col-sm-offset-3 col-sm-9 col-lg-offset-2 col-lg-10">
				  <!-- view page content -->
					<div class="panel panel-default">
						<div class="panel-heading"><strong><?php echo __('Database Site Help')?></strong></div>
						<?php 
							echo $this->Session->flash(); 
							echo $this->fetch('content');
						?>
					</div>
				</div>
			</div>
		</div>
		<div class="bg-info footer text-center">
			<?php echo $this->element('/Layout/footer'); ?>
		</div>
	</body>
</html>
