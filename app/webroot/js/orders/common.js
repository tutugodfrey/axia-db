	$("#orderItemsTable tr").mouseenter(
			function () {
				$(this).css("backgroundColor", "#FFB");
			}
	);
	$("#orderItemsTable tr").mouseleave(
			function () {
				$(this).css("backgroundColor", "");
			}
	);
	function equipmentItemChanged(ddObj, replacementRowId, orderitemItemShipCost) {
		if ($('#' + ddObj.id + ' option:selected').text() === 'Replacement') {
			$('#' + replacementRowId).show(400);
			//hide for replacements since they have their own separete shipment info
			$('#' + orderitemItemShipCost).hide(400);
		} else {
			$('#' + replacementRowId).hide(400);
			$('#' + orderitemItemShipCost).show(400);
		}
	}