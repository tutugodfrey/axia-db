var AJAXrequest;

/*
 * Autocomplete functionality - requires JQuery UI library
 * Can be implemented to any textield by simply passing its id to autocomplete
 * 
 * Implements a function callback to allow Ajax functionality 
 * 
 * @param string autoCompleteSelector, jquery selector for the merchant_id input. Ej: "#MerchantSearch"
 * @param string merchantActiveSelector, jquery selector for the checkbox input to indicate if the merchant must be active. NULL to not check the active field
 *
 * request = user input
 * response = Returns sets of matching Merchant MID numbers or Business names (DBA) depending on user input (JSON encoded array) 
 * Responce is passed as the source which requires an array or a JSON encoded object acting as associative array.
 * 
*/
function merchantAutocomplete(autoCompleteSelector, merchantActiveSelector) {
	$(autoCompleteSelector).autocomplete({
		source: function(request, response) {
			var url = appMerchantsSearchUrl + '/' + request.term;

            var merchantStatus = ($('#MerchantActive').is(':checked')) ? 1 : 0;
            $.ajax({
                type: "POST",
                url: "/Merchants/autoCompleteSuggestions/" + merchantStatus + "/" + request.term,
                dataType: 'html',
                success: function(data) {
                    response(JSON.parse(data));
                },
                error: function(data) {
                    /*If user session expired the server will return a Forbidden status 403
                     *Refreshing the page will redirect the user to the login page thus preventing it from inserting into the DOM*/
                    if (data.status === 403) {
                        location.reload();
                    }
                    //pass an empty array to close the menu if it was initially opened
                    response([]);
                }
            });/*end ajax*/
        }
    });
}

$(function() {
	merchantAutocomplete('#MerchantSearch', '#MerchantActive');
	merchantAutocomplete('#MerchantRejectMerchantId', null);
});

//When user is leaving the page ignore any unfinished ajax requests
//This prevents a permanent ajaxError to be bound to the window, causing all subsequent ajax requests to fail
$(window).bind('beforeunload', function () {
    $(document).unbind('ajaxError');
});

$(document).ready(function() {
  //Internet Explorer Browser do not support CSS transitions2D which are used used by the slider input
  if (navigator.userAgent.indexOf("Trident") > -1 || navigator.userAgent.indexOf("WOW64") > -1) {
    $("[name='sliderControlObj']").removeClass('slider');
  }

  $("span[name='lftRgtNavArrows']").hover(function(){$(this).css('text-shadow', '1px 1px #000000')}, function(){$(this).css('text-shadow', 'none')});
  //Set formatting for negative dollar amounts everywhere
  var regex = new RegExp(/^\(\$[\d{0,3},]*[\d]{0,3}\.[\d]{0,5}\)/m); // ($[ddd,ddd,ddd,]ddd.dddd)
  var allCells = document.getElementsByTagName('td');
  for(var i=0; i < allCells.length; i++) {
    if (regex.test($(allCells[i]).text())) {
        $(allCells[i]).addClass('text-danger');
      }
  }
  $('td:contains("'+" ' ' "+'")').addClass('text-center strong text-primary');

  //Clear value of this object located in admin maintenance dashboard
  $("#MaintenanceDashboardModelName").val([]);
  //Initialize BoostCake/Bootstrap PopOver and Tooltip functionalities
$('body').popover({
          selector: '[data-toggle="popover"]'
      });

$('body').tooltip({
    selector: 'a[rel="tooltip"], [data-toggle="tooltip"]'
}); 
    
  //Creates the title for the current view
    if(document.getElementById("thisViewTitle"))/*If object exists*/
       document.getElementById("layoutViewTitle").innerHTML = document.getElementById("thisViewTitle").value;
   
  // fade out non-error flash messages
  $('.success').fadeOut(5000, function() { $(this).remove(); });
  $('.message').fadeOut(5000, function() { $(this).remove(); });

    $("#ProductSettingGralFeeMultiplier, #ProductSettingGralFee, #ProductSettingMonthlyFee").on("blur", function(){
      multiplier = ($.isNumeric($("#ProductSettingGralFeeMultiplier").val()))? parseFloat($("#ProductSettingGralFeeMultiplier").val()) : 0;
      gralFee = ($.isNumeric($("#ProductSettingGralFee").val()))? parseFloat($("#ProductSettingGralFee").val()) : 0;
      monthlyFee = ($.isNumeric($("#ProductSettingMonthlyFee").val()))? parseFloat($("#ProductSettingMonthlyFee").val()) : 0;
      $("#ProductSettingMonthlyTotal").val((multiplier * gralFee) + monthlyFee);
  });
  //Calculate payment fusion product monthly total
  calcPfMonthlyTotal();

});

