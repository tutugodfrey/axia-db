<ul class="nav navbar-nav">
	<ul id="topnavbar">
		<li><a href="#">Merchants</a>
			<ul class="roundEdges ">
				<li><a href="/merchants/index">Merchant list</a></li>
				<?php
				//@rbac example: using renderContent
				$content = $this->Html->tag('li', $this->Html->link(__('Partners'), array(
								  'controller' => 'partners',
								  'action' => 'index',
				)));
				echo $this->Rbac->renderContent('Partners/index', $content, false);
				?>
				<?php
				//@rbac example: using isPermitted. Note permissions are matched with the permissions.php file after rbac sync is done
				if ($this->Rbac->isPermitted('Partners/index')):
					?>
					<li><a href="/partners/index">Partners</a></li>
				<?php endif; ?>
				<li><a href="/MerchantChanges/index/PEND">Pending Requests</a></li>
				<li><a href="/MerchantNotes/index">Search all notes</a></li>
			</ul>
		</li>

		<li><a href="#">Reports</a>
			<ul class="roundEdges ">
				<li><a href="/CommissionReports/index">Commission report</a></li>
				<li><a href="#">Cancellation report</a></li>
				<li><a href="#">Gross profit analysis</a></li>
				<li><a href="#">Goals report</a></li>
				<li><a href="#">Gross profit report</a></li>
				<li><a href="#">Last Deposit report</a></li>
				<li><a href="#">Rejects report</a></li>
				<li><a href="#">Residual report</a></li>
				<!--<li><a href="#">Replacement tracking report</a></li>-->
				<li><a href="#">MerchantUwVolume report</a></li>
			</ul>
		</li>

		<li><a href="#">Users</a>
			<ul class="roundEdges ">
				<li><a href="/users/index">User List</a></li>
				<li><a href="#">Recent activity</a></li>
				<li><a href="/referers/index/1">Referrers</a></li>
				<li><a href="/referers/index/0">Resellers</a></li>
			</ul>
		</li>

		<li><a href="#">Equipment</a>
			<ul class="roundEdges ">
				<li><a href="/EquipmentItems/index">Equipment list</a></li>
				<?php
				/* Only users with admin-like-permission can see this */
				if ($this->Rbac->isPermitted('Merchants/edit')):
					?>
					<li><a href="#">SB Inventory</a></li>
				<?php endif; ?>
			</ul>
		</li>

		<li><a href="#">Shortcuts</a>
			<ul class="roundEdges ">
				<li><a href="#">Team Contest</a></li>
				<li><a href="#">POA Materials</a></li>
			</ul>
		</li>
		<?php
		/* Only users admins can see this */
		//@todo: define a more significative permission name for this group
		if ($this->Rbac->isPermitted('AppUsers/upload')):
			?>
			<li><a href="#">Admin</a>
				<ul class="roundEdges ">
					<li><a href="#">Merchant upload</a></li>
					<li><a href="#">ControlScan Results Upload</a></li>
					<li><a href="#">Merchant pricing archive</a></li>
					<li><a href="#">Residual admin</a></li>
					<li><a href="#">Commission admin</a></li>
					<li><a href="#">Goals admin</a></li>
					<li><a href="#">Group admin</a></li>
					<li><a href="#">SAQ Overview</a></li>
					<li><a href="#">Reject Upload</a></li>
					<li><a href="#">Last Deposit Upload</a></li>
				</ul>
			</li>
		<?php endif; ?>

		<li><a href="#">Session</a>
			<ul class="roundEdges ">
				<li><a href="/Users/view/<?php echo $this->Session->read('Auth.User.id') ?>">My Profile</a></li>
				<li><a href="/users/logout">Logout</a></li>
			</ul>
		</li>
	</ul>
</ul>