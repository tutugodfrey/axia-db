<?php echo $this->AssetCompress->css('custom-bootstrap', array(
			'raw' => (bool)Configure::read('debug')
		));
?>
<div class="panel panel-info">
	<!-- Default panel contents -->
	<div class="panel-heading">
		<?php
		if (!empty($compProfiles['DefaultCompensationProfile']['id'])) { ?>
			<a href="javascript:void(0)" onClick='objSlider("compPanel", 500); rotateThis(document.getElementById("rsTwisty"), 180, 500)'>
				<span id="rsTwisty" class="glyphicon glyphicon-chevron-down"></span>
				User Compensation Profile(s): &nbsp;</a>
			|&nbsp;&nbsp;
			<?php 
			if ($this->Rbac->isPermitted('UserCompensationProfiles/delete') && $ucpCount > 0) {
				echo $this->Form->button('Enable Delete <span class="glyphicon glyphicon-trash text-danger"> </span>', ['escape' => false, 'id' => 'enableDelete','type' => 'button', 'class' => 'btn btn-default btn-xs']);
				echo $this->Form->button('Disable Delete <span class="glyphicon glyphicon-trash"> </span>', ['escape' => false, 'id' => 'cancelDelete','type' => 'button', 'class' => 'btn btn-danger btn-xs hide']);
			}
		}	
		if ($this->Rbac->isPermitted('UserCompensationProfiles/addCompProfile')): ?>
			<div class="btn-group dropdown">
				<button type="button" id="mainCreateUCPBtn" class="small btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
				  Create User Comp Profile <span class="glyphicon glyphicon-plus"></span>
				</button>
				<ul class=" dropdown-menu dropdown-menu-right " role="menu">
					<?php
						$cpMenuLabel = ($isPartner)? "Create a Copy for this Partner ({$user['User']['fullname']})" : "Copy Existing UCP";
						echo '<li class="small">';
						echo $this->Html->link($cpMenuLabel . '<span class="glyphicon glyphicon-duplicate" style="margin-left:25px"></span>', '#',
							array(
								'data-toggle' => 'modal',
								'data-target' => '#dynamicModal',
								'onClick' => "renderContentAJAX('', '', '', 'dynamicModalBody', '/UserCompensationProfiles/copyMany/{$user['User']['id']}')",
								'escape' => false
							)
						);
					echo '</li>';
					if (empty($compProfiles['DefaultCompensationProfile']['id'])) {
						echo '<li class="divider"></li>';
						echo '<li class="small">';
						echo $this->Form->postLink("Create New Default UCP", array('controller' => 'UserCompensationProfiles', 'action' => 'addCompProfile', $user['User']['id'], true), array('escape' => false, 'confirm' => __('Create default compensation profile for %s?', $user['User']['fullname'])));
						echo '</li>';
					}

					if (!empty($compProfiles['DefaultCompensationProfile']['id'])) {
						if ($isManager && !empty($managerRoles)) {
							echo $this->element('CompProfile/create_sm_comp', array(
							    "managerRoles" => $managerRoles,
							    "userId" => $user['User']['id'],
							    "isManager" => $isManager,
							));
						}
						if($isPartner && !empty($partnerReps)) : ?>
							<li class="divider"></li>
							<?php 
							echo $this->Html->link($this->Html->tag('span', " ", ['class' => "glyphicon glyphicon-info-sign"]), 
									"/Documentations/help#UCP::CreatePartnerRep",
									['escape' => false, 'target' => '_blank', 'class' => 'pull-right', 'style' => 'font-size:11pt;margin:5px']);
							?>
							<li class="dropdown-header small">Create Partner-Rep UCP:</li>
							<li class="divider"></li>
							<?php foreach($partnerReps as $uid => $repUser):?>
								<li class="dropdown-submenu small">
									<?php 
									echo $this->Html->link(h($repUser) . '<span class="glyphicon glyphicon-menu-right" style="position: absolute;left:85%;margin-top:4px"></span>', "#", array("tabindex" => "-1", 'escape' => false, 'class' => 'subdd-button'));
									?>
									<ul class="dropdown-menu" name="submenu-action-lists">
										<li class="small">
											<?php
											echo $this->Html->link("Copy from another Partner-Rep UCP" . '<span class="glyphicon glyphicon-duplicate" style="margin-left:15px"></span>', '#',
												array(
													'data-toggle' => 'modal',
													'data-target' => '#dynamicModal',
													'onClick' => "renderContentAJAX('', '', '', 'dynamicModalBody', '/UserCompensationProfiles/copyMany/$uid/" . $user['User']['id'] . "')",
													'escape' => false
												)
											);
											?>
										</li>
										<li class="small">
											<?php
											echo $this->Form->postLink("Create New Partner-Rep UCP". '<span class="glyphicon glyphicon-file" style="margin-left:55px"></span>', 
												array('controller' => 'UserCompensationProfiles', 'action' => 'addCompProfile', $uid, 0, $user['User']['id']),
												array(
													"tabindex" => "-1",
													'escape' => false,
													'confirm' => __("%s\nProceed?", "A blank Partner-Rep Compensation Profile for " . $repUser . " will be created and it will be associated with the partner: " . $user['User']['fullname'])));
											?>
										</li>
									</ul>
								</li>
							<?php endforeach;?>
						<?php endif; ?>
					<?php }?>
				</ul>
			</div>
		<?php endif;
		if ($this->Rbac->isPermitted('UserCompensationProfiles/addCompProfile')) {
			echo $this->Html->link($this->Html->tag('span', " ", ['class' => "glyphicon glyphicon-info-sign"]), 
				"/Documentations/help#UCP",
				['escape' => false, 'target' => '_blank', 'class' => 'pull-right', 'style' => 'font-size:12pt']);
		}
		?>
	</div>
	<div id="compPanel" style="display:none">
		<ul id="myTab" class="nav nav-tabs" role="tablist">
			<?php //Check if displaying default profiles for current user is permitted
			if (Hash::get($compProfiles, 'DefaultCompensationProfile.isDisplayPermitted') === true && !empty($compProfiles['DefaultCompensationProfile']['id'])):
			?>
				<li>
					<?php 
					if ($this->Rbac->isPermitted('UserCompensationProfiles/delete') && $ucpCount > 0) {
						echo $this->Html->tag('span', '', 
							['class' => 'glyphicon glyphicon-trash btn-sm btn-danger text-center roundEdges deleteOverlay', 'style' => 'display:none;', 'ucp-id' => $compProfiles['DefaultCompensationProfile']['id'], 'name' => 'ucpDeleteBtn']);
					}
					echo $this->Html->link(h($compProfiles['User']['fullname']), 'javascript:void(0)', array('class' => 'bg-primary', 'role' => 'tab',"data-toggle"=>"tab",'onclick' => "switchContent('{$compProfiles['DefaultCompensationProfile']['id']}')")); ?>
				</li>

				<?php foreach(Hash::extract($compProfiles,'MgrCompensation') as $mgrCompData): ?>
					<li>
					<?php
					if ($this->Rbac->isPermitted('UserCompensationProfiles/delete') && $ucpCount > 0) {
						echo $this->Html->tag('span', '', 
							['class' => 'glyphicon glyphicon-trash btn-sm btn-danger text-center roundEdges deleteOverlay', 'style' => 'display:none;', 'ucp-id' => $mgrCompData['UserCompensationProfile']['id'], 'name' => 'ucpDeleteBtn']);
					}
					echo $this->Html->link(h($mgrCompData['User']['fullname']) . " (" . h($mgrCompData['Role']['name']) . " compensation)", 'javascript:void(0)', array('role' => 'tab',"data-toggle"=>"tab",'onclick' => "switchContent('{$mgrCompData['UserCompensationProfile']['id']}')")); ?>
					</li>
				<?php endforeach;?>
		<?php endif;?>
			<?php foreach(Hash::extract($compProfiles,'{n}') as $userCompData): ?>
				<?php //Check if displaying profiles for current user is permitted
				if($userCompData['UserCompensationProfile']['isDisplayPermitted']):
				?>
				<li>
					<?php
					if ($this->Rbac->isPermitted('UserCompensationProfiles/delete') && $ucpCount > 0) {
						echo $this->Html->tag('span', '', 
							['class' => 'glyphicon glyphicon-trash btn-sm btn-danger text-center roundEdges deleteOverlay', 'style' => 'display:none;', 'ucp-id' => $userCompData['UserCompensationProfile']['id'], 'name' => 'ucpDeleteBtn']);
					}
					$switchParams = ($isPartner)?"'{$userCompData['UserCompensationProfile']['id']}/{$user['User']['id']}'":"'{$userCompData['UserCompensationProfile']['id']}'";
					$userFullName = (empty($userCompData['PartnerUser']['fullname']))?$userCompData['User']['fullname']: $userCompData['PartnerUser']['fullname'];
					echo $this->Html->link(h($userFullName), 'javascript:void(0)', array('class' => 'bg-success', 'role' => 'tab',"data-toggle"=>"tab",'onclick' => "switchContent($switchParams)"));
					?>
				</li>
				<?php endif;?>
			<?php endforeach;?>
		</ul>

		<div id="compMainContent" style="display:none">
			<!--USER COMPENSATION PROFILES WILL ASYNCHRONOUSLY RENDER HERE-->
		</div>
	</div>
