<?PHP
/*
 *                         ts3admin.class.php
 *                         ------------------                    
 *   created              : 18. December 2009
 *   last modified        : 13. March 2019
 *   version              : 1.0.2.5
 *   website              : http://ts3admin.info
 *   copyright            : (C) 2018 Stefan Zehnpfennig
 *  
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *  
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *  
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
/** 
 * @file
 * The ts3admin.class is a powerful api for communication with Teamspeak 3 Servers from your website! Your creativity knows no bounds!
 * 
 * @author      Stefan Zehnpfennig
 * @copyright   Copyright (c) 2018, Stefan Zehnpfennig
 * @version     1.0.2.5
 * @package     ts3admin
 *
 */
 
/** 
  * \class ts3admin
  * \brief The ts3admin.class
  */
namespace par0noid;

class ts3admin {

//*******************************************************************************************	
//****************************************** Vars *******************************************
//*******************************************************************************************

/**
  * runtime is an private handle and configuration storage
  *
  * @author     Stefan Zehnpfennig
  */
	private $runtime = array('socket' => '', 'selected' => false, 'host' => '', 'queryport' => '10011', 'timeout' => 2, 'debug' => array(), 'fileSocket' => '', 'bot_clid' => '', 'bot_name' => '');

//*******************************************************************************************	
//*************************************** Constants *****************************************
//*******************************************************************************************

	const HostMessageMode_NONE = 0;
	const HostMessageMode_LOG = 1;
	const HostMessageMode_MODAL = 2;
	const HostMessageMode_MODALQUIT = 3;
	
	const HostBannerMode_NOADJUST = 0;
	const HostBannerMode_IGNOREASPECT = 1;
	const HostBannerMode_KEEPASPECT = 2;
	
	const CODEC_SPEEX_NARROWBAND = 0;
	const CODEC_SPEEX_WIDEBAND = 1;
	const CODEC_SPEEX_ULTRAWIDEBAND = 2;
	const CODEC_CELT_MONO = 3;
	const CODEC_OPUS_VOICE = 4;
	const CODEC_OPUS_MUSIC = 5;
	
	const CODEC_CRYPT_INDIVIDUAL = 0;
	const CODEC_CRYPT_DISABLED = 1;
	const CODEC_CRYPT_ENABLED = 2;	
	
	const TextMessageTarget_CLIENT = 1;
	const TextMessageTarget_CHANNEL = 2;
	const TextMessageTarget_SERVER = 3;
	
	const LogLevel_ERROR = 1;
	const LogLevel_WARNING = 2;
	const LogLevel_DEBUG = 3;
	const LogLevel_INFO = 4;
	
	const REASON_KICK_CHANNEL = 4;
	const REASON_KICK_SERVER = 5;	

	const PermGroupDBTypeTemplate = 0;
	const PermGroupDBTypeRegular = 1;
	const PermGroupDBTypeQuery = 2;

	const PermGroupTypeServerGroup = 0;
	const PermGroupTypeGlobalClient = 1;
	const PermGroupTypeChannel = 2;
	const PermGroupTypeChannelGroup = 3;
	const PermGroupTypeChannelClient = 4;

