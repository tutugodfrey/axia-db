
<?php /* Drop breadcrumb */
$this->Html->addCrumb('Maintenance Dashboards', ["controller" => 'MaintenanceDashboards', 'action' => 'main']);
$this->Html->addCrumb('MID Numbers Generator');
 ?>
<input type="hidden" id="thisViewTitle" value="<?php echo __('MID Numbers Generator'); ?>" />
<div class="row">
	<div class="col-md-6 col-sm-12">
			<div class="panel panel-info">
				<div class="panel-body">

					<?php
						echo $this->Html->tag('div', 
							$this->Html->tag('span', null, ['class' => 'glyphicon glyphicon-info-sign pull-left text-primary', 'style' => 'font-size:14pt;margin: 0px 10px 20px 0px; z-index: 99']) . $this->Html->tag('/span') .
							$this->Html->tag('span', "The new MID's will be generated based on the MID number entered below, which must be the last (or close to the last) MID number known to exist.<br/> This will generate 16 digit MID numbers starting with 123922005 exactly divisible by 10 (Mod10), make sure the number you enter properly follows known sequence.", ['class' => 'list-group-item text-primary']), 
							['class' => 'strong']);
						echo "<br/>";
						echo $this->Form->create('Merchant', [
							'inputDefaults' => [
										'div' => 'form-group',
										'label' => false,
										'class' => 'form-control',
									],
							'class' => 'form-inline'
							]);
						echo $this->Form->input('last_known_mid', [
							'label' => 'Enter the last known unused MID number (must start with 123922005 and have a total of 16 digits):',
							'required' => 'required',
							'type' => 'number',
							'step' => '1',
						]);
						echo $this->Form->input('amount', [
							'label' => 'Enter amount of MID numbers to generate:',
							'type' => 'number',
							'placeholder' => '(max 2000)',
							'step' => '1',
							'min' => '1',
							'max' => '2000',
							'style' => 'width:150px',
						]);
					?>
				</div>
				<?php
					echo $this->Html->tag('div', 
						$this->Form->end(['label' =>__('Generate'), 'div' => 'center-block text-center', 'class' => 'btn btn-success']),
					['class' => 'panel-footer']);
				?>
			</div>
	</div>
	<div class="col-md-6 col-sm-12">
		<div class="panel panel-default panel-info">
			<div class="panel-body">
				<?php
					if (!empty($midList)) {
						$available = $assigned = [];
						foreach ($midList as $listItem) {
							if (empty($listItem['id'])) {
								$available[] = [h($listItem['mid'])];
							} else {
								$assigned[] = [h($listItem['mid'])];
							}
						}

						echo '<div style="max-height:200px;overflow:auto" class="col-md-5 col-sm-5">';
							echo "<table class='table table-condensed table-hover table-bordered' id='midList'>";
								echo $this->Html->tableHeaders([['New MIDs List' => ['class' => 'text-center bg-success']]]);
								if (!empty($available)) {
									echo $this->Html->tableCells($available);
								} else {
								 	echo $this->Html->tableCells([[['All the MIDs generated have already been assigned to existing merchants!<br/>Enter the last known number in the sequence and try again.', ['class' => 'strong bg-danger']]]]);
								}
							echo "</table>";
						echo "</div>";
						if (!empty($assigned)) {
							echo '<div style="max-height:200px;overflow:auto" class="col-md-5 col-sm-5">';
								echo "<table class='table table-condensed table-hover table-bordered bg-danger' style='width:300px; max-width:300px;'>";
								echo $this->Html->tableHeaders(['<span class="badge pull-right">' . count($assigned) . '</span>' . 'The result generated the following MIDs which are already assigned and were excluded.']);
								echo $this->Html->tableCells($assigned);
								echo "</table>";
							echo "</div>";
						}

					} else {
						echo $this->Html->tag('div', $this->Html->tag('span', '- A list has not been generated -', ['class' => 'list-group-item text-center text-muted']), ['class' => 'text-center']);
					}
				?>
			</div>
			<?php
				$expBtnHtml = '';
				if (isset($available) && !empty($available)) {
					$expBtnHtml = $this->Html->link(
							$this->Html->image('/img/csv.gif') . ' Export new MIDs to CSV',
							'#',
							['escape' => false, 'class' => 'btn btn-primary btn-sm strong', 'onClick' => "exportTableToCSV('newMidSequence.csv', 'midList')"]
						);
				}
					echo $this->Html->tag('div', $expBtnHtml, ['class' => 'panel-footer text-center']);
				?>
		</div>
	</div>
</div>
<?php 
echo $this->AssetCompress->script('reports', array('raw' => (bool)Configure::read('debug')));
?>