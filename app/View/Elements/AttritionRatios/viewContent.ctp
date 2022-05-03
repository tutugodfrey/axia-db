<div class="col-md-12">
	<div class="contentModuleTitle panel-heading bg-success">
		<?php
		echo $this->Html->sectionHeader(__('Attrition Ratios'));
		if($this->Rbac->isPermitted('AttritionRatios/editMany')){
			$editUrl = array(
				'controller' => 'AttritionRatios',
				'action' => 'editMany', $userId, $compensationId, $partnerUserId
			);
			echo $this->Html->link(
					$this->Html->editIcon('', array('title' => h('Edit Attrition Ratios'))),
					$editUrl,
					array('target' => '_blank', 'escape' => false)
				);
			echo $this->AxiaHtml->ajaxContentRefresh('AttritionRatios', 'ajaxView', [$userId, $compensationId, $partnerUserId], 'AttritionRatiosMainContainer');
		}
		?>
	</div>
	<table class="table">
	<?php
		echo $this->element('AttritionRatios/grid_table_headers');
		$attritionRatiosCells = array();
		//Create default data based on associations
		if (empty($attritionRatios)) {
			foreach ($associatedUsers as $assocUserId => $associatedUser) {
				if ($assocUserId === $userId) {
					$attritionRatiosRow = array(
						CakeText::insert(':role<br>(:name)', array_map('h', $associatedUser)),
						'- -',
					);
				} elseif ($this->Rbac->isPermitted('app/actions/UserCompensationProfiles/view/module/SMCompModule', true)) {
					$attritionRatiosRow = array(
						CakeText::insert(':role<br>(:name)', array_map('h', $associatedUser)),
						'- -',
					);
				}
				$attritionRatiosCells[] = $attritionRatiosRow;
			}
		} else {
			foreach ($attritionRatios as $attritionRatio) {
				if (Hash::get($attritionRatio, 'AttritionRatio.associated_user_id') === Hash::get($attritionRatio, 'UserCompensationProfile.user_id')) {
					$attritionRatiosRow = array(
						CakeText::insert(':role<br>(:name)', array_map('h', $associatedUsers[$attritionRatio['AttritionRatio']['associated_user_id']])),
						$this->Number->toPercentage($attritionRatio['AttritionRatio']['percentage'], 0),
					);
					$attritionRatiosCells[] = $attritionRatiosRow;
				} elseif ($this->Rbac->isPermitted('app/actions/UserCompensationProfiles/view/module/SMCompModule', true)) {
					if (array_key_exists(Hash::get($attritionRatio, 'AttritionRatio.associated_user_id'), $associatedUsers)) {
						$attritionRatiosRow = array(
							CakeText::insert(':role<br>(:name)', array_map('h', $associatedUsers[$attritionRatio['AttritionRatio']['associated_user_id']])),
							$this->Number->toPercentage($attritionRatio['AttritionRatio']['percentage'], 0),
						);
						unset($associatedUsers[Hash::get($attritionRatio, 'AttritionRatio.associated_user_id')]);
					} 
				}
				$attritionRatiosCells[] = $attritionRatiosRow;
			}
			//check is there is still more assiciated users wuth no current data
			if (!empty($associatedUsers) && $this->Rbac->isPermitted('app/actions/UserCompensationProfiles/view/module/SMCompModule', true)) {
				foreach ($associatedUsers as $assocUserId => $associatedUser) {
					if ($assocUserId !== $userId) {
						$attritionRatiosRow = array(
							CakeText::insert(':role<br>(:name)', array_map('h', $associatedUser)),
							'- -',
						);
					}
				}
			}
		}	
		
		echo $this->Html->tableCells($attritionRatiosCells);
	?>
	</table>
</div>