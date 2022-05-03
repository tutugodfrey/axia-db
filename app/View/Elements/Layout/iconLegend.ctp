<nav class="navbar navbar-default navbar-fixed-side panel panel-info" style="overflow-x:scroll">
	<div class="panel-heading">Legend</div>
	<table>
		<tr>
			<td>
			<?php if($this->Rbac->isPermitted("Orders/edit_equipment_invoice")): ?>
				<h6 ><img src="/img/icon_pencil_small.gif" border="0" alt=""> <span class="label label-default">Click to Edit</span></h6>
			<?php endif;?>
			<?php if($this->Rbac->isPermitted("Orders/edit_equipment_invoice")): ?>
				<h6><img src="/img/detail_page.png" border="0" alt=""> <span class="label label-default">View Details</span></h6>
			<?php endif;?>
				<h6><img src="/img/icon_greenflag.gif" border="0" alt=""> <span class="label label-success">Paid / Complete / Approved</span></h6>
				<h6><img src="/img/icon_yellowflag.gif" border="0" alt=""> <span class="label label-warning">Invoiced / In Progress / Active</span></h6>
				<h6><img src="/img/icon_redflag.gif" border="0" alt=""> <span class="label label-danger">Item Incomplete / Pending</span></h6>
			<?php if($this->Rbac->isPermitted("Orders/mark_as_paid_order")): ?>
				<h6><img src="/img/dollar_Icon.jpg" border="0" alt=""> <span class="label label-default">Mark Paid</span></h6>
			<?php endif;?>
				<?php if ($this->Rbac->isPermitted($this->name . '/delete')): ?>
					<h6><img src="/img/redx.png" border="0" alt=""> <span class="label label-default">Delete Item</span></h6>
					<?php endif; ?>
			</td>
		</tr>

	</table>
</nav>
