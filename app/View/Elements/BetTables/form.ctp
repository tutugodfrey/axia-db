<?php
echo $this->Form->create();
?>

<table class="table">
	<?php foreach ($networks as $network) : ?>
		<tr>
			<td>
				<?php
				echo h($network['Network']['network_description']);
				$this->Form->input($network['Network']['network_description']);
				?>
			</td>
			<td>
				<?php
				echo $this->Form->input('bc_pi', array(
						  'label' => __('BC Pi')
				));
				?>
			</td>
			<td>
				<?php
				echo $this->Form->input('bc_pi', array(
						  'label' => __('BC Pi')
				));
				?>
			</td>
			<td>
				<?php
				echo $this->Form->input('bc_pi', array(
						  'label' => __('BC Pi')
				));
				?>
			</td>
			<td>
				<?php
				echo $this->Form->input('bc_pi', array(
						  'label' => __('BC Pi')
				));
				?>
			</td>
			<td>
				<?php
				echo $this->Form->input('bc_pi', array(
						  'label' => __('BC Pi')
				));
				?>
			</td>
		</tr>
	<?php endforeach; ?>
</table>

<?php
echo $this->Form->button(__('Save BET & Apply Pricing'));
echo $this->Form->button(__('Cancel'));
echo $this->Form->end();
?>