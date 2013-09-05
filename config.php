<?php
/*
 *	Contains configuration details
 */




/*
 *	define the keys for reading from EC2.
 *
 *	Required API access:
 *	- RDS (?)
 *
 */
define('AWS_IAM_EC2_READ_KEY',			'REPLACE_WITH_IAM_KEY');
define('AWS_IAM_EC2_READ_SECRET',		'REPLACE_WITH_IAM_SECRET');

// your AWS account Unique ID
define('AWS_ACCOUNT_ID',			123456789012);

// paths to home directories
define('RDS_HOME',				'/var/aws/RDSCli-1.14.001');
define('JAVA_HOME',				'/usr/lib/jvm/java-7-oracle');

?>
