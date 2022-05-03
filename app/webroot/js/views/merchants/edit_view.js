$("#MerchantUserId").change(function() {
		if ($('#MerchantSmUserId').val() !== "") {
			$("#MerchantSmUserId ").effect("highlight", {}, 1900);
		}
		if ($('#MerchantSm2UserId').val() !== "") {
			$("#MerchantSm2UserId ").effect("highlight", {}, 1900);
		}
		$('#MerchantSmUserId option:selected, #MerchantSm2UserId option:selected').removeAttr('selected');
	});

$("#MerchantMerchantAcquirerId, #MerchantBetNetworkId").change(function() {
	if ($("#MerchantMerchantAcquirerId :selected").text() == 'Non Acquiring') {
		if (this.id !== $("#MerchantMerchantAcquirerId").attr('id')) {
			$('#MerchantMerchantAcquirerId option').filter(function() { 
			    return ($(this).text() == 'Non Acquiring'); //To select Non Acquiring
			}).prop('selected', true);
		}
		if (this.id !== $("#MerchantBetNetworkId").attr('id')) {
			$('#MerchantBetNetworkId option').filter(function() {
			    return ($(this).text() == 'Non Acquiring'); //To select Non Acquiring
			}).prop('selected', true);
		}
	}
});