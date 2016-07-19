<?PHP
/**
  * serverlist.php
  *
  * Is a small script to demonstrate how to get a serverlist via ts3admin.class
  *
  * by par0noid solutions - ts3admin.info
  *
*/

/*-------SETTINGS-------*/
$ts3_ip = '127.0.0.1';
$ts3_queryport = 10011;
$ts3_user = 'serveradmin';
$ts3_pass = 'password';
/*----------------------*/

#Include ts3admin.class.php
require("../lib/ts3admin.class.php");

#build a new ts3admin object
$tsAdmin = new ts3admin($ts3_ip, $ts3_queryport);

if($tsAdmin->getElement('success', $tsAdmin->connect())) {
	#login as serveradmin
	$tsAdmin->login($ts3_user, $ts3_pass);
	
	#get serverlist
	$servers = $tsAdmin->serverList();
	
	#set output var
	$output = '';
	
	#generate table codes for all servers
	foreach($servers['data'] as $server) {
		$output .= '<tr bgcolor="#ffffff" onmouseover="style.backgroundColor=\'#eeeeee\'" onmouseout="style.backgroundColor=\'#ffffff\'">';
		$output .= '<td width="50px" align="center">#'.$server['virtualserver_id'].'</td>';
		$output .= '<td width="300px">&nbsp;&nbsp;'.htmlspecialchars($server['virtualserver_name']).'</td>';
		$output .= '<td width="100px" align="center">'.$server['virtualserver_port'].'</td>';
		if(isset($server['virtualserver_clientsonline'])) {
			$clients = $server['virtualserver_clientsonline'] . '/' . $server['virtualserver_maxclients'];
		}else{
			$clients = '-';
		}
		$output .= '<td width="200px" align="center">'.$clients.'</td>';
		$output .= '<td width="100px" align="center">'.$server['virtualserver_status'].'</td>';
		if(isset($server['virtualserver_uptime'])) {
			$uptime = $tsAdmin->convertSecondsToStrTime(($server['virtualserver_uptime']));
		}else{
			$uptime = '-';
		}
		$output .= '<td width="150px" align="center">'.$uptime.'</td>';
	}
}else{
	echo 'Connection could not be established.';
}

if(count($tsAdmin->getDebugLog()) > 0) {
	foreach($tsAdmin->getDebugLog() as $logEntry) {
		echo '<script>alert("'.$logEntry.'");</script>';
	}
}

?>
<html>
	<head>
    	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    	<title>Serverlist example by Par0noid Solutions</title>
        <style>
			* {
				font-size:13px;
				font-family:Verdana, Geneva, sans-serif;
			}
		</style>
    </head>
    <body bgcolor="#a2ad9b">
    	<table bgcolor="#000000" cellpadding="5" cellspacing="1" width="900px" border="0" align="center">
        	<tr bgcolor="#c0c0c0">
            	<td width="50px" align="center"><b>ID<b></td>
                <td width="300px" align="center"><b>Servername<b></td>
            	<td width="100px" align="center"><b>Port<b></td>
            	<td width="200px" align="center"><b>Current clients<b></td>
                <td width="100px" align="center"><b>Status<b></td>
                <td width="150px" align="center"><b>Uptime<b></td>
            </tr>
            <?PHP echo $output; ?>
        </table>
    </body>
</html>