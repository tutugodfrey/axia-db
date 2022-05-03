<div class="col-md-12">
		<div class="contentModuleTitle panel-heading bg-success">
			<?php
			echo $this->Html->sectionHeader(__('User Parameters'));
			if($this->Rbac->isPermitted('UserParameters/editMany')){
				$editUrl = array(
					'controller' => 'UserParameters',
					'action' => 'editMany', Hash::get($user, 'User.id'),$user['UserCompensationProfile']['id'], $partnerUserId,
				);
				echo $this->Html->link(
						$this->Html->editIcon(),
						$editUrl,
						array('target' => '_blank', 'escape' => false)
					);
				echo $this->AxiaHtml->ajaxContentRefresh('UserParameters', 'ajaxView', [$user['User']['id'], $compensationId, $partnerUserId], 'UserParamsViewMainContainer');
			}
			?>
		</div>
		
			<?php
			echo $this->UserParameter->view($userParameterHeaders, $productsServicesTypes);
			?>
		
	</div>