function renderContentAJAX(controller, action, id, containerID, actionPath) {
  /*    This function asynchronously renders Elements stored in /View/Elements/AjaxElements.
   *    The cakephp action that responds to this ajax request must be defined in its corresponding Controller.
   *    Data returned by action must be the html,javascript,etc. that was defined in the AjaxElement.
   *    
   *    Requered args: 
   *     *  /controller/action or actionPath. The controller and action to make the request to.
   *     *  containerID = DOM id of element to dump returned HTML content
   *     
   *    actionPath = must be any valid path if the action path is deeper than 2 levels (/controller/action)
   *    id = an id relevant to requested action (optional)
   *               
   **/
    var reqURL;
    if(actionPath)
        reqURL = actionPath;
    else 
        reqURL= "/"+controller+"/"+action;
    if(id)
        reqURL += "/" + id;

      AJAXrequest = $.ajax({
            type: "POST",
            url: reqURL,
            dataType: 'html',
            success: function(data) { 
                
                $('#'+containerID).html(data);
            },
            error: function(data) {
                /*If user session expired the server will return a Forbidden status 403
                 *Refreshing the page will redirect the user to the login page thus preventing it from inserting into the DOM*/
                    if(data.status===403){                     
                     location.reload();
                    }
                    $('#'+containerID).html('<div class="error-message">Server Request Error: <br />Sorry try again later.</div>');
                  }
        });         
}

function ajaxFormSubmit (thisForm){
            var formData = $(thisForm).serialize();            
            var formUrl = $(thisForm).attr('action');             
            $.ajax({
                type: 'POST',
                url: formUrl,
                data: formData,
                dataType: 'html',
                success: function(jqXHR){
                    
                       if ( $("#flashMessage"))/*if object already exist remove it to avoid diplicate DOM id's*/
                           $("#flashMessage").remove();
                       
                     $('<div id="flashMessage" class="success">Data Processed successfully!</p>').insertBefore(thisForm.parentNode);
                     objFader('flashMessage',3000);
                     $(thisForm.parentNode).remove();/*remove the form when success submition*/
                },
                error: function(jqXHR) {
                    if ( $("#flashMessage"))/*if object already exist remove it to avoid diplicate DOM id's*/
                    $("#flashMessage").remove();
                    if(jqXHR.status === 401 ){//Unathorized. In this case password invalid
                        $('<div id="flashMessage" class="error-message">Sorry that is incorrect!</div>').insertBefore(thisForm.parentNode.firstChild);
                    }else if(jqXHR.status===403){                     
                            location.reload(); //user session expired
                    }else{
                        $('<div id="flashMessage" class="error-message">'+ jqXHR.responseText +'</div>').insertBefore(thisForm.parentNode.firstChild);
                        }
                    }
            }); 
}

function ajaxNote(mid, noteType,containerID) {

  /*All parameters are required! Ajaxian way to append
   *mid = currently active merchant id
   *containerID = DOM id to update with server response data which is of type HTML
   * noteType = string
   **/
        $.ajax({
            type: "POST",
            url: "/MerchantNotes/add/"+mid+"/"+noteType,
           // data: "data['MerchantNote'][note_type]=''",
            dataType: 'html',
            success: function(data) {
                $('#'+containerID).html(data);
            },
            error: function(data) {
                /*If user session expired the server will return a Forbidden status 403
                 *Refreshing the page will redirect the user to the login page thus preventing it from inserting into the DOM*/
                    if(data.status==403){                     
                        location.reload();
                    }                    
                  }
        });
}
/**
 * JQuery API Animations
 ** ***********************/

/** function objFader()
 * @description toggles fading in/out an object
 * @argument {string} objID the id of the object to fade in/out
 * @argument {int} custSpeed optional animation speed
 * */
function objFader(objID, custSpeed) {
	animSpeed = (custSpeed > 0) ? custSpeed : 1000;
	if ($("#" + objID).css('display') == 'none') {
		$('#' + objID).fadeIn(animSpeed, function() {
		});
	} else {
		$('#' + objID).fadeOut(animSpeed, function() {
		});
	}
}
/** function objFader()
 * @description toggles fading in/out an object
 * @argument {string} objID the id of the object to fade in/out
 * @argument {int} custSpeed optional animation speed
 * */
