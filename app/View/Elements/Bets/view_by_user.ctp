<div class="row">
	<div class="col-md-12">
		<div class="contentModuleTitle panel-heading bg-success">
			<?php	echo $this->Html->sectionHeader(__('Bet tables')); ?>
		</div>

		<div class="col-md-12">
			<div id="tabs">
				<ul>
					<?php
					// Tab menu extract card types
					$tabContentIds = array();
					foreach ($betTables as $cardTypeName => $betTablesData) {
						// We expect betTablesData to have >= 1 entries
						$cardTypeId = Hash::get($betTablesData, "0.card_type_id");
						$tabContentId = "cardType_{$cardTypeId}";
						$tabContentIds[$cardTypeId] = $tabContentId;
						echo $this->Html->tag('li', "<a href='#{$tabContentId}'>" . h($cardTypeName) . "</a>");
					}
					?>
				</ul>
				<?php
					// Tabs content. Notice we are reusing $betTablesData from above
					foreach ($tabContentIds as $cardTypeId => $tabContentId) {
						$tabContent = '';
						//Extract subset of bet tables for current $cardType
						foreach (Hash::extract($betTables, "{s}.{n}[card_type_id={$cardTypeId}]") as $betTableSubSet) {
							$betTableContainerId = "betTableContainer_{$cardTypeId}_{$betTableSubSet['id']}";
							$betTableTitleId = "betTableTitle_{$cardTypeId}_{$betTableSubSet['id']}";

							$tabContent .= $this->Html->tag(
								'h3',
								h($betTableSubSet['name']),
								array(
									'class' => 'bet-table-accordion-title',
									'id' => $betTableTitleId,
								)
							);
							$tabContent .= $this->Html->tag(
								'div',
								'', // the content is loaded from an ajax request
								array('id' => $betTableContainerId)
							);
						}

						echo $this->Html->tag('div', $tabContent, array('id' => $tabContentId));
					}
				?>
			</div>
		</div>
	</div>
</div>

<script type='text/javascript'>
	$(document).ready(function () {

		$("#user-profile-role-edit").bind("mouseover", function (event) {$("#user-profile-role-edit").css("cursor", "pointer")
return false;});
$("#user-profile-role-edit").bind("click", function (event) {$("#user-profile-role-links, #user-profile-role-input").toggle()
return false;});
$(".bet-table-accordion-title").bind("click", function (event) {
	var elementId = $(this).attr('id');
	var idParts = elementId.split('_');
	var cardTypeId = idParts[1];
	var betTableId = idParts[2];
	var userId = '<?php echo Hash::get($user, 'User.id'); ?>';
	var compensationId = '<?php echo Hash::get($user, 'UserCompensationProfile.id'); ?>';
	var partnerUserId = '<?php echo $partnerUserId; ?>';
	viewBetTable(cardTypeId, betTableId, userId, compensationId, partnerUserId);
return false;});

		$("#tabs").tabs();
		<?php
		foreach ($tabContentIds as $tabContentId) {
			echo "$('#{$tabContentId}').accordion({active: false, collapsible: true, heightStyle: 'content'});";
		}
		?>
	});

	function viewBetTable(cardTypeId, betTableId, userId, compensationId, partnerUserId) {
		var requestUrl = "<?php echo Router::url(array('plugin' => false, 'controller' => 'bets', 'action' => 'view')); ?>";
		requestUrl += '/' + cardTypeId + '/' + betTableId + '/' + userId + '/' + compensationId + '/' + partnerUserId;

		var containerId = '#betTableContainer_' + cardTypeId + '_' + betTableId;

		$.ajax({
			async: true,
			dataType: 'html',
			type: 'get',
			url: requestUrl,
			success: function (data, textStatus) {
				$(containerId).empty();
				$(containerId).html(data);
			},
			error: function (data, textStatus) {
				$(containerId).empty();
				<?php
				if (Configure::read('debug') > 0) {
					echo "$(containerId).html(data);";
				} else {
					echo "$(containerId).html('" . __('Error loading the bet table information') . "');";
				}
				?>
			}
		});
	}
</script>
