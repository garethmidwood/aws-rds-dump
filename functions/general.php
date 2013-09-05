<?php
	
// writes a file with supplied data. Returns false if it fails
function writeFile($param_sFileName, $param_aData, $param_sDirectoryPath = WRITE_DIR, $param_sPermissions = 0755) {
	// try and write a file
	$myFile = $param_sDirectoryPath . $param_sFileName;
	$fh = @fopen($myFile, 'w');
	
	// if we couldn't write the file then we don't have write permissions
	$bHasWritePermissions = $fh ? true : false;
	
	// stick some text in the file, then close it and change permissions
	if ($bHasWritePermissions) {
		foreach ($param_aData as $sData) {
			fwrite($fh, $sData . "\n");
		}
		fclose($fh);
		// set permissions
		chmod($myFile, $param_sPermissions);
	}
	
	return $bHasWritePermissions;
}

// returns true if we can write files, false otherwise
function checkWritePermissions($param_sDirectoryPath = WRITE_DIR) {
	// set file name
	$sFileName = "write-permissions-test.txt";
	
	// delete the file if it already exists. If it doesn't exist, then fail silently
	@unlink($param_sDirectoryPath . $sFileName);
	
	// try to write a file, to check write permissions
	$bHasWritePermissions = writeFile($sFileName, array("this is test text to check that we have write permissions"), $param_sDirectoryPath);
	
	// now delete the file
	$sCommand = "rm -r $param_sDirectoryPath . $sFileName";
	system($sCommand);
	
	return $bHasWritePermissions;
}
	
// deletes all files in the specified directory
function deleteFilesInDirectory($param_sDirectoryPath = WRITE_DIR) {
	$sCommand = "rm -r $param_sDirectoryPath*";
	system($sCommand);
}

// writes the credentials file
function writeCredentialsFile($param_sCredentialsFilename, $param_sAWSKey, $param_sAWSSecret) {

	// write the credentials to a file
	writeFile(	$param_sCredentialsFilename
			,	array("AWSAccessKeyId=" . $param_sAWSKey, "AWSSecretKey=" . $param_sAWSSecret)
			,	WRITE_DIR
			,	0755
			);
			
	// set the credentials key in the system PATH
	setCredentialsPath($param_sCredentialsFilename);
}

// sets the credentials file path in the system PATH
function setCredentialsPath($param_sFileName) {
	$sCommand = 'export AWS_CREDENTIAL_FILE="' . WRITE_DIR . $param_sFileName . '"';
	addToOutputScript($sCommand);
}


// adds environment variables (sorry, can't figure out why this won't load using source /etc/environment ... it works fine when run manually, but when the site tries to do it, it fails.)
function setEnvironmentVariables() {
	addToOutputScript('export RDS_HOME="' . RDS_HOME . '"');
	addToOutputScript('export JAVA_HOME="' . JAVA_HOME . '"');
	addToOutputScript('export PATH="/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin:/usr/games:${RDS_HOME}/bin"');
}






// check all required form fields have been filled in
function validateFormSubmission(&$param_aSubmittedValues) {

	$aErrors = array();
	$aRequiredTextFields = array(
		"aws-region"
	,	"aws-db-server"
	,	"aws-db-username"
	,	"aws-db-password"
	,	"aws-db-database"
	);
	
	$aRequiredNumberFields = array(
	);
	
	$aRequiredArrays = array(
	);
	
	// validate text fields
	foreach ($aRequiredTextFields as $sFieldName) {
		if (!validateTextField($sFieldName, $param_aSubmittedValues)) {
			array_push($aErrors, "Error: Field $sFieldName is required");
		}
	}	
	
	// validate number fields
	foreach ($aRequiredNumberFields as $sFieldName) {
		if (!validateNumberField($sFieldName, $param_aSubmittedValues)) {
			array_push($aErrors, "Error: Field $sFieldName is required and must be numeric");
		}
	}
	
	// validate arrays
	foreach ($aRequiredArrays as $sFieldName) {
		if (!validateCheckboxField($sFieldName, $param_aSubmittedValues)) {
			array_push($aErrors, "Error: Field $sFieldName is required");
		}
	}
	
	// return true if valid, the errors otherwise
	return (count($aErrors) > 0) ? $aErrors : true;
}

// checks a text field has been filled in
function validateTextField($param_sFieldName, $param_aSubmittedValues) {
	return (isset($param_aSubmittedValues[$param_sFieldName]) && strlen(trim($param_aSubmittedValues[$param_sFieldName])) > 0);
}

// checks a number field is filled in and numeric
function validateNumberField($param_sFieldName, $param_aSubmittedValues) {
	return (isset($param_aSubmittedValues[$param_sFieldName]) && is_numeric($param_aSubmittedValues[$param_sFieldName]));
}

// checks at least one of the checkboxes has been ticked
function validateCheckboxField($param_sFieldName, $param_aSubmittedValues) {
	return (isset($param_aSubmittedValues[$param_sFieldName]) && count($param_aSubmittedValues[$param_sFieldName]) > 0);
}





function addToOutputScript($param_sContent) {
	$oBashScript = Class_BashScript::GetInstance();
	$oBashScript->addLine($param_sContent);
}

function readOutputScript() {
	$file_path = WRITE_DIR . SCRIPT_FILENAME;
    	// open the file for reading
    	$file_handle = @fopen($file_path, 'r');

	// no file, no content
	if (!$file_handle) {
		return 'Script could not be found.';
	}
	
	// read in the existing contents of the file
	$file_contents = fread($file_handle, filesize($file_path));
	// close the read file
	fclose($file_handle);

	// now use Geshi to get some syntax highlighting!
	$oGeshi = new GeSHi($file_contents, 'bash');

	return $oGeshi->parse_code();
}
	
?>
