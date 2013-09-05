$(document).ready(function() {

	// when the region changes, change the availability zones you can see
	$('#aws-region').change(function() {
		var value = this.value;

		if (value) {
			// get the regions DB Servers
			getDBServers();
		}
	});

	// call it straight away to populate fields
	$('#aws-region').change();

	// run the script when the button is pressed...
	$('#aws-db-list').click(function() {
		getDBList(false);
		return false;
	});

	// try and update databases when the server is changed
	$('#aws-db-server').change(function() {
		// try and get the DB list, supress any error messages
		getDBList(true);
	});

	// call it straight away to populate databases
	$('#aws-db-server').change();

	// run the script when the button is pressed...
	$('#run-script').click(function() {
		runScript();
		return false;
	});
});



// adds loading span inside selected element
function loading(selector, text) {
	var loadingtext = (text) ? text : 'loading, please wait...';
	$(selector).append("<span class='load'>" + loadingtext + "</span>");
}

// removes loading span from selected element
function loaded(selector) {
	$('span.load', selector).remove();
}


/*
 *	retrieves list of SNS topics for the selected region
 *
 *	Params:
 *	ScriptName - The name of the ajax script being called
 *	LoadingElementSelector - jQuery selector for the element that will be 'loading'
 *	DropDownFieldSelector - jQuery selector for this drop down field
 *	SelectedFieldSelector - jQuery selector for the field containing the currently selected element
 */
function getDropDownFromAWS(ScriptName, LoadingElementSelector, DropDownFieldSelector, SelectedFieldSelector, AdditionalParams) {
	// get the currently selected region
	var value = $('#aws-region').val();
	
	if (value) {
		// the field will be loading in a second..
		loading(LoadingElementSelector);

		// build the ajax request URL
		var request_url = '/ajax/' + ScriptName + '?region=' + value;

		// see if we have a pre-selected item to pick...
		if ($(SelectedFieldSelector).length > 0) {
			var request_url = request_url + '&selected=' + $(SelectedFieldSelector).val();
		}

		// empty the dropdown
		$(DropDownFieldSelector).empty();

		// get the load balancers
		$.ajax(request_url, {
			data: AdditionalParams
		,	type: "POST"
		})
		.done(function(response) {
			var oItems = $.parseJSON(response);
			var length = oItems.length;

			// if we have an error then alert the user
			if (oItems['error']) {
				alert('ERROR: ' + oItems['error']);
			} else {
				// cycle through items, add them to the page
				for (var i = 0; i < length; i++) {
					var item = oItems[i];
					// check if this item is selected or not
					var selectedValue = (item['selected'] == "selected") ? " selected='" + item['selected'] + "'" : '';
					// find out whether we have a label. If not just use the name
					var itemLabel = (item['label']) ? item['label'] : item['name'];
					// items can be disabled..
					var disabled = (item['disabled'] && item['disabled']) ? ' disabled="disabled"' : '';
					// append the option
					$(DropDownFieldSelector).append("<option value='" + item['name'] + "'" + selectedValue + disabled + ">" + itemLabel + "</option>");
				}
			}

			loaded(LoadingElementSelector);
			
			// trigger the change function of the drop down
			$(DropDownFieldSelector).change();
		});
	}
}

// retrieves list of load balancers for the selected region
function getDBServers() {
	getDropDownFromAWS('get-servers.php', '#aws-db-server-row', '#aws-db-server', '#aws-db-server-selected');
}

// retrieves a list of DBs for the selected instance
function getDBList(bSupressMessages) {
	var dbserver = $('#aws-db-server').val();
	var username = $('#aws-db-username').val();
	var password = $('#aws-db-password').val();

	if (!username || username.length <= 0) {
		if (!bSupressMessages) { alert('Fill in the DB username, then retry'); }
		return false;
	}

	if (!password || password.length <= 0) {
		if (!bSupressMessages) { alert('Fill in the DB password, then retry'); }
		return false;
	}

	if (!dbserver || dbserver.length <= 0) {
		if (!bSupressMessages) { alert('Choose a database server, then retry'); }
		return false;
	}

	getDropDownFromAWS('get-databases.php', '#aws-db-database-row', '#aws-db-database', '#aws-dv-database-selected', 'username=' + username + '&password=' + password + '&server=' + dbserver);
}


// run the generated script
function runScript() {
	// load balancers will be loading in a second..
	loading('#code-area', 'Running script, please wait...');

	// run the script via ajax..
	$.ajax('/ajax/run-script.php')
	.done(function(response) {
		
		$('#code-area').html(response);
		$('#code-title').html('Script results:');
	});
}

