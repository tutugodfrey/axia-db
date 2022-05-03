<div class="modal fade" id="myModal"role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog" id="ModalContainer">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title text-info">About this upload</h4>
			</div>
			<div class="modal-body">
				<h5><strong>How it works?</strong></h5>
				<p>Data uploaded for a specified month and year can be re-uploaded in order to update some or all of the data that was originally uploaded.</p>
				<p>The CSV file must contain the predetermined headers listed below but not all headers are required to be always present in the file. Uploading any subset of the expected columns is supported, however at least one column is required in addition to the MID column which is always required.</p>
				<div style="overflow:auto">
					<table class="nowrap table-condensed table-bordered bg-info strong">
					<?php 
						echo $this->Html->tableCells(array($csvColNames));
					?>
					</table>
						<br/>
				</div>
			* The order of the columns in the file does not matter<br>
			* Unrecognized column names will be ignored<br>
			* To add new columns request a feature update<br>
			* Large data uploads will be scheduled and delegated to be processed by the server.
			</div>
		</div>
	</div>
</div>
<button type="button" class="pull-right btn btn-info" data-toggle="modal" data-target="#myModal"><span class="glyphicon glyphicon-info-sign"></span> Info</button>
<?php
/* Drop breadcrumb */
$this->Html->addCrumb('Data Collection ' . $this->action, '/' . $this->name . '/' . $this->action);
echo $this->Form->create('ImportedDataCollection', array(
	'type' => 'file',
	'inputDefaults' => array(
		'label' => array('class' => 'col-md-1 col-sm-2'),
		'div' => 'form-group',
		'wrapInput' => 'col-md-1 col-sm-2',
		'class' => 'form-control'
	),
	'url' => array(
		'plugin' => null,
		'controller' => 'ImportedDataCollections',
		'action' => 'upload',
	),
	'class' => 'well well-lg form-horizontal',
));

echo $this->Html->tag('div',
	$this->Form->input(
		'month',
		array(
			'required' => true,
			'options' => $months,
			'label' => 'Effective Month/Year',
			'type' => 'select',
			'div' => false,
			'default' => date('m')
		)
	) . $this->Form->input(
		'year',
		array(
			'empty' => false,
			'options' => $years,
			'label' => false,
			'div' => false,
			'type' => 'select',
			'default' => date('Y')
		)
	), array('class' => 'form-group')
);

echo $this->Form->input('file', array(
	'label' => __('Select a CSV file:'),
	'class' => 'btn btn-info btn-sm',
	'type' => 'file',
	'required' => true
));
echo $this->Html->tag('div', 
	$this->Html->tag('div', '<span class="glyphicon glyphicon-exclamation-sign"></span> <span id="errContainer"></span><hr>'
		. $this->Form->button($this->Html->tag('span', '', array('class' => 'glyphicon glyphicon-ok')), array('id' => 'ConfirmedSubmit', 'type' => 'submit','class' => 'btn btn-lg btn-success', 'style' => 'display:none'))
		. $this->Form->button($this->Html->tag('span', '', array('class' => 'glyphicon glyphicon-remove')), array('id' => 'cancelSubmit', 'type' => 'button','class' => 'btn btn-lg btn-danger', 'style' => 'display:none')), 
		array('class' => 'panel panel-body panel-danger text-danger col-md-6 col-sm-6 text-center shadow', 'style' => 'display:none;font-size:10pt', 'id' => 'alertOvwright'))
	. $this->Form->button($this->Html->tag('span', '', array('class' => 'glyphicon glyphicon-open')) . ' Upload', array('id' => 'PreSubmit','type' => 'button','class' => 'btn btn-primary btn-sm')),
	array('class' => 'form-group', 'style' => 'padding-left:15px')
);
echo $this->Form->end();
//Display errors 
if (isset($result) && !empty($result['log']['errors'])) :
	$class = (empty($result['log']['errors']))? 'success': 'warning';
	$class = ($result['result'] == false || $result['recordsAdded'] == false)? 'danger' : $class;
?>
<div class="center-block" style="max-width:60%">
	<div class="panel panel-<?php echo $class; ?>">
		<div class="panel-heading text-center">
			<strong class="panel-title">
			<?php 
				$title = 'Upload completed ';
				$title .= (empty($result['log']['errors']))? 'successfully': 'but errors occurred';
				$title = ($result['result'] == false || $result['recordsAdded'] == false)? 'Unexpected Internal Error!' : $title;
				echo $title;
			?>
			</strong>
		</div>
		<ul class="list-group">
			<li class="list-group-item list-group-item-info">
				<strong>Information:</strong>
			</li>
			<li class="list-group-item">
				<?php
					echo "<strong>";
					echo implode('<br/>', Hash::get($result, 'log.optional_msgs'));
					echo "</strong>";
				?>
			</li>
			<li class="list-group-item list-group-item-danger">
				<span class="badge"><?php echo count(Hash::get($result, 'log.errors'));?></span>Errors:
			</li>
			<li class="list-group-item">
				<?php
					echo "<strong class='text-danger'>";
					echo implode('<br/>', Hash::get($result, 'log.errors'));
					echo "</strong>";
				?>
			</li>
		</ul>
	</div>
</div>
<?php endif; ?>
<script>
$(document).ready(function(){
	$('#ImportedDataCollectionUploadForm').on('submit', function() {
		showLoadSpinner();
	});
	$('#PreSubmit').on('click', function() {
		var formData = $('#ImportedDataCollectionUploadForm').serialize();
		$.ajax({
            type: "POST",
            url: '/ImportedDataCollections/checkDataExists',
            data: formData,
            dataType: 'html',
            success: function(data) { 
                if(JSON.parse(data).exists) {
                	$('#errContainer').html('Warning: Data for selected month/year aready exists and may be overwritten! Continue with upload?');
                	$('#PreSubmit').hide();
					objSlider('alertOvwright', 200);
					objSlider('ConfirmedSubmit', 200);
					objSlider('cancelSubmit', 200);
                } else {
                	$('#ConfirmedSubmit').click();
                }
            },
            error: function(data) {
                /*If user session expired the server will return a Forbidden status 403
                 *Refreshing the page will redirect the user to the login page thus preventing it from inserting into the DOM*/
                if (data.status===403){                     
                	location.reload();
                } else {
                	//Attempt to proceed with upload regarless of ajax error
                	$('#ConfirmedSubmit').click();
                }
            }
        });  
	});
	$('[name^="data[ImportedDataCollection]"], #cancelSubmit').on('click', function() {
		if ($('#PreSubmit').css('display') == 'none') {
			$('#PreSubmit').show();
			$('#ConfirmedSubmit').hide();
			$('#alertOvwright').hide();
			$('#cancelSubmit').hide();
		}
	});
});
function showLoadSpinner() {
	$('#errContainer').html('Working on it...<br/><img src="/img/spinner.gif" style="width:50px" />');
	$('#PreSubmit').hide();
	$('#ConfirmedSubmit').hide();
	$('#cancelSubmit').hide();
	$('#alertOvwright').show();
}
</script>