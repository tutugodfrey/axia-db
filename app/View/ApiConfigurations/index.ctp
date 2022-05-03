<input type="hidden" id="thisViewTitle" value="<?php  echo __('Api Configurations'); ?> List" />
<?php $this->Html->addCrumb('Maintenance Dashboard', ["controller" => 'MaintenanceDashboards', 'action' => 'main_menu']); ?>
<div class="panel panel-default center-block"  style="width:60%">
 <table class="table table-hover table-bordered">
		<tr class="text-center">
			<td colspan=4 class="text-center"><?php 
			echo $this->Html->link('<span class="glyphicon glyphicon-cloud"></span> Add New Connection Config',['action' => 'add'],['escape' => false, "class" => "btn btn-info btn-sm"]);
			 ?></th>
			
		</tr>
		<tr class="bg-info text-center">
			<th class="text-center"><?php echo 'System/Connection Name'; ?></th>
			<th class="text-center"><?php echo 'Authentication Type'; ?></th>
			<th class="text-center"><?php echo 'Access token Issued on'; ?></th>
			<th></th>
		</tr>
		<?php
		foreach ($apiConfigs as $apiConfig): ?>
		<tr class="strong">
			<td class="text-center"><?php echo h($apiConfig['ApiConfiguration']['configuration_name']); ?>&nbsp;</td>
			<td class="text-center"><span class="glyphicon glyphicon-lock"></span> <?php echo h($apiConfig['ApiConfiguration']['auth_type']); ?>&nbsp;</td>
			<td class="text-center"><?php 
			$timestamp = $apiConfig['ApiConfiguration']['issued_at'];
			$issDateTime = null;
			try {
				if (!empty($timestamp) && is_numeric($timestamp)) {
					$issDateTime = date("M d, Y h:i:s A", substr($timestamp,  0, 10));
				} elseif(!empty($timestamp)) {
					$issDateTime = DateTime::createFromFormat('M d, Y h:i:s A', $timestamp);
				}
			} catch (Exception $e) {}

			echo (!empty($issDateTime))? $issDateTime : 'N/A';

			?>&nbsp;</td>
			<td><?php
				echo $this->Html->link('<img src="/img/icon_pencil_small.gif"></img>',['action' => 'edit', $apiConfig['ApiConfiguration']['id']],['escape' => false, "class" => "btn btn-default btn-sm"]);
			?></td>
		</tr>
	<?php endforeach; ?>
	</table>	
  <div class="panel-footer text-center">
  	<?php echo $this->Element('paginatorBottomNav')?>
  </div>
</div>