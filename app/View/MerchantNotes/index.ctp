<?php /* Drop breadcrumb */ $this->Html->addCrumb(Inflector::humanize(Inflector::underscore($this->name)) . ' ' . $this->action, '/' . $this->name); ?>
<input type="hidden" id="thisViewTitle" value="<?php echo __('Merchant Notes'); ?> List" />
<?php
//Load form imput control plugin
echo $this->element('Layout/selectizeAssets');
$exportLinks = [];
if (!empty($merchantNotes)) {
	$icon = $this->Csv->icon(null, [
		'title' => __('Export Notes and Requests'),
		'class' => 'icon'
	]);
	$exportLinks[] = $this->Csv->exportLink($icon, 
		array_merge([
			'plugin' => false,
			'controller' => 'MerchantNotes',
			'action' => 'index',
			'ext' => 'csv',
			'?' => $this->request->query,
		],
		Hash::extract($this->request->params, 'paging.Merchant.options'))
	);
}
if (!empty($exportLinks)) {
	echo '<span class="pull-left well-sm">';
	echo "<strong>Export Data:</strong><br>";
	echo $this->element('Layout/exportCsvUi', compact('exportLinks'));
	echo '</span>';
}
echo $this->Form->createFilterForm('MerchantNote');
echo $this->Form->input('action_type', array('label' => __('Request Type'), 'options' => $actionTypes,'empty' => 'All', 'required' => false));
echo $this->Form->input('general_status', array(
	'label' => __('Status'),
	'empty' => 'All',
	'options' => $statusList
));

$options = array('author' => __('by Author'), 'rep' => __('by Rep'));
$defaultVal = ($this->request->data('MerchantNote.author_type'))? $this->request->data('MerchantNote.author_type') : 'rep';
$attributes = array('separator' => '<br/>', 'legend' => false, 'value' => $defaultVal);
?>
<div class="form-group">
	<?php
	echo '<strong>Author or Rep?</strong><br />';
	echo $this->Form->radio('author_type', $options, $attributes);
	?>
</div>
<?php
echo $this->Form->complexUserInput('user_id', array("class" => "single col col-xs-12", "style" => "min-width:200px"));
echo $this->Form->input('author_name', array('label' => 'Enter Note Author ', 'div' => ['class' => 'form-group', 'style' => 'display:none']));
echo $this->Form->input('dba_mid', array('label' => 'Merchant '));
echo $this->Form->input('from_date', array(
	'type' => 'date',
	'dateFormat' => 'YM',
	'maxYear' => date('Y')
));
echo $this->Form->input('end_date', array(
	'type' => 'date',
	'dateFormat' => 'YM',
	'maxYear' => date('Y')
));
echo $this->Form->end(array('label' => 'Search', 'class' => 'btn btn-default', 'div' => array('class' => 'form-group')));
echo $this->Paginator->pagination(array('ul' => 'pagination pagination-mini'));
echo $this->element('MerchantNote/notes_and_requests');
echo $this->AssetCompress->script('merchantNotesIndex', array('raw' => (bool)Configure::read('debug')));
?>
<script>
	$('#MerchantNoteUserId').selectize();
	$('#MerchantNoteUserId')[0].selectize.setValue('<?php echo htmlspecialchars($this->request->data("MerchantNote.user_id"));?>');
	$('div.selectize-control').attr('style', 'min-width:200px');

</script>