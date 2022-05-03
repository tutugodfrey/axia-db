<?php
$this->Html->addCrumb('Maintenance Dashboard', ["controller" => 'MaintenanceDashboards', 'action' => 'main_menu']);
$this->Html->addCrumb('Api Configurations', ["controller" => 'ApiConfigurations', 'action' => 'index']);

		echo $this->Form->input('instance_url');
		echo $this->Form->input('authorization_url');
		echo $this->Form->input('access_token_url');
		echo $this->Form->input('redirect_url');
		echo $this->Form->input('client_id', ['type' => 'text', 'label' => 'Client ID/Username']);
		echo $this->Form->input('ApiConfiguration.client_secret', [
			'wrapInput' => 'col-md-6 col-sm-8', 
			'type' => 'password',
			'value' => $this->request->data('ApiConfiguration.client_secret'),
			'label' => ['text' => 'Client Secret/Password', 'class' => 'col col-md-5 col-sm-2 control-label'],
			'after' => '<div class="col-md-1 col-sm-2"><button type="button" class="btn-sm btn-danger" onClick="toggleShowPwField(\'ApiConfigurationClientSecret\')"><span class="glyphicon glyphicon-eye-close
"></span></button></div>'
		]);
		echo $this->Form->input('ApiConfiguration.access_token', [
			'wrapInput' => 'col-md-6 col-sm-8', 
			'label' => ['class' => 'col col-md-5 col-sm-2 control-label'],
			'type' => 'password',
			'value' => $this->request->data('ApiConfiguration.access_token'),
			'after' => '<div class="col-md-1 col-sm-2"><button type="button" class="btn-sm btn-danger" onClick="toggleShowPwField(\'ApiConfigurationAccessToken\')"><span class="glyphicon glyphicon-eye-close
"></span></button></div>'
		]);
		echo $this->Form->input('refresh_token', [
			'wrapInput' => 'col-md-6 col-sm-8', 
			'label' => ['class' => 'col col-md-5 col-sm-2 control-label'],
			'type' => 'password',
			'value' => $this->request->data('ApiConfiguration.refresh_token'),
			'after' => '<div class="col-md-1 col-sm-2"><button type="button" class="btn-sm btn-danger" onClick="toggleShowPwField(\'ApiConfigurationRefreshToken\')"><span class="glyphicon glyphicon-eye-close
"></span></button></div>'
		]);
		echo $this->Form->input('access_token_lifetime_hours', ['label' => 'Access Token lifetime in hours', "min" => 1, 'placeholder' => 'Leave blank if access token never expires']);
		echo $this->Form->input('auth_type');
?>
<div class="row col-md-8">
    <label for="ApiConfigurationIssuedAt" class="col col-md-5 control-label">Issued At</label>
    <div class="col col-md-7">
        <span class="form-control" style="padding: 6px 4px;background-color: #f0f0f0;font-size: 11px;height: auto;"  id="ApiConfigurationIssuedAt"><?php 
        $timestamp = $this->request->data('ApiConfiguration.issued_at');
		$issDateTime = null;
		try {
			if (!empty($timestamp) && is_numeric($timestamp)) {
				$issDateTime = date("M d, Y h:i:s A", substr($timestamp,  0, 10));
			} elseif(!empty($timestamp)) {
				$issDateTime = DateTime::createFromFormat('M d, Y h:i:s A', $timestamp);
			}
		} catch (Exception $e) {}

		echo (!empty($issDateTime))? h($issDateTime) : 'N/A';
		?>
    </span>
    </div>
</div>