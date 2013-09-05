<?php
	include_once('../header.php');

	// we need to know which region we're working in, or return nothing.
	if (!isset($_GET['region'])) {
		exit;
	}

	// gather variables
	$sRegion = $_GET['region'];
	$sSelected = isset($_GET['selected']) ? $_GET['selected'] : false;

	// set up AWS namespaces
	use Aws\Common\Aws;
	use Aws\Rds\Command\describeDBInstances;
	use Aws\Common\Enum\Region;

	// Set up the global AWS factories
	$oAWS = Aws::factory(array('key' => AWS_IAM_EC2_READ_KEY,'secret' => AWS_IAM_EC2_READ_SECRET,'region' => $sRegion));

	// Query AWS for instances
	$aServers = $oAWS->get('rds')->describeDBInstances()->toArray();

	$aResponseServers = array();

	foreach($aServers['DBInstances'] as $aServer) {
		// check if this is selected
		$bSelected = ($sSelected == $aServer['Endpoint']['Address']) ? "selected" : "";
		// define the label for this item
		$sLabel = $aServer['DBInstanceIdentifier'] . ' (' . $aServer['Engine'] . ' - ' . $aServer['DBInstanceStatus'] . ')';
		// decide whether to disable this option
		$bDisabled = ($aServer['Engine'] != 'mysql' || $aServer['DBInstanceStatus'] != 'available') ? true : false;
		// set up the item array
		$aServer = array("name" => $aServer['Endpoint']['Address'], "label" => $sLabel, "disabled" => $bDisabled);
		if ($bSelected) {
			$aServer["selected"] = $bSelected;
		}

		// add it to the final array
		array_push($aResponseServers, $aServer);
	}

	foreach ($aResponseServers as $key => $row) {
	    $aName[$key]  = $row['name'];
	    $aDescription[$key] = $row['label'];
	    $aSelected[$key] = isset($row['selected']) ? $row['selected'] : null;
	}

	// Sort the data with volume descending, edition ascending
	// Add $data as the last parameter, to sort by the common key
	array_multisort($aDescription, SORT_ASC, $aName, SORT_ASC, $aSelected, SORT_ASC, $aResponseServers);

	echo json_encode($aResponseServers);

?>
