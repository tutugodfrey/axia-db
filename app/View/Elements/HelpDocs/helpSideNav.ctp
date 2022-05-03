<div style="position:fixed;" class="col-sm-3 col-lg-2">
  <nav id="helpSideNav" class="navbar navbar-default navbar-fixed-side list-group panel-info">
	<!-- normal collapsible navbar markup -->
		<li class="list-group-item panel-heading">
			<h4 class="panel-title"><strong>Help Topics</strong></h4>
		</li>
		<li class="list-group-item"><a name="navItem" style="color:black" href='#newAcct'>Create New Merchants Accounts</a>
			<ul>
				<li>
					<a name="navItem" style="color:black" href='#newAcct::gw1'>Create Gateway 1 Merchant Accounts</a>
				</li>
				<li>
					<a name="navItem" style="color:black" href='#newAcct::Pfusion'>Create Payment Fusion Merchant Accounts</a>
				</li>
				<li>
					<a name="navItem" style="color:black" href='#newAcct::achProd'>Create Merchant Accounts with ACH product</a>
				</li>
			</ul>
		</li>
		<li class="list-group-item"><a name="navItem" style="color:black" href='#addingProductsM'>Adding Products to Merchants</a></li>
		<li class="list-group-item"><a name="navItem" style="color:black" href='#removingProductsM'>Deactivating Products from Merchants</a></li>
		<li class="list-group-item"><a name="navItem" style="color:black" href='#cancellations'>Merchant Cancellations</a></li>
		<li class="list-group-item"><a name="navItem" style="color:black" href='#UCP'>User Compensation Profiles (UCP)</a>
			<ul>
				<li>
					<a name="navItem" style="color:black" href='#UCP::CreateDefault'>Creating Default UCP</a>
				</li>
				<li>
					<a name="navItem" style="color:black" href='#UCP::CreatePartnerRep'>Creating Partner-Rep UCP</a>
				</li>
			</ul>
		</li>
		<li class="list-group-item"><a name="navItem" style="color:black" href='#RReport'>Residual Reporting</a></li>
		<li class="list-group-item"><a name="navItem" style="color:black" href='#Security'>Security</a>
			<ul>
				<li>
					<a name="navItem" style="color:black" href='#Security::rbac'>Role-based Access Control (RBAC)</a>
				</li>
				<li>
					<a name="navItem" style="color:black" href='#Security::ownControl'>Asset Owner Access Control</a>
				</li>
			</ul>
		</li>
		<?php 
		$newWinIcon = '<span class="pull-right glyphicon glyphicon-new-window small text-primary"></span>';
		echo $this->Html->tag('li', $this->Html->link("$newWinIcon Database API Documentation", '/AxiaApiDocs/index.html', array('escape' => false, 'target' => '_blank')), array('class' => 'list-group-item')); ?>
  </nav>
</div>
<script>
$("#helpSideNav").click(function (e) {
	//Catching event bubbles :)
	if (e.target.name === 'navItem') {
		//bg-primary has no effect on li elements that have the list-group-item class
		$("a[name="+ e.target.name +"]").parent().removeClass("active bg-primary");
		$(e.target).parent().addClass("active bg-primary");
	}
});
</script>