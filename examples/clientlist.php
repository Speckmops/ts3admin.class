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
	$clients = $tsAdmin->clientList();
	
	#print client count
	echo count($clients['data']) . ' clients on selected server<br><br>';
	
	#print clients to browser
	foreach($clients['data'] as $client) {
		echo $client['client_nickname'] . '<br>';	
	}
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