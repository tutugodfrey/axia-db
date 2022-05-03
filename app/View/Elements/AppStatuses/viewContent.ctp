<div class="col-md-12">
		<div class="contentModuleTitle panel-heading bg-success">
			<?php
			echo $this->Html->sectionHeader(__('App Status'));
			if($this->Rbac->isPermitted('AppStatuses/editMany')){
				$editUrl = array(
						'controller' => 'AppStatuses',
						'action' => 'editMany', Hash::get($appStatuses, 'User.id'), Hash::get($appStatuses, 'UserCompensationProfile.id'), $partnerUserId
				);
				echo $this->Html->link(
						$this->Html->editIcon('', array('title' => h('Edit App Statuses'))),
						$editUrl,
						array('target' => '_blank', 'escape' => false)
					);
				echo $this->AxiaHtml->ajaxContentRefresh('AppStatuses', 'ajaxView', [$compensationId], 'appStatusesMainContainer');
			}
			?>
		</div>
		<table class="table">
			<?php
			echo $this->element('AppStatuses/grid_table_headers');
			$userId = Hash::get($appStatuses, 'User.id');
			$appStatusesCells = array();
			foreach ($merchantAchAppStatuses as $merchantAchAppStatusId => $merchantAchAppStatuseName) {
				$values = Hash::extract($appStatuses['AppStatus'], "{n}[merchant_ach_app_status_id=$merchantAchAppStatusId]");
				$value = array();
				if (!empty($values)) {
					$value = array_pop($values);
				}
				$appCellRow = array(
						  __(h($merchantAchAppStatuseName)),
						  $this->Number->currency(Hash::get($value, "rep_cost")));

				if($this->Rbac->isPermitted('app/actions/AppStatuses/view/module/adminModules', true))
					$appCellRow[]= $this->Number->currency(Hash::get($value, "axia_cost"));

				$appCellRow[]=$this->Number->currency(Hash::get($value, "rep_expedite_cost"));
				if($this->Rbac->isPermitted('app/actions/AppStatuses/view/module/adminModules', true)){
					$appCellRow[]= $this->Number->currency(Hash::get($value, "axia_expedite_cost_tsys"));
					$appCellRow[]= $this->Number->currency(Hash::get($value, "axia_expedite_cost_sage"));
				}

				$appStatusesCells[] = $appCellRow;
			}
			echo $this->Html->tableCells($appStatusesCells);
			?>
		</table>
	</div>