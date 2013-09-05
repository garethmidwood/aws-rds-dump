<?php
/*
 *	UNIT4 Auto scaling page
 * 
 *	Note:
 *		In advance of running this, you run these commands (adjust paths accordingly) to set up the following environment variables
 *		-	export AWS_CREDENTIAL_FILE="/var/www/aws/autoscale/credentials"
 *
 *	Note:
 *		A list of available commands can be found here
 *		http://docs.amazonwebservices.com/AutoScaling/latest/DeveloperGuide/astools.html#verify-install
 *		
 *
 *	@Author:	Gareth Midwood
 *	@Date:		06/11/12
 */

include('header.php');


// check we have write permissions
$bHasWritePermissions = checkWritePermissions();

// get the form values if submitted
$aFormValues = isset($_POST) ? $_POST : array();
$bFormSubmitted = (count($aFormValues) > 0);

// if the form hasn't been submitted then we exit here
if (!$bFormSubmitted) {
	// include the tpl
	include('tpl' . DS . 'template.html.php');	
	// exit
	exit();
}

// check that all form fields have been filled in
$bFormValid = validateFormSubmission($aFormValues);

// if we got an array back then they are errors
if (is_array($bFormValid)) {
	// include the tpl
	include('tpl' . DS . 'template.html.php');	
	// exit
	exit();	
}

/*
 *	FORM HAS BEEN SUBMITTED
 */
$sFilename = $aFormValues['aws-db-database'] . '-' . date('ymdHi') . '.sql';

// spit out a few details after running
addToOutputScript(array(
	'mysqldump -h ' . $aFormValues['aws-db-server']
	. ' -u ' . $aFormValues['aws-db-username']
	. ' --password=' . $aFormValues['aws-db-password']
	. ' ' . $aFormValues['aws-db-database']
	. ' > ' . WRITE_DIR . $sFilename
,	'rm -rf ' . WRITE_DIR . SCRIPT_FILENAME
,	'echo "<a id="download-link" href=\"/bin/' . $sFilename . '\">Download the backup!</a>"'
));

// include the tpl
include('tpl' . DS . 'template.html.php');

// clear out the write directory
//deleteFilesInDirectory(WRITE_DIR);
?>
