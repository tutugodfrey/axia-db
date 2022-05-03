<?php /* Drop breadcrumb */ $this->Html->addCrumb(Inflector::humanize(Inflector::underscore($this->name)) . ' ' . $this->action, '/' . $this->name); ?>
<input type="hidden" id="thisViewTitle" value="<?php echo __('Merchants Products Pricing Archive'); ?> List" />
<div class="row well well-sm">
<?php 
$yearSelected = h($this->request->data('MerchantPricingArchive.year'));
echo $this->element('Layout/ReportsAdmin/reportSearchForm');

if ($this->Rbac->isPermitted('app/actions/MerchantPricingArchives/deleteMany')) {
	echo $this->element('Layout/ReportsAdmin/deleteManyForm');
}
?>
</div>
<?php 
	$goPrevYr = $this->Html->link('<span name="lftRgtNavArrows" class="glyphicon glyphicon-triangle-left text-success"></span>',
		array(
			'controller' => 'MerchantPricingArchives',
			'action' => 'index',
			'?' => array('year' => $yearSelected - 1)
		),
		array(
			'data-original-title' => $yearSelected - 1,
			'data-placement' => "left",
			'data-toggle' => "tooltip",
			'escape' => false,
		)
	);
	$goNextYr = $this->Html->link('<span name="lftRgtNavArrows" class="glyphicon glyphicon-triangle-right text-success"></span>',
		array(
			'controller' => 'MerchantPricingArchives',
			'action' => 'index',
			'?' => array('year' => $yearSelected + 1)
		),
		array(
			'data-original-title' => $yearSelected + 1,
			'data-placement' => "right",
			'data-toggle' => "tooltip",
			'escape' => false,
		)
	);
?>
<h2 class="text-center panel-heading">
	<?php echo $goPrevYr;?><span class="label list-group-item-success panel-heading">
		<span class="glyphicon glyphicon-calendar"></span> 
		 <?php 
		 	echo (!empty($data))? $yearSelected: $yearSelected . ' (No data)';
		 	//Hidden field used for JS deleteMany 
		 	echo $this->Form->hidden('current_year', ['value' => h($this->request->data('MerchantPricingArchive.year'))]);
		 ?>
	</span>
	<?php echo $goNextYr; ?>
</h2>
<?php 
	if (!(empty($data))) {
		$curMonth = null;
		$nextRecordMonth = null;
		foreach ($data as $idx => $val) {
			$prodName = $val['ProductsServicesType']['products_services_description'];
			$prodId = $val['ProductsServicesType']['id'];
			$curMonth = Hash::get($val, '0.month');
			$nextRecordMonth = Hash::get($data, $idx + 1 .'.0.month');
			$month = date("F", mktime(0, 0, 0, $curMonth, 10));
			$cells[] = array(
				h($prodName),
				array(
					$this->Html->link('<span class="glyphicon glyphicon-trash"></span>', 'javascript:void(0)', 
					array(
						'data-month' => Hash::get($val, '0.month'),
						'data-product-id' => $prodId,
						'name' => 'deleteBtnGroup',
						'class' => 'btn btn-xs btn-default',
						'escape' => false,
						'onClick' => "toggleToBeDeleted(this)"
					)),
					array('class' => 'text-right')
				)
			);

			if ($curMonth + 1 == $nextRecordMonth || is_null($nextRecordMonth)) {
				//we are at the last record of the curMonth or at the end of the list when nextRecordMonth is null
				//echo everything for curMonth
				echo '<div class ="col col-md-3 col-sm-4 col-xs-4">
						<div class ="panel panel-success">
							<div class ="panel-heading">
								<h3 class="panel-title text-center">' . $month . '</h3>
							</div>
							<table class="table table-condensed table-hover">';
								echo $this->Html->tableHeaders(array('Product', array('Functions' => array('class' => 'text-right'))));
								echo $this->Html->tableCells($cells) .
							'</table>
						</div>
					</div>';
				//for small devices show 3 columns properly lined up
				echo ($curMonth%3===0)? '<div class="clearfix visible-sm-block visible-xs-block"></div>' : null;
				//for larger devices show 4 columns properly lined up
				echo ($curMonth%4===0)? '<div class="clearfix visible-md-block visible-lg-block"></div>':null;
				//reset variables
				$cells = [];
			}
		}
	}
?>
<div class="clearfix"></div>
<?php if (!empty($data)) : ?>
		 <h2 class="text-center panel-heading">
		<?php echo $goPrevYr; ?><span class="label list-group-item-success panel-heading">
			<span class="glyphicon glyphicon-calendar"></span> 
			 <?php echo $yearSelected; ?>
		</span>
		<?php echo $goNextYr; ?>
	</h2>
<?php endif; ?>
<div class="row well well-sm">
	<?php echo $this->element('AjaxElements/Admin/bg_processes_tracker')?>
	<div class="col-lg-offset-1 col-md-offset-1 col-xs-5 col-sm-5 col-md-5 col-lg-5">
		<?php
		echo $this->Html->tag('div', null, ['class' => 'panel panel-info']);
		echo $this->Form->create(Inflector::singularize($this->name), array(
			'id' => 'archiveForm',
			'inputDefaults' => array(
				'div' => 'form-group',
				'wrapInput' => true,
				'class' => 'form-control'
			),
			'class' => 'form-inline panel-body'
		));
		echo $this->Html->tag('span', "Create New Pricing Archives", ['class' => 'col-md-12 col-sm-12 strong text-primary']);
		echo $this->Html->tag('hr', null, ['class' => 'alert-info']);
		?>
		<table class="center-block" style='width: min-content;'>
			<tr>
				<td>
					<?php
					echo $this->Form->input('archive_year', array('label' => 'Year', 'options' => $years));
					echo $this->Form->input('archive_month', array('label' => 'Month', 'options' => $months));
					echo $this->Form->input('products', array(
						'id' => 'archiveSubmit', 'multiple' => 'multiple',
						'style' => 'height:500px',
						'label' => array('text'=>'Select Product(s) to Archive',
							'data-toggle' => 'tooltip',
							'data-placement' => 'bottom',
							'title' => __('To select all products, first select one then pless Ctrl+A. (This menu is resizable).'),
							'class' => 'col col-md-12 control-label'),
						'options' => $optnsProducs));
					?>
				</td>
			</tr>
		</table>

		<?php
		$options2 = array('label' => 'Run Pricing Archive...',
				'name' => 'archiveBtn',
				'class' => 'center-block btn btn-success btn-sm',
				'before' => '');

		echo $this->Form->end($options2);
		echo $this->Html->tag('/div'); //closing panel panel-info div
		?>
	</div>
</div>
<script type="text/javascript">
	$(function() {
		$('#archiveSubmit').resizable({
			maxWidth: 290,
			minWidth: 290,
			maxHeight: 600,
			minHeight: 200
		});
	});
</script>