	const TokenServerGroup = 0;
	const TokenChannelGroup = 1;

//*******************************************************************************************	
//************************************ Public Functions *************************************
//******************************************************************************************

/**
  * banAddByIp
  *
  * Adds a new ban rule on the selected virtual server.
  *
  *	<b>Output:</b>
  * <pre>
  * Array
  * {
  *  [banid] => 109
  * }
  * </pre>
  *
  * @author     Stefan Zehnpfennig
  * @param		string	$ip			clientIp
  * @param		integer	$time		bantime in seconds (0=unlimited/default)  [optional]
  * @param		string	$banreason	Banreason [optional]
  * @return     array banId
  */
	function banAddByIp($ip, $time = 0, $banreason = NULL) {
		if(!$this->runtime['selected']) { return $this->checkSelected(); }
		
		if(!empty($banreason)) { $msg = ' banreason='.$this->escapeText($banreason); } else { $msg = NULL; }

		return $this->getData('array', 'banadd ip='.$ip.' time='.$time.$msg);
	}

/**
  * banAddByUid
  *
  *	Adds a new ban rule on the selected virtual server.
  *
  * <b>Output:</b>
  * <pre>
  * Array
  * {
  *  [banid] => 110
  * }
  * </pre>
  *
  * @author     Stefan Zehnpfennig
  * @param		string	$uid		clientUniqueId
  * @param		integer	$time		bantime in seconds (0=unlimited/default)  [optional]
  * @param		string	$banreason	Banreason [optional]
  * @return     array banId
  */
	function banAddByUid($uid, $time = 0, $banreason = NULL) {
		if(!$this->runtime['selected']) { return $this->checkSelected(); }
		
		if(!empty($banreason)) { $msg = ' banreason='.$this->escapeText($banreason); } else { $msg = NULL; }
		
		return $this->getData('array', 'banadd uid='.$uid.' time='.$time.$msg);
	}

/**
  * banAddByName
  *
  *	Adds a new ban rule on the selected virtual server.
  *
  * <b>Output:</b>
  * <pre>
  * Array
  * {
  *  [banid] => 111
  * }
  * </pre>
  *
  * @author     Stefan Zehnpfennig
  * @param		string	$name		clientName
  * @param		integer	$time		bantime in seconds (0=unlimited/default)  [optional]
  * @param		string	$banreason	Banreason [optional]
  * @return     array banId
  */
	function banAddByName($name, $time = 0, $banreason = NULL) {
		if(!$this->runtime['selected']) { return $this->checkSelected(); }
		
		if(!empty($banreason)) { $msg = ' banreason='.$this->escapeText($banreason); } else { $msg = NULL; }
										
		return $this->getData('array', 'banadd name='.$this->escapeText($name).' time='.$time.$msg);
	}

/**
  * banClient
  * 
  * Bans the client specified with ID clid from the server. Please note that this will create two separate ban rules for the targeted clients IP address and his unique identifier.
  *
  * <b>Output:</b>
  * <pre>
  * Array
  * {
  *  [1] => 129
  *  [2] => 130
  * }
  * </pre>
  *
  * @author     Stefan Zehnpfennig
  * @param		integer $clid		clientId
  * @param		integer $time		bantime in seconds (0=unlimited/default)  [optional]
  * @param		string	$banreason	Banreason [optional]
  * @return     array banIds
  */
	function banClient($clid, $time = 0, $banreason = NULL) {
		if(!$this->runtime['selected']) { return $this->checkSelected(); }
		
		if(!empty($banreason)) { $msg = ' banreason='.$this->escapeText($banreason); } else { $msg = ''; }
		
		$result = $this->getData('plain', 'banclient clid='.$clid.' time='.$time.$msg);
		
		if($result['success']) {
			return $this->generateOutput(true, $result['errors'], $this->splitBanIds($result['data']));
		}else{
			return $this->generateOutput(false, $result['errors'], false);
		}
	}

/**
  * banDelete
  * 
  * Deletes the ban rule with ID banid from the server.
  *
  * @author     Stefan Zehnpfennig
  * @param		integer $banID	banID
  * @return     array success
  */
	function banDelete($banID) {
		if(!$this->runtime['selected']) { return $this->checkSelected(); }
		return $this->getData('boolean', 'bandel banid='.$banID);
	}

/**
  * banDeleteAll
  * 
  * Deletes all active ban rules from the server.
  *
  * @author     Stefan Zehnpfennig
  * @return     array success
  */
	function banDeleteAll() {
		if(!$this->runtime['selected']) { return $this->checkSelected(); }
		return $this->getData('boolean', 'bandelall');
	}

/**
  * banList
  * 
  * Displays a list of active bans on the selected virtual server.
  *
  * Note: If [name] is always empty use [lastnickname]
  * 
  * <b>Output:</b>
  * <pre>
  * Array
  * {
  *  [banid] => 131
  *  [ip] => 1.2.3.4
  *  [name] => eugen
  *  [uid] => IYAntAcZHgVC7s3n3DNWmuJB/aM=
  *  [mytsid] =>
  *  [created] => 1286660391
  *  [duration] => 0
  *  [invokername] => Par0noid
  *  [invokercldbid] => 2086
  *  [invokeruid] => nUixbsq/XakrrmbqU8O30R/D8Gc=
  *  [reason] => insult
  *  [enforcements] => 0
  *  [lastnickname] => eugen
  * }
  * </pre>
  *
  * @author     Stefan Zehnpfennig
  * @return     array banlist
  */
	function banList() {
		if(!$this->runtime['selected']) { return $this->checkSelected(); }		
		return $this->getData('multi', 'banlist');
	}

/**
  * bindingList
  * 
  * Displays a list of IP addresses used by the server instance on multi-homed machines.
  *
  * <b>Output:</b>
  * <pre>
  * Array
  * {
  *  [ip] => 0.0.0.0
  * }
  * </pre>
  *
  * @author     Stefan Zehnpfennig
  * @param 		integer	$subsystem	(1: voice, 2: query, 3: filetransfer)
  * @return     array bindingList
  */
	function bindingList($subsystem = 0) {
		return $this->getData('multi', 'bindinglist'.($subsystem == 1 ? ' subsystem=voice' : '').($subsystem == 2 ? ' subsystem=query' : '').($subsystem == 3 ? ' subsystem=filetransfer' : ''));
	}

/**
  * channelAddPerm
  * 
  * Adds a set of specified permissions to a channel. Multiple permissions can be added by providing the two parameters of each permission. A permission can be specified by permid or permsid.
  * 
  * <b>Input-Array like this:</b>
  * <pre>
  * $permissions = array();
  * $permissions['permissionID'] = 'permissionValue';
  * //or you could use Permission Name
  * $permissions['permissionName'] = 'permissionValue';
  * </pre>
  *
  * @author     Stefan Zehnpfennig
  * @param		integer	$cid			channelId
  * @param		array	$permissions	permissions
  * @return     array success
  */
	function channelAddPerm($cid, $permissions) {
		if(!$this->runtime['selected']) { return $this->checkSelected(); }
		
		if(count($permissions) > 0) {
			//Permissions given
			
			//Errorcollector
			$errors = array();
			
			//Split Permissions to prevent query from overload
			$permissions = array_chunk($permissions, 50, true);
			
			//Action for each splitted part of permission
			foreach($permissions as $permission_part)
			{
				//Create command_string for each command that we could use implode later
				$command_string = array();
				
				foreach($permission_part as $key => $value)
				{
					$command_string[] = (is_numeric($key) ? "permid=" : "permsid=").$this->escapeText($key).' permvalue='.$value;
				}
				
				$result = $this->getData('boolean', 'channeladdperm cid='.$cid.' '.implode('|', $command_string));
				
				if(!$result['success'])
				{
					foreach($result['errors'] as $error)
					{
						$errors[] = $error;
					}
				}
			}
			
			if(count($errors) == 0)
			{
				return $this->generateOutput(true, array(), true);
			}else{
				return $this->generateOutput(false, $errors, false);
			}
			
		}else{
			// No permissions given
			$this->addDebugLog('no permissions given');
			return $this->generateOutput(false, array('Error: no permissions given'), false);
		}
	}

/**
  * channelClientAddPerm
  * 
  * Adds a set of specified permissions to a client in a specific channel. Multiple permissions can be added by providing the three parameters of each permission. A permission can be specified by permid or permsid.
  * 
  * <b>Input-Array like this:</b>
  * <pre>
  * $permissions = array();
  * $permissions['permissionID'] = 'permissionValue';
  * //or you could use Permission Name
  * $permissions['permissionName'] = 'permissionValue';
  * </pre>
  *
  * @author     Stefan Zehnpfennig
  * @param		integer		$cid			channelID
  * @param		integer		$cldbid			clientDBID
  * @param		array		$permissions	permissions
  * @return     array success
  */
	function channelClientAddPerm($cid, $cldbid, $permissions) {
		if(!$this->runtime['selected']) { return $this->checkSelected(); }
		
		if(count($permissions) > 0) {
			//Permissions given
				
			//Errorcollector
			$errors = array();
				
			//Split Permissions to prevent query from overload
			$permissions = array_chunk($permissions, 50, true);
				
			//Action for each splitted part of permission
			foreach($permissions as $permission_part)
			{
				//Create command_string for each command that we could use implode later
				$command_string = array();
		
				foreach($permission_part as $key => $value)
				{
					$command_string[] = (is_numeric($key) ? "permid=" : "permsid=").$this->escapeText($key).' permvalue='.$value;
				}
		
				$result = $this->getData('boolean', 'channelclientaddperm cid='.$cid.' cldbid='.$cldbid.' '.implode('|', $command_string));
		
				if(!$result['success'])
				{
					foreach($result['errors'] as $error)
					{
						$errors[] = $error;
					}
				}
			}
				
			if(count($errors) == 0)
			{
				return $this->generateOutput(true, array(), true);
			}else{
				return $this->generateOutput(false, $errors, false);
			}
				
		}else{
			// No permissions given
			$this->addDebugLog('no permissions given');
			return $this->generateOutput(false, array('Error: no permissions given'), false);
		}
	}

/**
  * channelClientDelPerm
  * 
  * Removes a set of specified permissions from a client in a specific channel. Multiple permissions can be removed at once. A permission can be specified by permid or permsid.
  *
  * <b>Input-Array like this:</b>
  * <pre>
  * $permissions = array();
  * $permissions[] = 'permissionID';
  * $permissions[] = 'permissionName';
  * //or
  * $permissions = array('permissionID', 'permissionName', 'permissionID');
  * </pre>
  *
  * @author     Stefan Zehnpfennig
  * @param		integer		$cid				channelID
  * @param		integer		$cldbid				clientDBID
  * @param		array		$permissions		permissions
  * @return     array success
  */
	function channelClientDelPerm($cid, $cldbid, $permissions) {
		if(!$this->runtime['selected']) { return $this->checkSelected(); }
		$permissionArray = array();
		
		if(count($permissions) > 0) {
			foreach($permissions AS $value) {
				$permissionArray[] = is_numeric($value) ? 'permid='.$value : 'permsid='.$value;
			}
			return $this->getData('boolean', 'channelclientdelperm cid='.$cid.' cldbid='.$cldbid.' '.implode('|', $permissionArray));
		}else{
			$this->addDebugLog('no permissions given');
			return $this->generateOutput(false, array('Error: no permissions given'), false);
		}
	}

/**
  * channelClientPermList
  * 
  * Displays a list of permissions defined for a client in a specific channel.
  *
  * <b>Output:</b>
  * <pre>
  * Array
  * {
  *  [cid] => 250 (only in first result)
  *  [cldbid] => 2086 (only in first result)
  *  [permid] => 12876 (if permsid = false)
  *  [permsid] => b_client_info_view (if permsid = true)
  *  [permvalue] => 1
  *  [permnegated] => 0
  *  [permskip] => 0
  * }
  * </pre>
  *
  * @author     Stefan Zehnpfennig
  * @param		integer		$cid		channelID
  * @param		integer		$cldbid		clientDBID
  * @param		boolean		$permsid	displays permissionName instead of permissionID
  * @return     array	channelclientpermlist
  */
	function channelClientPermList($cid, $cldbid, $permsid = false) {
		if(!$this->runtime['selected']) { return $this->checkSelected(); }		
		return $this->getData('multi', 'channelclientpermlist cid='.$cid.' cldbid='.$cldbid.($permsid ? ' -permsid' : ''));
	}

/**
  * channelCreate
  * 
  * Creates a new channel using the given properties and displays its ID. Note that this command accepts multiple properties which means that you're able to specifiy all settings of the new channel at once.
  * 
  * <b style="color:red">Hint:</b> don't forget to set channel_flag_semi_permanent = 1 or channel_flag_permanent = 1
  * 
  * <b style="color:red">Hint:</b> you'll get an error if you want to create a channel without channel_name
  * 
  * <b style="color:red">Hint:</b> to set the parent channel you've to use cpid instead of pid
  * 
  * <b>Input-Array like this:</b>
  * <pre>
  * $data = array();
  * 
  * $data['setting'] = 'value';
  * $data['setting'] = 'value';
  * </pre>
  * 
  * <b>Output:</b>
  * <pre>
  * Array
  * {
  *  [cid] => 257
  * }
  * </pre>
  *
  * <b>Possible properties:</b> CHANNEL_NAME, CHANNEL_TOPIC, CHANNEL_DESCRIPTION, CHANNEL_PASSWORD, CHANNEL_CODEC, CHANNEL_CODEC_QUALITY, CHANNEL_MAXCLIENTS, CHANNEL_MAXFAMILYCLIENTS, CHANNEL_ORDER, CHANNEL_FLAG_PERMANENT, CHANNEL_FLAG_SEMI_PERMANENT, CHANNEL_FLAG_TEMPORARY, CHANNEL_FLAG_DEFAULT, CHANNEL_FLAG_MAXCLIENTS_UNLIMITED, CHANNEL_FLAG_MAXFAMILYCLIENTS_UNLIMITED, CHANNEL_FLAG_MAXFAMILYCLIENTS_INHERITED, CHANNEL_NEEDED_TALK_POWER, CHANNEL_NAME_PHONETIC, CHANNEL_ICON_ID, CHANNEL_CODEC_IS_UNENCRYPTED, CPID
  *
  * @author     Stefan Zehnpfennig
  * @param		array $data properties
  * @return     array channelInfo
  */
	function channelCreate($data) {
		if(!$this->runtime['selected']) { return $this->checkSelected(); }
		
		$propertiesString = '';
		
		foreach($data as $key => $value) {
			$propertiesString .= ' '.strtolower($key).'='.$this->escapeText($value);
		}
		
		return $this->getData('array', 'channelcreate '.$propertiesString);
	}

/**
  * channelDelete
  * 
  * Deletes an existing channel by ID. If force is set to 1, the channel will be deleted even if there are clients within. The clients will be kicked to the default channel with an appropriate reason message.
  *
  * @author     Stefan Zehnpfennig
  * @param		integer $cid channelID
  * @param		integer $force {1|0} (default: 1)
  * @return     array success
  */
	function channelDelete($cid, $force = 1) {
		if(!$this->runtime['selected']) { return $this->checkSelected(); }
		return $this->getData('boolean', 'channeldelete cid='.$cid.' force='.$force);
	}

/**
  * channelDelPerm
  * 
  * Removes a set of specified permissions from a channel. Multiple permissions can be removed at once. A permission can be specified by permid or permsid.
  *
  * <b>Input-Array like this:</b>
  * <pre>
  * $permissions = array();
  * $permissions[] = 'permissionID';
  * //or you could use
  * $permissions[] = 'permissionName';
  * </pre>
  *
  * @author     Stefan Zehnpfennig
  * @param		integer		$cid				channelID
  * @param		array		$permissions		permissions
  * @return     array success
  */
	function channelDelPerm($cid, $permissions) {
		if(!$this->runtime['selected']) { return $this->checkSelected(); }
		$permissionArray = array();
		
		if(count($permissions) > 0) {
			foreach($permissions AS $value) {
				$permissionArray[] = (is_numeric($value) ? 'permid=' : 'permsid=').$value;
			}
			return $this->getData('boolean', 'channeldelperm cid='.$cid.' '.implode('|', $permissionArray));
		}else{
			$this->addDebugLog('no permissions given');
			return $this->generateOutput(false, array('Error: no permissions given'), false);
		}
	}

/**
  * channelEdit
  * 
  * Changes a channels configuration using given properties. Note that this command accepts multiple properties which means that you're able to change all settings of the channel specified with cid at once.
  *
  * <b>Input-Array like this:</b>
  *	<pre>
  *	$data = array();
  *	$data['setting'] = 'value';
  *	$data['setting'] = 'value';
  *	</pre>
  *
  * <b>Possible properties:</b> Take a look at channelCreate function
  *
  * @author     Stefan Zehnpfennig
  * @param		integer	$cid	$channelID
  * @param		array	$data	edited settings
  * @return     array success
  */
	function channelEdit($cid, $data) {
		if(!$this->runtime['selected']) { return $this->checkSelected(); }
		
		$settingsString = '';
		
		foreach($data as $key => $value) {
			$settingsString .= ' '.strtolower($key).'='.$this->escapeText($value);
		}

		return $this->getData('boolean', 'channeledit cid='.$cid.$settingsString);
	}

/**
  * channelFind
  * 
  * displays a list of channels matching a given name pattern.
  *
  * <b>Output:</b>
  * <pre>
  * Array
  * {
  *  [cid] => 2
  *  [channel_name] => Lobby
  * }
  * </pre>
  *
  * @author     Stefan Zehnpfennig
  * @param		string	$pattern	channelName
  * @return     array channelList 
  */
	function channelFind($pattern) {
		if(!$this->runtime['selected']) { return $this->checkSelected(); }
		return $this->getData('multi', 'channelfind pattern='.$this->escapeText($pattern));
	}

/**
 * channelGetIconByChannelID
 *
 * Will return the base64 encoded binary of the channelIcon
 * 
 * <pre>
 * $result = $tsAdmin->channelGetIconByChannelID($channelID);
 * You can display it like: echo '<img src="data:image/png;base64,'.$result["data"].'" />';
 * </pre>
 *
 * @author  Stefan Zehnpfennig
 * @param  string  $channelID  channelID
 * @return array  base64 image
 */
	function channelGetIconByChannelID($channelID) {
	  if(!$this->runtime['selected']) { return $this->checkSelected(); }

	  if(empty($channelID))
	  {
		return $this->generateOutput(false, array('Error: empty channelID'), false);
	  }
	  
	  $channel = $this->channelInfo($channelID);
	  
	  if(!$channel["success"])
	  {
		return $this->generateOutput(false, $channel["error"], false);
	  }
	  
	  return $this->getIconByID($channel["data"]["channel_icon_id"]);
	}

/**
  * channelGroupAdd
  * 
  * Creates a new channel group using a given name and displays its ID. The optional type parameter can be used to create ServerQuery groups and template groups.
  *
  * <b>groupDbTypes:</b>
  *	<ol start="0">
  *		<li>template group (used for new virtual servers)</li>
  *		<li>regular group (used for regular clients)</li>
  *		<li>global query group (used for ServerQuery clients)</li>
  *	</ol>
  *
  * <b>Output:</b>
  * <pre>
  * Array
  * {
  *  [cgid] => 86
  * }
  * </pre>
  *
  * @author     Stefan Zehnpfennig
  * @param		integer	$name	groupName
  * @param		integer	$type   groupDbType [optional] (default: 1)
  * @return     array success
  */
	function channelGroupAdd($name, $type = 1) {
		if(!$this->runtime['selected']) { return $this->checkSelected(); }
		return $this->getData('array', 'channelgroupadd name='.$this->escapeText($name).' type='.$type);
	}

/**
  *	channelGroupAddClient
  *
  * Sets the channel group of a client to the ID specified with cgid.
  *
  * @author     Stefan Zehnpfennig
  * @param		  integer $cgid	groupID
  * @param		  integer $cid	channelID
  * @param		  integer $cldbid	clientDBID
  * @return     array success
  */
	function channelGroupAddClient($cgid, $cid, $cldbid) {
    return $this->setclientchannelgroup($cgid, $cid, $cldbid);
	}

/**
  * channelGroupAddPerm
  * 
  * Adds a set of specified permissions to a channel group. Multiple permissions can be added by providing the two parameters of each permission. A permission can be specified by permid or permsid.
  *
  * <b>Input-Array like this:</b>
  * <pre>
  * $permissions = array();
  * $permissions['permissionID'] = 'permissionValue';
  * //or you could use:
  * $permissions['permissionName'] = 'permissionValue';
  * </pre>
  *
  * @author     Stefan Zehnpfennig
  * @param		integer		$cgid			channelGroupID
  * @param		array		$permissions	permissions
  * @return     array success
  */
	function channelGroupAddPerm($cgid, $permissions) {
		if(!$this->runtime['selected']) { return $this->checkSelected(); }
		
		if(count($permissions) > 0) {
			//Permissions given
		
			//Errorcollector
			$errors = array();
		
			//Split Permissions to prevent query from overload
			$permissions = array_chunk($permissions, 50, true);
		
			//Action for each splitted part of permission
			foreach($permissions as $permission_part)
			{
				//Create command_string for each command that we could use implode later
				$command_string = array();
		
				foreach($permission_part as $key => $value)
				{
					$command_string[] = (is_numeric($key) ? "permid=" : "permsid=").$this->escapeText($key).' permvalue='.$value;
				}
		
				$result = $this->getData('boolean', 'channelgroupaddperm cgid='.$cgid.' '.implode('|', $command_string));
		
				if(!$result['success'])
				{
					foreach($result['errors'] as $error)
					{
						$errors[] = $error;
					}
				}
			}
		
			if(count($errors) == 0) {
				return $this->generateOutput(true, array(), true);
			}else{
				return $this->generateOutput(false, $errors, false);
			}
		
		}else{
			// No permissions given
			$this->addDebugLog('no permissions given');
			return $this->generateOutput(false, array('Error: no permissions given'), false);
		}
	}

/**
  * channelGroupClientList
  * 
  * Displays all the client and/or channel IDs currently assigned to channel groups. All three parameters are optional so you're free to choose the most suitable combination for your requirement
  *
  * <b>Output:</b>
  * <pre>
  * Array
  * {
  *  [cid] => 2
  *  [cldbid] => 9
  *  [cgid] => 9
  * }
  * </pre>
  *
  * @author     Stefan Zehnpfennig
  * @param		integer $cid		channelID [optional]
  * @param		integer $cldbid		clientDBID [optional]
  * @param		integer $cgid		channelGroupID [optional]
  * @return     array channelGroupClientList
  */
	function channelGroupClientList($cid = NULL, $cldbid = NULL, $cgid = NULL) {
		if(!$this->runtime['selected']) { return $this->checkSelected(); }
		
		return $this->getData('multi', 'channelgroupclientlist'.(!empty($cid) ? ' cid='.$cid : '').(!empty($cldbid) ? ' cldbid='.$cldbid : '').(!empty($cgid) ? ' cgid='.$cgid : ''));
	}

/**
  * channelGroupCopy
  * 
  * Creates a copy of the channel group specified with scgid. If tcgid is set to 0, the server will create a new group. To overwrite an existing group, simply set tcgid to the ID of a designated target group. If a target group is set, the name parameter will be ignored. The type parameter can be used to create ServerQuery groups and template groups.
  *
  * <b>groupDbTypes:</b>
  *	<ol start="0">
  *		<li>template group (used for new virtual servers)</li>
  *		<li>regular group (used for regular clients)</li>
  *		<li>global query group (used for ServerQuery clients)</li>
  *	</ol>
  *
  * <b>Output:</b>
  * <pre>
  * Array
  * {
  *  [cgid] => 86
  * }
  * </pre>
  *
  * @author     Stefan Zehnpfennig
  * @param		integer	$scgid	sourceChannelGroupID
  * @param		integer	$tcgid	targetChannelGroupID 
  * @param		integer $name	groupName
  * @param		integer	$type	groupDbType
  * @return     array groupId
  */
	function channelGroupCopy($scgid, $tcgid, $name, $type = 1) {
		if(!$this->runtime['selected']) { return $this->checkSelected(); }
		return $this->getData('array', 'channelgroupcopy scgid='.$scgid.' tcgid='.$tcgid.' name='.$this->escapeText($name).' type='.$type);
	}

/**
  * channelGroupDelete
  * 
  * Deletes a channel group by ID. If force is set to 1, the channel group will be deleted even if there are clients within.
  *
  * @author     Stefan Zehnpfennig
  * @param		integer $cgid	channelGroupID
  * @param		integer $force	forces deleting channelGroup (default: 1)
  * @return     array success
  */
	function channelGroupDelete($cgid, $force = 1) {
		if(!$this->runtime['selected']) { return $this->checkSelected(); }
		return $this->getData('boolean', 'channelgroupdel cgid='.$cgid.' force='.$force);
	}

/**
  * channelGroupDelPerm
  * 
  * Removes a set of specified permissions from the channel group. Multiple permissions can be removed at once. A permission can be specified by permid or permsid.
  *
  * <b>Input-Array like this:</b>
  * <pre>
  * $permissions = array();
  * $permissions[] = 'permissionID';
  * $permissions[] = 'permissionName';
  * </pre>
  *
  * @author     Stefan Zehnpfennig
  * @param		integer		$cgid				channelGroupID
  * @param		array		$permissions		permissions
  * @return     array success
  */
	function channelGroupDelPerm($cgid, $permissions) {
		if(!$this->runtime['selected']) { return $this->checkSelected(); }
		$permissionArray = array();
		
		if(count($permissions) > 0) {
			foreach($permissions AS $value) {
				$permissionArray[] = (is_numeric($value) ? 'permid=' : 'permsid=').$value;
			}
			return $this->getData('boolean', 'channelgroupdelperm cgid='.$cgid.' '.implode('|', $permissionArray));
		}else{
			$this->addDebugLog('no permissions given');
			return $this->generateOutput(false, array('Error: no permissions given'), false);
		}
	}

/**
 * channelGroupGetIconByCGID
 *
 * Will return the base64 encoded binary of the channelGroupIcon
 * 
 * <pre>
 * $result = $tsAdmin->channelGroupGetIconByCGID($channelGroupID);
 * You can display it like: echo '<img src="data:image/png;base64,'.$result["data"].'" />';
 * </pre>
 *
 * @author  Stefan Zehnpfennig
 * @param  string  $channelGroupID  channelGroupID
 * @return array  base64 image
 */
	function channelGroupGetIconByCGID($channelGroupID) {
	  if(!$this->runtime['selected']) { return $this->checkSelected(); }

	  if(empty($channelGroupID))
	  {
		return $this->generateOutput(false, array('Error: empty channelGroupID'), false);
	  }
	  
	  $channelGroupList = $this->channelGroupList();
	  
	  if(!$channelGroupList["success"])
	  {
		return $this->generateOutput(false, $channelGroupList["error"], false);
	  }
	  
	  $cgid = -1;
	  $iconID = 0;
	  
	  foreach($channelGroupList['data'] as $group)
	  {
		  if($group['cgid'] == $channelGroupID)
		  {
			  $cgid = $group['cgid'];
			  $iconID = $group['iconid'];
			  break;
		  }
	  }
	  
	  if($cgid == -1)
	  {
		return $this->generateOutput(false, array('Error: invalid channelGroupID'), false);
	  }
	  
	  if($iconID == '0')
	  {
		return $this->generateOutput(false, array('Error: channelGroup has no icon'), false);
	  }
	  
	  return $this->getIconByID($iconID);
	}
	
/**
  * channelGroupList
  * 
  * Displays a list of channel groups available on the selected virtual server.
  * 
  * <b>Output:</b>
  * <pre>
  * Array
  * {
  *  [cgid] => 3
  *  [name] => Testname
  *  [type] => 0
  *  [iconid] => 100
  *  [savedb] => 1
  *  [sortid] => 0
  *  [namemode] => 0
  *  [n_modifyp] => 75
  *  [n_member_addp] => 50
  *  [n_member_removep] => 50
  * }
  * </pre>
  *
  * @author     Stefan Zehnpfennig
  * @return     array channelGroupList
  */
	function channelGroupList() {
		if(!$this->runtime['selected']) { return $this->checkSelected(); }
		
		return $this->getData('multi', 'channelgrouplist');
	}

/**
  * channelGroupPermList
  * 
  * Displays a list of permissions assigned to the channel group specified with cgid.
  * If the permsid option is specified, the output will contain the permission names instead of the internal IDs.
  * 
  * <b>Output:</b>
  * <pre>
  * Array
  * {
  *  [permid] => 8471 (displayed if permsid is false)
  *  [permsid] => i_channel_create_modify_with_codec_latency_factor_min (displayed if permsid is true)
  *  [permvalue] => 1
  *  [permnegated] => 0
  *  [permskip] => 0
  * }
  * </pre>
  *
  * @author     Stefan Zehnpfennig
  * @param		integer		$cgid		channelGroupID
  * @param		boolean		$permsid	permsid
  * @return		array	channelGroupPermlist
  */
  	function channelGroupPermList($cgid, $permsid = false) {
		if(!$this->runtime['selected']) { return $this->checkSelected(); }		
		return $this->getData('multi', 'channelgrouppermlist cgid='.$cgid.($permsid ? ' -permsid' : ''));
	}

/**
  * channelGroupRename
  * 
  * Changes the name of a specified channel group.
  *
  * @author     Stefan Zehnpfennig
  * @param		integer $cgid groupID
  * @param		integer $name groupName
  * @return     array success
  */
	function channelGroupRename($cgid, $name) {
		if(!$this->runtime['selected']) { return $this->checkSelected(); }
		return $this->getData('boolean', 'channelgrouprename cgid='.$cgid.' name='.$this->escapeText($name));
	}

/**
  * channelInfo
  *
  *	Displays detailed configuration information about a channel including ID, topic, description, etc.

  * <b>Output:</b>
  * <pre>
  * Array
  * {
  *  [pid] => 0
  *  [channel_name] => Test
  *  [channel_topic] => 
  *  [channel_description] => 
  *  [channel_password] => cc97Pm4oOYq0J9fXDAgiWv/qScQ=
  *  [channel_codec] => 2
  *  [channel_codec_quality] => 7
  *  [channel_maxclients] => -1
  *  [channel_maxfamilyclients] => -1
  *  [channel_order] => 1
  *  [channel_flag_permanent] => 1
  *  [channel_flag_semi_permanent] => 0
  *  [channel_flag_default] => 0
  *  [channel_flag_password] => 0
  *  [channel_codec_latency_factor] => 1
  *  [channel_codec_is_unencrypted] => 1
  *	 [channel_security_salt] => 
  *  [channel_delete_delay] => 0
  *  [channel_flag_maxclients_unlimited] => 1
  *  [channel_flag_maxfamilyclients_unlimited] => 0
  *  [channel_flag_maxfamilyclients_inherited] => 1
  *  [channel_filepath] => files\\virtualserver_1\\channel_2
  *  [channel_needed_talk_power] => 0
  *  [channel_forced_silence] => 0
  *  [channel_name_phonetic] => 
  *  [channel_icon_id] => 0
  *	 [channel_flag_private] => 0
  *  [seconds_empty] => 61 (If it's a temporary channel with a channel delete delay)
  * }
  * </pre>
  *
  * @author     Stefan Zehnpfennig
  * @param		integer $cid channelID
  * @return     array channelInfo
  */
	function channelInfo($cid) {
		if(!$this->runtime['selected']) { return $this->checkSelected(); }
		return $this->getData('array', 'channelinfo cid='.$cid);
	}

/**
  * channelClientList
  * 
  * Displays a list of clients online on a virtual server in a specific channel including their ID, nickname, status flags, etc. The output can be modified using several command options. Please note that the output will only contain clients which are currently in channels you're able to subscribe to.
  *
  * <b>Possible params:</b> [-uid] [-away] [-voice] [-times] [-groups] [-info] [-icon] [-country] [-ip] [-badges]
  *
  * <b>Output:</b>
  * <pre>
  * Array
  * {
  *  [clid] => 1
  *  [cid] => 3
  *  [client_database_id] => 2
  *  [client_nickname] => Par0noid
  *  [client_type] => 0
  *  [-uid] => [client_unique_identifier] => nUixbsq/XakrrmbqU8O30R/D8Gc=
  *  [-away] => [client_away] => 0
  *  [-away] => [client_away_message] => 
  *  [-voice] => [client_flag_talking] => 0
  *  [-voice] => [client_input_muted] => 0
  *  [-voice] => [client_output_muted] => 0
  *  [-voice] => [client_input_hardware] => 0
  *  [-voice] => [client_output_hardware] => 0
  *  [-voice] => [client_talk_power] => 0
  *  [-voice] => [client_is_talker] => 0
  *  [-voice] => [client_is_priority_speaker] => 0
  *  [-voice] => [client_is_recording] => 0
  *  [-voice] => [client_is_channel_commander] => 0
  *  [-times] => [client_idle_time] => 1714
  *  [-times] => [client_created] => 1361027850
  *  [-times] => [client_lastconnected] => 1361042955
  *  [-groups] => [client_servergroups] => 6,7
  *  [-groups] => [client_channel_group_id] => 8
  *  [-groups] => [client_channel_group_inherited_channel_id] => 1
  *  [-info] => [client_version] => 3.0.9.2 [Build: 1351504843]
  *  [-info] => [client_platform] => Windows
  *  [-icon] => [client_icon_id] => 0
  *  [-country] => [client_country] => 
  *  [-ip] => [connection_client_ip] => 127.0.0.1
  *  [-badges] => [client_badges] => Overwolf=0
  * }
  * 
  * <b>Usage:</b>
  * 
  * $ts3->channelClientList(3); //No parameters
  * $ts3->channelClientList(3, "-uid"); //Single parameter
  * $ts3->channelClientList(3, "-uid -away -voice -times -groups -info -country -icon -ip -badges"); //Multiple parameters
  * </pre>
  *
  * @author     Stefan Zehnpfennig
  * @param		  string	$cid	channelID
  * @param		  string	$params	additional parameters [optional]
  * @return     array clientList 
  */
	function channelClientList($cid, $params = null) {
		if(!$this->runtime['selected']) { return $this->checkSelected(); }
		
		if(!empty($params)) { $params = ' '.$params; }
		
		$result = $this->getData('multi', 'clientlist'.$params);

    if($result['success'])
    {
      $clients = array();

      if(count($result['data']) > 0)
      {
        foreach($result['data'] as $client)
        {
          if($client['cid'] == $cid)
          {
            $clients[] = $client;
          }
        }
      }

      return $this->generateOutput(true, null, $clients);
    }
    else
    {
      return $result;
    }
	}

/**
  * channelList
  * 
  * Displays a list of channels created on a virtual server including their ID, order, name, etc. The output can be modified using several command options.
  *
  * <b>Possible parameters:</b> [-topic] [-flags] [-voice] [-limits] [-icon] [-secondsempty]
  *
  * <b>Output: (without parameters)</b>
  * <pre>
  * Array
  * {
  *  [cid] => 2
  *  [pid] => 0
  *  [channel_order] => 1
  *  [channel_name] => Test
  *  [total_clients] => 0
  *  [channel_needed_subscribe_power] => 0
  * }
  * </pre>
  * <b>Output: (from parameters)</b>
  * <pre>
  * Array
  * {
  *  [-topic] => [channel_topic] => Default Channel has no topic
  *  [-flags] => [channel_flag_default] => 1
  *  [-flags] => [channel_flag_password] => 0
  *  [-flags] => [channel_flag_permanent] => 1
  *  [-flags] => [channel_flag_semi_permanent] => 0
  *  [-voice] => [channel_codec] => 2
  *  [-voice] => [channel_codec_quality] => 7
  *  [-voice] => [channel_needed_talk_power] => 0
  *  [-limits] => [total_clients_family] => 1
  *  [-limits] => [channel_maxclients] => -1
  *  [-limits] => [channel_maxfamilyclients] => -1
  *  [-icon] => [channel_icon_id] => 0
  *  [-seconds_empty] => [seconds_empty] => -1
  * }
  * </pre>
  * <b>Usage:</b>
  * <pre>
  * $ts3->channelList(); //No parameters
  * $ts3->channelList("-flags"); //Single parameter
  * $ts3->channelList("-topic -flags -voice -limits -icon"); //Multiple parameters / all
  * </pre>
  *
  * @author     Stefan Zehnpfennig
  * @param		string		$params		additional parameters [optional]
  * @return		array	channelList
  */
	function channelList($params = null) {
		if(!$this->runtime['selected']) { return $this->checkSelected(); }
		if(!empty($params)) { $params = ' '.$params; }
		
		return $this->getData('multi', 'channellist'.$params);
	}

/**
  * channelMove
  * 
  * Moves a channel to a new parent channel with the ID cpid. If order is specified, the channel will be sorted right under the channel with the specified ID. If order is set to 0, the channel will be sorted right below the new parent.
  *
  * @author     Stefan Zehnpfennig
  * @param		integer $cid	channelID
  * @param		integer $cpid	channelParentID
  * @param		integer $order	channelSortOrder
  * @return     array success
  */
	function channelMove($cid, $cpid, $order = null) {
		if(!$this->runtime['selected']) { return $this->checkSelected(); }		
		return $this->getData('boolean', 'channelmove cid='.$cid.' cpid='.$cpid.($order != null ? ' order='.$order : ''));
	}

/**
  * channelPermList
  * 
  * Displays a list of permissions defined for a channel.
  *
  * <b>Output:</b>
  * <pre>
  * Array
  * {
  *  [cid] => 2 (only in first result)
  *  [permid] => 8471 (if permsid = false)
  *  [permsid] => i_channel_needed_delete_power (if permsid = true)
  *  [permvalue] => 1
  *  [permnegated] => 0
  *  [permskip] => 0
  * }
  * </pre>
  *
  * @author     Stefan Zehnpfennig
  * @param		integer		$cid		channelID
  * @param		boolean		$permsid	displays permissionName instead of permissionID [optional]
  * @return     array channelpermlist
  */
	function channelPermList($cid, $permsid = false) {
		if(!$this->runtime['selected']) { return $this->checkSelected(); }	
		return $this->getData('multi', 'channelpermlist cid='.$cid.($permsid ? ' -permsid' : ''));
	}

/**
  * clientAddPerm
  * 
  * Adds a set of specified permissions to a client. Multiple permissions can be added by providing the three parameters of each permission. A permission can be specified by permid or permsid.
  *
  * <b>Input-Array like this:</b>
  * <pre>
  * $permissions = array();
  * $permissions['permissionID'] = array('permissionValue', 'permskip');
  * //or you could use Permission Name
  * $permissions['permissionName'] = array('permissionValue', 'permskip');
  * </pre>
  *
  * @author     Stefan Zehnpfennig
  * @param		integer	$cldbid			clientDBID
  * @param		array	$permissions	permissions
  * @return     array success
  */
	function clientAddPerm($cldbid, $permissions) {
		if(!$this->runtime['selected']) { return $this->checkSelected(); }
		
		if(count($permissions) > 0) {
			//Permissions given
				
			//Errorcollector
			$errors = array();
				
			//Split Permissions to prevent query from overload
			$permissions = array_chunk($permissions, 50, true);
				
			//Action for each splitted part of permission
			foreach($permissions as $permission_part)
			{
				//Create command_string for each command that we could use implode later
				$command_string = array();
		
				foreach($permission_part as $key => $value)
				{
					$command_string[] = (is_numeric($key) ? "permid=" : "permsid=").$this->escapeText($key).' permvalue='.$this->escapeText($value[0]).' permskip='.$this->escapeText($value[1]);
				}
		
				$result = $this->getData('boolean', 'clientaddperm cldbid='.$cldbid.' '.implode('|', $command_string));
		
				if(!$result['success'])
				{
					foreach($result['errors'] as $error)
					{
						$errors[] = $error;
					}
				}
			}
				
			if(count($errors) == 0)
			{
				return $this->generateOutput(true, array(), true);
			}else{
				return $this->generateOutput(false, $errors, false);
			}
		}else{
			// No permissions given
			$this->addDebugLog('no permissions given');
			return $this->generateOutput(false, array('Error: no permissions given'), false);
		}
	}

/**
 * clientAvatar
 *
 * Will return the base64 encoded binary of the clients avatar
 * 
 * <pre>
 * $result = $tsAdmin->clientAvatar($uid);
 * You can display it like: echo '<img src="data:image/png;base64,'.$result["data"].'" />';
 * </pre>
 *
 * @author  Stefan Zehnpfennig
 * @param  string  $uid  clientUID
 * @return array  base64 image
 */
	function clientAvatar($uid) {
	  if(!$this->runtime['selected']) { return $this->checkSelected(); }

	  if(empty($uid))
	  {
		return $this->generateOutput(false, array('Error: empty uid'), false);
	  }

	  $newChars = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p');
	  $auid = '';

	  for ($i = 0; $i <= 19; $i++) {
			  $char = ord(substr(base64_decode($uid), $i, 1));
			  $auid .= $newChars[($char & 0xF0) >> 4];
			  $auid .= $newChars[$char & 0x0F];
	  }

	  $check = $this->ftgetfileinfo(0, '', '/avatar_'.$auid);

	  if(!$check["success"])
	  {
		return $this->generateOutput(false, array('Error: avatar does not exist'), false);
	  }

	  $init = $this->ftInitDownload('/avatar_'.$auid, 0, '');

	  if(!$init["success"])
	  {
		return $this->generateOutput(false, array('Error: init failed'), false);
	  }

	  $download = $this->ftDownloadFile($init);

	  if(is_array($download))
	  {
		return $download;
	  }else{
		return $this->generateOutput(true, false, base64_encode($download));
	  }

	}
	
/**
  * clientDbDelete
  * 
  * Deletes a clients properties from the database.
  *
  * @author     Stefan Zehnpfennig
  * @param		integer $cldbid	clientDBID
  * @return     array success
  */
	function clientDbDelete($cldbid) {
		if(!$this->runtime['selected']) { return $this->checkSelected(); }
		return $this->getData('boolean', 'clientdbdelete cldbid='.$cldbid);
	}

/**
  * clientDbEdit
  * 
  * Changes a clients settings using given properties.
  *
  * <b>Input-Array like this:</b>
  * <pre>
  * $data = array();
  * 
  * $data['property'] = 'value';
  * $data['property'] = 'value';
  * </pre>
  *
  * @author     Stefan Zehnpfennig
  * @param		integer		$cldbid		clientDBID
  * @param		array		$data	 	clientProperties
  * @return     array success
  */
	function clientDbEdit($cldbid, $data) {
		if(!$this->runtime['selected']) { return $this->checkSelected(); }
		
		$settingsString = '';
		
		foreach($data as $key => $value) {
			$settingsString .= ' '.strtolower($key).'='.$this->escapeText($value);
		}
		
		return $this->getData('boolean', 'clientdbedit cldbid='.$cldbid.$settingsString);
	}

/**
  * clientDbFind
  * 
  * Displays a list of client database IDs matching a given pattern. You can either search for a clients last known nickname or his unique identity by using the -uid option.
  *
  * <b>Output:</b>
  * <pre>
  * Array
  * {
  *  [cldbid] => 2
  * }
  *	</pre>
  *
  * @author     Stefan Zehnpfennig
  * @param		string	$pattern	clientName
  * @param		boolean	$uid		set true to add -uid param [optional]
  * @return     array clientList 
  */
	function clientDbFind($pattern, $uid = false) {
		if(!$this->runtime['selected']) { return $this->checkSelected(); }
		return $this->getData('multi', 'clientdbfind pattern='.$this->escapeText($pattern).($uid ? ' -uid' : ''));
	}

/**
  * clientDbInfo
  *
  * Displays detailed database information about a client including unique ID, creation date, etc.
  *
  * <b>Output:</b>
  * <pre>
  * Array
  * {
  *  [client_unique_identifier] => nUixbsq/XakrrmbqU8O30R/D8Gc=
  *  [client_nickname] => par0noid
  *  [client_database_id] => 2
  *  [client_created] => 1361027850
  *  [client_lastconnected] => 1361027850
  *  [client_totalconnections] => 1
  *  [client_flag_avatar] => 
  *  [client_description] => 
  *  [client_month_bytes_uploaded] => 0
  *  [client_month_bytes_downloaded] => 0
  *  [client_total_bytes_uploaded] => 0
  *  [client_total_bytes_downloaded] => 0
  *  [client_base64HashClientUID] => jneilbgomklpfnkjclkoggokfdmdlhnbbpmdpagh
  *  [client_lastip] => 127.0.0.1
  * }
  * </pre>
  *
  * @author     Stefan Zehnpfennig
  * @param		integer		$cldbid		clientDBID
  * @return     array	clientDbInfo
  */
	function clientDbInfo($cldbid) {
		if(!$this->runtime['selected']) { return $this->checkSelected(); }
		return $this->getData('array', 'clientdbinfo cldbid='.$cldbid);
	}

/**
  * clientDbList
  * 
  * Displays a list of client identities known by the server including their database ID, last nickname, etc.
  *
  * <b>Possible params:</b> [start={offset}] [duration={limit}] [-count]
  *
  * <b>Output:</b>
  * <pre>
  * Array
  * {
  *  [count] => 1 (if count parameter is set)
  *  [cldbid] => 2
  *  [client_unique_identifier] => nUixbsq/XakrrmbqU8O30R/D8Gc=
  *  [client_nickname] => par0noid
  *  [client_created] => 1361027850
  *  [client_lastconnected] => 1361027850
  *  [client_totalconnections] => 1
  *  [client_description] => 
  *  [client_lastip] => 127.0.0.1
  * }
  * </pre>
  *
  * @author     Stefan Zehnpfennig
  * @param		integer	$start		offset [optional] (Default: 0)
  * @param		integer	$duration	limit [optional] (Default: -1)
  * @param		boolean	$count		set true to add -count param [optional]
  * @return     array clientdblist
  */
	function clientDbList($start = 0, $duration = -1, $count = false) {
		if(!$this->runtime['selected']) { return $this->checkSelected(); }
		return $this->getData('multi', 'clientdblist'.($start != 0 ? ' start='.$start : '').($duration != -1 ? ' duration='.$duration : '').($count ? ' -count' : ''));
	}

/**
  * clientDelPerm
  * 
  * Removes a set of specified permissions from a client. Multiple permissions can be removed at once. A permission can be specified by permid or permsid.
  *
  * <b>Input-Array like this:</b>
  * <pre>
  * $permissions = array();
  * $permissions['permissionID'] = 'permissionValue';
  * //or you could use Permission Name
  * $permissions['permissionName'] = 'permissionValue';
  * </pre>
  *
  * @author     Stefan Zehnpfennig
  * @param		integer		$cldbid				clientDBID
  * @param		array		$permissionIds		permissionIDs
  * @return     array success
  */
	function clientDelPerm($cldbid, $permissionIds) {
		if(!$this->runtime['selected']) { return $this->checkSelected(); }
		
		$permissionArray = array();
		
		if(count($permissionIds) > 0) {
			foreach($permissionIds AS $value) {
				$permissionArray[] = (is_numeric($value) ? 'permid=' : 'permsid=').$value;
			}
			return $this->getData('boolean', 'clientdelperm cldbid='.$cldbid.' '.implode('|', $permissionArray));
		}else{
			$this->addDebugLog('no permissions given');
			return $this->generateOutput(false, array('Error: no permissions given'), false);
		}
	}

/**
  * clientEdit
  * 
  * Changes a clients settings using given properties.
  *
  * <b>Input-Array like this:</b>
  * <pre>
  * $data = array();
  *	
  * $data['property'] = 'value';
  * $data['property'] = 'value';
  * </pre>
  *
  * <b>Possible properties:</b> CLIENT_IS_TALKER, CLIENT_DESCRIPTION, CLIENT_IS_CHANNEL_COMMANDER, CLIENT_ICON_ID
  *
  * @author     Stefan Zehnpfennig
  * @param		integer	$clid 			clientID
  * @param		array	$data			clientProperties
  * @return     array success
  */
	function clientEdit($clid, $data) {
		if(!$this->runtime['selected']) { return $this->checkSelected(); }
		
		$settingsString = '';
		
		foreach($data as $key => $value) {
			$settingsString .= ' '.strtolower($key).'='.$this->escapeText($value);
		}
		
		return $this->getData('boolean', 'clientedit clid='.$clid.$settingsString);
	}

/**
  * clientFind
  * 
  * Displays a list of clients matching a given name pattern.
  *
  * <b>Output:</b>
  * <pre>
  * Array
  * {
  *  [clid] => 18
  *  [client_nickname] => par0noid
  * }
  * </pre>
  *
  * @author     Stefan Zehnpfennig
  * @param		string	$pattern	clientName
  * @return     array clienList
  */
	function clientFind($pattern) {
		if(!$this->runtime['selected']) { return $this->checkSelected(); }
		return $this->getData('multi', 'clientfind pattern='.$this->escapeText($pattern));
	}

/**
  * clientGetDbIdFromUid
  * 
  * Displays the database ID matching the unique identifier specified by cluid.
  *
  *	<b>Output:</b>
  * <pre>
  * Array
  * {
  *  [cluid] => nUixbsq/XakrrmbqU8O30R/D8Gc=
  *  [cldbid] => 2
  * }
  * </pre>
  *
  * @author     Stefan Zehnpfennig
  * @param		string	$cluid	clientUID
  * @return     array clientInfo
  */
	function clientGetDbIdFromUid($cluid) {
		if(!$this->runtime['selected']) { return $this->checkSelected(); }
		return $this->getData('array', 'clientgetdbidfromuid cluid='.$cluid);
	}

/**
  * clientGetIds
  * 
  * Displays all client IDs matching the unique identifier specified by cluid.
  *
  * <b>Output:</b>
  * <pre>
  * Array
  * {
  *  [cluid] => nUixbdf/XakrrmsdffO30R/D8Gc=
  *  [clid] => 7
  *  [name] => Par0noid
  * }
  * </pre>
  *
  * @author     Stefan Zehnpfennig
  * @param		string	$cluid	clientUID
  * @return     array clientList 
  */
	function clientGetIds($cluid) {
		if(!$this->runtime['selected']) { return $this->checkSelected(); }
		return $this->getData('multi', 'clientgetids cluid='.$cluid);
	}

/**
  * clientGetNameFromDbid
  * 
  * Displays the unique identifier and nickname matching the database ID specified by cldbid.
  *
  *	<b>Output:</b>
  * <pre>
  * Array
  * {
  *  [cluid] => nUixbsq/XakrrmbqU8O30R/D8Gc=
  *  [cldbid] => 2
  *  [name] => Par0noid
  * }
  * </pre>
  *
  * @author     Stefan Zehnpfennig
  * @param		integer	$cldbid	clientDBID
  * @return     array clientInfo
  */
	function clientGetNameFromDbid($cldbid) {
		if(!$this->runtime['selected']) { return $this->checkSelected(); }
		return $this->getData('array', 'clientgetnamefromdbid cldbid='.$cldbid);
	}
	
/**
  * clientGetNameFromUid
  * 
  * Displays the database ID and nickname matching the unique identifier specified by cluid.
  *
  *	<b>Output:</b>
  * <pre>
  * Array
  * {
  *  [cluid] => nUixbsq/XakrrmbqU8O30R/D8Gc=
  *  [cldbid] => 2
  *  [name] => Par0noid
  * }
  * </pre>
  *
  * @author     Stefan Zehnpfennig
  * @param		string	$cluid	clientUID
  * @return     array clientInfo
  */
	function clientGetNameFromUid($cluid) {
		if(!$this->runtime['selected']) { return $this->checkSelected(); }
		return $this->getData('array', 'clientgetnamefromuid cluid='.$cluid);
	}

/**
  * clientInfo
  * 
  * Displays detailed configuration information about a client including unique ID, nickname, client version, etc.
  *
  * <b>Output:</b>
  * <pre>
  * Array
  * {
  *  [cid] => 2
  *  [client_idle_time] => 4445369
  *  [client_unique_identifier] => nUixbsq/XakrrmbqU8O30R/D8Gc=
  *  [client_nickname] => par0noid
  *  [client_version] => 3.0.9.2 [Build: 1351504843]
  *  [client_platform] => Windows
  *  [client_input_muted] => 1
  *  [client_output_muted] => 1
  *  [client_outputonly_muted] => 0
  *  [client_input_hardware] => 1
  *  [client_output_hardware] => 1
  *  [client_default_channel] => 
  *  [client_meta_data] => 
  *  [client_is_recording] => 0
  *  [client_version_sign] => ldWL49uDKC3N9uxdgWRMTOzUabc1nBqUiOa+Nal5HvdxJiN4fsTnmmPo5tvglN7WqoVoFfuuKuYq1LzodtEtCg==
  *  [client_security_hash] => 
  *  [client_login_name] => 
  *  [client_database_id] => 2
  *  [client_channel_group_id] => 5
  *  [client_servergroups] => 6
  *  [client_created] => 1361027850
  *  [client_lastconnected] => 1361027850
  *  [client_totalconnections] => 1
  *  [client_away] => 0
  *  [client_away_message] => 
  *  [client_type] => 0
  *  [client_flag_avatar] => 
  *  [client_talk_power] => 75
  *  [client_talk_request] => 0
  *  [client_talk_request_msg] => 
  *  [client_description] => 
  *  [client_is_talker] => 0
  *  [client_month_bytes_uploaded] => 0
  *  [client_month_bytes_downloaded] => 0
  *  [client_total_bytes_uploaded] => 0
  *  [client_total_bytes_downloaded] => 0
  *  [client_is_priority_speaker] => 0
  *  [client_nickname_phonetic] => 
  *  [client_needed_serverquery_view_power] => 75
  *  [client_default_token] => 
  *  [client_icon_id] => 0
  *  [client_is_channel_commander] => 0
  *  [client_country] => 
  *  [client_channel_group_inherited_channel_id] => 2
  *  [client_badges] => Overwolf=0
  *  [client_myteamspeak_id] => 
  *  [client_base64HashClientUID] => jneilbgomklpfnkjclkoggokfdmdlhnbbpmdpagh
  *  [connection_filetransfer_bandwidth_sent] => 0
  *  [connection_filetransfer_bandwidth_received] => 0
  *  [connection_packets_sent_total] => 12130
  *  [connection_bytes_sent_total] => 542353
  *  [connection_packets_received_total] => 12681
  *  [connection_bytes_received_total] => 592935
  *  [connection_bandwidth_sent_last_second_total] => 82
  *  [connection_bandwidth_sent_last_minute_total] => 92
  *  [connection_bandwidth_received_last_second_total] => 84
  *  [connection_bandwidth_received_last_minute_total] => 88
  *  [connection_connected_time] => 5908749
  *  [connection_client_ip] => 127.0.0.1
  * } 
  * </pre>
  *
  * @author     Stefan Zehnpfennig
  * @param		integer	$clid	clientID
  * @return     array	clientInformation
  */
	function clientInfo($clid) {
		if(!$this->runtime['selected']) { return $this->checkSelected(); }
		return $this->getData('array', 'clientinfo clid='.$clid);
	}

/**
  * clientKick
  * 
  * Kicks one or more clients specified with clid from their currently joined channel or from the server, depending on reasonid. The reasonmsg parameter specifies a text message sent to the kicked clients. This parameter is optional and may only have a maximum of 40 characters.
  *
  * @author     Stefan Zehnpfennig
  * @param		integer $clid		clientID
  * @param		string	$kickMode	kickMode (server or channel) (Default: server)
  * @param		string	$kickmsg 	kick reason [optional]
  * @return     array success
  */
	function clientKick($clid, $kickMode = "server", $kickmsg = "") {
		if(!$this->runtime['selected']) { return $this->checkSelected(); }
		
		if(in_array($kickMode, array('server', 'channel'))) {
		
			if($kickMode == 'server') { $from = '5'; }
			if($kickMode == 'channel') { $from = '4'; }
			
			if(!empty($kickmsg)) { $msg = ' reasonmsg='.$this->escapeText($kickmsg); } else{ $msg = ''; }
			
			return $this->getData('boolean', 'clientkick clid='.$clid.' reasonid='.$from.$msg);
		}else{
			$this->addDebugLog('invalid kickMode');
			return $this->generateOutput(false, array('Error: invalid kickMode'), false);
		}
	}

/**
  * clientList
  * 
  * Displays a list of clients online on a virtual server including their ID, nickname, status flags, etc. The output can be modified using several command options. Please note that the output will only contain clients which are currently in channels you're able to subscribe to.
  *
  * <b>Possible params:</b> [-uid] [-away] [-voice] [-times] [-groups] [-info] [-icon] [-country] [-ip] [-badges]
  *
  * <b>Output:</b>
  * <pre>
  * Array
  * {
  *  [clid] => 1
  *  [cid] => 1
  *  [client_database_id] => 2
  *  [client_nickname] => Par0noid
  *  [client_type] => 0
  *  [-uid] => [client_unique_identifier] => nUixbsq/XakrrmbqU8O30R/D8Gc=
  *  [-away] => [client_away] => 0
  *  [-away] => [client_away_message] => 
  *  [-voice] => [client_flag_talking] => 0
  *  [-voice] => [client_input_muted] => 0
  *  [-voice] => [client_output_muted] => 0
  *  [-voice] => [client_input_hardware] => 0
  *  [-voice] => [client_output_hardware] => 0
  *  [-voice] => [client_talk_power] => 0
  *  [-voice] => [client_is_talker] => 0
  *  [-voice] => [client_is_priority_speaker] => 0
  *  [-voice] => [client_is_recording] => 0
  *  [-voice] => [client_is_channel_commander] => 0
  *  [-times] => [client_idle_time] => 1714
  *  [-times] => [client_created] => 1361027850
  *  [-times] => [client_lastconnected] => 1361042955
  *  [-groups] => [client_servergroups] => 6,7
  *  [-groups] => [client_channel_group_id] => 8
  *  [-groups] => [client_channel_group_inherited_channel_id] => 1
  *  [-info] => [client_version] => 3.0.9.2 [Build: 1351504843]
  *  [-info] => [client_platform] => Windows
  *  [-icon] => [client_icon_id] => 0
  *  [-country] => [client_country] => 
  *  [-ip] => [connection_client_ip] => 127.0.0.1
  *  [-badges] => [client_badges] => Overwolf=0
  * }
  * 
  * <b>Usage:</b>
  * 
  * $ts3->clientList(); //No parameters
  * $ts3->clientList("-uid"); //Single parameter
  * $ts3->clientList("-uid -away -voice -times -groups -info -country -icon -ip -badges"); //Multiple parameters
  * </pre>
  *
  * @author     Stefan Zehnpfennig
  * @param		string	$params	additional parameters [optional]
  * @return     array clientList 
  */
	function clientList($params = null) {
		if(!$this->runtime['selected']) { return $this->checkSelected(); }
		
		if(!empty($params)) { $params = ' '.$params; }
		
		return $this->getData('multi', 'clientlist'.$params);
	}

/**
  * clientMove
  * 
  * Moves one or more clients specified with clid to the channel with ID cid. If the target channel has a password, it needs to be specified with cpw. If the channel has no password, the parameter can be omitted.
  *
  * @author     Stefan Zehnpfennig
  * @param		integer $clid	clientID
  * @param		integer $cid	channelID
  * @param		string	$cpw	channelPassword [optional]
  * @return     array success
  */
	function clientMove($clid, $cid, $cpw = null) {
		if(!$this->runtime['selected']) { return $this->checkSelected(); }
		return $this->getData('boolean', 'clientmove clid='.$clid.' cid='.$cid.(!empty($cpw) ? ' cpw='.$this->escapeText($cpw) : ''));
	}

/**
  * clientPermList
  * 
  * Displays a list of permissions defined for a client.
  *
  * <b>Output:</b>
  * <pre>
  * Array
  * {
  *  [permid] => 20654 //with permsid = false
  *  [permsid] => b_client_ignore_bans //with permsid = true
  *  [permvalue] => 1
  *  [permnegated] => 0
  *  [permskip] => 0
  * }
  * </pre>
  *
  * @author     Stefan Zehnpfennig
  * @param		integer		$cldbid 	clientDBID
  * @param		boolean		$permsid	set true to add -permsid param [optional]
  * @return     array clientPermList
  */
	function clientPermList($cldbid, $permsid = false) {
		if(!$this->runtime['selected']) { return $this->checkSelected(); }		
		return $this->getData('multi', 'clientpermlist cldbid='.$cldbid.($permsid ? ' -permsid' : ''));
	}

/**
  * clientPoke
  * 
  * Sends a poke message to the client specified with clid.
  *
  * @author     Stefan Zehnpfennig
  * @param		integer $clid	clientID
  * @param		string 	$msg 	pokeMessage
  * @return     array success
  */
	function clientPoke($clid, $msg) {
		if(!$this->runtime['selected']) { return $this->checkSelected(); }
		return $this->getData('boolean', 'clientpoke clid='.$clid.' msg='.$this->escapeText($msg));
	}

/**
  * clientSetServerQueryLogin
  * 
  * Updates your own ServerQuery login credentials using a specified username. The password will be auto-generated.
  * 
  * <b>Output:</b>
  * <pre>
  * Array
  * {
  *  [client_login_password] => +r\/TQqvR
  * }
  * </pre>
  *
  * @author     Stefan Zehnpfennig
  * @param		string	$username	username
  * @return     array userInfomation
  */
	function clientSetServerQueryLogin($username) {
		return $this->getData('array', 'clientsetserverquerylogin client_login_name='.$this->escapeText($username));
	}

/**
  * clientUpdate
  * 
  * Change your ServerQuery clients settings using given properties.
  * 
  * <b>Input-Array like this:</b>
  * <pre>
  * $data = array();
  * $data['property'] = 'value';
  * $data['property'] = 'value';
  * </pre>
  *
  * @author     Stefan Zehnpfennig
  * @param		array	$data	clientProperties
  * @return     array success
  */
	function clientUpdate($data) {
		$settingsString = '';
		
		foreach($data as $key => $value) {
			$settingsString .= ' '.strtolower($key).'='.$this->escapeText($value);
		}
		
		return $this->getData('boolean', 'clientupdate '.$settingsString);
	}

/**
  * complainAdd
  *
  * Submits a complaint about the client with database ID tcldbid to the server.
  *
  * @author     Stefan Zehnpfennig
  * @param		integer $tcldbid	targetClientDBID
  * @param		string	$msg		complainMessage
  * @return     array success
  */
	function complainAdd($tcldbid, $msg) {
		if(!$this->runtime['selected']) { return $this->checkSelected(); }
		return $this->getData('boolean', 'complainadd tcldbid='.$tcldbid.' message='.$this->escapeText($msg));
	}

/**
  * complainDelete
  * 
  * Deletes the complaint about the client with ID tcldbid submitted by the client with ID fcldbid from the server.
  *
  * @author     Stefan Zehnpfennig
  * @param		integer $tcldbid targetClientDBID
  * @param		integer $fcldbid fromClientDBID
  * @return     array success
  */
	function complainDelete($tcldbid, $fcldbid) {
		if(!$this->runtime['selected']) { return $this->checkSelected(); }
		return $this->getData('boolean', 'complaindel tcldbid='.$tcldbid.' fcldbid='.$fcldbid);
	}

/**
  * complainDeleteAll
  * 
  * Deletes all complaints about the client with database ID tcldbid from the server.
  *
  * @author     Stefan Zehnpfennig
  * @param		integer $tcldbid targetClientDBID
  * @return     array success
  */
	function complainDeleteAll($tcldbid) {
		if(!$this->runtime['selected']) { return $this->checkSelected(); }
		return $this->getData('boolean', 'complaindelall tcldbid='.$tcldbid);
	}

/**
  * complainList
  * 
  * Displays a list of complaints on the selected virtual server. If tcldbid is specified, only complaints about the targeted client will be shown.
  *
  * <b>Output:</b>
  * <pre>
  * Array
  * {
  *  [tcldbid] => 2
  *  [tname] => par0noid
  *  [fcldbid] => 1
  *  [fname] => serveradmin from 127.0.0.1:6814
  *  [message] => Steals crayons
  *  [timestamp] => 1361044090
  * }
  * </pre>
  *
  * @author     Stefan Zehnpfennig
  * @param		string $tcldbid	targetClientDBID [optional]
  * @return     array complainList
  */
	function complainList($tcldbid = null) {
		if(!$this->runtime['selected']) { return $this->checkSelected(); }
		if(!empty($tcldbid)) { $tcldbid = ' tcldbid='.$tcldbid; }
		return $this->getData('multi', 'complainlist'.$tcldbid);
	}
	
/**
  * customDelete
  * 
  * Removes a custom property from a client specified by the cldbid.
  *
  *
  * @author     Stefan Zehnpfennig
  * @param    string  $cldbid   clientDBID
  * @param    string  $ident    customIdent
  * @return   boolean   success
  */
	function customDelete($cldbid, $ident) {
		if(!$this->runtime['selected']) { return $this->checkSelected(); }
		return $this->getData('boolean', 'customdelete cldbid='.$cldbid.' ident='.$this->escapeText($ident));
	}


/**
  * customInfo
  * 
  * Displays a list of custom properties for the client specified with cldbid.
  *
  * <b>Output:</b>
  * <pre>
  * Array
  * {
  * 	[0] => Array
  *  	{
  *  		[cldbid] => 1
  *			[ident] => abc
  *			[value] => def
  *	  	}
  * 	[1] => Array
  *  	{
  *			[ident] => ghi
  *			[value] => jkl
  *	  	}
  * }
  * </pre>
  *
  * @author     Stefan Zehnpfennig
  * @param		string $cldbid	clientDBID
  * @return     array customInfos
  */
	function customInfo($cldbid) {
		if(!$this->runtime['selected']) { return $this->checkSelected(); }
		return $this->getData('multi', 'custominfo cldbid='.$cldbid);
	}

/**
  * customSearch
  * 
  * Searches for custom client properties specified by ident and value. The value parameter can include regular characters and SQL wildcard characters (e.g. %).
  *
  * <b>Output: (ident=abc, pattern=%)</b>
  * <pre>
  * Array
  * {
  * 	[0] => Array
  *  	{
  *  		[cldbid] => 1
  *			[ident] => abc
  *			[value] => def
  *	  	}
  * 	[1] => Array
  *  	{
  *  		[cldbid] => 2
  *			[ident] => abc
  *			[value] => def
  *	  	}
  * }
  * </pre>
  *
  * @author     Stefan Zehnpfennig
  * @param		string	$ident		customIdent
  * @param		string	$pattern	searchpattern
  * @return     array	customSearchInfos
  */
	function customSearch($ident, $pattern) {
		if(!$this->runtime['selected']) { return $this->checkSelected(); }
		return $this->getData('multi', 'customsearch ident='.$this->escapeText($ident).' pattern='.$this->escapeText($pattern));
	}

/**
  * customSet
  * 
  * Creates or updates a custom property for client specified by the cldbid. Ident and value can be any value, and are the key value pair of the custom property.
  *
  *
  * @author     Stefan Zehnpfennig
  * @param    string  $cldbid   clientDBID
  * @param    string  $ident    customIdent
  * @param    string  $value    customValue
  * @return   boolean   success
  */
	function customSet($cldbid, $ident, $value) {
		if(!$this->runtime['selected']) { return $this->checkSelected(); }
		return $this->getData('boolean', 'customset cldbid='.$cldbid.' ident='.$this->escapeText($ident).' value='.$this->escapeText($value));
	}

/**
  * execOwnCommand
  * 
  * executes a command that isn't defined in class and returns data like your propose
  * 
  * <b>Modes:</b>
  * <ul>
  * 	<li><b>0:</b> execute -> return boolean</li>
  * 	<li><b>1:</b> execute -> return normal array</li>
  * 	<li><b>2:</b> execute -> return multidimensional array</li>
  * 	<li><b>3:</b> execute -> return plaintext serverquery</li>
  * </ul>
  *
  * @author     Stefan Zehnpfennig
  * @param		string	$mode		executionMode
  * @param		string	$command	command
  * @return     mixed result
  */
	function execOwnCommand($mode, $command) {
		if($mode == '0') {
			return $this->getData('boolean', $command);
		}
		if($mode == '1') {
			return $this->getData('array', $command);
		}
		if($mode == '2') {
			return $this->getData('multi', $command);
		}
		if($mode == '3') {
			return $this->getData('plain', $command);
		}
	}

/**
  * ftCreateDir
  * 
  * Creates new directory in a channels file repository.
  *
  * @author     Stefan Zehnpfennig
  * @param		string	$cid		channelId
  * @param		string	$cpw		channelPassword (leave blank if not needed)
  * @param		string	$dirname	dirPath
  * @return     array success
  */
	function ftCreateDir($cid, $cpw = null, $dirname) {
		if(!$this->runtime['selected']) { return $this->checkSelected(); }
		return $this->getData('boolean', 'ftcreatedir cid='.$cid.' cpw='.$this->escapeText($cpw).' dirname='.$this->escapeText($dirname));
	}

/**
  * ftDeleteFile
  * 
  * Deletes one or more files stored in a channels file repository.
  *
  * <b>Input-Array like this:</b>
  * <pre>
  * $files = array();
  *	
  * $files[] = '/pic1.jpg';
  * $files[] = '/dokumente/test.txt';
  * $files[] = '/dokumente';
  * </pre>
  *
  * @author     Stefan Zehnpfennig
  * @param		string	$cid	channelID
  * @param		string	$cpw	channelPassword (leave blank if not needed)
  * @param		array	$files	files
  * @return     array success
  */
	function ftDeleteFile($cid, $cpw = '', $files) {
		if(!$this->runtime['selected']) { return $this->checkSelected(); }
		$fileArray = array();
		
		if(count($files) > 0) {
			foreach($files AS $file) {
				$fileArray[] = 'name='.$this->escapeText($file);
			}
			return $this->getData('boolean', 'ftdeletefile cid='.$cid.' cpw='.$this->escapeText($cpw).' '.implode('|', $fileArray));
		}else{
			$this->addDebugLog('no files given');
			return $this->generateOutput(false, array('Error: no files given'), false);
		}
	}

/**
  * ftDownloadFile
  * 
  * Ddownloads a file and returns its contents
  *
  * @author     Stefan Zehnpfennig
  * @param		array	$data	return of ftInitDownload
  * @return     array downloadedFile
  */
	function ftDownloadFile($data) {
		$errnum = null;
		$errstr = null;
  		$this->runtime['fileSocket'] = @fsockopen($this->runtime['host'], $data['data']['port'], $errnum, $errstr, $this->runtime['timeout']);
  		if($this->runtime['fileSocket']) {
  			$this->ftSendKey($data['data']['ftkey']);
  			$content = $this->ftRead($data['data']['size']);
  			@fclose($this->runtime['fileSocket']);
  			$this->runtime['fileSocket'] = '';
  			return $content;
  		}else{
  			$this->addDebugLog('fileSocket returns '.$errnum. ' | '.$errstr);
  			return $this->generateOutput(false, array('Error in fileSocket: '.$errnum. ' | '.$errstr), false);
  		}
	}
	
/**
  * ftGetFileInfo
  * 
  * Displays detailed information about one or more specified files stored in a channels file repository.
  * 
  * <b>Output:</b>
  * <pre>
  * Array
  * {
  *  [cid] => 0
  *  [name] => /icon_1947482249
  *  [size] => 744
  *  [datetime] => 1286633633
  * }
  * </pre>
  *
  * @author     Stefan Zehnpfennig
  * @param		string	$cid	channelID
  * @param		string	$cpw	channelPassword (leave blank if not needed)
  * @param		string 	$file	path to file
  * @return     array success
  */
	function ftGetFileInfo($cid, $cpw = '', $file) {
		if(!$this->runtime['selected']) { return $this->checkSelected(); }

		return $this->getData('multi', 'ftgetfileinfo cid='.$cid.' cpw='.$this->escapeText($cpw).' name='.$this->escapeText($file));
	}

/**
  * ftGetFileList
  *
  * Displays a list of files and directories stored in the specified channels file repository.
  *
  * <b>Output:</b>
  * <pre>
  * Array
  * {
  *  [cid] => 231
  *  [path] => /
  *  [name] => Documents
  *  [size] => 0
  *  [datetime] => 1286633633
  *  [type] => 0
  * }
  * </pre>
  *
  * @author     Stefan Zehnpfennig
  * @param		string	$cid	channelID
  * @param		string	$cpw	channelPassword (leave blank if not needed)
  * @param		string	$path	filePath
  * @return     array	fileList
  */
	function ftGetFileList($cid, $cpw = '', $path) {
		if(!$this->runtime['selected']) { return $this->checkSelected(); }
		return $this->getData('multi', 'ftgetfilelist cid='.$cid.' cpw='.$this->escapeText($cpw).' path='.$this->escapeText($path));
	}
	
/**
  * ftInitDownload
  * 
  * Initializes a file transfer download. clientftfid is an arbitrary ID to identify the file transfer on client-side. On success, the server generates a new ftkey which is required to start downloading the file through TeamSpeak 3's file transfer interface. Since version 3.0.13 there is an optional proto parameter. The client can request a protocol version with it. Currently only 0 and 1 are supported which only differ in the way they handle some timings. The server will reply which protocol version it will support. The server will reply with an ip parameter if it determines the filetransfer subsystem is not reachable by the ip that is currently being used for the query connection.
  *
  * <b>Output:</b>
  * <pre>
  * Array
  * {
  *  [clientftfid] => 89
  *  [serverftfid] => 3
  *  [ftkey] => jSzWiRmFGdZnoJzW7BSDYJRUWB2WAUhb
  *  [port] => 30033
  *  [size] => 94
  * }
  * </pre>
  *
  * @author     Stefan Zehnpfennig
  * @param		string	$name			filePath
  * @param		string	$cid			channelID
  * @param		string	$cpw			channelPassword (leave blank if not needed)
  * @param		integer	$seekpos		seekpos (default = 0) [optional]
  * @param		integer	$proto			proto (default = NULL) [optional]
  * @return     array	initDownloadFileInfo
  */	
	function ftInitDownload($name, $cid, $cpw = '', $seekpos = 0, $proto = null) {
		if(!$this->runtime['selected']) { return $this->checkSelected(); }
		return $this->getData('array', 'ftinitdownload clientftfid='.rand(1,65535).' name='.$this->escapeText($name).' cid='.$cid.' cpw='.$this->escapeText($cpw).' seekpos='.$seekpos.($proto !== null ? ' proto='.$proto: ''));
	}

/**
  * ftInitUpload
  * 
  * Initializes a file transfer upload. clientftfid is an arbitrary ID to identify the file transfer on client-side. On success, the server generates a new ftkey which is required to start uploading the file through TeamSpeak 3's file transfer interface. Since version 3.0.13 there is an optional proto parameter. The client can request a protocol version with it. Currently only 0 and 1 are supported which only differ in the way they handle some timings. The server will reply which protocol version it will support. The server will reply with an ip parameter if it determines the filetransfer subsystem is not reachable by the ip that is currently being used for the query connection
  *
  * <b>Output:</b>
  * <pre>
  * Array
  * {
  *  [clientftfid] => 84
  *  [serverftfid] => 41
  *  [ftkey] => HCnXpunOdAorqj3dGqfiuLszX18O0PHP
  *  [port] => 30033
  *  [seekpos] => 0
  * }
  * </pre>
  *
  * @author     Stefan Zehnpfennig
  * @param		string	$filename	filePath
  * @param		string	$cid		channelID
  * @param		integer	$size		fileSize in bytes
  * @param		string	$cpw		channelPassword (leave blank if not needed)
  * @param		boolean	$overwrite	overwrite	[optional] (default = 0)
  * @param		boolean	$resume		resume		[optional] (default = 0)
  * @param		integer	$proto		proto (default = NULL) [optional]
  * @return     array	initUploadFileInfo
  */	
	function ftInitUpload($filename, $cid, $size, $cpw = '', $overwrite = false, $resume = false, $proto = null) {
		if(!$this->runtime['selected']) { return $this->checkSelected(); }
		
		if($overwrite) { $overwrite = ' overwrite=1'; }else{ $overwrite = ' overwrite=0'; }
		if($resume) { $resume = ' resume=1'; }else{ $resume = ' resume=0'; }
		
		return $this->getData('array', 'ftinitupload clientftfid='.rand(1,65535).' name='.$this->escapeText($filename).' cid='.$cid.' cpw='.$this->escapeText($cpw).' size='.($size + 1).$overwrite.$resume.($proto !== null ? ' proto='.$proto: ''));
	}
	
/**
  * ftList
  * 
  * Displays a list of running file transfers on the selected virtual server. The output contains the path to which a file is uploaded to, the current transfer rate in bytes per second, etc
  *
  * <b>Output:</b>
  * <pre>
  * Array
  * {
  *  [clid] => 1
  *  [cldbid] => 2019
  *  [path] => files/virtualserver_11/channel_231
  *  [name] => 1285412348878.png
  *  [size] => 1161281
  *  [sizedone] => 275888
  *  [clientftfid] => 15
  *  [serverftfid] => 52
  *  [sender] => 0
  *  [status] => 1
  *  [current_speed] => 101037.4453
  *  [average_speed] => 101037.4453
  *  [runtime] => 2163
  * }
  * </pre>
  *
  * @author     Stefan Zehnpfennig
  * @return     array	fileTransferList
  */
	function ftList() {
		if(!$this->runtime['selected']) { return $this->checkSelected(); }
		return $this->getData('multi', 'ftlist');
	}

/**
  * ftRenameFile
  * 
  * Renames a file in a channels file repository. If the two parameters tcid and tcpw are specified, the file will be moved into another channels file repository.
  *
  * @author     Stefan Zehnpfennig
  * @param		integer	$cid		channelID
  * @param		string	$cpw		channelPassword (leave blank if not needed)
  * @param		string	$oldname	oldFilePath
  * @param		string	$newname	newFilePath
  * @param		string  $tcid		targetChannelID [optional]
  * @param		string  $tcpw		targetChannelPassword [optional]
  * @return     array success
  */
	function ftRenameFile($cid, $cpw = null, $oldname, $newname, $tcid = null,  $tcpw = null) {
		if(!$this->runtime['selected']) { return $this->checkSelected(); }
		$newTarget = ($tcid != null ? ' tcid='.$tcid.' '.$tcpw : '');
		return $this->getData('boolean', 'ftrenamefile cid='.$cid.' cpw='.$cpw.' oldname='.$this->escapeText($oldname).' newname='.$this->escapeText($newname).$newTarget);
	}

/**
  * ftStop
  * 
  * Stops the running file transfer with server-side ID serverftfid.
  *
  * @author     Stefan Zehnpfennig
  * @param		integer	$serverftfid	serverFileTransferID
  * @param		boolean	$delete			delete incomplete file [optional] (default: true) 
  * @return     array success
  */
	function ftStop($serverftfid, $delete = true) {
		if(!$this->runtime['selected']) { return $this->checkSelected(); }		
		return $this->getData('boolean', 'ftstop serverftfid='.$serverftfid.' delete='.($delete ? '1' : '0'));
	}

/**
  * ftUploadFile
  * 
  * Uploads a file to server
  * To check if upload was successful, you have to search for this file in fileList after
  *
  * @author     Stefan Zehnpfennig
  * @param		array	$data			return of ftInitUpload
  * @param		string	$uploadData		data which should be uploaded
  * @return     array response
  */
	function ftUploadFile($data, $uploadData) {
  		$this->runtime['fileSocket'] = @fsockopen($this->runtime['host'], $data['data']['port'], $errnum, $errstr, $this->runtime['timeout']);
  		if($this->runtime['fileSocket']) {
  			$this->ftSendKey($data['data']['ftkey']);
  			$this->ftSendData($uploadData);
  			@fclose($this->runtime['fileSocket']);
  			$this->runtime['fileSocket'] = '';
  			return $this->generateOutput(true, array(), true);
  		}else{
  			$this->addDebugLog('fileSocket returns '.$errnum. ' | '.$errstr);
  			return $this->generateOutput(false, array('Error in fileSocket: '.$errnum. ' | '.$errstr), false);
  		}
	}

/**
 * getIconByID
 *
 * Will return the base64 encoded binary of the icon
 * 
 * <pre>
 * $result = $tsAdmin->getIconByID($iconId);
 * You can display it like: echo '<img src="data:image/png;base64,'.$result["data"].'" />';
 * </pre>
 *
 * @author  Stefan Zehnpfennig
 * @param   string  $iconID  IconID
 * @return  array  base64 image
 */
	function getIconByID($iconID) {
	  if(!$this->runtime['selected']) { return $this->checkSelected(); }

	  if(empty($iconID) or $iconID == 0)
	  {
		return $this->generateOutput(false, array('Error: empty iconID'), false);
	  }
	  
	  if($iconID == 100 OR $iconID == 200 OR $iconID == 300 OR $iconID == 500 OR $iconID == 600)
	  {
		return $this->generateOutput(false, array('Error: you can\'t download teamspeak default icons'), false);
	  }
	  
	  if($iconID < 0)
	  {
		  $iconID = sprintf('%u', $iconID & 0xffffffff);
	  }

	  $check = $this->ftgetfileinfo(0, '', '/icon_'.$iconID);

	  if(!$check["success"])
	  {
		return $this->generateOutput(false, array('Error: icon does not exist'), false);
	  }

	  $init = $this->ftInitDownload('/icon_'.$iconID, 0, '');

	  if(!$init["success"])
	  {
		return $this->generateOutput(false, array('Error: init failed'), false);
	  }

	  $download = $this->ftDownloadFile($init);

	  if(is_array($download))
	  {
		return $download;
	  }else{
		return $this->generateOutput(true, false, base64_encode($download));
	  }
	}
	
/**
  * gm
  * 
  * Sends a text message to all clients on all virtual servers in the TeamSpeak 3 Server instance.
  *
  * @author     Stefan Zehnpfennig
  * @param		string	$msg	message
  * @return     array success
  */
	function gm($msg) {
		if(empty($msg)) {
			$this->addDebugLog('empty message given');
			return $this->generateOutput(false, array('Error: empty message given'), false);
		}
		return $this->getData('boolean', 'gm msg='.$this->escapeText($msg));
	}

/**
  * hostInfo
  * 
  * Displays detailed connection information about the server instance including uptime, number of virtual servers online, traffic information, etc.
  *
  * <b>Output:</b>
  * <pre>
  * Array
  * {
  *  [instance_uptime] => 19038
  *  [host_timestamp_utc] => 1361046825
  *  [virtualservers_running_total] => 1
  *  [virtualservers_total_maxclients] => 32
  *  [virtualservers_total_clients_online] => 1
  *  [virtualservers_total_channels_online] => 2
  *  [connection_filetransfer_bandwidth_sent] => 0
  *  [connection_filetransfer_bandwidth_received] => 0
  *  [connection_filetransfer_bytes_sent_total] => 0
  *  [connection_filetransfer_bytes_received_total] => 0
  *  [connection_packets_sent_total] => 24853
  *  [connection_bytes_sent_total] => 1096128
  *  [connection_packets_received_total] => 25404
  *  [connection_bytes_received_total] => 1153918
  *  [connection_bandwidth_sent_last_second_total] => 82
  *  [connection_bandwidth_sent_last_minute_total] => 81
  *  [connection_bandwidth_received_last_second_total] => 84
  *  [connection_bandwidth_received_last_minute_total] => 87
  * }
  * </pre>
  *
  * @author     Stefan Zehnpfennig
  * @return     array hostInformation
  */
	function hostInfo() {
		return $this->getData('array', 'hostinfo');
	}

/**
  * instanceEdit
  * 
  * Changes the server instance configuration using given properties.
  *
  * <b>Input-Array like this:</b>
  * <pre>
  * $data = array();
  *	
  * $data['setting'] = 'value';
  * $data['setting'] = 'value';
  * </pre>
  *
  * <b>Possible properties:</b> SERVERINSTANCE_GUEST_SERVERQUERY_GROUP, SERVERINSTANCE_TEMPLATE_SERVERADMIN_GROUP, SERVERINSTANCE_FILETRANSFER_PORT, SERVERINSTANCE_MAX_DOWNLOAD_TOTAL_BANDWITDH, SERVERINSTANCE_MAX_UPLOAD_TOTAL_BANDWITDH, SERVERINSTANCE_TEMPLATE_SERVERDEFAULT_GROUP, SERVERINSTANCE_TEMPLATE_CHANNELDEFAULT_GROUP, SERVERINSTANCE_TEMPLATE_CHANNELADMIN_GROUP, SERVERINSTANCE_SERVERQUERY_FLOOD_COMMANDS, SERVERINSTANCE_SERVERQUERY_FLOOD_TIME, SERVERINSTANCE_SERVERQUERY_FLOOD_BAN_TIME
  *
  * @author     Stefan Zehnpfennig
  * @param		array	$data	instanceProperties
  * @return     array success
  */
	function instanceEdit($data) {
		if(count($data) > 0) {
			$settingsString = '';
			
			foreach($data as $key => $val) {
				$settingsString .= ' '.strtolower($key).'='.$this->escapeText($val);
			}
			return $this->getData('boolean', 'instanceedit '.$settingsString);
		}else{
			$this->addDebugLog('empty array entered');
			return $this->generateOutput(false, array('Error: You can \'t give an empty array'), false);
		}
	}

/**
  * instanceInfo
  * 
  * Displays the server instance configuration including database revision number, the file transfer port, default group IDs, etc.
  *
  * <b>Output:</b>
  * <pre>
  * Array
  * {
  *  [serverinstance_database_version] => 20
  *  [serverinstance_filetransfer_port] => 30033
  *  [serverinstance_max_download_total_bandwidth] => 18446744073709551615
  *  [serverinstance_max_upload_total_bandwidth] => 18446744073709551615
  *  [serverinstance_guest_serverquery_group] => 1
  *  [serverinstance_serverquery_flood_commands] => 10
  *  [serverinstance_serverquery_flood_time] => 3
  *  [serverinstance_serverquery_ban_time] => 600
  *  [serverinstance_template_serveradmin_group] => 3
  *  [serverinstance_template_serverdefault_group] => 5
  *  [serverinstance_template_channeladmin_group] => 1
  *  [serverinstance_template_channeldefault_group] => 4
  *  [serverinstance_permissions_version] => 15
  * }
  * </pre>
  *
  * @author     Stefan Zehnpfennig
  * @return     array instanceInformation
  */
	function instanceInfo() {
		return $this->getData('array', 'instanceinfo');
	}

/**
  * logAdd
  * 
  * Writes a custom entry into the servers log. Depending on your permissions, you'll be able to add entries into the server instance log and/or your virtual servers log. The loglevel parameter specifies the type of the entry.
  *
  * <b>Hint:</b> Log level 1: "Error", Log Level 2: "Warning", Log Level 3: "Debug", Log level 4: "Info"
  *
  * @author     Stefan Zehnpfennig
  * @param		integer	$logLevel	loglevel between 1 and 4
  * @param		string	$logMsg		logMessage
  * @return     array success
  */
	function logAdd($logLevel, $logMsg) {
		if($logLevel >=1 and $logLevel <= 4) { 
			if(!empty($logMsg)) {
				return $this->getData('boolean', 'logadd loglevel='.$logLevel.' logmsg='.$this->escapeText($logMsg));
			}else{
				$this->addDebugLog('logMessage empty!');
				return $this->generateOutput(false, array('Error: logMessage empty!'), false);
			}
		}else{
			$this->addDebugLog('invalid logLevel!');
			return $this->generateOutput(false, array('Error: invalid logLevel!'), false);
		}
	}

/**
  * login
  * 
  * Authenticates with the TeamSpeak 3 Server instance using given ServerQuery login credentials.
  *
  * @author     Stefan Zehnpfennig
  * @param		string	$username	username
  * @param		string	$password	password
  * @return     array success
  */
	function login($username, $password) {
		return $this->getData('boolean', 'login '.$this->escapeText($username).' '.$this->escapeText($password));
	}

/**
  * logout
  * 
  * Deselects the active virtual server and logs out from the server instance.
  *
  * @author     Stefan Zehnpfennig
  * @return     array success
  */
	function logout() {
		$this->runtime['selected'] = false;
		return $this->getData('boolean', 'logout');
	}

/**
  * logView
  * 
  * Displays a specified number of entries from the servers log. If instance is set to 1, the server will return lines from the master logfile (ts3server_0.log) instead of the selected virtual server logfile.
  * 
  * <b>Output:</b>
  * <pre>
  * Array
  * {
  *  [last_pos] => 0
  *  [file_size] => 1085
  *  [l] => 2012-01-10 20:34:31.379260|INFO    |ServerLibPriv |   | TeamSpeak 3 Server 3.0.1 (2011-11-17 07:34:30)
  * }
  * {
  *  [l] => 2012-01-10 20:34:31.380260|INFO    |DatabaseQuery |   | dbPlugin name:    SQLite3 plugin, Version 2, (c)TeamSpeak Systems GmbH
  * }
  * {
  *  [l] => 2012-01-10 20:34:31.380260|INFO    |DatabaseQuery |   | dbPlugin version: 3.7.3
  * }
  * </pre>
  *
  * @author     Stefan Zehnpfennig
  * @param		integer	$lines	between 1 and 100
  * @param		integer	$reverse	{1|0} [optional]
  * @param		integer	$instance	{1|0} [optional]
  * @param		integer	$begin_pos	{1|0} [optional]
  * @return     array logEntries
  */
	function logView($lines, $reverse = 0, $instance = 0, $begin_pos = 0) {		
		if($lines >=1 and $lines <=100) {
			return $this->getData('multi', 'logview lines='.$lines.' reverse='.($reverse == 0 ? '0' : '1').' instance='.($instance == 0 ? '0' : '1').' begin_pos='.($begin_pos == 0 ? '0' : $begin_pos));
		}else{
			$this->addDebugLog('please choose a limit between 1 and 100');
			$this->generateOutput(false, array('Error: please choose a limit between 1 and 100'), false);
		}
	}

/**
  * messageAdd
  * 
  * Sends an offline message to the client specified by cluid.
  *
  * @author     Stefan Zehnpfennig
  * @param		string	$cluid		clientUID
  * @param		string	$subject	Subject of the message
  * @param		string	$message	Text of the message
  * @return     array success
  */
	function messageAdd($cluid, $subject, $message) {		
        if(!$this->runtime['selected']) { return $this->checkSelected(); }
        return $this->getData('boolean', 'messageadd cluid='.$cluid.' subject='.$this->escapeText($subject).' message='.$this->escapeText($message)); 
	}

/**
  * messageDelete
  * 
  * Deletes an existing offline message with ID msgid from your inbox.
  *
  * @author     Stefan Zehnpfennig
  * @param		string	$messageID		messageID
  * @return     array success
  */
	function messageDelete($messageID) {		
        if(!$this->runtime['selected']) { return $this->checkSelected(); }
        return $this->getData('boolean', 'messagedel msgid='.$messageID); 
	}

/**
  * messageGet
  * 
  * Displays an existing offline message with ID msgid from your inbox. Please note that this does not automatically set the flag_read property of the message.
  *
  * @author     Stefan Zehnpfennig
  * @param		string	$messageID		messageID
  * @return     array messageInformation
  */
	function messageGet($messageID) {		
        if(!$this->runtime['selected']) { return $this->checkSelected(); }
        return $this->getData('array', 'messageget msgid='.$messageID); 
	}

/**
  * messageList
  * 
  * Displays a list of offline messages you've received. The output contains the senders unique identifier, the messages subject, etc.
  *
  * @author     Stefan Zehnpfennig
  * @return     array messageInformation
  */
	function messageList() {		
        if(!$this->runtime['selected']) { return $this->checkSelected(); }
        return $this->getData('array', 'messagelist'); 
	}

/**
  * messageUpdateFlag
  * 
  * Updates the flag_read property of the offline message specified with msgid. If flag is set to 1, the message will be marked as read.
  *
  * @author     Stefan Zehnpfennig
  * @param		string	$messageID		messageID
  * @param		integer	$flag			flag {1|0}
  * @return     array messageInformation
  */
	function messageUpdateFlag($messageID, $flag = 1) {		
        if(!$this->runtime['selected']) { return $this->checkSelected(); }
        return $this->getData('boolean', 'messageupdateflag msgid='.$messageID.' flag='.$flag); 
	}

/**
 * uploadIcon
 *
 * Uploads an icon to the server
 * 
 * <b>Output:</b>
 * <pre>
 * Array
 * {
 *  [cid] => 0
 *  [name] => /icon_1947482249
 *  [size] => 744
 *  [datetime] => 1286633633
 * }
 * </pre>
 *
 * @author  Stefan Zehnpfennig
 * @param   string  $filepath	Path to your file
 * @param   string|integer  $iconID  	[optional] Desired IconID (Must be higher than 1.000.000.000, and lower than 2.147.483.647)
 * @return  array	ftGetFileInfo
 */
	function uploadIcon($filepath, $iconID = -1) {
		if(!$this->runtime['selected']) { return $this->checkSelected(); }
		
		if($iconID == -1)
		{
			$iconID = mt_rand(1000000000, 2147483647);
		}
		
		if($iconID <= 1000000000 and $iconID <= 2147483647)
		{
			return $this->generateOutput(false, array('Error: Must be higher than 1.000.000.000, and lower than 2.147.483.647'), false);
		}
		
		if(!file_exists($filepath))
		{
			return $this->generateOutput(false, array('Error: file does not exist'), false);
		}
		
		$data = null;
		
		try {
			$data = file_get_contents($filepath);
		} catch (Exception $e) {
			return $this->generateOutput(false, array('Error: can\'t read file - '.$e->getMessage()), false);
		}
		
		$size = filesize($filepath);
		
		$init = $this->ftInitUpload('/icon_'.$iconID, 0, $size, '', true);
		
		if(!$this->succeeded($init))
		{
			return $this->generateOutput(false, $init['error'], false);
		}		

		$result = $this->ftUploadFile($init, $data);
		
		if($this->succeeded($result))
		{
			return $this->ftGetFileInfo(0, '', '/icon_'.$iconID);
			//return $this->generateOutput(true, null, array('iconid' => $iconID, 'size' => $size));
		}
		else
		{
			return $result;
		}
	}
	
/**
  * permFind
  * 
  * Displays detailed information about all assignments of the permission specified with permid. The output is similar to permoverview which includes the type and the ID of the client, channel or group associated with the permission. A permission can be specified by permid or permsid.
  *
  * <b>Output:</b>
  * <pre>
  * Array
  * {
  *  [token] => eKnFZQ9EK7G7MhtuQB6+N2B1PNZZ6OZL3ycDp2OW
  * }
  * </pre>
  *
  * @author     Stefan Zehnpfennig
  * @param		mixed	$perm	permid or permsid
  * @return     array permissionInfoList
  */
	function permFind($perm) { 
        if(!$this->runtime['selected']) { return $this->checkSelected(); }
        return $this->getData('multi', 'permfind '.(is_int($perm) || ctype_digit($perm) ? 'permid=' : 'permsid=').$perm); 
    }
	
	
/**
  * permGet
  * 
  * Displays the current value of the permission specified with permid or permsid for your own connection. This can be useful when you need to check your own privileges.
  *
  * The perm parameter can be used as permid or permsid, it will switch the mode automatically.
  *
  * <b>Output:</b>
  * <pre>
  * Array
  * {
  *		[permsid] => i_channel_create_modify_with_codec_maxquality
  *     [permid] => 96
  *     [permvalue] => 10	
  * }
  * </pre>
  *
  * @author     Stefan Zehnpfennig
  * @param		mixed	$perm	permid or permsid
  * @return     array permissionInfo
  */
	function permGet($perm) { 
        if(!$this->runtime['selected']) { return $this->checkSelected(); }
        return $this->getData('array', 'permget '.(is_int($perm) || ctype_digit($perm) ? 'permid=' : 'permsid=').$perm); 
    }	
	
/**
  * permIdGetByName
  * 
  * Displays the database ID of one or more permissions specified by permsid.
  *
  * <b>Input-Array like this:</b>
  * <pre>
  * $permissions = array();
  * $permissions[] = 'permissionName';
  * </pre>
  * <b>Output:</b>
  * <pre>
  * Array
  * {
  *  [permsid] => b_serverinstance_help_view
  *  [permid] => 4353
  * }
  * </pre>
  *
  * @author     Stefan Zehnpfennig
  * @param		array	$permsids		permNames
  * @return     array	permissionList 
  */
	function permIdGetByName($permsids) {
		$permissionArray = array();
		
		if(count($permsids) > 0) {
			foreach($permsids AS $value) {
				$permissionArray[] = 'permsid='.$value;
			}
			return $this->getData('multi', 'permidgetbyname '.$this->escapeText(implode('|', $permissionArray)));
		}else{
			$this->addDebugLog('no permissions given');
			return $this->generateOutput(false, array('Error: no permissions given'), false);
		}
		
	}


/**
  * permissionList
  * 
  * Displays a list of permissions available on the server instance including ID, name and description.
  * If the new parameter is set the permissionlist will return with the new output format.
  *
  * <b>Output: (with new parameter)</b>
  * <pre>
  * [0] => Array
  *     (
  *         [num] => 1
  *         [group_id_end] => 0
  *         [pcount] => 0
  *     )
  *
  * [1] => Array
  *     (
  *         [num] => 2
  *         [group_id_end] => 7
  *         [pcount] => 7
  *         [permissions] => Array
  *             (
  *                 [0] => Array
  *                     (
  *                         [permid] => 1
  *                         [permname] => b_serverinstance_help_view
  *                         [permdesc] => Retrieve information about ServerQuery commands
  *                         [grantpermid] => 32769
  *                     )
  *
  *                 [1] => Array
  *                     (
  *                         [permid] => 2
  *                         [permname] => b_serverinstance_version_view
  *                         [permdesc] => Retrieve global server version (including platform and build number)
  *                         [grantpermid] => 32770
  *                     )
  *
  *                 [2] => Array
  *                     (
  *                         [permid] => 3
  *                         [permname] => b_serverinstance_info_view
  *                         [permdesc] => Retrieve global server information
  *                         [grantpermid] => 32771
  *                     )
  *
  *                 [3] => Array
  *                     (
  *                         [permid] => 4
  *                         [permname] => b_serverinstance_virtualserver_list
  *                         [permdesc] => List virtual servers stored in the database
  *                         [grantpermid] => 32772
  *                     )
  *
  *                 [4] => Array
  *                     (
  *                         [permid] => 5
  *                         [permname] => b_serverinstance_binding_list
  *                         [permdesc] => List active IP bindings on multi-homed machines
  *                         [grantpermid] => 32773
  *                     )
  *
  *                [5] => Array
  *                     (
  *                         [permid] => 6
  *                         [permname] => b_serverinstance_permission_list
  *                         [permdesc] => List permissions available available on the server instance
  *                         [grantpermid] => 32774
  *                     )
  *
  *                 [6] => Array
  *                     (
  *                         [permid] => 7
  *                         [permname] => b_serverinstance_permission_find
  *                         [permdesc] => Search permission assignments by name or ID
  *                         [grantpermid] => 32775
  *                     )
  *
  *             )
  *
  *     )
  * </pre>
  *
  * @author     Stefan Zehnpfennig
  * @param		boolean		$new		[optional] add new parameter
  * @return     array permissionList
  */
	function permissionList($new = false) {
		if($new === true) {
			$groups = array();
			$permissions = array();
			
			$response = $this->getElement('data', $this->getData('multi', 'permissionlist -new'));
			
			$gc = 1;
			
			foreach($response as $field) {
				if(isset($field['group_id_end'])) {
					$groups[] = array('num' => $gc, 'group_id_end' => $field['group_id_end']);
					$gc++;
				}else{
					$permissions[] = $field;
				}
			}
			
			$counter = 0;
			
			for($i = 0; $i < count($groups); $i++) {
				$rounds = $groups[$i]['group_id_end'] - $counter;
				$groups[$i]['pcount'] = $rounds;
				for($j = 0; $j < $rounds; $j++) {
					$groups[$i]['permissions'][] = array('permid' => ($counter + 1), 'permname' => $permissions[$counter]['permname'], 'permdesc' => $permissions[$counter]['permdesc'], 'grantpermid' => ($counter + 32769));
					$counter++;
				}
			}
			
			return $groups;
			
		}else{
			return $this->getData('multi', 'permissionlist');
		}
	}

/**
  * permOverview
  * 
  * Displays all permissions assigned to a client for the channel specified with cid. If permid is set to 0, all permissions will be displayed. A permission can be specified by permid or permsid.
  *
  * If you set the permsid parameter, the permid parameter will be ignored.
  *
  * <b>Output:</b>
  * <pre>
  * Array
  * {
  *  [t] => 0
  *  [id1] => 2
  *  [id2] => 0
  *  [p] => 16777
  *  [v] => 1
  *  [n] => 0
  *  [s] => 0
  * }
  * </pre>
  *
  * @author     Stefan Zehnpfennig
  * @param		integer		$cid		cchannelId
  * @param		integer 	$cldbid		clientDbId
  * @param		integer 	$permid		permId (Default: 0)
  * @param		string	 	$permsid	permName
  * @return     array permOverview
  */
	function permOverview($cid, $cldbid, $permid='0', $permsid=false ) { 
        if(!$this->runtime['selected']) { return $this->checkSelected(); } 
        if($permsid) { $additional = ' permsid='.$permsid; }else{ $additional = ''; } 
         
        return $this->getData('multi', 'permoverview cid='.$cid.' cldbid='.$cldbid.($permsid == false ? ' permid='.$permid : '').$additional); 
    }

/**
  * permReset
  * 
  * Restores the default permission settings on the selected virtual server and creates a new initial administrator token. Please note that in case of an error during the permreset call - e.g. when the database has been modified or corrupted - the virtual server will be deleted from the database.
  *
  * <b>Output:</b>
  * <pre>
  * Array
  * {
  *  [token] => eKnFZQ9EK7G7MhtuQB6+N2B1PNZZ6OZL3ycDp2OW
  * }
  * </pre>
  *
  * @author     Stefan Zehnpfennig
  * @return     array token
  */
	function permReset() { 
        if(!$this->runtime['selected']) { return $this->checkSelected(); } 
        return $this->getData('array', 'permreset'); 
    }
	
/**
  * privilegekeyAdd
  * 
  * Create a new token. If tokentype is set to 0, the ID specified with tokenid1 will be a server group ID. Otherwise, tokenid1 is used as a channel group ID and you need to provide a valid channel ID using tokenid2. The tokencustomset parameter allows you to specify a set of custom client properties. This feature can be used when generating tokens to combine a website account database with a TeamSpeak user. The syntax of the value needs to be escaped using the ServerQuery escape patterns and has to follow the general syntax of:
  * ident=ident1 value=value1|ident=ident2 value=value2|ident=ident3 value=value3
  *
  * <b>Input-Array like this:</b>
  * <pre>
  * $customFieldSet = array();
  *	
  * $customFieldSet['ident'] = 'value';
  * $customFieldSet['ident'] = 'value';
  * </pre>
  *
  * @author     Stefan Zehnpfennig
  * @param		integer	$tokentype				token type
  * @param		integer	$tokenid1				groupID
  * @param		integer	$tokenid2				channelID
  * @param		string	$description			token description [optional]
  * @param		array	$customFieldSet			customFieldSet [optional]
  * @return     array	tokenInformation
  */
	function privilegekeyAdd($tokentype, $tokenid1, $tokenid2, $description ='', $customFieldSet = array()) {
		return $this->tokenAdd($tokentype, $tokenid1, $tokenid2, $description, $customFieldSet);
	}

/**
  * privilegekeyDelete
  * 
  * Deletes an existing token matching the token key specified with token.
  *
  * @author     Stefan Zehnpfennig
  * @param		string	$token	token
  * @return     array success
  */
	function privilegekeyDelete($token) {
		return $this->tokenDelete($token);
	}

/**
  * privilegekeyList
  * 
  * Displays a list of privilege keys available including their type and group IDs. Tokens can be used to gain access to specified server or channel groups. A privilege key is similar to a client with administrator privileges that adds you to a certain permission group, but without the necessity of a such a client with administrator privileges to actually exist. It is a long (random looking) string that can be used as a ticket into a specific server group.
  *
  * <b>Output:</b>
  * <pre>
  * Array
  * {
  *  [token] => GdqedxSEDle3e9+LtR3o9dO09bURH+vymvF5hOJg
  *  [token_type] => 0
  *  [token_id1] => 71
  *  [token_id2] => 0
  *  [token_created] => 1286625908
  *  [token_description] => for you
  * }
  * </pre>
  *
  * @author     Stefan Zehnpfennig
  * @return     array tokenListist 
  */
	function privilegekeyList() {
		return $this->tokenList();
	}

/**
  * privilegekeyUse
  * 
  * Use a token key gain access to a server or channel group. Please note that the server will automatically delete the token after it has been used.
  *
  * @author     Stefan Zehnpfennig
  * @param		string	$token	token
  * @return     array success
  */
	function privilegekeyUse($token) {		
		return $this->tokenUse($token);
	}
 
/**
  * quit closes the connection to host 
  *
  * @author     Stefan Zehnpfennig
  * @return 	void
  */
	private function quit() {
		$this->logout();
		@fputs($this->runtime['socket'], "quit\n");
		@fclose($this->runtime['socket']);
	}
/**
  * loadQueryData
  * 
  * Loads the query current nickname and current clid
  *
  * @author     toxiicdev 
  */
	private function loadQueryData()
	{
		$whoAmI = $this->getElement('data', $this->whoAmI());
		$this->runtime['bot_name'] = $whoAmI['client_nickname'];
		
		$clients = $this->clientList();
		foreach($clients['data'] as $client)
		{
			if(strstr($this->runtime['bot_name'], $client['client_nickname']))
			{
				$this->runtime['bot_clid'] = $client['clid'];
				break;
			}
		}
	}
	
/**
  * selectServer
  * 
  * Selects the virtual server specified with sid or port to allow further interaction. The ServerQuery client will appear on the virtual server and acts like a real TeamSpeak 3 Client, except it's unable to send or receive voice data. If your database contains multiple virtual servers using the same UDP port, use will select a random virtual server using the specified port.
  *
  * @author     Stefan Zehnpfennig
  * @param		integer	$value		Port or ID
  * @param		string	$type		value type ('port', 'serverId') (default='port')
  * @param		boolean	$virtual	set true to add -virtual param [optional]
  * @param		string 	$name		set the name of the client before entry (has to be more than 3 characters) [optional]
  * @return     array success
  */
	function selectServer($value, $type = 'port', $virtual = false, $name = null) { 
		if(strlen($name) < 3 ){ $name = ''; }else{ $name = " client_nickname=".$this->escapeText($name).""; }
        if(in_array($type, array('port', 'serverId'))) { 
            if($type == 'port') { 
                if($virtual) { $virtual = ' -virtual'; }else{ $virtual = ''; } 
                $res = $this->getData('boolean', 'use port='.$value.$virtual.$name); 
                if($res['success']) { 
                   	$this->runtime['selected'] = true; 
			$this->loadQueryData();
                } 
                return $res; 
            }else{ 
                if($virtual) { $virtual = ' -virtual'; }else{ $virtual = ''; } 
                $res = $this->getData('boolean', 'use sid='.$value.$virtual.$name); 
                if($res['success']) { 
                    	$this->runtime['selected'] = true; 
			$this->loadQueryData();
                } 
                return $res; 
            } 
        }else{ 
            $this->addDebugLog('wrong value type'); 
            return $this->generateOutput(false, array('Error: wrong value type'), false); 
        } 
    }

/**
  * sendMessage
  * 
  * Sends a text message a specified target. The type of the target is determined by targetmode while target specifies the ID of the recipient, whether it be a virtual server, a channel or a client.
  * <b>Hint:</b> You can just write to the channel the query client is in. See link in description for details.
  *
  * <b>Modes:</b>
  * <ul>
  * 	<li><b>1:</b> send to client</li>
  * 	<li><b>2:</b> send to channel</li>
  * 	<li><b>3:</b> send to server</li>
  * </ul>
  * <b>Targets:</b>
  * <ul>
  * 	<li>clientID</li>
  * 	<li>channelID</li>
  * 	<li>serverID</li>
  * </ul>
  *
  * @author     Stefan Zehnpfennig
  * @param		integer $mode
  * @param		integer $target
  * @param		string	$msg	Message
  * @see		http://forum.teamspeak.com/showthread.php/84280-Sendtextmessage-by-query-client http://forum.teamspeak.com/showthread.php/84280-Sendtextmessage-by-query-client
  * @return     array	success
  */
	function sendMessage($mode, $target, $msg) {
		if(!$this->runtime['selected']) { return $this->checkSelected(); }
		return $this->getData('boolean', 'sendtextmessage targetmode='.$mode.' target='.$target.' msg='.$this->escapeText($msg));
	}

/**
  * serverCreate
  * 
  * Creates a new virtual server using the given properties and displays its ID, port and initial administrator privilege key. If virtualserver_port is not specified, the server will test for the first unused UDP port. The first virtual server will be running on UDP port 9987 by default. Subsequently started virtual servers will be running on increasing UDP port numbers.
  * 
  * <b>Input-Array like this:</b>
  * <pre>
  * $data = array();
  *	
  * $data['setting'] = 'value';
  * $data['setting'] = 'value';
  * </pre>
  *
  * <b>Output:</b>
  * <pre>
  * Array
  * {
  *  [sid] => 2
  *  [virtualserver_port] => 9988
  *  [token] => eKnFZQ9EK7G7MhtuQB6+N2B1PNZZ6OZL3ycDp2OW
  * }
  * </pre>
  *
  * <b>Possible properties:</b> VIRTUALSERVER_NAME, VIRTUALSERVER_WELCOMEMESSAGE, VIRTUALSERVER_MAXCLIENTS, VIRTUALSERVER_PASSWORD, VIRTUALSERVER_HOSTMESSAGE, VIRTUALSERVER_HOSTMESSAGE_MODE, VIRTUALSERVER_DEFAULT_SERVER_GROUP, VIRTUALSERVER_DEFAULT_CHANNEL_GROUP, VIRTUALSERVER_DEFAULT_CHANNEL_ADMIN_GROUP, VIRTUALSERVER_MAX_DOWNLOAD_TOTAL_BANDWIDTH, VIRTUALSERVER_MAX_UPLOAD_TOTAL_BANDWIDTH, VIRTUALSERVER_HOSTBANNER_URL, VIRTUALSERVER_HOSTBANNER_GFX_URL, VIRTUALSERVER_HOSTBANNER_GFX_INTERVAL, VIRTUALSERVER_COMPLAIN_AUTOBAN_COUNT, VIRTUALSERVER_COMPLAIN_AUTOBAN_TIME, VIRTUALSERVER_COMPLAIN_REMOVE_TIME, VIRTUALSERVER_MIN_CLIENTS_IN_CHANNEL_BEFORE_FORCED_SILENCE, VIRTUALSERVER_PRIORITY_SPEAKER_DIMM_MODIFICATOR, VIRTUALSERVER_ANTIFLOOD_POINTS_TICK_REDUCE, VIRTUALSERVER_ANTIFLOOD_POINTS_NEEDED_COMMAND_BLOCK, VIRTUALSERVER_ANTIFLOOD_POINTS_NEEDED_IP_BLOCK, VIRTUALSERVER_HOSTBANNER_MODE, VIRTUALSERVER_HOSTBUTTON_TOOLTIP, VIRTUALSERVER_HOSTBUTTON_GFX_URL, VIRTUALSERVER_HOSTBUTTON_URL, VIRTUALSERVER_DOWNLOAD_QUOTA, VIRTUALSERVER_UPLOAD_QUOTA, VIRTUALSERVER_MACHINE_ID, VIRTUALSERVER_PORT, VIRTUALSERVER_AUTOSTART, VIRTUALSERVER_STATUS, VIRTUALSERVER_LOG_CLIENT, VIRTUALSERVER_LOG_QUERY, VIRTUALSERVER_LOG_CHANNEL, VIRTUALSERVER_LOG_PERMISSIONS, VIRTUALSERVER_LOG_SERVER, VIRTUALSERVER_LOG_FILETRANSFER, VIRTUALSERVER_MIN_CLIENT_VERSION, VIRTUALSERVER_MIN_ANDROID_VERSION, VIRTUALSERVER_MIN_IOS_VERSION, VIRTUALSERVER_MIN_WINPHONE_VERSION, VIRTUALSERVER_NEEDED_IDENTITY_SECURITY_LEVEL, VIRTUALSERVER_NAME_PHONETIC, VIRTUALSERVER_ICON_ID, VIRTUALSERVER_RESERVED_SLOTS, VIRTUALSERVER_WEBLIST_ENABLED, VIRTUALSERVER_CODEC_ENCRYPTION_MODE
  *
  * @author     Stefan Zehnpfennig
  * @param		array	$data	serverSettings	[optional]
  * @return     array serverInfo
  */
	function serverCreate($data = array()) {
		$settingsString = '';
		
		if(count($data) == 0) {	$data['virtualserver_name'] = 'Teamspeak 3 Server'; }
		
		
		foreach($data as $key => $value) {
			if(!empty($value)) { $settingsString .= ' '.strtolower($key).'='.$this->escapeText($value); }
		}
		
		return $this->getData('array', 'servercreate'.$settingsString);
	}

/**
  * serverDelete
  * 
  * Deletes the virtual server specified with sid. Please note that only virtual servers in stopped state can be deleted.
  *
  * @author     Stefan Zehnpfennig
  * @param		integer	$sid	serverID
  * @return     array success
  */
	function serverDelete($sid) {
		$this->serverStop($sid);
		return $this->getdata('boolean', 'serverdelete sid='.$sid);
	}

/**
  * serverEdit
  * 
  * Changes the selected virtual servers configuration using given properties. Note that this command accepts multiple properties which means that you're able to change all settings of the selected virtual server at once.
  *
  * <b>Input-Array like this:</b>
  * <pre>
  * $data = array();
  *	
  * $data['setting'] = 'value';
  * $data['setting'] = 'value';
  * </pre>
  *
  * <b>Possible properties:</b> Take a look at serverCreate function
  *
  * @author     Stefan Zehnpfennig
  * @param		array	$data	serverSettings
  * @return     array success
  */
	function serverEdit($data) {
		if(!$this->runtime['selected']) { return $this->checkSelected(); }
		
		$settingsString = '';
		
		foreach($data as $key => $value) {
			$settingsString .= ' '.strtolower($key).'='.$this->escapeText($value);
		}
		
		return $this->getData('boolean', 'serveredit'.$settingsString);
	}

/**
  * serverGroupAdd
  * 
  * Creates a new server group using the name specified with name and displays its ID. The optional type parameter can be used to create ServerQuery groups and template groups. For detailed information, see
  *
  * <b>Output:</b>
  * <pre>
  * Array
  * {
  *  [sgid] => 86
  * }
  * </pre>
  *
  * @author     Stefan Zehnpfennig
  * @param		integer $name	groupName
  * @param		integer	$type	groupDbType (0 = template, 1 = normal, 2 = query | Default: 1)
  * @return     array groupId
  */
	function serverGroupAdd($name, $type = 1) {
		if(!$this->runtime['selected']) { return $this->checkSelected(); }
		return $this->getData('array', 'servergroupadd name='.$this->escapeText($name).' type='.$type);
	}

/**
  * serverGroupAddClient
  * 
  * Adds a client to the server group specified with sgid. Please note that a client cannot be added to default groups or template groups.
  *
  * @author     Stefan Zehnpfennig
  * @param		integer $sgid	serverGroupId
  * @param		integer $cldbid	clientDBID
  * @return     array success
  */
	function serverGroupAddClient($sgid, $cldbid) {
		if(!$this->runtime['selected']) { return $this->checkSelected(); }
		return $this->getData('boolean', 'servergroupaddclient sgid='.$sgid.' cldbid='.$cldbid);
	}

/**
  * serverGroupAddPerm
  * 
  * Adds a set of specified permissions to the server group specified with sgid. Multiple permissions can be added by providing the four parameters of each permission. A permission can be specified by permid or permsid.
  *
  * <b>Input-Array like this:</b>
  * <pre>
  * $permissions = array();
  * $permissions['permissionID'] = array('permissionValue', 'permskip', 'permnegated');
  * //or you could use
  * $permissions['permissionName'] = array('permissionValue', 'permskip', 'permnegated');
  * </pre>
  * 
  * @author     Stefan Zehnpfennig
  * @param		integer $sgid	serverGroupID
  * @param		array	$permissions	permissions
  * @return     array success
  */
	function serverGroupAddPerm($sgid, $permissions) {
		if(!$this->runtime['selected']) { return $this->checkSelected(); }
		
		if(count($permissions) > 0) {
			//Permissions given
				
			//Errorcollector
			$errors = array();
				
			//Split Permissions to prevent query from overload
			$permissions = array_chunk($permissions, 50, true);
				
			//Action for each splitted part of permission
			foreach($permissions as $permission_part)
			{
				//Create command_string for each command that we could use implode later
				$command_string = array();
		
				foreach($permission_part as $key => $value)
				{
					$command_string[] = (is_numeric($key) ? "permid=" : "permsid=").$this->escapeText($key).' permvalue='.$value[0].' permskip='.$value[1].' permnegated='.$value[2];
				}
		
				$result = $this->getData('boolean', 'servergroupaddperm sgid='.$sgid.' '.implode('|', $command_string));
		
				if(!$result['success'])
				{
					foreach($result['errors'] as $error)
					{
						$errors[] = $error;
					}
				}
			}
				
			if(count($errors) == 0)
			{
				return $this->generateOutput(true, array(), true);
			}else{
				return $this->generateOutput(false, $errors, false);
			}
				
		}else{
			// No permissions given
			$this->addDebugLog('no permissions given');
			return $this->generateOutput(false, array('Error: no permissions given'), false);
		}
	}

/**
  * serverGroupAutoAddPerm
  * 
  * Adds a set of specified permissions to *ALL* regular server groups on all virtual servers. The target groups will be identified by the value of their i_group_auto_update_type permission specified with sgtype. Multiple permissions can be added at once. A permission can be specified by permid or permsid. The known values for sgtype are: 10: Channel Guest 15: Server Guest 20: Query Guest 25: Channel Voice 30: Server Normal 35: Channel Operator 40: Channel Admin 45: Server Admin 50: Query Admin
  *
  * <b>Input-Array like this:</b>
  * <pre>
  * $permissions = array();
  * $permissions['permissionID'] = array('permissionValue', 'permskip', 'permnegated');
  * //or you could use
  * $permissions['permissionName'] = array('permissionValue', 'permskip', 'permnegated');
  * </pre>
  * 
  * @author     Stefan Zehnpfennig
  * @param		integer	$sgtype			serverGroupType
  * @param		array	$permissions	permissions
  * @return     array success
  */
	function serverGroupAutoAddPerm($sgtype, $permissions) {
		if(!$this->runtime['selected']) { return $this->checkSelected(); }
		
		if(count($permissions) > 0) {
			//Permissions given
				
			//Errorcollector
			$errors = array();
				
			//Split Permissions to prevent query from overload
			$permissions = array_chunk($permissions, 50, true);
				
			//Action for each splitted part of permission
			foreach($permissions as $permission_part)
			{
				//Create command_string for each command that we could use implode later
				$command_string = array();
		
				foreach($permission_part as $key => $value)
				{
					$command_string[] = (is_numeric($key) ? "permid=" : "permsid=").$this->escapeText($key).' permvalue='.$value[0].' permskip='.$value[1].' permnegated='.$value[2];
				}
		
				$result = $this->getData('boolean', 'servergroupautoaddperm sgtype='.$sgtype.' '.implode('|', $command_string));
		
				if(!$result['success'])
				{
					foreach($result['errors'] as $error)
					{
						$errors[] = $error;
					}
				}
			}
				
			if(count($errors) == 0)
			{
				return $this->generateOutput(true, array(), true);
			}else{
				return $this->generateOutput(false, $errors, false);
			}
				
		}else{
			// No permissions given
			$this->addDebugLog('no permissions given');
			return $this->generateOutput(false, array('Error: no permissions given'), false);
		}
	}

/**
  * serverGroupAutoDeletePerm
  * 
  * Removes a set of specified permissions from *ALL* regular server groups on all virtual servers. The target groups will be identified by the value of their i_group_auto_update_type permission specified with sgtype. Multiple permissions can be removed at once. A permission can be specified by permid or permsid. The known values for sgtype are: 10: Channel Guest 15: Server Guest 20: Query Guest 25: Channel Voice 30: Server Normal 35: Channel Operator 40: Channel Admin 45: Server Admin 50: Query Admin
  *
  * <b>Input-Array like this:</b>
  * <pre>
  * $permissions = array();
  * $permissions[] = 'permissionID';
  * //or you could use
  * $permissions[] = 'permissionName';
  * </pre>
  *
  * @author     Stefan Zehnpfennig
  * @param		integer		$sgtype				serverGroupType
  * @param		array		$permissions		permissions
  * @return     array success
  */
	function serverGroupAutoDeletePerm($sgtype, $permissions) {
		if(!$this->runtime['selected']) { return $this->checkSelected(); }
		$permissionArray = array();
		
		if(count($permissions) > 0) {
			foreach($permissions AS $value) {
				$permissionArray[] = is_numeric($value) ? 'permid='.$value : 'permsid='.$this->escapeText($value);
			}
			return $this->getData('boolean', 'servergroupautodelperm sgtype='.$sgtype.' '.implode('|', $permissionArray));
		}else{
			$this->addDebugLog('no permissions given');
			return $this->generateOutput(false, array('Error: no permissions given'), false);
		}
	}

/**
  * serverGroupClientList
  * 
  * Displays the IDs of all clients currently residing in the server group specified with sgid. If you're using the optional -names option, the output will also contain the last known nickname and the unique identifier of the clients.
  *
  * <b>Possible params:</b> -names
  *
  * <b>Output: (with -names param)</b>
  * <pre>
  * Array
  * {
  *  [cldbid] => 2017
  *  [client_nickname] => Par0noid //with -names parameter
  *  [client_unique_identifier] => nUixbsq/XakrrmbqU8O30R/D8Gc=
  * }
  * </pre>
  *
  * @author     Stefan Zehnpfennig
  * @param		integer	$sgid		groupId
  * @param		boolean	$names		set true to add -names param [optional]
  * @return     array	serverGroupClientList
  */
	function serverGroupClientList($sgid, $names = false) {
		if(!$this->runtime['selected']) { return $this->checkSelected(); }
		if($names) { $names = ' -names'; }else{ $names = ''; }
		return $this->getData('multi', 'servergroupclientlist sgid='.$sgid.$names);
	}

/**
  * serverGroupCopy
  * 
  * Creates a copy of the server group specified with ssgid. If tsgid is set to 0, the server will create a new group. To overwrite an existing group, simply set tsgid to the ID of a designated target group. If a target group is set, the name parameter will be ignored.
  *
  * <b>Output:</b>
  * <pre>
  * Array
  * {
  *  [sgid] => 86
  * }
  * </pre>
  *
  * @author     Stefan Zehnpfennig
  * @param		integer	$ssgid	sourceGroupID
  * @param		integer	$tsgid	targetGroupID
  * @param		integer $name	groupName
  * @param		integer	$type	groupDbType (0 = template, 1 = normal, 2 = query | Default: 1)
  * @return     array groupId
  */
	function serverGroupCopy($ssgid, $tsgid, $name, $type = 1) {
		if(!$this->runtime['selected']) { return $this->checkSelected(); }
		return $this->getData('array', 'servergroupcopy ssgid='.$ssgid.' tsgid='.$tsgid.' name='.$this->escapeText($name).' type='.$type);
	}

/**
  * serverGroupDelete
  * 
  * Deletes the server group specified with sgid. If force is set to 1, the server group will be deleted even if there are clients within.
  *
  * @author     Stefan Zehnpfennig
  * @param		integer $sgid	serverGroupID
  * @param		integer $force 	forces deleting group (Default: 1)
  * @return     array success
  */
	function serverGroupDelete($sgid, $force = 1) {
		if(!$this->runtime['selected']) { return $this->checkSelected(); }
		return $this->getData('boolean', 'servergroupdel sgid='.$sgid.' force='.$force);
	}

/**
  * serverGroupDeleteClient
  * 
  * Removes a client specified with cldbid from the server group specified with sgid.
  *
  * @author     Stefan Zehnpfennig
  * @param		integer $sgid	groupID
  * @param		integer $cldbid	clientDBID
  * @return     array success
  */
	function serverGroupDeleteClient($sgid, $cldbid) {
		if(!$this->runtime['selected']) { return $this->checkSelected(); }
		return $this->getData('boolean', 'servergroupdelclient sgid='.$sgid.' cldbid='.$cldbid);
	}

/**
  * serverGroupDeletePerm
  * 
  * Removes a set of specified permissions from the server group specified with sgid. Multiple permissions can be removed at once. A permission can be specified by permid or permsid.
  *
  * <b>Input-Array like this:</b>
  * <pre>
  * $permissions = array();
  * $permissions[] = 'permissionID';
  * //or you could use
  * $permissions[] = 'permissionName';
  * </pre>
  *
  * @author     Stefan Zehnpfennig
  * @param		integer		$sgid				serverGroupID
  * @param		array		$permissionIds		permissionIds
  * @return     array success
  */
	function serverGroupDeletePerm($sgid, $permissionIds) {
		if(!$this->runtime['selected']) { return $this->checkSelected(); }
		$permissionArray = array();
		
		if(count($permissionIds) > 0) {
			foreach($permissionIds AS $value) {
				$permissionArray[] = is_numeric($value) ? 'permid='.$value : 'permsid='.$this->escapeText($value);
			}
			return $this->getData('boolean', 'servergroupdelperm sgid='.$sgid.' '.implode('|', $permissionArray));
		}else{
			$this->addDebugLog('no permissions given');
			return $this->generateOutput(false, array('Error: no permissions given'), false);
		}
	}
	
/**
 * serverGroupGetIconBySGID
 *
 * Will return the base64 encoded binary of the serverGroupIcon
 * 
 * <pre>
 * $result = $tsAdmin->serverGroupGetIconBySGID($serverGroupID);
 * You can display it like: echo '<img src="data:image/png;base64,'.$result["data"].'" />';
 * </pre>
 *
 * @author  Stefan Zehnpfennig
 * @param  string  $serverGroupID  serverGroupID
 * @return array  base64 image
 */
	function serverGroupGetIconBySGID($serverGroupID) {
	  if(!$this->runtime['selected']) { return $this->checkSelected(); }

	  if(empty($serverGroupID))
	  {
		return $this->generateOutput(false, array('Error: empty serverGroupID'), false);
	  }
	  
	  $serverGroupList = $this->serverGroupList();
	  
	  if(!$serverGroupList["success"])
	  {
		return $this->generateOutput(false, $serverGroupList["error"], false);
	  }
	  
	  $sgid = -1;
	  $iconID = 0;
	  
	  foreach($serverGroupList['data'] as $group)
	  {
		  if($group['sgid'] == $serverGroupID)
		  {
			  $sgid = $group['sgid'];
			  $iconID = $group['iconid'];
			  break;
		  }
	  }
	  
	  if($sgid == -1)
	  {
		return $this->generateOutput(false, array('Error: invalid serverGroupID'), false);
	  }
	  
	  if($iconID == '0')
	  {
		return $this->generateOutput(false, array('Error: serverGroup has no icon'), false);
	  }
	  
	  return $this->getIconByID($iconID);
	}
	
/**
  * serverGroupList
  * 
  * Displays a list of server groups available. Depending on your permissions, the output may also contain global ServerQuery groups and template groups.
  *
  * <b>Output:</b>
  * <pre>
  * Array
  * {
  *  [sgid] => 1
  *  [name] => Guest Server Query
  *  [type] => 2
  *  [iconid] => 0
  *  [savedb] => 0
  *  [sortid] => 0
  *  [namemode] => 0
  *  [n_modifyp] => 100
  *  [n_member_addp] => 0
  *  [n_member_removep] => 0
  * }
  * </pre>
  *
  * @author     Stefan Zehnpfennig
  * @param      integer $type groupDbType (0 = template, 1 = normal, 2 = query) By default get all groups
  * @return     array serverGroupList
  */
    function serverGroupList($type = null) {
        if(!$this->runtime['selected']) { return $this->checkSelected(); }
        $groups = $this->getData('multi', 'servergrouplist');

        if($type) {
            foreach ($groups['data'] as $key => $value) {
                if($value['type'] != $type) {
                    unset($groups['data'][$key]);
                }
            }
        }

        return $groups;
    }

/**
  * serverGroupPermList
  * 
  * Displays a list of permissions assigned to the server group specified with sgid. If the permsid option is specified, the output will contain the permission names instead of the internal IDs.
  *
  * <b>Output:</b>
  * <pre>
  * Array
  * {
  *  [permid] => 12876 (if permsid = false)
  *  [permsid] => b_client_info_view (if permsid = true)
  *  [permvalue] => 1
  *  [permnegated] => 0
  *  [permskip] => 0
  * }
  * </pre>
  *
  * @author     Stefan Zehnpfennig
  * @param		integer	$sgid		serverGroupID
  * @param		boolean	$permsid	set true to add -permsid param [optional]
  * @return     array serverGroupPermList
  */
	function serverGroupPermList($sgid, $permsid = false) {
		if(!$this->runtime['selected']) { return $this->checkSelected(); }
		if($permsid) { $additional = ' -permsid'; }else{ $additional = ''; }
		return $this->getData('multi', 'servergrouppermlist sgid='.$sgid.$additional);
	}

/**
  * serverGroupRename
  * 
  * Changes the name of the server group specified with sgid.
  *
  * @author     Stefan Zehnpfennig
  * @param		integer $sgid	serverGroupID
  * @param		integer $name	groupName
  * @return     array success
  */
	function serverGroupRename($sgid, $name) {
		if(!$this->runtime['selected']) { return $this->checkSelected(); }
		return $this->getData('boolean', 'servergrouprename sgid='.$sgid.' name='.$this->escapeText($name));
	}

/**
  * serverGroupsByClientID
  * 
  * Displays all server groups the client specified with cldbid is currently residing in.
  *
  * <b>Output:</b>
  * <pre>
  * Array
  * {
  *  [name] => Guest
  *  [sgid] => 73
  *  [cldbid] => 2
  * }
  * </pre>
  *
  * @author     Stefan Zehnpfennig
  * @param		integer	$cldbid	clientDBID
  * @return     array serverGroupsByClientId
  */
	function serverGroupsByClientID($cldbid) {
		if(!$this->runtime['selected']) { return $this->checkSelected(); }
		return $this->getData('multi', 'servergroupsbyclientid cldbid='.$cldbid);
	}

/**
  * serverIdGetByPort
  * 
  * Displays the database ID of the virtual server running on the UDP port specified by virtualserver_port.
  * 
  * <b>Output:</b>
  * <pre>
  * Array
  * {
  *  [server_id] => 1
  * }
  * </pre>
  *
  * @author     Stefan Zehnpfennig
  * @param		integer $port	serverPort
  * @return     array serverInfo
  */
	function serverIdGetByPort($port) {
		return $this->getData('array', 'serveridgetbyport virtualserver_port='.$port);
	}

/**
  * serverInfo
  * 
  * Displays detailed configuration information about the selected virtual server including unique ID, number of clients online, configuration, etc.
  *	
  * <b>Output:</b>
  * <pre>
  * Array
  * {
  *  [virtualserver_unique_identifier] => 1GvKR12fg/mY75flwN/u7pn7KIs=
  *  [virtualserver_name] => TeamSpeak ]I[ Server
  *  [virtualserver_welcomemessage] => Welcome to TeamSpeak, check [URL]www.teamspeak.com[/URL] for latest information
  *  [virtualserver_platform] => Windows
  *  [virtualserver_version] => 3.0.12.4 [Build: 1461597405]
  *  [virtualserver_maxclients] => 32
  *  [virtualserver_password] => 
  *  [virtualserver_clientsonline] => 2
  *  [virtualserver_channelsonline] => 1
  *  [virtualserver_created] => 0
  *  [virtualserver_uptime] => 6517
  *  [virtualserver_codec_encryption_mode] => 0
  *  [virtualserver_hostmessage] => 
  *  [virtualserver_hostmessage_mode] => 0
  *  [virtualserver_filebase] => files\\virtualserver_1
  *  [virtualserver_default_server_group] => 11
  *  [virtualserver_default_channel_group] => 12
  *  [virtualserver_flag_password] => 0
  *  [virtualserver_default_channel_admin_group] => 9
  *  [virtualserver_max_download_total_bandwidth] => 18446744073709551615
  *  [virtualserver_max_upload_total_bandwidth] => 18446744073709551615
  *  [virtualserver_hostbanner_url] => 
  *  [virtualserver_hostbanner_gfx_url] => 
  *  [virtualserver_hostbanner_gfx_interval] => 0
  *  [virtualserver_complain_autoban_count] => 5
  *  [virtualserver_complain_autoban_time] => 1200
  *  [virtualserver_complain_remove_time] => 3600
  *  [virtualserver_min_clients_in_channel_before_forced_silence] => 100
  *  [virtualserver_priority_speaker_dimm_modificator] => -18.0000
  *  [virtualserver_id] => 1
  *  [virtualserver_antiflood_points_tick_reduce] => 5
  *  [virtualserver_antiflood_points_needed_command_block] => 150
  *  [virtualserver_antiflood_points_needed_ip_block] => 250
  *  [virtualserver_client_connections] => 1
  *  [virtualserver_query_client_connections] => 54
  *  [virtualserver_hostbutton_tooltip] => 
  *  [virtualserver_hostbutton_url] => 
  *  [virtualserver_hostbutton_gfx_url] => 
  *  [virtualserver_queryclientsonline] => 1
  *  [virtualserver_download_quota] => 18446744073709551615
  *  [virtualserver_upload_quota] => 18446744073709551615
  *  [virtualserver_month_bytes_downloaded] => 0
  *  [virtualserver_month_bytes_uploaded] => 16045
  *  [virtualserver_total_bytes_downloaded] => 0
  *  [virtualserver_total_bytes_uploaded] => 16045
  *  [virtualserver_port] => 9987
  *  [virtualserver_autostart] => 1
  *  [virtualserver_machine_id] => 
  *  [virtualserver_needed_identity_security_level] => 8
  *  [virtualserver_log_client] => 0
  *  [virtualserver_log_query] => 0
  *  [virtualserver_log_channel] => 0
  *  [virtualserver_log_permissions] => 1
  *  [virtualserver_log_server] => 0
  *  [virtualserver_log_filetransfer] => 0
  *  [virtualserver_min_client_version] => 1445512488
  *  [virtualserver_name_phonetic] => 
  *  [virtualserver_icon_id] => 0
  *  [virtualserver_reserved_slots] => 0
  *  [virtualserver_total_packetloss_speech] => 0.0000
  *  [virtualserver_total_packetloss_keepalive] => 0.0000
  *  [virtualserver_total_packetloss_control] => 0.0000
  *  [virtualserver_total_packetloss_total] => 0.0000
  *  [virtualserver_total_ping] => 0.0000
  *  [virtualserver_ip] => 
  *  [virtualserver_weblist_enabled] => 1
  *  [virtualserver_ask_for_privilegekey] => 0
  *  [virtualserver_hostbanner_mode] => 0
  *  [virtualserver_channel_temp_delete_delay_default] => 0
  *  [virtualserver_min_android_version] => 1407159763
  *  [virtualserver_min_ios_version] => 1407159763
  *  [virtualserver_status] => online
  *  [connection_filetransfer_bandwidth_sent] => 0
  *  [connection_filetransfer_bandwidth_received] => 0
  *  [connection_filetransfer_bytes_sent_total] => 0
  *  [connection_filetransfer_bytes_received_total] => 0
  *  [connection_packets_sent_speech] => 0
  *  [connection_bytes_sent_speech] => 0
  *  [connection_packets_received_speech] => 0
  *  [connection_bytes_received_speech] => 0
  *  [connection_packets_sent_keepalive] => 12959
  *  [connection_bytes_sent_keepalive] => 531319
  *  [connection_packets_received_keepalive] => 12959
  *  [connection_bytes_received_keepalive] => 544277
  *  [connection_packets_sent_control] => 396
  *  [connection_bytes_sent_control] => 65555
  *  [connection_packets_received_control] => 397
  *  [connection_bytes_received_control] => 44930
  *  [connection_packets_sent_total] => 13355
  *  [connection_bytes_sent_total] => 596874
  *  [connection_packets_received_total] => 13356
  *  [connection_bytes_received_total] => 589207
  *  [connection_bandwidth_sent_last_second_total] => 81
  *  [connection_bandwidth_sent_last_minute_total] => 92
  *  [connection_bandwidth_received_last_second_total] => 83
  *  [connection_bandwidth_received_last_minute_total] => 88
  * }
  * </pre>
  *
  * @author     Stefan Zehnpfennig
  * @return     array serverInformation
  */
	function serverInfo() {
		if(!$this->runtime['selected']) { return $this->checkSelected(); }
		return $this->getData('array', 'serverinfo');
	}

/**
  * serverList
  * 
  * Displays a list of virtual servers including their ID, status, number of clients online, etc. If you're using the -all option, the server will list all virtual servers stored in the database. This can be useful when multiple server instances with different machine IDs are using the same database. The machine ID is used to identify the server instance a virtual server is associated with. The status of a virtual server can be either online, offline, deploy running, booting up, shutting down and virtual online. While most of them are self-explanatory, virtual online is a bit more complicated. Please note that whenever you select a virtual server which is currently stopped, it will be started in virtual mode which means you are able to change its configuration, create channels or change permissions, but no regular TeamSpeak 3 Client can connect. As soon as the last ServerQuery client deselects the virtual server, its status will be changed back to offline.
  *
  * <b>Possible params:</b> [-uid] [-short] [-all] [-onlyoffline]
  *
  * <b>Output:</b>
  * <pre>
  * Array
  * {
  *  [virtualserver_id] => 1 //displayed on -short
  *  [virtualserver_port] => 9987 //displayed on -short
  *  [virtualserver_status] => online //displayed on -short
  *  [virtualserver_clientsonline] => 2
  *  [virtualserver_queryclientsonline] => 1
  *  [virtualserver_maxclients] => 32
  *  [virtualserver_uptime] => 3045
  *  [virtualserver_name] => TeamSpeak ]I[ Server
  *  [virtualserver_autostart] => 1
  *  [virtualserver_machine_id] =>
  *  [-uid] => [virtualserver_unique_identifier] => bYrybKl/APfKq7xzpIJ1Xb6C06U= 
  * }
  * </pre>
  *
  * @author     Stefan Zehnpfennig
  * @param		string		$options		optional parameters
  * @return     array serverList
  */
	function serverList($options = NULL) {
		return $this->getData('multi', 'serverlist'.(!empty($options) ? ' '.$options : ''));
	}
	
	
/**
  * serverProcessStop
  * 
  * Stops the entire TeamSpeak 3 Server instance by shutting down the process.
  *
  * @author     Stefan Zehnpfennig
  * @param		  string  $reasonMessage	reasonMessage [optional]
  * @return     array success
  */
	function serverProcessStop($reasonMessage = null) {
		return $this->getData('boolean', 'serverprocessstop'.($reasonMessage != null && !empty($reasonMessage) ? " reasonmsg=".$this->escapeText($reasonMessage) : ""));
	}

/**
  * serverRequestConnectionInfo
  * 
  * Displays detailed connection information about the selected virtual server including uptime, traffic information, etc.
  *
  * <b>Output:</b>
  * <pre>
  * Array
  * {
  *  [connection_filetransfer_bandwidth_sent] => 0
  *  [connection_filetransfer_bandwidth_received] => 0
  *  [connection_filetransfer_bytes_sent_total] => 0
  *  [connection_filetransfer_bytes_received_total] => 0
  *  [connection_packets_sent_total] => 3333
  *  [connection_bytes_sent_total] => 149687
  *  [connection_packets_received_total] => 3333
  *  [connection_bytes_received_total] => 147653
  *  [connection_bandwidth_sent_last_second_total] => 123
  *  [connection_bandwidth_sent_last_minute_total] => 81
  *  [connection_bandwidth_received_last_second_total] => 352
  *  [connection_bandwidth_received_last_minute_total] => 87
  *  [connection_connected_time] => 3387
  *  [connection_packetloss_total] => 0.0000
  *  [connection_ping] => 0.0000
  * }
  * </pre>
  *
  * @author     Stefan Zehnpfennig
  * @return     array serverRequestConnectionInfo
  */
	function serverRequestConnectionInfo() {
		if(!$this->runtime['selected']) { return $this->checkSelected(); }
		return $this->getData('array', 'serverrequestconnectioninfo');
	}

/**
  * serverSnapshotCreate
  * 
  * Displays a snapshot of the selected virtual server containing all settings, groups and known client identities. The data from a server snapshot can be used to restore a virtual servers configuration, channels and permissions using the serversnapshotdeploy command.
  *
  * @author     Stefan Zehnpfennig
  * @return     array snapshot
  */
	function serverSnapshotCreate() {
		if(!$this->runtime['selected']) { return $this->checkSelected(); }
		return $this->getData('plain', 'serversnapshotcreate');
	}

/**
  * serverSnapshotDeploy
  * 
  * Restores the selected virtual servers configuration using the data from a previously created server snapshot. Please note that the TeamSpeak 3 Server does NOT check for necessary permissions while deploying a snapshot so the command could be abused to gain additional privileges.
  *
  * + added "-mapping" to the serversnapshotdeploy command. This optional parameters will add a mapping of the old and new channelid's in the return
  *
  * @author     Stefan Zehnpfennig
  * @param		string	$snapshot	snapshot
  * @param		bool	$mapping	mapping [optional]
  * @return     array success
  */
	function serverSnapshotDeploy($snapshot, $mapping = false) {
		if(!$this->runtime['selected']) { return $this->checkSelected(); }
		return $this->getData('boolean', 'serversnapshotdeploy '.($mapping ? '-mapping ' : '').$snapshot);
	}
	
/**
  * serverStart
  * 
  * Starts the virtual server specified with sid. Depending on your permissions, you're able to start either your own virtual server only or all virtual servers in the server instance.
  *
  * @author     Stefan Zehnpfennig
  * @param		integer $sid	serverID
  * @return     array success
  */
	function serverStart($sid) {
		return $this->getdata('boolean', 'serverstart sid='.$sid);
	}	

/**
  * serverStop
  * 
  * Stops the virtual server specified with sid. Depending on your permissions, you're able to stop either your own virtual server only or all virtual servers in the server instance.
  *
  * @author     Stefan Zehnpfennig
  * @param		integer $sid	serverID
  * @param		string  $reasonMessage	reasonMessage [optional]
  * @return     array success
  */
	function serverStop($sid, $reasonMessage = null) {
		return $this->getdata('boolean', 'serverstop sid='.$sid.($reasonMessage != null && !empty($reasonMessage) ? " reasonmsg=".$this->escapeText($reasonMessage) : ""));
	}

/**
  * serverTemppasswordAdd
  * 
  * Sets a new temporary server password specified with pw. The temporary password will be valid for the number of seconds specified with duration. The client connecting with this password will automatically join the channel specified with tcid. If tcid is set to 0, the client will join the default channel.
  *
  * @author     Stefan Zehnpfennig
  * @param		string	$pw				temporary password
  * @param		string	$duration		durations in seconds
  * @param		string	$desc			description [optional]
  * @param		integer	$tcid			cid user enters on connect (0 = Default channel) [optional]
  * @param		string	$tcpw			channelPW
  * @return     array success
  */
	function serverTempPasswordAdd($pw, $duration, $desc = 'none', $tcid = 0, $tcpw = null) {
		if(!$this->runtime['selected']) { return $this->checkSelected(); }
		return $this->getdata('boolean', 'servertemppasswordadd pw='.$this->escapeText($pw).' desc='.(!empty($desc) ? $this->escapeText($desc) : 'none').' duration='.$duration.' tcid='.$tcid.(!empty($tcpw) ? ' tcpw='.$this->escapeText($tcpw) : ''));
	}

/**
  * serverTemppasswordDel
  * 
  * Deletes the temporary server password specified with pw.
  * 
  * @author     Stefan Zehnpfennig
  * @param		string	$pw		temporary password
  * @return     array success
  */	
	function serverTempPasswordDel($pw) {
		if(!$this->runtime['selected']) { return $this->checkSelected(); }
		return $this->getdata('boolean', 'servertemppassworddel pw='.$this->escapeText($pw));
	}

/**
  * serverTemppasswordList
  * 
  * Returns a list of active temporary server passwords. The output contains the clear-text password, the nickname and unique identifier of the creating client.
  *
  * <b>Output:</b>
  * <pre>
  * Array
  * {
  *  [nickname] => serveradmin
  *  [uid] => 1
  *  [desc] => none
  *  [pw_clear] => test
  *  [start] => 1334996838
  *  [end] => 1335000438
  *  [tcid] => 0
  * }
  * </pre>
  *
  * @author     Stefan Zehnpfennig
  * @return     array	serverTemppasswordList
  */
	function serverTempPasswordList() {
		if(!$this->runtime['selected']) { return $this->checkSelected(); }
		return $this->getData('multi', 'servertemppasswordlist');
	}
	

/**
  *	setClientChannelGroup
  *
  * Sets the channel group of a client to the ID specified with cgid.
  *
  * @author     Stefan Zehnpfennig
  * @param		integer $cgid	groupID
  * @param		integer $cid	channelID
  * @param		integer $cldbid	clientDBID
  * @return     array success
  */
	function setClientChannelGroup($cgid, $cid, $cldbid) {
		if(!$this->runtime['selected']) { return $this->checkSelected(); }
		return $this->getData('boolean', 'setclientchannelgroup cgid='.$cgid.' cid='.$cid.' cldbid='.$cldbid);
	}

/**
  * setName
  * 
  * Sets your nickname in server query
  *
  * @author     Stefan Zehnpfennig
  * @param		string	$newName	new name in server query
  * @return     array success
  */
	function setName($newName) {
		return $this->getData('boolean', 'clientupdate client_nickname='.$this->escapeText($newName));
	}

/**
  * tokenAdd
  * 
  * Create a new token. If tokentype is set to 0, the ID specified with tokenid1 will be a server group ID. Otherwise, tokenid1 is used as a channel group ID and you need to provide a valid channel ID using tokenid2. The tokencustomset parameter allows you to specify a set of custom client properties. This feature can be used when generating tokens to combine a website account database with a TeamSpeak user. The syntax of the value needs to be escaped using the ServerQuery escape patterns and has to follow the general syntax of:
  * ident=ident1 value=value1|ident=ident2 value=value2|ident=ident3 value=value3
  *
  * <b>Input-Array like this:</b>
  * <pre>
  * $customFieldSet = array();
  *	
  * $customFieldSet['ident'] = 'value';
  * $customFieldSet['ident'] = 'value';
  * </pre>
  *
  * @author     Stefan Zehnpfennig
  * @param		integer	$tokentype				token type
  * @param		integer	$tokenid1				groupID
  * @param		integer	$tokenid2				channelID
  * @param		string	$description			token description [optional]
  * @param		array	$customFieldSet			customFieldSet [optional]
  * @return     array	tokenInformation
  */
	function tokenAdd($tokentype, $tokenid1, $tokenid2, $description ='', $customFieldSet = array()) {
		if(!$this->runtime['selected']) { return $this->checkSelected(); }
		
		if(!empty($description)) { $description = ' tokendescription=' . $this->escapeText($description); }

		if($tokentype == '0') { $tokenid2 = '0'; }
		
		if(count($customFieldSet)) {
			$settingsString = array();
		
			foreach($customFieldSet as $key => $value) {
				$settingsString[] = 'ident='.$this->escapeText($key).'\svalue='.$this->escapeText($value);
			}
			
			$customFieldSet = ' tokencustomset='.implode('\p', $settingsString);
		}else{
			$customFieldSet = '';
		}
		
		return $this->getData('array', 'privilegekeyadd tokentype='.$tokentype.' tokenid1='.$tokenid1.' tokenid2='.$tokenid2.$description.$customFieldSet);
	}

/**
  * tokenDelete
  * 
  * Deletes an existing token matching the token key specified with token.
  *
  * @author     Stefan Zehnpfennig
  * @param		string	$token	token
  * @return     array success
  */
	function tokenDelete($token) {
		if(!$this->runtime['selected']) { return $this->checkSelected(); }			
		return $this->getData('boolean', 'privilegekeydelete token='.$token);
	}

/**
  * tokenList
  * 
  * Displays a list of privilege keys available including their type and group IDs. Tokens can be used to gain access to specified server or channel groups. A privilege key is similar to a client with administrator privileges that adds you to a certain permission group, but without the necessity of a such a client with administrator privileges to actually exist. It is a long (random looking) string that can be used as a ticket into a specific server group.
  *
  * <b>Output:</b>
  * <pre>
  * Array
  * {
  *  [token] => GdqedxSEDle3e9+LtR3o9dO09bURH+vymvF5hOJg
  *  [token_type] => 0
  *  [token_id1] => 71
  *  [token_id2] => 0
  *  [token_created] => 1286625908
  *  [token_description] => for you
  * }
  * </pre>
  *
  * @author     Stefan Zehnpfennig
  * @return     array tokenListist 
  */
	function tokenList() {
		if(!$this->runtime['selected']) { return $this->checkSelected(); }

		return $this->getData('multi', 'privilegekeylist');
	}

/**
  * tokenUse
  * 
  * Use a token key gain access to a server or channel group. Please note that the server will automatically delete the token after it has been used.
  *
  * @author     Stefan Zehnpfennig
  * @param		string	$token	token
  * @return     array success
  */
	function tokenUse($token) {
		if(!$this->runtime['selected']) { return $this->checkSelected(); }			
		return $this->getData('boolean', 'privilegekeyuse token='.$token);
	}

/**
  * version
  * 
  * Displays the servers version information including platform and build number.
  *
  * <b>Output:</b>
  * <pre>
  * Array
  * {
  *  [version] => 3.0.6.1
  *  [build] => 1340956745
  *  [platform] => Windows
  * }
  * </pre>
  *
  * @author     Stefan Zehnpfennig
  * @return     array versionInformation
  */
	function version() {
		return $this->getData('array', 'version');
	}

/**
  * whoAmI
  * 
  * Displays information about your current ServerQuery connection including your loginname, etc.
  *
  * <b>Output:</b>
  * <pre>
  * Array
  * {
  *  [virtualserver_status] => online
  *  [virtualserver_id] => 1
  *  [virtualserver_unique_identifier] => bYrybKl/APfKq7xzpIJ1Xb6C06U=
  *  [virtualserver_port] => 9987
  *  [client_id] => 5
  *  [client_channel_id] => 1
  *  [client_nickname] => serveradmin from 127.0.0.1:15208
  *  [client_database_id] => 1
  *  [client_login_name] => serveradmin
  *  [client_unique_identifier] => serveradmin
  *  [client_origin_server_id] => 0
  * }
  * </pre>
  *
  * @author     Stefan Zehnpfennig
  * @return     array clientinformation
  */
	function whoAmI() {
		return $this->getData('array', 'whoami');
	}

//*******************************************************************************************	
//************************************ Helper Functions ************************************
//*******************************************************************************************

/**
  * checkSelected throws out 2 errors
  *
  * <b>Output:</b>
  * <pre>
  * Array
  * {
  *  [success] => false
  *  [errors] => Array 
  *  [data] => false
  * }
  * </pre>
  *
  * @author     Stefan Zehnpfennig
  * @return     array error
  */
	private function checkSelected() {
		$backtrace = debug_backtrace();
		$this->addDebugLog('you can\'t use this function if no server is selected', $backtrace[1]['function'], $backtrace[0]['line']);
		return $this->generateOutput(false, array('you can\'t use this function if no server is selected'), false);
	}

/**
  * convertSecondsToStrTime
  * 
  * Converts seconds to a strTime (bsp. 5d 1h 23m 19s)
  *
  * @author     Stefan Zehnpfennig
  * @param		integer	$seconds	time in seconds
  * @return     string strTime
  */
 	public function convertSecondsToStrTime($seconds) {
		$conv_time = $this->convertSecondsToArrayTime($seconds);
    	return $conv_time['days'].'d '.$conv_time['hours'].'h '.$conv_time['minutes'].'m '.$conv_time['seconds'].'s';
	}

/**
  * convertSecondsToArrayTime
  * 
  * Converts seconds to a array: time
  *
  * <b>Output:</b>
  * <pre>
  * Array
  * {
  *  [days] => 3
  *  [hours] => 9
  *  [minutes] => 45
  *  [seconds] => 17
  * }
  * </pre>
  *
  * @author     Stefan Zehnpfennig
  * @param		integer	$seconds	time in seconds
  * @return     array time
  */
 	public function convertSecondsToArrayTime($seconds) {
		$conv_time = array();
		$conv_time['days']=floor($seconds / 86400);
		$conv_time['hours']=floor(($seconds - ($conv_time['days'] * 86400)) / 3600);
		$conv_time['minutes']=floor(($seconds - (($conv_time['days'] * 86400)+($conv_time['hours']*3600))) / 60);
		$conv_time['seconds']=floor(($seconds - (($conv_time['days'] * 86400)+($conv_time['hours']*3600)+($conv_time['minutes'] * 60))));
		return $conv_time;
	}

/**
  * getElement
  * 
  * Returns the given associated element from an array
  * This can be used to get a result in a one line operation
  * 
  * For example you got this array:
  * <pre>
  * Array
  * {
  *  [success] => false
  *  [errors] => Array 
  *  [data] => false
  * }
  * </pre>
  * Now you can grab the element like this:
  * <pre>
  * $ts = new ts3admin('***', '***');
  * 
  * if($ts->getElement('success', $ts->connect())) {
  *  //operation
  * }
  * </pre>
  *
  * @author     Stefan Zehnpfennig
  * @param		string	$element	key of element
  * @param		array	$array		array
  * @return     mixed
  */
	public function getElement($element, $array) {
		return $array[$element];
	}

/**
  * succeeded
  * 
  * Succeeded will check the success element of a return array
  * <pre>
  * $ts = new ts3admin('***', '***');
  * 
  * if($ts->succeeded($ts->connect())) {
  *  //operation
  * }
  * </pre>
  *
  * @author     Stefan Zehnpfennig
  * @param		array	$array	result
  * @return     boolean
  */
	public function succeeded($array) {
		if(isset($array['success'])) {
			return $array['success'];
		}else{
			return false;
		}
	}
	
	

//*******************************************************************************************	
//*********************************** Internal Functions ************************************
//*******************************************************************************************

/**
 * __construct
 *
 * Note: When specifying a numerical IPv6 address (e.g. fe80::1), you must enclose the IP in square brackets—for example, [fe80::1]
 * 
 * @author	Stefan Zehnpfennig
 * @param	string	$host		ts3host
 * @param	integer	$queryport	ts3queryport
 * @param	integer	$timeout	socket timeout (default = 2) [optional]
 * @return	void
*/
	function __construct($host, $queryport, $timeout = 2) {
		if($queryport >= 1 and $queryport <= 65536) {
			if($timeout >= 1) {
				$this->runtime['host'] = $host;
				$this->runtime['queryport'] = $queryport;
				$this->runtime['timeout'] = $timeout;
			}else{
				$this->addDebugLog('invalid timeout value');
			}
		}else{
			$this->addDebugLog('invalid queryport');
		}
	}

/**
 * __destruct
 * 
 * @author	Stefan Zehnpfennig
 * @return	void
*/
	function __destruct() {
		$this->quit();
	}

/**
 * __call
 * 
 * prevents your website from php errors if you want to execute a method which doesn't exists
 * 
 * @author	Stefan Zehnpfennig
 * @param	string	$name	method name
 * @param	array	$args	method arguments
 * @return	array
*/
	function __call($name, $args) {
		$this->addDebugLog('Method '.$name.' doesn\'t exist', $name, 0);
		return $this->generateOutput(false, array('Method '.$name.' doesn\'t exist'), false);
	}

/**
  * isConnected
  * 
  * Checks if the connection is established
  *
  * @author     Stefan Zehnpfennig
  * @return     boolean connected
  */
	private function isConnected() {
		return !empty($this->runtime['socket']);
	}

/**
  * generateOutput
  * 
  * Builds a method return as array
  *
  * @author     Stefan Zehnpfennig
  * @param		boolean		$success	true/false
  * @param		array		$errors		all errors which occured while executing a method
  * @param		mixed		$data		parsed data from server
  * @return     array output
  */
	private function generateOutput($success, $errors, $data) {
		return array('success' => $success, 'errors' => $errors, 'data' => $data);
	}

/**
  * unEscapeText
  * 
  * Turns escaped chars to normals
  *
  * @author     Stefan Zehnpfennig
  * @param		string	$text	text which should be escaped
  * @return     string	text
  */
 	private function unEscapeText($text) {
 		$escapedChars = array("\t", "\v", "\r", "\n", "\f", "\s", "\p", "\/");
 		$unEscapedChars = array('', '', '', '', '', ' ', '|', '/');
		$text = str_replace($escapedChars, $unEscapedChars, $text);
		return $text;
	}

/**
  * escapeText
  * 
  * Escapes chars that we can use it in the query
  *
  * @author     Stefan Zehnpfennig
  * @param		string	$text	text which should be escaped
  * @return     string	text
  */
 	private function escapeText($text) {
 		$text = str_replace("\t", '\t', $text);
		$text = str_replace("\v", '\v', $text);
		$text = str_replace("\r", '\r', $text);
		$text = str_replace("\n", '\n', $text);
		$text = str_replace("\f", '\f', $text);
		$text = str_replace(' ', '\s', $text);
		$text = str_replace('|', '\p', $text);
		$text = str_replace('/', '\/', $text);
		return $text;
	}

/**
  * splitBanIds
  * 
  * Splits banIds to array
  *
  * @author     Stefan Zehnpfennig
  * @param		string	$text	plain text server response
  * @return     array	text
  */
 	private function splitBanIds($text) {
		$data = array();
		$text = str_replace(array("\n", "\r"), '', $text);
		$ids = explode("banid=", $text);
		unset($ids[0]);
		return $ids;
	}

//*******************************************************************************************	
//************************************ Network Functions ************************************
//*******************************************************************************************

/**
  * connect
  * 
  * Connects to a ts3instance query port
  *
  * @author     Stefan Zehnpfennig
  * @return		array success
  */
	function connect() {
		if($this->isConnected()) { 
			$this->addDebugLog('Error: you are already connected!');
			return $this->generateOutput(false, array('Error: the script is already connected!'), false);
		}
		
		$socket = @fsockopen($this->runtime['host'], $this->runtime['queryport'], $errnum, $errstr, $this->runtime['timeout']);

		if(!$socket)
		{
			$this->addDebugLog('Error: connection failed!');
			return $this->generateOutput(false, array('Error: connection failed!', 'Server returns: '.$errstr), false);
		}
		else
		{
			if(strpos(fgets($socket), 'TS3') !== false)
			{
				$tmpVar = fgets($socket);
				$this->runtime['socket'] = $socket;
				return $this->generateOutput(true, array(), true);
			}
			else
			{
				$this->addDebugLog('host isn\'t a ts3 instance!');
				return $this->generateOutput(false, array('Error: host isn\'t a ts3 instance!'), false);
			}
		}
	}

/**
  * getQueryClid
  * 
  * Returns the server query client id
  *
  * @author     toxiicdev 
  *	@return		int value
  */
  
