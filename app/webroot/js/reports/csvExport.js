/**
 * WYSIWYG HTML-TABLE-TO-CSV export
 * 
 * Fast way to convert the cells of an HTML table into a CSV string then into CSV file.
 * Uses the table id attribute to identify the table to be converted to csv
 * Checks and parses table cel contents, if existing commas or line breaks are present to
 * prevent unwanted addidional colums/rows from being created in the csv.
 */

function downloadCSV(csv, filename) {
	var csvFile;
	var downloadLink;

	// CSV file
	csvFile = new Blob([csv], {type: "text/csv"});

	// Download link
	downloadLink = document.createElement("a");

	// File name
	downloadLink.download = filename;

	// Create a link to the file
	downloadLink.href = window.URL.createObjectURL(csvFile);

	// Hide download link
	downloadLink.style.display = "none";

	// Add the link to DOM
	document.body.appendChild(downloadLink);

	// Click/download link
	if (navigator.msSaveOrOpenBlob) {
		//for IE
    	navigator.msSaveOrOpenBlob(csvFile, filename);
    } else {
		downloadLink.click();
    }

	// Remove the link from DOM
	document.body.removeChild(downloadLink);
}
function exportTableToCSV(filename, tableId) {
	var csv = [];
	var rows = document.querySelectorAll("#" + tableId + " tr");
	
	for (var i = 0; i < rows.length; i++) {
		var row = [], cols = rows[i].querySelectorAll("td, th");
		for (var j = 0; j < cols.length; j++) {
			//Assuming the first row is usually the header row
			if (i === 0) {
				//remove newlines or line breaks to prevent unwanted extra rows in the csv
				var celText = cols[j].innerText.replace(/\r?\n|\r/g, '');
			} else {
				//Some cells use "traffic light" images to indicate negation of affirmation, this needs a textual representation
				if (cols[j].innerHTML.indexOf('red_orb.png') >= 0) {
					var celText = "-NO-";
				} else if (cols[j].innerHTML.indexOf('green_orb.gif') >= 0) {
					var celText = "-YES-";
				} else {
					//replace newlines or line breaks with dashes to prevent unwanted extra rows in the csv
					var celText = cols[j].innerText.replace(/\r?\n|\r/g, ' - ');
				}
			}
			//Remove trailing spaces
			celText = celText.trim();
			//then place quotes around strings containing commas to prevent unwanted extra columns in the csv
			if (celText.indexOf(",") >= 0) {
					celText = '"' + celText + '"';
			}
			//Numbers >= 12 digits are converted to scientific notation by excel when CSV is opened, we don't want that
			if (isNaN(celText) === false &&  celText.length >= 12) {
				celText = '="' + celText + '"';
			}

			row.push(celText);
		}
		csv.push(row.join(","));
	}

	// Download CSV file
	downloadCSV(csv.join("\n"), filename);
}