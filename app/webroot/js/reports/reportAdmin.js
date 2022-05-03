
/**
 * confirmDeleteMany() 
 * Displays a confirmation message and submits the form '#deleteManyForm' which will 
 * trigger a request to delete all selected records.
 */
function confirmDeleteMany() {
	if (confirm("This will delete all data for all selected records\nAre you sure?")) {
		if (!hasSelected()) {
			alert('Nothing selected! Select records to delete first');
			return false;
		} else {
			setToDeleteDataJson();
			$('#deleteManyForm').submit();
			
		}
	}
}
/**
* setToDeleteDataJson
* Builds a JSON string of data that to be deleted by extracting data from 
* data-month and data-product-id attributes as well as the value from the element with id current_year.
* The JSON string is then assigned to a hidden input field with the id that end with "[id$='JsonDeleteData']"
* which is expected to be inside the form that submits this JSON data.
* 
* @returns void
*/
function setToDeleteDataJson() {
	var toDelete = {};
	var monthsArr = [];
	var productsArr = [];
	if ($('#current_year').length > 0 && $("[id$='JsonDeleteData']").length > 0) {
		$("[name='deleteBtnGroup'].btn-danger").each(function() {			
			if (!monthsArr[$(this).attr('data-month')]) {
				monthsArr[$(this).attr('data-month')] = new Array();
			}
			
			monthsArr[$(this).attr('data-month')].push($(this).attr('data-product-id'));
		});
		toDelete.year = parseInt($('#current_year').val());
		toDelete.months_products = monthsArr;
	}
	if (toDelete.months_products.length > 0) {

		jsonData = JSON.stringify(toDelete);
	} else {
		jsonData = {};
	}
	
	$("[id$='JsonDeleteData']").val(jsonData);
}

/**
 * hasSelected()
 * Checks if any of the togglable delete buttons are turned on or selected
 * 
 * @returns true if at least one button is turned on of false otherwise
 */
function hasSelected() {
	return ($("[name='deleteBtnGroup'].btn-danger").length > 0);
}

/**
 * toggleToBeDeleted()
 * Method is mainly intended for buttons or element objects that can behave as togglable on and off
 * or selected/unselected.
 * Works well with inline interactive elements such as buttons, anchors, spans and similar. 
 * Toggles on/off the passed element object
 *
 * @param object elementObj the DOM object that triggered this function call
 * @return void
 */
function toggleToBeDeleted(elementObj) {
	if ($(elementObj).hasClass('btn-default')) {
		$(elementObj).removeClass('btn-default');
		$(elementObj).addClass('btn-danger');
	} else if($(elementObj).hasClass('btn-danger')) {
		$(elementObj).removeClass('btn-danger');
		$(elementObj).addClass('btn-default');
	}
	if (hasSelected()) {
		$('#submitDeleteManyBtn').removeClass('btn-default');
		$('#submitDeleteManyBtn').addClass('btn-danger');
		$('#submitDeleteManyBtn').prop('disabled', false);
	} else {
		$('#submitDeleteManyBtn').removeClass('btn-danger');
		$('#submitDeleteManyBtn').addClass('btn-default');
		$('#submitDeleteManyBtn').prop('disabled', true);
	}
}