	public function getQueryClid()
	{
		return $this->runtime['bot_clid'];
	}
	
/**
  * executeCommand
  * 
  * Executes a command and fetches the response
  *
  * @author     Stefan Zehnpfennig
  * @param		string	$command	command which should be executed
  * @param		array	$tracert	array with information from first exec
  * @return     mixed data
  */
	private function executeCommand($command, $tracert = null) {
		if(!$this->isConnected()) {
			$this->addDebugLog('script isn\'t connected to server', $tracert[1]['function'], $tracert[0]['line']);
			return $this->generateOutput(false, array('Error: script isn\'t connected to server'), false);
		}
		
		$data = '';
				
		$splittedCommand = str_split($command, 1024);
		
		$splittedCommand[(count($splittedCommand) - 1)] .= "\n";
		
		foreach($splittedCommand as $commandPart)
		{
			if(!(@fputs($this->runtime['socket'], $commandPart)))
			{
				$this->runtime['socket'] = $this->runtime['bot_clid'] = '';
				$this->addDebugLog('Socket closed.', $tracert[1]['function'], $tracert[0]['line']);
				return $this->generateOutput(false, array('Socket closed.'), false);
			}
		}
		do {
			$data .= @fgets($this->runtime['socket'], 4096);
			
			if(empty($data))
			{
				$this->runtime['socket'] = $this->runtime['bot_clid'] = '';
				$this->addDebugLog('Socket closed.', $tracert[1]['function'], $tracert[0]['line']);
				return $this->generateOutput(false, array('Socket closed.'), false);
			}
			else if(strpos($data, 'error id=3329 msg=connection') !== false) {
				$this->runtime['socket'] = $this->runtime['bot_clid'] = '';
				$this->addDebugLog('You got banned from server. Socket closed.', $tracert[1]['function'], $tracert[0]['line']);
				return $this->generateOutput(false, array('You got banned from server. Connection closed.'), false);
			}
			
		} while(strpos($data, 'msg=') === false or strpos($data, 'error id=') === false);

		if(strpos($data, 'error id=0 msg=ok') === false) {
			$splittedResponse = explode('error id=', $data);
			$chooseEnd = count($splittedResponse) - 1;
			
			$cutIdAndMsg = explode(' msg=', $splittedResponse[$chooseEnd]);
						
			if($tracert != null)
				$this->addDebugLog('ErrorID: '.$cutIdAndMsg[0].' | Message: '.$this->unEscapeText($cutIdAndMsg[1]), $tracert[1]['function'], $tracert[0]['line']);
			
			if($cutIdAndMsg[0] == 1281){ // if "database empty result set"
				return $this->generateOutput(true, array(), '');
			}else{
				return $this->generateOutput(false, array('ErrorID: '.$cutIdAndMsg[0].' | Message: '.$this->unEscapeText($cutIdAndMsg[1])), false);
			}
		}else{
			return $this->generateOutput(true, array(), $data);
		}
	}

/**
  * readChatMessage
  * 
  * Read chat message by its type (Result: http://bit.ly/2dtBXnT)
  *
  * IMPORTANT: Check always for message success, sometimes you can get an empty message 
  * and it will return empty data
  * 
  * <b>Output:</b>
  * <pre>
  * Array
  * {
  *		[invokerid] => 37
  *		[invokeruid] => /jl8QCHJWrHDKXgVtF+9FX7zg1E=
  *		[invokername] => toxiicdev.net
  *		[msg] => It's just a prank bro
  *		[targetmode] => 3
  * }
  * </pre>
  * @author	toxiicdev 
  * @param	string	$type		textserver|textchannel|textprivate
  * @param	boolean	$keepalive	default false
  * @param	int		$cid		channel id (required only for textchannel)
  * @return	array	data
  */	
	public function readChatMessage($type = 'textchannel', $keepalive = false, $cid = -1)
	{
		$availTypes = array('textserver', 'textchannel', 'textprivate');
		$rtnData = array('success' => 0, 'data' => array('invokerid' => '', 'invokeruid' => '', 'invokername' => '', 'msg' => '', 'targetmode' => ''));
		
		if(!$this->isConnected()) {
			$this->addDebugLog('script isn\'t connected to server', $tracert[1]['function'], $tracert[0]['line']);
			return $rtnData;
		}
		
		if(!in_array($type, $availTypes)) {
			$this->addDebugLog('Invalid passed read type', $tracert[1]['function'], $tracert[0]['line']);
			return $rtnData;
		}
		
		if(!$this->runtime['selected']) { return $this->checkSelected(); }
		
		$whoami = $this->whoami();
		
		if(!$whoami['success']) { return $whoami; }
		
		if($type == 'textchannel' && $whoami['data']['client_channel_id'] != $cid)
		{
			$this->clientMove($this->getQueryClid(), $cid);
		}
		
		$this->executeCommand("servernotifyregister event=$type" . ($cid != -1 ? " id=$cid" : "") , null);
		
		$data = fgets($this->runtime['socket'], 4096);
		
		if(!empty($data))
		{		
			$rtnData['success'] = 1;
			$msgData = explode(" ", $data);
			foreach($msgData as $param)
			{
				$paramData = explode("=", $param);
				if(array_key_exists($paramData[0], $rtnData['data']))
				{
					$rtnData['data'][$paramData[0]] = $this->unescapeText(implode("=", array_slice($paramData, 1, count($paramData) -1)));
				}
			}
		}
		if(!$keepalive) $this->serverNotifyUnregister();
		
		return $rtnData;
	}
/**
 * serverNotifyUnregister
 * 
 * Unregisters server notify event
 * 
 * @author		toxiicdev 
 */	
	public function serverNotifyUnregister()
	{
		$this->executeCommand("servernotifyunregister", null);	
	}
/**
 * getData
 * 
 * Parses data from query and returns an array
 * 
 * @author		Stefan Zehnpfennig
 * @access		private
 * @param		string	$mode		select return mode ('boolean', 'array', 'multi', 'plain')
 * @param		string	$command	command which should be executed
 * @return		array data
 */
	private function getData($mode, $command) {
	
		$validModes = array('boolean', 'array', 'multi', 'plain');
	
		if(!in_array($mode, $validModes)) {
			$this->addDebugLog($mode.' is an invalid mode');
			return $this->generateOutput(false, array('Error: '.$mode.' is an invalid mode'), false);
		}
		
		if(empty($command)) {
			$this->addDebugLog('you have to enter a command');
			return $this->generateOutput(false, array('Error: you have to enter a command'), false);
		}
		
		$fetchData = $this->executeCommand($command, debug_backtrace());
		
		
		$fetchData['data'] = str_replace(array('error id=0 msg=ok', chr('01')), '', $fetchData['data']);
		
		
		if($fetchData['success']) {
			if($mode == 'boolean') {
				return $this->generateOutput(true, array(), true);
			}
			
			if($mode == 'array') {
				if(empty($fetchData['data'])) { return $this->generateOutput(true, array(), array()); }
				$datasets = explode(' ', $fetchData['data']);
				
				$output = array();
				
				foreach($datasets as $dataset) {
					$dataset = explode('=', $dataset);
					
					if(count($dataset) > 2) {
						for($i = 2; $i < count($dataset); $i++) {
							$dataset[1] .= '='.$dataset[$i];
						}
						$output[$this->unEscapeText($dataset[0])] = $this->unEscapeText($dataset[1]);
					}else{
						if(count($dataset) == 1) {
							$output[$this->unEscapeText($dataset[0])] = '';
						}else{
							$output[$this->unEscapeText($dataset[0])] = $this->unEscapeText($dataset[1]);
						}
						
					}
				}
				return $this->generateOutput(true, array(), $output);
			}
			if($mode == 'multi') {
				if(empty($fetchData['data'])) { return $this->generateOutput(true, array(), array()); }
				$datasets = explode('|', $fetchData['data']);
				
				$output = array();
				
				foreach($datasets as $datablock) {
					$datablock = explode(' ', $datablock);
					
					$tmpArray = array();
					
					foreach($datablock as $dataset) {
						$dataset = explode('=', $dataset);
						if(count($dataset) > 2) {
							for($i = 2; $i < count($dataset); $i++) {
								$dataset[1] .= '='.$dataset[$i];
							}
							$tmpArray[$this->unEscapeText($dataset[0])] = $this->unEscapeText($dataset[1]);
						}else{
							if(count($dataset) == 1) {
								$tmpArray[$this->unEscapeText($dataset[0])] = '';
							}else{
								$tmpArray[$this->unEscapeText($dataset[0])] = $this->unEscapeText($dataset[1]);
							}
						}					
					}
					$output[] = $tmpArray;
				}
				return $this->generateOutput(true, array(), $output);
			}
			if($mode == 'plain') {
				return $fetchData;
			}
		}else{
			return $this->generateOutput(false, $fetchData['errors'], false);
		}
	}

/**
  * ftSendKey
  * 
  * Sends down/upload-key to ftHost
  * 
  * @author     Stefan Zehnpfennig
  * @param		string	$key
  * @param		string $additional
  * @return     void
 */
	private function ftSendKey($key, $additional = NULL) {
		@fputs($this->runtime['fileSocket'], $key.$additional);
	}

/**
  * ftSendData
  * 
  * Sends data to ftHost
  * 
  * @author     Stefan Zehnpfennig
  * @param		mixed	$data
  * @return     void
 */
	private function ftSendData($data) {
		$data = str_split($data, 4096);
		foreach($data as $dat) {
			@fputs($this->runtime['fileSocket'], $dat);
		}
	}

/**
  * ftRead
  * 
  * Reads data from ftHost
  * 
  * @author     Stefan Zehnpfennig
  * @param		int	$size
  * @return     string data
 */
	private function ftRead($size) {
		$data = '';
		while(strlen($data) < $size) {		
			$data .= fgets($this->runtime['fileSocket'], 4096);
		}
		return $data;
	}

//*******************************************************************************************	
//************************************* Debug Functions *************************************
//*******************************************************************************************

/**
  * getDebugLog
  * 
  * Returns the debug log
  *
  * <b>Output:</b>
  * <pre>
  * Array
  * {
  *  [0] => Error in login() on line 1908: ErrorID: 520 | Message: invalid loginname or password
  *  [1] => Error in selectServer() on line 2044: ErrorID: 1540 | Message: convert error
  * }
  * </pre>
  *
  * @author     Stefan Zehnpfennig
  * @return     array debugLog
  */
 	public function getDebugLog() {
		return $this->runtime['debug'];
	}

/**
  * addDebugLog
  * 
  * Adds an entry to debugLog
  *
  * @author     Stefan Zehnpfennig
  * @param		string	$text			text which should added to debugLog
  * @param		string	$methodName		name of the executed method [optional]
  * @param		string	$line			line where error triggered [optional]
  * @return     void
  */
	private function addDebugLog($text, $methodName = '', $line = '') {
		if(empty($methodName) and empty($line)) {
			$backtrace = debug_backtrace();
			$methodName = $backtrace[1]['function'];
			$line = $backtrace[0]['line'];
		}
		$this->runtime['debug'][] = 'Error in '.$methodName.'() on line '.$line.': '.$text;	
	}
}

/** \mainpage ts3admin.class
 *
 * \section intro_sec Welcome
 *
 * The ts3admin.class is a powerful api for communication with Teamspeak 3 Servers from your website! Your creativity knows no bounds!
 *
 * <a href="http://ts3admin.info" />http://ts3admin.info</a>
 */
