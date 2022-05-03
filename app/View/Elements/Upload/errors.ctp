<?php if (!empty($this->request->data)) : ?>
	<div class="form-upload">
		<?php
		echo $this->Html->tag('h2', __('Import results'));
		echo $this->Html->tag('h3', __('Imported %s rows', is_countable($importResult)?count($importResult):0));
		echo $this->Html->tag('h3', __('Skipped %s rows', is_countable($importSkippedRows)?count($importSkippedRows):0));
		?>
		<?php if (!empty($importSkippedRows)) {
			$skippedResons = array_unique($importSkippedRows);
			echo $this->Html->tag('div', 
				$this->Html->tag('div', 
					$this->Html->tag('div', '<strong>Skipped Log</strong>', ['class' => 'panel-heading']) .
					$this->Html->tag('div', implode('.<br>', $skippedResons), ['class' => 'panel-body', 'style' => 'max-height:500px;overflow:auto']),
				['class' => 'panel panel-default'])
			, ['class' => 'col-sm-5 col-md-5']);
		}

		?>
		<?php if (!empty($importErrors)): ?>
			<table class="table table-bordered table-condensed table-striped">
				<thead>
				<th><?php echo __('Row'); ?></th>
				<th><?php echo __('Errors'); ?></th>
				</thead>
				<tbody>
				<?php foreach ($importErrors as $key => $row): ?>
					<tr>
						<td><strong><?php echo ($key + 1); ?></strong></td>
						<td>
							<?php if (!is_array($row)):
								echo $row;
							else: ?>
								<?php foreach($row['validation'] as $field => $errors): ?>
									<strong><?php echo Inflector::humanize($field); ?></strong>
									<ul>
										<?php foreach($errors as $error): ?>
											<li><?php echo $error; ?></li>
										<?php endforeach; ?>
									</ul>
								<?php endforeach; ?>
							<?php endif; ?>
						</td>
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>
		<?php endif; ?>
	</div>
<?php endif;
