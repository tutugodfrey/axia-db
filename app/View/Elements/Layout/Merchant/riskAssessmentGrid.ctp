	<div >
		<div class="panel panel-info">
			<!-- Default panel contents -->
			<div class="panel-heading">
				<a href="javascript:void(0)" onClick='objSlider("RiskGrid", 500); rotateThis(document.getElementById("rsTwisty"), 180, 500)'>
					<span id="rsTwisty" class="glyphicon glyphicon-chevron-down"></span>
					&nbsp; Risk Assessment Grid
				</a>&nbsp;
				<?php if ($this->Rbac->isPermitted('UsersProductsRisks/editMany')) {
						echo $this->Html->image("/img/icon_pencil_small.gif", array('url' => array('controller' => 'UsersProductsRisks', 'action' => 'editMany', $merchant['Merchant']['id'])));
					}
				?>
			</div>
			<table class="table table-condensed table-hover" id="RiskGrid" style="display:none">
				<tr>
					<th class="text-center">Product Name:</th>
					<th class="text-center" style="border-right-style:ridge;border-right-color:white" colspan="2">
						<?php echo (!empty($merchant['Merchant']['partner_id']))?"Partner Rep":"Rep";?>
					</th>
					<th class="text-center" style="border-right-style:ridge;border-right-color:white" colspan="2">Mgr:</th>
					<th class="text-center" style="border-right-style:ridge;border-right-color:white" colspan="2">Mgr2:</th>
					<th class="text-center" style="border-right-style:ridge;border-right-color:white" colspan="2">Partner:</th>
					<th class="text-center" style="border-right-style:ridge;border-right-color:white" colspan="2">Referrer:</th>
					<th class="text-center" colspan="2">Reseller:</th>
				</tr>
				<tr>
					<th><!-- SPACER --></th>
					<td class="text-center">Rate:</td>
					<td class="text-center" style="border-right-style:ridge;border-right-color:white">Per Item:</td>
					<td class="text-center">Rate:</td>
					<td class="text-center" style="border-right-style:ridge;border-right-color:white">Per Item:</td>
					<td class="text-center">Rate:</td>
					<td class="text-center" style="border-right-style:ridge;border-right-color:white">Per Item:</td>
					<td class="text-center">Rate:</td>
					<td class="text-center" style="border-right-style:ridge;border-right-color:white">Per Item:</td>
					<td class="text-center">Rate:</td>
					<td class="text-center" style="border-right-style:ridge;border-right-color:white">Per Item:</td>
					<td class="text-center">Rate:</td>
					<td class="text-center" style="border-right-style:ridge;border-right-color:white">Per Item:</td>
				</tr>
				<?php foreach($riskData as $pName => $dat):?>
				<tr>
					<td>
						<?php echo h($pName)?>
					</td>
						<?php foreach($dat as $userRisk):?>
						<td>
							<?php echo !empty($userRisk)? "<h5><span class='label label-success'>".$this->Number->toPercentage( $userRisk['risk_assmnt_pct'])."</span></h5>" : '--'; ?>
						</td>
						<td style="border-right-style:ridge;border-right-color:white">
							<?php echo !empty($userRisk)? "<h5><span class='label label-info'>".$this->Number->currency($userRisk['risk_assmnt_per_item'],'USD', array('after' => false))."</span></h5>" : '--'; ?>
						</td>
						<?php  endforeach;?>
				</tr>
				<?php endforeach; ?>
			</table>
		</div>
	</div>