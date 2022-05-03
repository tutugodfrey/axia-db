<input type="hidden" id="thisViewTitle"
	   value="User Profile | <?php echo h($user['User']['fullname']); ?>"/>
<?php
$this->extend('/BaseViews/base');
echo $this->element('Users/view');
//The Sales Goals Element has been removed from this view but the element still exists in Elements/SalesGoals/view
//We might need it later but not at this time.
echo '<hr/>';
if($this->Rbac->isPermitted('app/actions/UserCompensationProfiles/view/module/CompensatonTabs', true)) {
	echo $this->element('Users/compensation_panel');
}
