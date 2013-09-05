<?php
	include_once('../header.php');

	// we need to know which region we're working in, or return nothing.
	if (!isset($_GET['region']) || !isset($_POST['username']) || !isset($_POST['password']) || !isset($_POST['server'])) {
		exit;
	}

	// gather variables
	$sRegion = $_GET['region'];
	$sUsername = $_POST['username'];
	$sPassword = $_POST['password'];
	$sDBServer = $_POST['server'];
	$sSelected = isset($_GET['selected']) ? $_GET['selected'] : false;

//$sDBServer = 'localhost';

	// we'll fill this up and return it
	$aResponseDatabases = array();

	// connect to the server and pull back a list of databases
	try {
		$oDB = @mysql_connect($sDBServer, $sUsername, $sPassword);
		if (!$oDB) {
			exit(json_encode(array("error" => "Could not connect to database")));
		}
	} catch (Exception $e) {
		exit(json_encode(array("error" => "Could not connect to database")));
	}

	try {
		$oRes = mysql_query("SHOW DATABASES");
		if (!$oRes) {
			exit(json_encode(array("error" => "Could not list databases")));
		}
	} catch (Exception $e) {
		exit(json_encode(array("error" => "Could not list databases")));
	}

	while ($aDB = mysql_fetch_assoc($oRes)) {
		// check if this is selected
		$bSelected = ($sSelected == $aDB['Database']) ? "selected" : "";

		// set up the item array
		$aDB = array("name" => $aDB['Database'], "label" => $aDB['Database']);
		if ($bSelected) {
			$aDB["selected"] = $bSelected;
		}

		array_push($aResponseDatabases, $aDB);
	}

	foreach ($aResponseDatabases as $key => $row) {
	    $aName[$key]  = $row['name'];
	    $aDescription[$key] = $row['label'];
	    $aSelected[$key] = isset($row['selected']) ? $row['selected'] : null;
	}

	// Sort the data with volume descending, edition ascending
	// Add $data as the last parameter, to sort by the common key
	array_multisort($aDescription, SORT_ASC, $aName, SORT_ASC, $aSelected, SORT_ASC, $aResponseDatabases);

	echo json_encode($aResponseDatabases);

?>
