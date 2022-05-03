<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<?php echo $this->Html->charset(); ?>
		<link href="/css/bootstrap.min.css" rel="stylesheet" media="all"></link>
		<link rel="stylesheet" href="/jquery-ui-1.13.1/jquery-ui.theme.min.css"></link>
		<link rel="stylesheet" type="text/css" href="/jquery-ui-1.13.1/jquery-ui.css"></link>
		<title>
			<?php
			echo __('Data Warehouse') . ': ';
			echo $this->fetch('title');
			?>
		</title>
		<?php
		echo $this->Html->meta('icon');
		echo $this->fetch('meta');
		echo $this->AssetCompress->css('default', array(
			'raw' => (bool)Configure::read('debug')
		));
		echo $this->AssetCompress->includeCss();
		echo $this->fetch('css');
		echo $this->Html->script('/js/jquery-2.2.4.min.js');
		echo $this->Html->script('/jquery-ui-1.13.1/jquery-ui.min.js');
		echo $this->Html->script('/js/bootstrap.min.js');
		?>
	</head>
	<body>
		<div id="header">
			<?php echo $this->element('/Layout/quickNavigation'); ?>
		</div>

		<div class="mainContainer">
			<div class="container-fluid">
  				<div class="row">
					<?php 
					$showMerchNav = !empty($merchant['Merchant']['id']);
					$showUserNav = (!empty($user['User']['id']) || ($this->name === 'Users' && $this->action !== 'index' && strpos($this->action, 'editResidual') === false));
					if ($showMerchNav || $showUserNav || isset($displayIconLegend)) {
						echo $this->element('/Layout/leftSideNav', array(
							'showMerchNav' => $showMerchNav, 
							'showUserNav' => $showUserNav
						));
					}
					?>
					<?php
					$tmpClass = (
						$showMerchNav ||
						!empty($user['User']['id']) ||
						(!empty($this->data['User']['id']) && $this->action !== 'editMany' && strpos($this->action, 'editResidual') === false && strpos($this->action, '_equipment_invoice') === false) ||
						!empty($displayIconLegend)
					) ? "col-xs-9 col-sm-9 col-md-10" : "index";
					?>
					<div id="mainContentPane" class="<?php echo $tmpClass; ?>" >
						<span class="hidden-print">
							<?php
							/* Show breadcrumb trail */
							echo $this->Html->getCrumbs($this->Html->image('right-arrow.jpg'), array(
								'text' => 'Home',
								'url' => array('controller' => 'Dashboards', 'action' => 'home'),
								'escape' => false
							));
							?>
						</span>
						<div class="panel panel-primary">
							<div class="panel-heading strong">
								<div id="layoutViewTitle"></div>
							</div>
							<div class="panel-body">
								<?php echo $this->Session->flash('auth'); ?>
								<?php echo $this->Session->flash(); ?>
								<?php echo $this->fetch('content'); ?>
							</div>
						</div>

						
					</div>
				</div>
			</div>
		</div>

		<div class="push"></div>
		<div class="footer">
			<?php echo $this->element('/Layout/footer'); ?>
		</div>
		<?php
		// Application url paths for JS files
		$merchantsSearchUrl = Router::url(array(
			'plugin' => false,
			'controller' => 'Merchants',
			'action' => 'autoCompleteSuggestions'
		));
		$jsConfigVariables = "var appMerchantsSearchUrl = '{$merchantsSearchUrl}';";
		echo $this->Html->scriptBlock($jsConfigVariables);
		echo $this->AssetCompress->script('default', array(
			'raw' => (bool)Configure::read('debug')
		));
		echo $this->AssetCompress->includeJs();
		echo $this->fetch('script');
		echo $this->Js->writeBuffer();
		?>
	</body>
</html>
