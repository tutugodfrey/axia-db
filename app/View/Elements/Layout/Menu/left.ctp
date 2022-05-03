<?php //@todo: refactor to use TW Bootstrap   ?>
<div id="leftSideNav">
	<div class="leftNavSection roundEdges " style="text-align: center; padding: 3px 0px 3px 0px">
		<span> Logged in as:
			<span class="contentModuleTitle"> <?php echo h($this->Session->read('Auth.User.loggedInUser')) ?></span>
		</span>
	</div>
	<?php
	/* Show Merchant Navigation */
	if (!empty($merchant['Merchant']['id']))
		echo $this->element('Layout/Merchant/navigation');

	/* Show User Navigation */
	if (!empty($user['User']['id']) || !empty($this->data['User']['id']))
		echo $this->element('Layout/User/navigation');
	?>
	<div class="leftNavSection roundEdges ">
		<div class="contrTitle roundEdges" style="margin:1px;text-align: center">Search Merchant</div>
		<?php
		echo $this->Form->create('Merchant', array(
				  'url' => array_merge(array('action' => 'find'))
		));
		echo $this->Form->input('search', array('div' => true, 'label' => false, 'title' => 'Enter a merchant MID or DBA (not case sensitive).'));
		echo $this->Form->input('active', array('div' => false, 'label' => 'Active merchants',
				  'default' => 1, 'type' => 'checkbox'));
		echo $this->Form->submit(__('Search'), array('div' => true));
		echo $this->Form->end();
		?>
	</div>

	<div class="leftNavSection roundEdges ">
		<?php //@todo get rid of this table, use twb layout ?>
		<table id="miscLinks">
			<tr>
				<th class="contrTitle roundEdges">
					Links
				</th>
			</tr>
			<tr>
				<td>
					<a href="http://www.axiapayments.com" target="_blank">
						Axiapayments.com</a><br/>
					<a href="http://www.axiapayments.com/rep/" target="_blank">
						Axiapayments.com/rep/</a><br/>
					<a href="https://app.axiapayments.com" target="_blank">
						Online App</a><br/>
					<a href="https://handbook.axiapayments.com" target="_blank">
						Affilliate Handbook</a><br/>
					<a href="https://mail.axiapayments.com:987" target="_blank">
						Companyweb</a><br/>
					<a href="http://www.axiapayments.com/frame.phtml?navigateUrl=https%3A%2F%2Fsecure.usaepay.com%2Flogin" target="_blank">
						Axia Gateway</a><br/>
					<a href="http://www.myvirtualreports.com" target="_blank">
						Sage Online Reporting</a><br/>
				</td>
			</tr>

		</table>
	</div>
</div>