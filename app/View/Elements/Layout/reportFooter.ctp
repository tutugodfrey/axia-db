<?php
if (!isset($exportLinks)) {
	$exportLinks = null;
}

if ($this->Rbac->isPermitted('app/actions/Dashboards/view/module/sysCsvExport', true)): ?>
	<div class="contrTitle box-shadow " style="height:25px">
		<?php echo $this->element('Layout/exportCsvUi', compact('exportLinks')); ?>
	</div>
<?php
endif;
