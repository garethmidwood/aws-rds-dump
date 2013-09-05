<!DOCTYPE html>
<html>
	<head>
		<title>RDS - DB Backup Tool</title>
		<link rel="stylesheet" type="text/css" href="/css/layout.css">
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
		<script src="/js/general.js"></script>
	</head>
	<body>
		<header>
			<h1>RDS - DB Backup Tool</h1>
		</header>
		<div id="body">
			<p> 
				Testing write permissions: 
				<?php if (!$bHasWritePermissions) { ?>
					<span class="error">Error: Test file could not be written to /bin directory. Please check the permissions and try again</span>
				<?php } else { ?>
					<span class="positive">Passed: Test file written to /bin directory</span>
				<?php } ?>
			</p>
			
			<?php
				if (isset($bFormValid) && is_array($bFormValid)) {
					?>
						<h2>Error</h2>
						<p>
					<?php
					
					foreach($bFormValid as $sErrorMsg) {
						?>
							<span class="error">
								<?php echo $sErrorMsg; ?>
							</span>
						<?php
					}
					
					?>
						</p>
					<?php
				}
			?>
						
			<form method="POST" action="">
				<fieldset>
					
					<ul id="dbbackup-form">
						<li>
							<h1>Environment</h1>
						</li>
							<div class="right">
								<label for="aws-region">Region</label>
								<select name="aws-region" id="aws-region">
									<option value=""> -- Choose a Region -- </option>
									<option value="eu-west-1"<?php if (isset($aFormValues["aws-region"]) && $aFormValues["aws-region"] == 'eu-west-1') { echo 'selected="selected"'; } ?>>EU West 1 (Dublin)</option>
									<option value="us-east-1"<?php if (isset($aFormValues["aws-region"]) && $aFormValues["aws-region"] == 'us-east-1') { echo 'selected="selected"'; } ?>>US East 1 (N. Virginia)</option>
									<option value="ap-southeast-1"<?php if (isset($aFormValues["aws-region"]) && $aFormValues["aws-region"] == 'ap-southeast-1') { echo 'selected="selected"'; } ?>>AP Southeast 1 (Singapore)</option>
								</select>
							</div>
						</li>



						<li>
							<hr/>
							<h1>The Server</h1>
						</li>

						<li>
							<div id="aws-db-server-row">
								<label for="aws-db-server">DB Server</label>
								<input type="hidden" id="aws-db-server-selected" value="<?php if (isset($aFormValues['aws-db-server'])) { echo $aFormValues['aws-db-server']; } ?>" />
								<select name="aws-db-server" id="aws-db-server">
								</select>
							</div>

							<div id="aws-db-username-row">
								<label for="aws-db-username">DB Username</label>
								<input type="text" id="aws-db-username" name="aws-db-username" value="<?php if (isset($aFormValues['aws-db-username'])) { echo $aFormValues['aws-db-username']; } ?>" />
							</div>

							<div id="aws-db-password-row">
								<label for="aws-db-password">DB Password</label>
								<input type="password" id="aws-db-password" name="aws-db-password" value="<?php if (isset($aFormValues['aws-db-password'])) { echo $aFormValues['aws-db-password']; } ?>" />
							</div>

							<div>
								<button id="aws-db-list">Get Database List</button>
							</div>

							<div id="aws-db-database-row">
								<label for="aws-db-database">Database to backup</label>
								<input type="hidden" id="aws-db-database-selected" value="<?php if (isset($aFormValues['aws-db-database'])) { echo $aFormValues['aws-db-database']; } ?>" />
								<select name="aws-db-database" id="aws-db-database">
								</select>
							</div>
						</li>



						<li class="code">
							<hr/>
							<h1 id="code-title">The script as it is</h1>
							<div id="code-area">
								<?php
									$sCode = readOutputScript();
									if (strlen(trim($sCode)) > 0) {
										echo $sCode . "<button id='run-script'>Run script!</button>";
									}
								?>
							</div>
						</li>			
						
						<li>
							<button>Generate Script</button>
						</li>
					</ul>
					
				</fieldset>
			</form>			
		</div>
	</body>
</html>
