//Auth fields are used to calculate Billing Auth fields ammounts
//The Auth fields are related to the Billing auth fields as follows
var relatedAuthFields = {
	MerchantPricingMcViAuth:"MerchantPricingBillingMcViAuth",
	MerchantPricingAmexAuthFee:"MerchantPricingBillingAmexAuth",
	MerchantPricingDsAuthFee:"MerchantPricingBillingDiscoverAuth",
	MerchantPricingDebitAuthFee:"MerchantPricingBillingDebitAuth",
	MerchantPricingEbtAuthFee:"MerchantPricingBillingEbtAuth"
}

//Create extended Jquery method
$.fn.extend({
	calcBillingAuth : function() {
		//get the id of the associated element where the result of this calculation will be set
		valTarget = relatedAuthFields[$(this).attr("id")];
		wirelessAuth = ($("#MerchantPricingWirelessAuthFee").val() == "")? 0 : parseFloat($("#MerchantPricingWirelessAuthFee").val());
		authFee = ($(this).val() == "")? 0 : parseFloat($(this).val());
		billingAuthFee = (authFee + wirelessAuth).toString();
		
		//get position of any decimal period
		dotPosition = billingAuthFee.indexOf(".");

		//If there is a decimal period, set presition to 3 decimals without rounding
		if (dotPosition !== -1) {
			billingAuthFee = billingAuthFee.substring(0, dotPosition + 4);
		}

		$("#" + valTarget).val(billingAuthFee);
	}
});

$("#MerchantPricingPerWirelessTermCost, #MerchantPricingNumWirelessTerm").blur(function(){
	totalWCost = $('#MerchantPricingPerWirelessTermCost').val() * $("#MerchantPricingNumWirelessTerm").val();
	$("#MerchantPricingTotalWirelessTermCost").val(totalWCost.toFixed(3));
});
$("#MerchantPricingDebitAcquirerId").change(function(){
	$("#MerchantPricingEbtAcquirerId").val($(this).val());
});

$("#MerchantPricingMcViAuth, #MerchantPricingAmexAuthFee, #MerchantPricingDsAuthFee, #MerchantPricingDebitAuthFee, #MerchantPricingEbtAuthFee, #MerchantPricingWirelessAuthFee").keyup(function() {
	if ($(this).attr("id") !== "MerchantPricingWirelessAuthFee") {
		$(this).calcBillingAuth();
	} else {
		$.each(relatedAuthFields, function(key, val) {
			$("#" + key).calcBillingAuth();
		});
	}
});

