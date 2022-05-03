$(function() {
	$('#merchant-rejects-index, #rejectFilterTable').on('click', 'a.edit-merchant-rejects', function() {
		var dataUrl = $(this).attr('data-target');
		var targetTr = $(this).parent().parent();

		//replace row with the edit form
		$.ajax({
			type: "GET",
			url: dataUrl,
			success: function(result) {
				TrUtils.replaceTr(targetTr, result);
			}
		});
		return false;
	});

	$('#merchant-rejects-index, #rejectFilterTable').on('click', 'a.submit-merchant-rejects', function() {
		var form = $(this).closest("form");

		var dataUrl = form.prop('action');
		var targetTr = form.parent().parent();

		//replace row with the edit result
		$.ajax({
			type: "POST",
			url: dataUrl,
			data: form.serialize(),
			success: function(result) {
				TrUtils.updateMerchantReject(targetTr);
				TrUtils.replaceTr(targetTr, result);
			}
		});
		return false;
	});

	$('#merchant-rejects-index').on('click', 'a.delete-merchant-rejects', function() {
		var dataUrl = $(this).attr('data-target');
		var targetTr = $(this).parent().parent();
		// hide row after delete
		$.ajax({
			type: "POST",
			url: dataUrl,
			success: function(result) {
				if (result == 'ok') {
					TrUtils.updateMerchantReject(targetTr);
					targetTr.remove();
					TrUtils.updateTrOddClass('#merchant-rejects-index');
				} else {
					alert('The Merchant reject could not be deleted');
				}
			}
		});
		return false;
	});

	$('#merchant-rejects-index, #rejectFilterTable').on('click', 'a.cancel-merchant-rejects', function() {
		var form = $(this).closest("form");

		var dataUrl = $(this).attr('data-target');
		var targetTr = form.parent().parent();

		//replace row with the view row
		$.ajax({
			type: "GET",
			url: dataUrl,
			success: function(result) {
				TrUtils.replaceTr(targetTr, result);
			}
		});
		return false;
	});

	$('#merchant-rejects-index').on('click', 'a.add-merchant-reject-lines', function() {
		var dataUrl = $(this).attr('data-target');
		var targetTr = $(this).parent().parent();
		// create a new row with the add form
		$.ajax({
			type: "GET",
			url: dataUrl,
			success: function(result) {
				var findClass = 'even';
				if (!targetTr.hasClass(findClass)) {
					findClass = 'odd';
				}
				var currentTr = targetTr;
				while (currentTr.hasClass(findClass)) {
					targetTr = currentTr;
					currentTr = currentTr.next('tr');
				}
				targetTr.after(result);
				$('html, body').animate({
					scrollTop: targetTr.offset().top
				}, 2000);
				TrUtils.updateMerchantReject(targetTr);
				TrUtils.updateTrOddClass('#merchant-rejects-index');
			}
		});
		return false;
	});
});
var TrUtils = {
	/**
	 * Remove the old TR and add the new in the same position
	 *
	 * @param jQueryObject targetTr element to be replaced
	 * @param html result to be inserted
	 *
	 * @returns void
	 */
	replaceTr : function _replaceTr(targetTr, result) {
		var previousTr = targetTr.prev('tr');
		if (previousTr.prop('tagName') === 'TR') {
			previousTr.after(result);
		} else {
			// if there is no previous Tr, add it at the begin of the table
			targetTr.parent().prepend(result);
		}
		targetTr.remove();
		TrUtils.updateTrOddClass('#merchant-rejects-index');
	},
	/**
	 * Check all tr child nodes and update the class based on the data-is-reject attribute
	 * @param {type} parentSelector
	 * @returns {undefined}
	 */
	updateTrOddClass : function _updateTrOddClass(parentSelector) {
		var trs = $(parentSelector).find("tr");//[data-is-reject='1']");
		var rejectIndex = 0;
		var trsLength = trs.length;
		for (var index = 1; index < trsLength; ++index) {
			var tr = trs[index];
			if (($(tr).attr('data-is-reject') == '1')) {
				rejectIndex++;
			}
			if (rejectIndex % 2 === 1) {
				$(tr).addClass('even');
			} else {
				$(tr).addClass('odd');
			}
		}
	},
	/**
	 * Update the Merchant Reject row of a Reject line
	 *
	 * @param jQueryObject targetTr element of the updated reject line
	 *
	 * @returns void
	 */
	updateMerchantReject : function _updateMerchantReject(targetTr) {
		var currentTr = targetTr;
		var rejectTr = false;
		// Search for the parent MerchantReject TR
		do {
			if ((currentTr.attr('data-is-reject') == '1')) {
				rejectTr = currentTr;
			}
			currentTr = currentTr.prev('tr');
		} while ((rejectTr === false) && (currentTr.prop('tagName') === 'TR'));

		if (rejectTr !== false) {
			var dataUrl = rejectTr.attr('update-target');
			$.ajax({
				type: "GET",
				url: dataUrl,
				success: function(result) {
					TrUtils.replaceTr(rejectTr, result);
				}
			});
		}
	}
};
