<?php
/**
  * viewreturn.php
  *
  * Is a small script to debug and view the return of a command in ts3admin.class
  *
  * by par0noid solutions - ts3admin.info
  *
*/

/*-------SETTINGS-------*/
$ts3_ip = '127.0.0.1';
$ts3_queryport = 10011;
$ts3_port = 9987; // Set 0 if you don't want to select a server
$ts3_user = 'serveradmin'; // leave blank if you want to execute your command as a guest
$ts3_pass = 'password';
/*----------------------*/

//#####################################################################################################
//#####################################################################################################
//############################################# IMPORTANT #############################################
//#####################################################################################################
//#####################################################################################################
//#####################################################################################################
//#####################################################################################################
//################################# Edit the debug command in line 118 ################################
//#####################################################################################################
//#####################################################################################################
//#####################################################################################################
//#####################################################################################################
//#####################################################################################################
//#####################################################################################################
//#####################################################################################################
//#####################################################################################################
//#####################################################################################################
//#####################################################################################################

function is_multi($array) { return (count($array) != count($array, 1)); }
function is_assoc($array) { return (bool)count(array_filter(array_keys($array), 'is_string')); }
function array2table($array) {
	$html = '';

	if(is_multi($array)) {
		$html .= '<table cellpadding="5" cellspacing="1" bgcolor="black" align="center"><tr>';

		foreach($array as $array2) {
			foreach($array2 as $key => $value) {
				$html .= '<td bgcolor="#c0c0c0" style="font-weight:bold" align="center">&nbsp;&nbsp;&nbsp;'.htmlspecialchars($key).'&nbsp;&nbsp;&nbsp;</td>';
			}
			break;
		}

		$html .= '</tr>';

		foreach($array as $array2) {
			$html .= '<tr>';
			foreach($array2 as $key => $value) {
				$html .= '<td bgcolor="#ffffff" align="center">&nbsp;&nbsp;&nbsp;'.htmlspecialchars($value).'&nbsp;&nbsp;&nbsp;</td>';
			}
			$html .= '</tr>';
		}
	}else{
		$html .= '<table cellpadding="5" cellspacing="1" bgcolor="black" align="center" width="';

		if(is_assoc($array)) {
			$html .= '400px">';
			$html .= '<tr>';
			$html .= '<td bgcolor="#c0c0c0" style="font-weight:bold" align="center">Key</td>';
			$html .= '<td bgcolor="#c0c0c0" style="font-weight:bold" align="center">Value</td>';
			$html .= '</tr>';

			foreach($array as $key => $value) {
				$html .= '<tr>';
				$html .= '<td bgcolor="#ffffff" align="center">'.htmlspecialchars($key).'</td>';
				$html .= '<td bgcolor="#ffffff" align="center">'.htmlspecialchars($value).'</td>';
				$html .= '</tr>';
			}
		}else{
			$html .= '200px">';
			$html .= '<tr>';
			$html .= '<td bgcolor="#c0c0c0" style="font-weight:bold" align="center">Value</td>';
			$html .= '</tr>';

			foreach($array as $value) {
				$html .= '<tr>';
				$html .= '<td bgcolor="#ffffff" align="center">'.htmlspecialchars($value).'</td>';
				$html .= '</tr>';
			}
		}
	}
	return $html;
}

#Include ts3admin.class.php
require("../lib/ts3admin.class.php");

#build a new ts3admin object
$tsAdmin = new ts3admin($ts3_ip, $ts3_queryport);

$html = '';

if($tsAdmin->getElement('success', $tsAdmin->connect())) {
	
	#login if username is given
	if(!empty($ts3_user)) { $tsAdmin->login($ts3_user, $ts3_pass); }
	
	#select teamspeakserver if needed
	if($ts3_port != 0) { $tsAdmin->selectServer($ts3_port); }
	
//#####################################################################################################
//#####################################################################################################
//#####################################################################################################
//#####################################################################################################
//#####################################################################################################
//#####################################################################################################
//####################### Edit here ###################################################################
//########################VVVVVVVVV####################################################################
	$output = $tsAdmin->clientInfo();
//#####################################################################################################
//#####################################################################################################
//#####################################################################################################
//#####################################################################################################
//#####################################################################################################
//#####################################################################################################
//#####################################################################################################
//#####################################################################################################
	
	$html = array2table($output['data']);
}else{
	$html = '<h1 style="color:red" align="center">Connection could not be established.</align>';
}

/**
 * This code retuns all errors from the debugLog
 */
if(count($tsAdmin->getDebugLog()) > 0) {
	foreach($tsAdmin->getDebugLog() as $logEntry) {
		$html .= '<script>alert("'.$logEntry.'");</script>';
	}
}
?>
<html>
	<head>
	
	</head>
	<body bgcolor="#B1BDA6">
		<br><br>
		<h1 align="center">Returnvalues:</h1>
		<br><br>
		<?php echo $html; ?>
	</body>
</html>