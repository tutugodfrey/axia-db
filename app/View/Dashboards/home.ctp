<?php
/**
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       Cake.View.Pages
 * @since         CakePHP(tm) v 0.10.0.1076
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
//When in production mode this will throw an error unless commented.
App::uses('Debugger', 'Utility');
?>

<input type="hidden" id="thisViewTitle" value="Axia Intranet | Home" />
<?php if (isset($isLogin) && $isLogin === true) : 
		echo $this->element('Dashboard/welcome'); 
		if (empty($this->session->read('Auth.User.secret')) && $this->session->read('Auth.User.opt_out_2fa') != true): ?>
		<div class="modal fade" id="myModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-dialog" id="ModalContainer">
				<div class="panel panel-info">
					<div class="panel-heading"><div class="panel-title strong"> <span class="glyphicon glyphicon-lock"></span> Two-Factor Authentication</div></div>
					<div class="panel-body">
						<p class="panel-title">Please help keep our system secured by enabling two-factor authentication on your account.</p>
					</div>
					<div class="panel-footer text-center">
						<?php
							echo $this->Form->button('Remind me again later', ['type' => 'button', 'data-dismiss' => 'modal', 'class' => 'btn btn-default']);
							echo $this->Form->postLink("<span class='glyphicon glyphicon-ban-circle'></span> Opt-out",
								['controller' => 'Users', 'action' => 'turn_off_2FA', $this->session->read('Auth.User.id'), true],
								['class' => 'btn btn-danger', 'escape' => false, 'confirm' => __("This is not recommended! Are you sure?")]
							);
							echo $this->Html->link('OK', ['controller' => 'Users', 'action' => 'secret'], ['class' => 'btn btn-success']);
						?>
					</div>
				</div>
			</div>
		</div>
		<script>
		$(document).ready(function(){
			$("#myModal").modal();
		});
		</script>
	<?php endif;?>
<?php endif;?>
<script type="text/javascript" src="/js/charts/Chart.bundle.js"></script>

<div class="col-md-12 panel panel-primary">
	<div class="col-md-offset-1 col-sm-offset-1 col-md-3 col-sm-4 ">
		<?php echo $this->element('Dashboard/charts/piChart', ['piChartData' => $mActivityStats])?>
	</div>
	<div class="col-md-offset-1 col-md-6 col-sm-7">
		<?php echo $this->element('Dashboard/charts/barChart', ['acquiringMerchCount' => $acquiringMerchCount])?>
	</div>
</div>
<table class="table-condensed" >
	<tr>
		<td>
			<!-- ******Recently added Merchants Container DIV -->
			<div class="panel panel-primary">
				<div class="panel-heading">Recently added merchants</div>
					<!-- ******Recently added Merchants Data Table -->
					<table class="table table-condensed table-hover table-striped">
						<tr>
							<th><?php echo 'Merchant'; ?></th>
							<th><?php echo 'Date Added'; ?></th>
						</tr>

						<?php if (empty($recentlyAdded)) : ?>
							<tr>
								<td colspan="2" ><div class="noresults roundEdges "><?php echo "No recent data available at this time"; ?></div>&nbsp;</td>
							</tr>
						<?php endif; ?>
						<?php foreach ($recentlyAdded as $merchant): ?>
							<tr>
								<td><?php echo $this->Html->link($merchant['Merchant']['merchant_dba'], array('controller' => 'Merchants', 'action' => 'view', $merchant['Merchant']['id'])); ?>&nbsp;</td>
								<td><?php echo $this->AxiaTime->date($merchant['Merchant']['active_date']); ?>&nbsp;</td>
							</tr>
						<?php endforeach; ?>
						<!-- ******END Recent Merchants Data Table -->
					</table>
					<div class="panel-footer"><a href="/merchants/index"><span class='glyphicon glyphicon-list'></span> View All Merchants</a></div>
				<!-- ******END Recently added Merchants Container DIV -->
			</div>
			<?php if($this->Rbac->isPermitted('app/actions/Dashboards/view/module/homeAdminModule', true)) :?>
			<!-- ******Recent Logins Container DIV -->
			<div class="panel panel-primary">
				<div class="panel-heading">Most Recent Logins</div>
					<!-- ******Recently added Merchants Data Table -->
					<table class="table table-condensed table-hover table-striped" >
						<tr>
							<th><?php echo 'User'; ?></th>
							<th><?php echo 'Login'; ?></th>
						</tr>
						<?php if (empty($recentLogins)) : ?>
							<tr>
								<td colspan="2" ><div class="noresults roundEdges "><?php echo "No recent data available at this time"; ?></div>&nbsp;</td>
							</tr>
						<?php endif; ?>
						<?php
						foreach ($recentLogins as $user):
							?>
							<tr>
								<td><?php echo $this->Html->link($user['User']['fullname'], array('controller' => 'Users', 'action' => 'view', $user['User']['id'])); ?>&nbsp;</td>
								<td><?php echo $this->AxiaTime->datetime($user['SystemTransaction']['system_transaction_date']  . " " . $user['SystemTransaction']['system_transaction_time']);?>&nbsp;</td>
							</tr>
						<?php
						endforeach;
						?>
						<!-- ******END Recent Logins Data Table -->
					</table>
					<div class="panel-footer"><?php echo $this->Html->link("<span class='glyphicon glyphicon-list'></span> View all Recent Activity", array("controller" => 'SystemTransactions', 'action' => 'userActivity'), array('escape' => false))?></div>
					
				<!-- ******END Recently added Merchants Container DIV -->
			</div>
			<?php endif;?>
		</td>
		<td>
			<!-- ******Recently submitted requests Container DIV -->
			<div class="panel panel-primary">
				<div class="panel-heading">Recently Submitted Requests</div>
							<!-- ******Recently submitted requests Data Table -->
							<table class="table table-condensed table-hover table-striped">
								<tr>
									<th><?php echo 'Merchant'; ?></th>
									<th><?php echo 'Changed'; ?></th>
									<th><?php echo 'Date Submitted'; ?></th>

								</tr>
								<?php if (empty($recentPENDRequest)) : ?>
									<tr>
										<td colspan="3" ><div class="noresults roundEdges "><?php echo "No recent data available at this time"; ?></div>&nbsp;</td>
									</tr>
								<?php endif; ?>
								<?php
								foreach ($recentPENDRequest as $pendReqData):
									?>
									<tr>
										<td>
											<?php
											echo $this->Html->link($pendReqData['Merchant']['merchant_dba'], array(
												'controller' => 'Merchants',
												'action' => 'view',
												$pendReqData['Merchant']['id']
											));
											?>
										</td>
										<td>
											<?php
											echo $this->Html->link($pendReqData['MerchantNote']['note_title'], array(
												'controller' => 'MerchantNotes',
												'action' => 'view',
												$pendReqData['MerchantNote']['id']
											));
											?>
										</td>
										<td><?php echo $this->AxiaTime->date($pendReqData['MerchantNote']['note_date']); ?></td>
									</tr>
								<?php
								endforeach;
								?>
								<!-- ******END Recently submitted requests Data Table -->
							</table>
							<div class="panel-footer">
								<?php
								echo $this->Html->link("<span class='glyphicon glyphicon-list'></span> " . __('View all pending requests'), array(
									'plugin' => false,
									'controller' => 'merchant_notes',
									'action' => 'index',
									'?' => array(
										'note_type_id' => $changeRequestTypeId,
										'general_status' => MerchantNote::STATUS_PENDING
										)
									),
									array('escape' => false)
								);
								?>
							</div>
				<!-- ******END Recently added Merchants Container DIV -->
			</div>


			<!-- ******Recently added Merchants Container DIV -->
			<div class="panel panel-primary">
				<div class="panel-heading">Recently Approved Changes</div>
							<!-- ******Recently Approved Changes Data Table -->
							<table class="table table-condensed table-hover table-striped">
								<tr>
									<th><?php echo 'Merchant'; ?></th>
									<th><?php echo 'Changed'; ?></th>
									<th><?php echo 'Date Approved'; ?></th>

								</tr>
								<?php if (empty($approvedChngs)) : ?>
									<tr>
										<td colspan="3" ><div class="noresults roundEdges "><?php echo "No recent data available at this time"; ?></div>&nbsp;</td>
									</tr>
								<?php endif; ?>
								<?php
								foreach ($approvedChngs as $changesData):
									?>
									<tr>
										<td>
											<?php
											echo $this->Html->link($changesData['Merchant']['merchant_dba'], array(
												'controller' => 'Merchants',
												'action' => 'view', $changesData['Merchant']['id']
											));
											?>
										</td>
										<td>
											<?php
											echo $this->Html->link($changesData['MerchantNote']['note_title'], array(
												'controller' => 'MerchantNotes',
												'action' => 'view',
												$changesData['MerchantNote']['id']
											));
											?>
										</td>
										<td><?php echo $this->AxiaTime->date($changesData['MerchantNote']['resolved_date']); ?></td>
									</tr>
								<?php
								endforeach;
								?>
								<!-- ******END Recently Approved Changes Data Table -->
							</table>
							<div class="panel-footer">
								<?php
								echo $this->Html->link("<span class='glyphicon glyphicon-list'></span> " . __('View all approved changes'), array(
									'plugin' => false,
									'controller' => 'merchant_notes',
									'action' => 'index',
									'?' => array(
										'note_type_id' => $changeRequestTypeId,
										'general_status' => MerchantNote::STATUS_COMPLETE
										)
									),
									array('escape' => false)
								);
								?>
						</div>
				<!-- ******END Recently added Merchants Container DIV -->
			</div>
			<?php if($this->Rbac->isPermitted('app/actions/Dashboards/view/module/homeAdminModule', true)) :?>
				<!-- ******Recently added Merchants Container DIV -->
				<div class="panel panel-primary">
					<div class="panel-heading">Recent Orders</div>
					<!-- ******Recently added Merchants Data Table -->
					<table class="table table-condensed table-hover table-striped">
						<tr>
							<th><?php echo 'User'; ?></th>
							<th><?php echo 'Invoice'; ?></th>
							<th><?php echo 'Date Ordered'; ?></th>
						</tr>
						<?php if (empty($recentOrders)) : ?>
							<tr>
								<td colspan="3" ><div class="noresults roundEdges "><?php echo "No recent data available at this time"; ?></div>&nbsp;</td>
							</tr>
						<?php endif; ?>
								<?php foreach ($recentOrders as $orders): ?>
									<tr>
										<td><?php echo $this->Html->link($orders['User']['user_first_name'] . " " . $orders['User']['user_last_name'], array('controller' => 'Users', 'action' => 'view', $orders['User']['id'])); ?>&nbsp;</td>
										<td>
											<?php if (!empty($orders['Order']['invoice_number'])) : ?>
												<?php echo $this->Html->link($orders['Order']['invoice_number'], array('controller' => 'Orders', 'action' => 'view', $orders['Order']['id'])); ?>&nbsp;
											<?php else: ?>
												<?php echo $this->Html->link("(none)", array('controller' => 'Orders', 'action' => 'equipment_invoice', $orders['Order']['id'])); ?>&nbsp;
											<?php endif; ?>
										</td>
										<td><?php echo $this->AxiaTime->date($orders['Order']['date_ordered']); ?>&nbsp;</td>
									</tr>
								<?php endforeach; ?>
						<!-- ******END Recent Merchants Data Table -->
					</table>
					<div class="panel-footer">
						<a href="/orders/index"><span class='glyphicon glyphicon-list'></span> View all equipment orders</a>
					</div>
					<!-- ******END Recently added Merchants Container DIV -->
				</div>
			<?php endif;?>
			<div class="panel panel-primary">
				<div class="panel-heading">Recent Notes</div>
				<!-- ******Recently Notes Table -->
				<table class="table table-condensed table-hover table-striped">
					<tr>
						<th><?php echo 'Merchant'; ?></th>
						<th><?php echo 'Title'; ?></th>
						<th><?php echo 'Date Entered'; ?></th>

					</tr>
					<?php if (empty($recentNotes)) : ?>
						<tr>
							<td colspan="3" ><div class="noresults roundEdges "><?php echo "No recent data available at this time"; ?></div>&nbsp;</td>
						</tr>
					<?php endif; ?>
					<?php foreach ($recentNotes as $notes): ?>
						<tr>
							<td><?php echo $this->Html->link($notes['Merchant']['merchant_dba'], array('controller' => 'Merchants', 'action' => 'view', $notes['Merchant']['id'])); ?>&nbsp;</td>
							<td><?php echo h($notes['MerchantNote']['note_title']); ?>&nbsp;</td>
							<td><?php echo $this->AxiaTime->date($notes['MerchantNote']['note_date']); ?>&nbsp;</td>
						</tr>
					<?php endforeach; ?>
					<!-- ******END Recent Notes Data Table -->
				</table>
				<div class="panel-footer">
					<a href="/MerchantNotes/index"><span class='glyphicon glyphicon-list'></span> View all merchants' notes</a>
				</div>
				<!-- ******END Recently added Merchants Container DIV -->
			</div>
		</td>
	</tr>
</table>
<script>
	function updateChart(chartObj, callableFunc, month, year) {
		if (callableFunc == 'getPiChartData' && !month) {
			$("#piChartMonthMonth").val(function() {
				d = new Date();
				m = d.getMonth() + 1; //0 = January
				return m < 10? '0' + m: ''+m;
			});
			month = $("#piChartMonthMonth").val();
		}
		if (callableFunc == 'getAcquiringMerch' && !year) {
			$("#barChartYearYear").val(function() {
				d = new Date();
				return d.getFullYear();
			});
			year = $("#barChartYearYear").val();
			var param = year;
		}
		if (callableFunc == 'getPiChartData') {
			var param = month;
		}
		if (callableFunc == 'getAcquiringMerch') {
			var param = year;
		}
		$.ajax({
			type: "POST",
			url: "/Dashboards/getChartData/"+ callableFunc + "/" + param,
			dataType: 'json',
			success: function(data) {
				chartObj.data.datasets[0].data = data;
				chartObj.update();
			},
			error: function(data) {
				// If user session expired the server will return status 401
				// *Refreshing the page will redirect the user to the login page thus preventing it from inserting into the DOM
					if(data.status==403){
						location.reload();
					}
				 }
		});
	}
</script>