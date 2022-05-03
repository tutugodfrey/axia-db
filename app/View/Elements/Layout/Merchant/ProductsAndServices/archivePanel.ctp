
<?php
echo $this->AssetCompress->css('custom-form-inputs', array(
		'raw' => (bool)Configure::read('debug')
	));
$ddLinks = ''; // Define vars
/* HTML link uses Bootstrap attributes */
$lnkOptions = array('role' => 'menuitem','tabindex' => '-1');

if (empty($archivedProds)){
	$ddLinks = '<li class="disabled label-warning">' . $this->Html->link('None at this time', '#archiveContent', $lnkOptions) . '</li>';
} else {
	foreach ($archivedProds as $pId => $pName) {
		$lnkOptions['onClick'] = "renderContentAJAX('', '', '', 'archiveEditMenuContainer', '/MerchantPricingArchives/ajaxShowArchiveEditMenu/{$merchant['Merchant']['id']}/$pId')";
		$ddLinks .= '<li class="small">' . $this->Html->link(trim($pName), 'javascript:void(0)', $lnkOptions) . '</li>';
	}
}
?>
<div class="col-md-5">
	<div class="panel panel-primary">
	    <div class="contrTitle">
	        <h3 class="panel-title" style="display:inline">
				Merchant Pricing Archive <a name="archiveContent">&nbsp;</a></h3> &nbsp;
			<!--         Single button -->
	        <ul class="nav navbar-nav navbar-right" >
	            <li id="fat-menu" class="dropdown dropup">
					<a href="#" id="drop1" role="button" class="dropdown-toggle" data-toggle="dropdown" style="display:inline;padding-bottom: 0; background: none; color:white">Options <b class="glyphicon glyphicon-chevron-up"></b></a>
					<ul class="dropdown-menu" role="menu" aria-labelledby="drop1" style="overflow:auto; max-height:10cm">
						<li><?php echo $this->Html->link(
							$this->Html->tag('div', 
								'Open Archive Generator',
								array('class' => "text-center strong btn-primary btn-sm row", 'onClick' => "renderContentAJAX('', '', '', 'archiveEditMenuContainer', '/MerchantPricingArchives/createManyMenu/{$merchant['Merchant']['id']}')")),
							'javascript:void(0)', array('escape' => false, 'role' => 'menuitem','tabindex' => '-1')); ?>
						</li>
						<li role="separator" class="divider"></li>
						<li role="presentation" class="dropdown-header small">Edit Archived Products:</li>
						<?php echo $ddLinks ?>
					</ul>
	            </li>
	        </ul>
	    </div>
	    <div class="panel-body">
	        <!-- Tab content pane -->
	        <div class='tab-content' id="archiveEditMenuContainer">
				<div class="list-group-item text-center text-muted">(Select item from the options menu)</div>
	        </div> <!-- Close Tab panes-->
	    </div>
	</div>
</div>

