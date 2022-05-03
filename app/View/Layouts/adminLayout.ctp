<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
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
			
			<div class="index">
				<?php
				/* Show breadcrumb trail */
				echo $this->Html->getCrumbs($this->Html->image('right-arrow.jpg'), array(
					'text' => 'Home',
					'url' => array('controller' => 'Dashboards', 'action' => 'home'),
					'escape' => false
				));
				$pTitleInner =  $this->Html->tag('h3', "Maintenance Dashboard " . Inflector::humanize($this->action), ["class" => "panel-title"]);
				$pTitle = $this->Html->tag('div', $pTitleInner, ["class" => "panel-heading"]);				
				$content = $this->Session->flash();
				$content .= $this->fetch('content');
				$pBody = $this->Html->tag('div', $content, ["class" => "panel-body"]);
				echo $this->Html->tag('div', $pTitle . $pBody, ["class" => "panel panel-primary"]);
				?>				
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
