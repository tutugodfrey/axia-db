/**
* Some requests are too long to wait for them to finish processing and are handed over to the server as a background job
* This set of procedures will help track all backgrounded jobs and their statuses asynchronously usng a long polling strategy.
* 
* @autor Oscar Mota 2018-11-09
*/

//Keep track of number of calls made to request for updates
var POLL_CALL_COUNT = 0;

$(document).ready(function() {
	//Begin polling
	pollJobStatuses();
});

/**
 * pollJobStatuses 
 * Creates a time out which triggers an ajax call after a given number of seconds.
 * Increases the poll count and after a given number of calls discontinues the poll completely
 * in order to prevent infinite calls and allow user session to expire as usual due to inactivity.
 * 
 * @return void
 */
function pollJobStatuses() {
	if (POLL_CALL_COUNT < 1000) {
		POLL_CALL_COUNT = POLL_CALL_COUNT + 1;
	    setTimeout(updateList, 5000);
	}
}
/**
 * updateList
 * Updates the $('#bg-status-list') and displays a list of all bacgrounded processes' statuses
 * 
 * @param boolean keepPolling if omitted polling will begin. When false, only one AJAX call will be made to retrieve the latest data
 * @return void
 */
function updateList(keepPolling) {
	ajaxParams = {
        url: "/BackgroundJobs/updateList",
        type: "GET",
        dataType: 'html',
        success: function(data) {
            $('#bg-status-list').html(data);
        },
        error: function(data) {
       		/*If user session expired the server will return a Forbidden status 403
         	*Refreshing the page will redirect the user to the login page thus preventing it from inserting into the DOM*/
            if(data.status===403){                     
				location.reload();
            }
            $('#bg-status-list').html('<div class="alert alert-danger text-center strong">Server Request Error: <br />Try again later.</div>');
      	},
        timeout: 6000
    }

    if (keepPolling !== false) {
    	ajaxParams.complete = pollJobStatuses;
    }

    $.ajax(ajaxParams);
}