function objSlider(objID, custSpeed) {
	/*Prevent new animation while one is already in progress*/
	if ($('#'+ objID).is(":animated"))
		return;
	/*set animation speed*/
	animSpeed = (custSpeed > 0) ? custSpeed : 1000;
		$('#' + objID).slideToggle(animSpeed);	
}


/**
 * rotateThis()
 * @description Rotation animation for any object
 * @param {object} elementObj element to be animated
 * @param {int} deg rotation in positive (clockwise) negative (counter clockwise) degrees
 * @param {int} custSpeed
 * @returns {void} 
 */

function rotateThis(elementObj, deg, custSpeed) {
	/*Prevent new animation while one is already in progress*/
	if ($(elementObj).is(":animated"))
		return;
	
	//Set a random numerical id for this object if it's not set;
	elementObj.id = (elementObj.id)? elementObj.id :  Math.floor((Math.random()*10000)+1);
	
	//Set amount of degrees to spin
	if($('#'+elementObj.id).data('twistDegrees')){
		deg += $('#'+elementObj.id).data('twistDegrees');
		$('#'+elementObj.id).data('twistDegrees', deg);
	} else 
		$('#'+elementObj.id).data('twistDegrees', deg);
		
	degrees = $('#'+elementObj.id).data('twistDegrees');
		
	animSpeed = (custSpeed > 0) ? custSpeed : 400;	
	$(elementObj).animate({borderSpacing: degrees}, {
		step: function(now, fx) {
			$(this).css('-webkit-transform', 'rotate(' + now + 'deg)');
			$(this).css('-moz-transform', 'rotate(' + now + 'deg)');
			$(this).css('transform', 'rotate(' + now + 'deg)');
		},
		duration: animSpeed
	}, 'linear');
	
}
/**
 * appendHTMLContent
 * 
 * Method appends if atEnd == true or else prepends content to the passed appendHTMLContent
 * 
 * @param {DOM Object} elementObj the container object to inser to
 * @param {type} contents DOM element, array of elements, HTML string, or jQuery object to inser
 * @param {boolean} atEnd
 * @returns {undefined}
 */
function appendHTMLContent(elementObj, contents, atEnd){
	
	if(atEnd){ //Append to the end of elementObj in the set of matched elements.
		$(elementObj).append(contents);
	}else{   
		$(elementObj).prepend(contents);
	}	
}
/*	makeDraggable
 * 
 *	Function makes any object dragable by passing a reference to the object
 *	id or the DOM object. The handle is a child object inside the draggable parent from which the
 *	drag operation will begin and end. i.e. The top window title.
 *	
 * @param {Object}/{string} objRef the object to make draggable itself or its id attribute
 * @param {string} grabHandle The handle from which to grab the draggable object.
 * @returns {undefined}
 */
function makeDraggable(objRef, grabHandle){
	if(typeof(objRef)==="object")
		$( objRef ).draggable({ handle: grabHandle });
	if(typeof(objRef)==="string")
		$('#'+objRef ).draggable({ handle: grabHandle });			
}
/* addDec
 * 
 * Function adds two decimal numbers with 2 or 3 decimals with high precision.
 *	
 * @param {number} First decimal
 * @param {number} second decimal number to add.
 * @param {string} precision default is 2.
 * @returns {float}
 */
  function addDec(dec1,dec2, precision){
    if(isNaN(dec1) || isNaN(dec2))
        return 'NaN';
      
    if(precision === null)
        precision = 2;
          
  	if (precision === 2) {
        return ((dec1 * 10) + (dec2 * 10))/10;
    } else if(precision === 3) {
        return ((dec1 * 100) + (dec2 * 100))/100;
    } else {
        return null;
    }
  }
/*printWindow
 * 
 */
function printWindow() {
            v = parseInt(navigator.appVersion);
            if (v >= 4) window.print();
        }

/* This function's parameter is the begining part of the substring of the id attribute that one or many elements also have in common but differs slightly at the end for each one of them
 * This substring is used to match a specific group of cakephp date dropdowns in a page.
 ***
 */
    function setTimeStampNow(objIdBeginsWith){
       
        var dateObjGroup = $("[id*="+objIdBeginsWith+"]");
        var d = new Date();
        //Set date 
        //getMonth() is zero-based. Append zero to single digit months < 9 which is Oct
        $("#"+dateObjGroup[0].id).val((d.getMonth() < 9)? '0' + (d.getMonth() + 1) : d.getMonth() + 1);
        $("#"+dateObjGroup[1].id).val((d.getDate() < 10)? '0' + (d.getDate()) : d.getDate());
        $("#"+dateObjGroup[2].id).val(d.getFullYear());
        
        //Do we also need to Set time?
        if(dateObjGroup.length>3){

            $("#"+dateObjGroup[3].id).val((d.getHours() > 12) ? '0'+(d.getHours() - 12) :((d.getHours() < 10)? '0'+d.getHours():d.getHours()));
            $("#"+dateObjGroup[4].id).val((d.getMinutes()<10) ? ('0' + d.getMinutes()):d.getMinutes());
            $("#"+dateObjGroup[5].id).val(d.getHours() > 12 ? 'pm' : 'am');
        }            
    }

