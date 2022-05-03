
<input type="hidden" id="thisViewTitle" value="<?php echo __('Axia Extranet Database Login'); ?>" />
<div>
	<?php echo $this->Session->flash('auth'); ?>
	<?php echo $this->Form->create('User'); ?>
    <table cellpadding="0" cellspacing="0" border="0" align="center">
        <tr><td style="text-align:center">
				<?php
				echo $this->Form->input('username');
				echo $this->Form->input('password');
				echo $this->Form->submit('Submit', array('div' => false));
				?>
			</td>
        </tr>
	</table>   

</div>