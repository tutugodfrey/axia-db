<?php 
/* Drop breadcrumb */
$this->Html->addCrumb(Inflector::humanize($this->name), ["controller" => $this->name, 'action' => 'main_menu']);
?>
<div class="col-md-5 col-md-offset-1 col-sm-9 ">
	<div class="panel panel-default panel-body modal-content btn-default">
		<a href="/MaintenanceDashboards/content" style="text-decoration:none">
			<div class="media">
				<div class="media pull-left">
						<img class="media-object" src="/img/editDb.png"style="width: 64px; height: 64px;">
				</div>
				<div class="media-body">
					<h3 class="media-heading">Database Metadata and Content Editor</h3>
					<p>This tool allows adding, editing and in some cases deleting database metadata and content used throughout this system. It allows to create and configure things such as products, networks, card types, gateways, and much more. </p>
				</div>
			</div>
		</a>
	</div>
</div>
<div class="col-md-5 col-sm-9">
	<div class="panel panel-default panel-body modal-content btn-default">
		<a href="/Bets/mass_update" style="text-decoration:none">
			<div class="media">
				<div class="media pull-left">
					<img class="media-object" src="/img/menuIconUCP1.png"style="width: 64px; height: 64px;">
				</div>
				<div class="media-body">
					<h3 class="media-heading">User BET's Mass Update tool</h3>
					<p>This tool allows admins to make mass updates of user compensation profiles' Billing Elements Tables (BET) data.</p>
				</div>
			</div>
		</a>
	</div>
</div>
<div class="clearfix"></div>
<div class="col-md-5 col-md-offset-1 col-sm-9 ">
	<div class="panel panel-default panel-body modal-content btn-default">
		<a href="/ApiConfigurations/index" style="text-decoration:none">
			<div class="media">
				<div class="media pull-left">
					<img class="media-object" src="/img/secureApi.png"style="width: 64px; height: 64px;">
				</div>
				<div class="media-body">
					<h3 class="media-heading">External API Connections and Security</h3>
					<p>Configure connections to third party APIs such as connection URLs, user tokens, access tokens (etc...) in order to access resources in tose external APIs.</p>
				</div>
			</div>
		</a>
	</div>
</div>
<div class="col-md-5 col-sm-9">
	<div class="panel panel-default panel-body modal-content btn-default">
		<a href="/Merchants/mid_generator" style="text-decoration:none">
			<div class="media">
				<div class="media pull-left">
					<img class="media-object" src="/img/midGen.png"style="width: 64px; height: 64px;">
				</div>
				<div class="media-body">
					<h3 class="media-heading">AxiaMed MID Number Generator</h3>
					<p>Generate Merchant ID numbers for future use using this tool.</p>
				</div>
			</div>
		</a>
	</div>
</div>