<?php
/* Options for the organization_id dropdown are set atomagically
 * by setting a variable named $organizations in the controller
 * rendering the view in which this element is embedded
 * 
 * Options for the other two fields will be generated via AJAX
 * as in a drilldown behavior.
 */
$editingMerchant = $this->name . '/' . $this->action === 'Merchants/edit';
echo $this->Form->input('Merchant.organization_id', ['id' => 'OrganizationId', 'empty' => '--', 'style' => (!$editingMerchant)?'min-width:120px;max-width:120px':null]);
echo $this->Form->input('Merchant.region_id', ['id' => 'RegionId', 'empty' => '--', 'style' => (!$editingMerchant)?'min-width:120px;max-width:120px':null]);
echo $this->Form->input('Merchant.subregion_id', ['id' => 'SubregionId', 'empty' => '--', 'style' => (!$editingMerchant)?'min-width:120px;max-width:120px':null]);
//Do not show locations from merchant edit action
if (!$editingMerchant) {
	echo $this->Form->input('Address.location_description', [
		'label' => 'Location',
		'type' => 'select',
		'id' => 'AddressLoc',
		'empty' => '--',
		'style' => 'min-width:140px;max-width:141px',
		'after' => '<img id="locsLoaderMsg" src="/img/indicator.gif" class="hide icon">']);
}
?>
<script>
	function getLocations(eventTriggerObject) {
		$('#AddressLoc').find('option').remove().end().append('<option value>--</option>');
		$('#locsLoaderMsg').removeClass('hide');
		$.ajax({
			async: true,
			data: $("#OrganizationId, #RegionId, #SubregionId").serialize(),
			dataType: "json",
			success: function(data, textStatus) {
				if (Object.keys(data).length) {
					$.each(data, function(k, v) {
						$("#AddressLoc").append('<option value="' + k + '">' + v + '</option>');
					});
				}
			},
			type: "post",
			url: "\/Addresses\/getLocations"
		}).always(function() {
			$('#locsLoaderMsg').addClass('hide');			
		});
		
		return false;
	}
	$(document).ready(function() {
		$("#OrganizationId").bind("change", function(event) {
			$('#RegionId').find('option').remove().end().append('<option value>--</option>');
			$.ajax({
				async: true,
				data: $("#OrganizationId").serialize(),
				dataType: "json",
				success: function(data, textStatus) {
					if (Object.keys(data).length) {
						$.each(data, function(k, v) {
							$("#RegionId").append('<option value="' + k + '">' + v + '</option>');
						});
					}
				},
				type: "post",
				url: "\/Organizations\/getRegions"
			});
			return false;
		});
		$("#OrganizationId, #RegionId").bind("change", function(event) {
			$('#SubregionId').find('option').remove().end().append('<option value>--</option>');
			$.ajax({
				async: true,
				data: $("#RegionId").serialize(),
				dataType: "json",
				success: function(data, textStatus) {
					if (Object.keys(data).length) {
						$.each(data, function(k, v) {
							$("#SubregionId").append('<option value="' + k + '">' + v + '</option>');
						});
					}
				},
				type: "post",
				url: "\/Organizations\/getSubregionsByRegion"
			});
			return false;
		});		

		if ($("#AddressLoc").length == 1) {
			$("#AddressLoc").bind('mousedown', function(event) {
				//Remove event handler
				$("#AddressLoc").unbind('mousedown');
				getLocations($("#OrganizationId"));
			});
		}
		$("#OrganizationId, #RegionId, #SubregionId").bind("change", function (event) {
			//Remove mousedown event handler when change event is triggered on related dropdown menus
			$("#AddressLoc").unbind('mousedown');
			getLocations(event.target)
		});
	});
</script>
<?php
