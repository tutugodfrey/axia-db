<?php
App::uses('NoteType', 'Model');
App::uses('MerchantNote', 'Model');
?>
<nav class="navbar navbar-default">
	<div class="container-fluid">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
		<?php
			echo $this->Html->image("/img/axia.logo.png",
				[
					"url" => "/",
					"class" => "navbar-brand",
					"style" => "width: 100px;height: 60px;padding: 1px 0px 1px 0px;"
				]
			);
		?>
		</div>
		<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
		<?php
			echo $this->Html->tag('div', null, null);
			if ($this->Rbac->isPermitted('app/actions/Merchants/view/module/quickSearch', true)) {
				echo $this->Form->create('Merchant', ['url' => ['action' => 'find' , 'plugin' => false],
					'inputDefaults' => [
						'label' => false,
						'wrapInput' => false,
						'class' => 'form-control'
					],
					'class' => 'navbar-form navbar-left'
				]);
				echo $this->Html->tag('div', null, array('class' => 'input-group'));
				echo $this->Form->input('search', [
					'div' => false,
					'label' => false,
					"placeholder" => "DBA or last 4 of MID", 'title' =>
						'Enter a merchant MID or DBA (not case sensitive).'
				]);

				echo $this->Html->tag('span', null, array('class' => 'input-sm input-group-addon'));
				echo $this->Form->checkbox('active', array('checked' => 'true'));
				echo $this->Html->tag('span', "<strong> Active</strong>", array('class' => 'small', 'style' => 'vertical-align:text-top'));
				echo $this->Html->tag('/span', null);

				echo $this->Html->tag('div', null, array('class' => 'input-group-btn'));
				echo $this->Form->button('Go!',
					array(
						'class' => 'btn btn-sm btn-default',
						'name' => 'Search',
						'type' => 'submit'
					)
				);
				echo $this->Html->tag('/div', null);
				echo $this->Html->tag('/div', null);
				echo $this->Form->end();
			}
			echo $this->Html->tag('/div', null);
		?>
			<ul class="nav navbar-nav">
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Links <span class="caret"></span></a>
					<ul class="dropdown-menu" style="min-width:210px">
					<?php
						$newWinIcon = '<span class="pull-right glyphicon glyphicon-new-window small text-muted text-primary"></span>';
						echo $this->Html->tag('li', $this->Html->link("$newWinIcon Axia Med", 'https://www.axiamed.com/', array('escape' => false, 'target' => '_blank')));
						echo $this->Html->tag('li', $this->Html->link("$newWinIcon Axiapayments.com", 'http://www.axiapayments.com', array('escape' => false, 'target' => '_blank')));
						echo $this->Html->tag('li', $this->Html->link("$newWinIcon Axiapayments.com/rep/", 'http://www.axiapayments.com/rep/', array('escape' => false, 'target' => '_blank')));
						echo $this->Html->tag('li', $this->Html->link("$newWinIcon Online App", 'https://app.axiatech.com/users/login', array('escape' => false, 'target' => '_blank')));
						echo $this->Html->tag('li', $this->Html->link("$newWinIcon Sage Online Reporting", 'https://www.myvirtualreports.com/virtualreports/', array('escape' => false, 'target' => '_blank')));
						echo $this->Html->tag('li', $this->Html->link("$newWinIcon Axia Med Database<br>API Documentation", '/AxiaApiDocs/index.html', array('escape' => false, 'target' => '_blank')));
					?>
					</ul>
				</li>
				<?php if ($this->Rbac->isPermitted('Merchants/view') || $this->Rbac->isPermitted('MerchantNotes/index')) :?>
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Merchants <span class="caret"></span></a>
					<ul class="dropdown-menu">
					<?php
						if ($this->Rbac->isPermitted('Merchants/view')) {
							echo $this->Html->tag('li', $this->Html->link(__('Merchant list'), [
								'plugin' => false,
								'controller' => 'Merchants',
								'action' => 'index',
							]));
						}
						if ($this->Rbac->isPermitted('MerchantNotes/index')) {
							echo $this->Html->tag('li', $this->Html->link(__('Search Notes & Requests'), [
								'plugin' => false,
								'controller' => 'MerchantNotes',
								'action' => 'index',
							]));
						}
					?>
					</ul>
				</li>
			<?php endif; ?>
			<?php if ($this->Rbac->isPermitted('app/actions/Dashboards/view/module/reportsTopNav', true)): ?>
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Reports <span class="caret"></span></a>
					<ul class="dropdown-menu">
					<?php
						if ($this->Rbac->isPermitted('CommissionReports/report')) {
							echo $this->Html->tag('li', $this->Html->link(__('Commission Report'), [
								'controller' => 'CommissionReports',
								'action' => 'report',
								'plugin' => false
							]));
						}

						if ($this->Rbac->isPermitted('ResidualReports/report')) {
							echo $this->Html->tag('li', $this->Html->link('Residual Report', [
								'controller' => 'ResidualReports',
								'action' => 'report',
								'plugin' => false
							]));
						}
						if ($this->Rbac->isPermitted('CommissionPricings/grossProfitReport')) {
							echo $this->Html->tag('li', $this->Html->link(__('Gross Profit Report'), [
								'controller' => 'CommissionPricings',
								'action' => 'grossProfitReport',
								'plugin' => false
							]));
						}
						if ($this->Rbac->isPermitted('CommissionPricings/commission_multiple_analysis')) {
							echo $this->Html->tag('li', $this->Html->link(__('Commission Multiple Analysis'), [
								'controller' => 'CommissionPricings',
								'action' => 'commission_multiple_analysis',
								'plugin' => false
							]));
						}
						if ($this->Rbac->isPermitted('CommissionPricings/gp_analysis')) {
							echo $this->Html->tag('li', $this->Html->link(__('Gross Profit Analysis'), [
								'controller' => 'CommissionPricings',
								'action' => 'gp_analysis',
								'plugin' => false
							]));
						}
						if ($this->Rbac->isPermitted('ProductsAndServices/merchant_products_report')) {
							echo $this->Html->tag('li', $this->Html->link('Merchant Products Report', [
								'controller' => 'ProductsAndServices',
								'action' => 'merchant_products_report',
								'plugin' => false
							]));
						}
						if ($this->Rbac->isPermitted('MerchantCancellations/index')) {
							echo $this->Html->tag('li', $this->Html->link('Cancellation Report', [
								'controller' => 'MerchantCancellations',
								'action' => 'index',
								'plugin' => false
							]));
						}
						if ($this->Rbac->isPermitted('MerchantUws/index')) {
							echo $this->Html->tag('li', $this->Html->link('Underwriting Report', [
								'controller' => 'MerchantUws',
								'action' => 'index',
								'plugin' => false
							]));
						}
						if ($this->Rbac->isPermitted('LastDepositReports/index')) {
							echo $this->Html->tag('li', $this->Html->link('Last Activity Report', [
								'controller' => 'LastDepositReports',
								'action' => 'index',
								'plugin' => false
							]));
						}
						if ($this->Rbac->isPermitted('MerchantRejects/report')) {
							echo $this->Html->tag('li', $this->Html->link('Rejects Report', [
								'controller' => 'MerchantRejects',
								'action' => 'report',
								'plugin' => false
							]));
						}
						if ($this->Rbac->isPermitted('DataBreachBillingReports/report')) {
							echo $this->Html->tag('li', $this->Html->link(__('Data Breach Billing Report'), [
								'controller' => 'DataBreachBillingReports',
								'action' => 'report',
								'plugin' => false
							]));
						}
						if ($this->Rbac->isPermitted('MultipurposeReports/report')) {
							echo $this->Html->tag('li', $this->Html->link('Merchants Export Report', [
								'controller' => 'MultipurposeReports',
								'action' => 'report',
								'plugin' => false
							]));
						}
						if ($this->Rbac->isPermitted('ProfitabilityReports/report')) {
							echo $this->Html->tag('li', $this->Html->link('Profitability Report', [
								'controller' => 'ProfitabilityReports',
								'action' => 'report',
								'plugin' => false
							]));
						}
						if ($this->Rbac->isPermitted('MerchantAches/accounting_report')) {
							echo $this->Html->tag('li', $this->Html->link('Accounting Report', [
								'controller' => 'MerchantAches',
								'action' => 'accounting_report',
								'plugin' => false
							]));
						}
						if ($this->Rbac->isPermitted('ImportedDataCollections/report')) {
							echo $this->Html->tag('li', $this->Html->link('Data Collection Report', [
								'controller' => 'ImportedDataCollections',
								'action' => 'report',
								'plugin' => false
							]));
						}
					?>
					</ul>
				</li>
			<?php endif; ?>
			<?php if ($this->Rbac->isPermitted('Users/edit')): ?>
				<li>
					<?php
					echo $this->Html->link(__('User List'), [
							'controller' => 'users',
							'action' => 'index',
							'plugin' => false
						])
					?>
				</li>
			<?php endif;
			if ($this->Rbac->isPermitted('EquipmentItems/index') || $this->Rbac->isPermitted('Orders/index')): ?>
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Equipment <span class="caret"></span></a>
					<ul class="dropdown-menu">
						<?php
						if ($this->Rbac->isPermitted('EquipmentItems/index')) {
							echo $this->Html->tag('li', $this->Html->link(__('Equipment list'), [
								'controller' => 'EquipmentItems',
								'action' => 'index',
								'plugin' => false
							]));
						}

						/* Only users with admin permission can see this */
						if ($this->Rbac->isPermitted('Orders/index')) {
							echo $this->Html->tag('li', $this->Html->link(__('Inventory'), [
								'controller' => 'Orders',
								'action' => 'index',
								'plugin' => false
							]));
						}
						?>
					</ul>
				</li>
			<?php endif; ?>
				<?php if (($this->Rbac->isPermitted('Users/edit') && $this->Rbac->isPermitted('Merchants/edit')) || $this->Rbac->isPermitted('Merchants/upload')) : ?>
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Admin <span class="caret"></span></a>
					<ul class="dropdown-menu">
						<?php
						echo $this->Html->tag('li', $this->Html->link(__('User activity'), [
							'controller' => 'system_transactions',
							'action' => 'userActivity',
							'plugin' => false
						]));
						if ($this->Rbac->isPermitted('app/actions/Rbac/Perms/security_roles')) {
							echo $this->Html->tag('li', $this->Html->link(__('Rbac Admin GUI'), [
								'controller' => 'perms',
								'action' => 'index',
								'plugin' => 'rbac'
							]));
						}
						if ($this->Rbac->isPermitted('Merchants/upload')) {
							echo $this->Html->tag('li', $this->Html->link('Merchant Upload', [
								'controller' => 'Merchants',
								'action' => 'upload',
								'plugin' => false
							]));
						}
						if ($this->Rbac->isPermitted('MerchantPricingArchives/index')) {
							echo $this->Html->tag('li', $this->Html->link(__('Merchant pricing archive'), [
								'controller' => 'MerchantPricingArchives',
								'action' => 'index',
								'plugin' => false
							]));
						}
						if ($this->Rbac->isPermitted('ResidualReports/upload')) {
							echo $this->Html->tag('li', $this->Html->link(__('Residual admin'), [
								'controller' => 'ResidualReports',
								'action' => 'upload',
								'plugin' => false
							]));
						}
						if ($this->Rbac->isPermitted('CommissionReports/build')) {
							echo $this->Html->tag('li', $this->Html->link(__('Commission admin'), [
								'controller' => 'CommissionReports',
								'action' => 'build',
								'plugin' => false
							]));
						}
						echo $this->Html->tag('li', $this->Html->link(__('Rejects Upload'), [
							'controller' => 'merchantRejects',
							'action' => 'import',
							'plugin' => false
						]));
						if ($this->Rbac->isPermitted('LastDepositReports/upload')) {
							echo $this->Html->tag('li', $this->Html->link(__('Last Activity Report Upload'), [
								'controller' => 'LastDepositReports',
								'action' => 'upload',
								'plugin' => false
							]));
						}
						if ($this->Rbac->isPermitted('ProfitabilityReports/import')) {
							echo $this->Html->tag('li', $this->Html->link(__('Profitability Data Import'), [
								'controller' => 'ProfitabilityReports',
								'action' => 'import',
								'plugin' => false
							]));
						}
						if ($this->Rbac->isPermitted('ImportedDataCollections/upload')) {
							echo $this->Html->tag('li', $this->Html->link(__('Data Collection Upload'), [
								'controller' => 'ImportedDataCollections',
								'action' => 'upload',
								'plugin' => false
							]));
						}
						if ($this->Rbac->isPermitted('MaintenanceDashboards/main_menu')) {
							echo $this->Html->tag('li', $this->Html->link(__('System Maintenance Dashboard'), [
								'controller' => 'MaintenanceDashboards',
								'action' => 'main_menu',
								'plugin' => false
							]));
						}
						if ($this->Rbac->isPermitted('Documentations/help')) {
							echo $this->Html->tag('li', $this->Html->link(__('Help & Documentation'), [
								'controller' => 'Documentations',
								'action' => 'help',
								'plugin' => false
							]));
						}
						?>
					</ul>
				</li>
				<?php endif; ?>
			</ul>
			<ul class="nav navbar-nav navbar-right">
				<?php
					echo $this->Html->tag('li', $this->Html->link($this->Session->read('Auth.User.loggedInUser'), [
						'controller' => 'Users',
						'action' => 'view',
						'plugin' => false,
						$this->Session->read('Auth.User.id')
						]));
				?>
				<?php
					echo $this->Html->tag('li', $this->Html->link(
						__('Logout'),
						[
							'controller' => 'Users',
							'action' => 'logout',
							'plugin' => false
						]
					), ['class' => 'strong text-primary bg-info roundEdges']);
				?>
			</ul>
		</div><!-- /.navbar-collapse -->
	</div><!-- /.container-fluid -->
</nav>
