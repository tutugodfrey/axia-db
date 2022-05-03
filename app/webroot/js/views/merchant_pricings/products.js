//Toggle groups of products on/off in products and services
$("#productsOnBtn, #productsOffBtn").click(function(){
	if ($(this).attr('id') === 'productsOnBtn' && $(':checked[name=enablePrdsCheck]').length >= 2) {
		if (confirm("A total of " + $(':checked[name=enablePrdsCheck]').length + " products will be added to this merchant, continue?")) {
			showProductAlert('Adding selected products, please wait... <img src="/img/indicator.gif">', 'alert-info');
			productIds = getProductIds('enablePrdsCheck');
			ajaxAddRemoveProducts('addMany', $(this).attr('merchant-id'), productIds);
		}
	} else if ($(this).attr('id') === 'productsOffBtn' && $(':checked[name=disablePrdsCheck]').length >= 2) {
		if (confirm("A total of " + $(':checked[name=disablePrdsCheck]').length + " products will be removed from this merchant, are you sure?")) {
			showProductAlert('Removing selected products, please wait ... <img src="/img/indicator.gif">', 'alert-info');
			productIds = getProductIds('disablePrdsCheck');
			ajaxAddRemoveProducts('deleteMany', $(this).attr('merchant-id'), productIds);
		}else {
			return false;
		}
	} else {
		showProductAlert('Select more than one product to add multiple products.<br/>To add only one, just click on a product.', 'alert-danger');
	}
});

$("input[name=enablePrdsCheck], input[name=disablePrdsCheck").click(function(){
	if ($(':checked[name=enablePrdsCheck]').length >= 2) {
		$("#productsOnBtn").removeAttr('disabled');
		$("#productsOnBtn").removeClass('disabled');
	} else {
		$("#productsOnBtn").attr('disabled', true);
		$("#productsOnBtn").addClass('disabled');
	}
	if ($(':checked[name=disablePrdsCheck]').length >= 2) {
		$("#productsOffBtn").removeAttr('disabled');
		$("#productsOffBtn").removeClass('disabled');
	} else {
		$("#productsOffBtn").attr('disabled', 'disabled');
		$("#productsOffBtn").addClass('disabled');
	}
});

/**
 * getProductIds
 * Builds an array of product ids using the id attribute of the products checkboxes .
 * Only the id attribute of the checkboxes that are checked will be returned.
 * 
 * @param string checkboxName the name of the checkbox elements to retrieve the product ids from.
 * @return array 
 */ 
 	function getProductIds(checkboxName) {
 		productIdsArr = [];
		$.each($(":checked[name="+ checkboxName +"]"), function( index, element ) {
			productIdsArr[index] = $(element).attr('id');
		});
		return productIdsArr;
 	}
/**
 * showProductAlert
 * 
 * @param string msg the message to display in the products alert box
 * @param string cssClass the bootstrap-css class to use for context
 */ 
function showProductAlert(msg, cssClass){
	$("#productAlert").removeClass('alert-danger');
	$("#productAlert").removeClass('alert-success');
	$("#productAlert").removeClass('alert-warning');
	$("#productAlert").addClass(cssClass);
	$('#productAlertMsg').html(msg);
	$('#productAlertWrapper').slideDown();
}
/**
 * ajaxAddRemoveProducts
 * 
 *@param string action action to perform
 *@param string merchantId a merchant id
 *@param array productIds a single-dimention JS array of product ids
 */
function ajaxAddRemoveProducts(action, merchantId, productIds) {
	$.ajax({
			async: true, 
			type: "POST", 
			url: "/ProductsAndServices/" + action,
			dataType: "text", 
			data: {
				"merchant_id":merchantId,
				"product_ids": productIds
				}, 
			success: function (data) {
				if (data.match(/error/gi) === null) {
					showProductAlert(data.replace(/EOL/g, '<br>'), 'alert-success');
					location.reload();
				} else {
					showProductAlert(data, 'alert-danger');
					location.reload();
				}
			},
			error: function(data) {
				/*If user session expired the server will return a Unathorized status 401
				 *Refreshing the page will redirect the user to the login page thus preventing it from inserting into the DOM*/
				if(data.status===401) {
					showProductAlert('<strong>ERROR 401:<strong> Unauthorized', 'alert-danger');
					location.reload();
				}
			}
		});
}