/* This function's parametes should match the SELECT elements ids designed for month and year selection with numerical values for month and year respectively
 *
 * @param {string} monthObjId the id of a dropdown object with integer months as values
 * @param {string} yearObjId the id of a dropdown object with four digit integer years as values
 */
    function setMonthAndYear(monthObjId, yearObjId){
        var d = new Date();
        //Set date 
        //getMonth() is zero-based. Append zero to single digit months < 9 which is Oct
        $("#"+monthObjId).val(d.getMonth() + 1);
        $("#"+yearObjId).val(d.getFullYear());
    }

/**
* requestNewCompProfile
* creates a request to create a new compensation profile. 
*
* @param {string} roleId
* @param {string} roleName
* @return {boolean} 
*/
function requestNewCompProfile(roleId, roleName, userId, isManager) {
    if (confirm("A blank " + roleName + " compensation profile will be created for this user.\n(To create a copy hit cancel and use the 'Copy Existing UCP' option instead)")) {
      renderContentAJAX(null,null,null,null,"/UserCompensationProfiles/addCompProfile/" + userId + "/0/0/" + roleId + "/" + isManager);
      $( "#mainCreateUCPBtn" ).prepend( "<span class='label label-danger'><img src='/img/loaderSpinner.gif' /> Working</span>" );
      //Listen for completion of request
      ajaxRequestCompleteListener();
      return true;
    }
  return false;
  }

/* ajaxRequestCompleteListener
 * 
 * Some ajax requests require special handling on reqest completion this listener will handle those
 *  
 * @returns {void}
 */
function ajaxRequestCompleteListener() {

    $(document).ajaxComplete(function(event, xhr, settings) {
      if (settings.url.indexOf("checkDecrypPassword") > 0) {
        if (xhr.status === 200) {
          var params = jQuery.parseJSON(xhr.responseText);
          if (params.redirectAction === 'ajaxAddAndEdit' || params.redirectAction === 'ajaxDisplayDecryptedVal')
            renderContentAJAX(params.redirectController, params.redirectAction, params.id + '/' + params.password + '/' + params.redirectActionParams, 'ModalContainer', '');
        }
      } else if(settings.url.indexOf("addCompProfile") > 0 || settings.url.indexOf("copy") > 0) {
          location.reload();
      } else if (settings.url.indexOf("MerchantNotes/add") > 0) {
        /*Refresh page iff a note is successfully submited but not when the note form HTML is returned in the response because it must be displayed*/
        if (xhr.status === 200 && xhr.responseText.indexOf('id="MerchantNoteAddForm"') == -1) {
          location.reload();
        }
      }

    });
  }

/* showAdminBtns
 * Controlls whether to show/hide buttons in admin maintenance table view
 *  
 * @returns {void}
 */
  function showAdminBtns() {
    if ($("#MaintenanceDashboardModelName option:selected").text() !== '') {
      $('#adminTableButtons').fadeIn(800, function() {});
    } else {
      $('#adminTableButtons').fadeOut(200, function() {});
    }
  }

/* getTableOfContent
 * Passes currently selected values as params to renderContentAjax method
 *  
 * @returns {void}
 */
  function getTableOfContent() {
    $("#adminTableOfContent").html('<img src="/img/spinner-small.gif" class="center-block">');
    renderContentAJAX('MaintenanceDashboards', 'content', $('#MaintenanceDashboardModelName').val(), 'adminTableOfContent');
  }

/**
 * displayToggleObjByName
 *
 * @param string targetName the name of the objects to hide/show
 * @return void
 */
  function displayToggleObjByName(targetName) {    
    // $("[name='" + targetName + "']").fadeToggle(900);
    $("[name='" + targetName + "']").toggleClass('hidden');
  }

/**
 * isFloat 
 * Checks if param is a decimal number. (Checks data type also)
 * 
 * @param number x the number to validate as decimal
 * @return boolean true if the number is actually a decimal number by both type and any precision. Fals if NaN or not a decimal
 */
    function isFloat(x) {
        //The modulus 1 of any decimal equals a non-zero number which evaluates to true in a logical context. 
        //Negate twise to get boolean true.
        return (typeof x === 'number' && !!(x % 1));
    }

