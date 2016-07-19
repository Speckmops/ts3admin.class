<?PHP
/**
  * clientlist.php
  *
  * Is a small script to demonstrate how to get a clientlist via ts3admin.class
  *
  * by par0noid solutions - ts3admin.info
  *
*/

/*-------SETTINGS-------*/
$ts3_ip = '127.0.0.1';
$ts3_queryport = 10011;
$ts3_user = 'serveradmin';
$ts3_pass = 'password';
$ts3_port = 9987;
/*----------------------*/

#Include ts3admin.class.php
require("../lib/ts3admin.class.php");

#build a new ts3admin object
$tsAdmin = new ts3admin($ts3_ip, $ts3_queryport);

if($tsAdmin->getElement('success', $tsAdmin->connect())) {
	#login as serveradmin
	$tsAdmin->login($ts3_user, $ts3_pass);

	#select teamspeakserver
	$tsAdmin->selectServer($ts3_port);
	
	#get clientlist
	$clients = $tsAdmin->clientList("-uid");
	
	echo '<table border="1">';

	#print clients to browser
	foreach($clients['data'] as $client) {
		if($client['client_type'] == '0'){
			$avatar = $tsAdmin->clientAvatar($client['client_unique_identifier']);
			if($avatar["success"]) {
				echo '<tr><td><img src="data:image/png;base64,'.$avatar["data"].'" /></td><td>'.$client['client_nickname'].'</tr>';
			}else{
				echo '<tr><td>X</td><td>'.$client['client_nickname'].'</tr>';
			}
		}
	}

	echo '</table>';
}else{
	echo 'Connection could not be established.';
}

/**
 * This code retuns all errors from the debugLog
 */
if(count($tsAdmin->getDebugLog()) > 0) {
	foreach($tsAdmin->getDebugLog() as $logEntry) {
		echo '<script>alert("'.$logEntry.'");</script>';
	}
}
?>