</div>

<script type="text/javascript">
//Bootstrap Sub-dropdown-menu functionality
$(document).ready(function(){
  $('.dropdown-submenu a.subdd-button').on("click", function(e) {
		$("ul[name='submenu-action-lists']").hide();
		$(this).next('ul').toggle( "blind", {direction: 'left'}, 250);
		e.stopPropagation();
		e.preventDefault();
  });
	$("#cancelDelete, #enableDelete").on('click', function () {
		if ($(this).attr('id') == 'enableDelete') {
			$('#compMainContent').html(' ');
			if ($('#compPanel').is(":visible") === false) {
				objSlider("compPanel", 500);
			}
		}
		$("#cancelDelete, #enableDelete").toggleClass('hide');
		$("[name='ucpDeleteBtn']").toggle('slide');
	});

	$("[name='ucpDeleteBtn']").on('click', function () {
		if(!confirm("Are you sure you want to delete that comp profile? All data will be lost!")) {
			return;
		}
		//Trigger click event to turn off delete mode
		$('#cancelDelete').click();
		$('#compMainContent').html("<div class='text-center'><h5><span class='label label-danger'>Deleting All Data...</span></h5><img src='/img/indicator.gif'></div>");
		$('#compMainContent').show();
		//remove click event to prevent users from clicking button again while original request is processing
		$(this).off('click');
		profileID = $(this).attr('ucp-id');
		$.post( "/UserCompensationProfiles/delete/" + profileID, function(data) {})
			.done(function(data) {
				$('#compMainContent').html('<div class="text-center alert alert-success">Success! Refreshing...</div>');
				location.reload();
			})
			.fail(function(data) {
				if(data.status === 403) {
					location.reload();
				}
				$('#compMainContent').html('<div class="text-center alert alert-danger">Server Request Error ' + data.status + ': <br />Sorry try again later.</div>');
			});
	});
	
});
	function switchContent(profileID){
		$('#compMainContent').slideDown('fast');
		loaderHtml = "<div class='text-center'><h5><span class='label label-danger'>Loading...</span></h5><img src='/img/indicator.gif'></div>";
		$("#compMainContent").html(loaderHtml);
		renderContentAJAX("Users", "view", profileID, "compMainContent");
	}
</script>
<?php 
echo $this->Element('AjaxElements/modalDynamic');