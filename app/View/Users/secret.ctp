<div style="width: fit-content;" class="center-block">
	<table class="table table-bordered shadow">
    	<tr><td>
		<div class="text-center text-primary strong"><?php echo $this->Html->image('/img/google-authenticator.png', ["style" => "max-width: 435px;"]); ?></div>
		
		<div class="alert alert-success strong text-center">
			<span class="glyphicon glyphicon-lock"></span>
			<span class="glyphicon glyphicon-ok"></span>
			Two factor Authentication is now enabled on your account!
		</div>
		<div class="strong" style="max-width: fit-content;">
		Thank you for helping us keep our systen secure! Please install the Google Authenticator app on your mobile device and scan the QRcode below.
		The app generates a temporary code which you are now required to enter in order to log in.</div>
		<div class="text-center"> <?php echo $this->Html->image($url);?></div>
		<div class="panel text-center strong">Or enter your secret token manually: 
			<div class="form-control"><?php echo $secret;?></div>
		</div>

		<div class="text-center">
			<?php echo $this->Html->link('Generate different secret', array('action' => 'secret', 'renew'), ['class' => 'btn btn-sm btn-primary']);?>
			<?php echo $this->Form->postLink('Disable Two Factor Authentication', null, ['class' => 'btn btn-sm btn-danger']);?>
		</div>
		<br/>
		</td></tr>
	</table>
</div>