/**
 * showAlert 
 * Inserts html messages into an empty parent element. The function of the parent must be only to display the messages.
 * The contents of the parent will be replaced with new message HTML code every time this method is called.
 * 
 * @param string message the message to display
 * @param string | object parentRef could be the id of an empty parent container or the parent object itself.
 * @param string class the bootstrap or other existing custom css classes
 * @return void
 */
    function showAlert(message, parentRef, cssClasses) {
      msgHtml = '<span class="' + cssClasses + '">' + message + '</span>';
      if(typeof(parentRef) ==="object"){
        $( parentRef ).html(msgHtml);
      }
      if(typeof(parentRef) ==="string") {
        $('#' + parentRef ).html(msgHtml);
      }
    }

/**
 * calcPfMonthlyTotal 
 * Calculates the total payment fusion monthly fee based on the each device fee and the number of each devices.
 * 
 * @return void
 */
    function  calcPfMonthlyTotal() {
      total = $("#PaymentFusionMonthlyTotal");
      if (total.length) {
        $("#PaymentFusionStandardNumDevices, #PaymentFusionStandardDeviceFee, #PaymentFusionVp2peNumDevices, #PaymentFusionVp2peDeviceFee, #PaymentFusionPfccNumDevices, #PaymentFusionPfccDeviceFee, #PaymentFusionVp2pePfccNumDevices, #PaymentFusionVp2pePfccDeviceFee").on("blur", function() {
              //To avoid Floating point presicion errors during arithmetic calculation multiply all fees by 1000 to eliminate up to 3 decimal places
              qty1 = ($.isNumeric($("#PaymentFusionStandardNumDevices").val()))? parseFloat($("#PaymentFusionStandardNumDevices").val()) : 0;
              fee1 = ($.isNumeric($("#PaymentFusionStandardDeviceFee").val()))? parseFloat($("#PaymentFusionStandardDeviceFee").val()) * 1000 : 0;
              qty2 = ($.isNumeric($("#PaymentFusionVp2peNumDevices").val()))? parseFloat($("#PaymentFusionVp2peNumDevices").val()) : 0;
              fee2 = ($.isNumeric($("#PaymentFusionVp2peDeviceFee").val()))? parseFloat($("#PaymentFusionVp2peDeviceFee").val()) * 1000 : 0;
              qty3 = ($.isNumeric($("#PaymentFusionPfccNumDevices").val()))? parseFloat($("#PaymentFusionPfccNumDevices").val()) : 0;
              fee3 = ($.isNumeric($("#PaymentFusionPfccDeviceFee").val()))? parseFloat($("#PaymentFusionPfccDeviceFee").val()) * 1000 : 0;
              qty4 = ($.isNumeric($("#PaymentFusionVp2pePfccNumDevices").val()))? parseFloat($("#PaymentFusionVp2pePfccNumDevices").val()) : 0;
              fee4 = ($.isNumeric($("#PaymentFusionVp2pePfccDeviceFee").val()))? parseFloat($("#PaymentFusionVp2pePfccDeviceFee").val()) * 1000 : 0;
              totalMonthly = ((qty1 * fee1) + (qty2 * fee2) + (qty3 * fee3) + (qty4 * fee4)) / 1000;
              total.val(totalMonthly);
              if ($("#PaymentFusionStandardDeviceFee").val() > 30 || $("#PaymentFusionVp2peDeviceFee").val() > 30 || $("#PaymentFusionPfccDeviceFee").val() > 30 || $("#PaymentFusionVp2pePfccDeviceFee").val() > 30) {
                $('#PaymentFusionIsHwAsSrvc').prop('checked', true);
              } else if ($("#PaymentFusionStandardDeviceFee").val() <= 30 && $("#PaymentFusionVp2peDeviceFee").val() <= 30 && $("#PaymentFusionPfccDeviceFee").val() <= 30 && $("#PaymentFusionVp2pePfccDeviceFee").val() <= 30) {
                $('#PaymentFusionIsHwAsSrvc').prop('checked', false);
              }
        });
      }
    }


/**
 * toggleShowPwField
 * Toggle mask unmask contents of password fields
 * 
 * @param string objId the id of the password input field
 * @return void
 */

  function toggleShowPwField(objId) {
    var x = document.getElementById(objId);
    if (x.type === "password") {
      x.type = "text";
    } else {
      x.type = "password";
    }
}
