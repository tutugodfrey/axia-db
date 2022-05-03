<?php
echo $this->AssetCompress->css('custom-bootstrap.css', array('raw' => (bool)Configure::read('debug')));
 ?>

<div class="col-xs-5 col-sm-5 col-md-3 col-lg-3">
	<div class="panel panel-info">    
		<div class="strong text-primary panel-heading">Real-Time Process Tracking
			<a class="pull-right" onClick="updateList(false)" data-toggle="tooltip" data-placement="top" title="Refresh List Now!"><img src="/img/Button-Refresh.png"></a>
			<span class="glyphicon glyphicon-info-sign pull-right" style="font-size:12pt; margin-right:3px;" data-toggle="tooltip" data-placement="top" title="List updates itself automatically"> </span>
		</div>
		<div class="clearfix"></div>
		<ul class="list-group " id='bg-status-list' style="max-height:300px;overflow:auto;">
			<?php echo $this->element('AjaxElements/Admin/bg_processes_list');
			?>
		</ul>
	</div>
</div>
<?php echo $this->AssetCompress->script('report-admin-bg-job-tracking', array(
    'raw' => (bool)Configure::read('debug')
));
