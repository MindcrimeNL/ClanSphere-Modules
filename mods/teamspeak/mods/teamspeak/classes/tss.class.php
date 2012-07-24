<?php

/**
 * Generic TS class which interfaces between the TS2 and TS3 class
 */
class tss
{
	const	VERSION_TS2 = 0;
	const	VERSION_TS3 = 1;

	const TCP_DEFAULT_TS2 = 51234;
	const TCP_DEFAULT_TS3 = 10011;

	const UDP_DEFAULT_TS2 = 8767;
	const UDP_DEFAULT_TS3 = 9987;

	const TYPE_NORMAL_CLIENT = '0';
	const TYPE_QUERY_CLIENT = '1';

	/* When showing flags, what mode we are in */
	const FLAG_CHANNEL_MODE_ARRAY = 0;
	const FLAG_CHANNEL_MODE_CHANNEL_IMAGE = 1;
	const FLAG_CHANNEL_MODE_CHANNEL_STATES_TEXT = 2;
	const FLAG_CHANNEL_MODE_CHANNEL_STATES_IMAGE = 3;

	const FLAG_PLAYER_MODE_GLOBAL_ARRAY = 0;
	const FLAG_PLAYER_MODE_GLOBAL_TEXT = 1;
	const FLAG_PLAYER_MODE_GLOBAL_IMAGE = 2;
	
	const FLAG_PLAYER_MODE_STATUS_ARRAY = 0;
	const FLAG_PLAYER_MODE_STATUS_TEXT = 1;
	const FLAG_PLAYER_MODE_STATUS_IMAGE = 2;

	/* Channel flags */
	const FLAG_CHANNEL_PERMANENT = 1;
	const FLAG_CHANNEL_MODERATED = 2;
	const FLAG_CHANNEL_PASSWORD = 4;
	const FLAG_CHANNEL_SUBCHANNELS = 8;
	const FLAG_CHANNEL_DEFAULT = 16;
	const FLAG_CHANNEL_SEMI_PERMANENT = 32; // TS3 only
	
	/* Player flags status */
	const FLAG_PLAYER_STATUS_CHANNEL_COMMANDER = 1;
	const FLAG_PLAYER_STATUS_VOICE_REQUEST = 2;
	const FLAG_PLAYER_STATUS_NO_WHISPERS = 4;
	const FLAG_PLAYER_STATUS_AWAY = 8;
	const FLAG_PLAYER_STATUS_MUTE_MICROPHONE = 16;
	const FLAG_PLAYER_STATUS_MUTE_SPEAKERS_HEADPHONE = 32;
	const FLAG_PLAYER_STATUS_RECORDING = 64;
	const FLAG_PLAYER_STATUS_IS_TALKER = 128; // TS3 only
	
	/* Player flags channel */
	const FLAG_PLAYER_CHANNEL_CHANNEL_ADMIN = 1;
	const FLAG_PLAYER_CHANNEL_OPERATOR = 2;
	const FLAG_PLAYER_CHANNEL_VOICE = 4;
	const FLAG_PLAYER_CHANNEL_AUTO_OPERATOR = 8;
	const FLAG_PLAYER_CHANNEL_AUTO_VOICE = 16;
	
	/* Player flags global */
	const FLAG_PLAYER_GLOBAL_SERVER_ADMIN = 1;
	const FLAG_PLAYER_GLOBAL_ALLOW_REGISTRATION = 2;
	const FLAG_PLAYER_GLOBAL_REGISTERED = 4;
	const FLAG_PLAYER_GLOBAL_INTERNAL_USE = 8;
	const FLAG_PLAYER_GLOBAL_STICKY = 16;
	
	/* enum ReasonIdentifier */
	const REASON_KICK_CHANNEL = 4; // 4: kick client from channel
	const REASON_KICK_SERVER = 5; // 5: kick client from server

	/* enum TokenType */
	const TOKEN_SERVER_GROUP = 0; // 0: server group token (id1={groupID} id2=0)
	const TOKEN_CHANNEL_GROUP = 1; // 1: channel group token (id1={groupID} id2={channelID})
	
	/* enum PermissionGroupDatabaseTypes */
	const PERM_GROUP_DB_TYPE_TEMPLATE = 0; // 0: template group (used for new virtual servers)
	const PERM_GROUP_DB_TYPE_REGULAR = 1; // 1: regular group (used for regular clients)
	const PERM_GROUP_DB_TYPE_QUERY = 2; // 2: global query group (used for ServerQuery clients)
	
	/* TS3 only */
	const DEFAULT_SERVER_GROUP_ADMIN = 6;
	const DEFAULT_SERVER_GROUP_NORMAL = 7;
	const DEFAULT_SERVER_GROUP_GUEST = 8;

	/* TS3 only */
	const DEFAULT_CHANNEL_GROUP_ADMIN = 5;
	const DEFAULT_CHANNEL_GROUP_OPERATOR = 6;
	const DEFAULT_CHANNEL_GROUP_VOICE = 7;
	const DEFAULT_CHANNEL_GROUP_GUEST = 8;
	
	private $_version = 0; // default TS2
	private $_charset = 'ISO-8859-1'; // default TS2
	private $_tssclass = null;
	private $_serverInfo = null;
	private $_channelList = null;
	private $_clientList = null;
	private $_clientDbList = null;
	private $_sgidAdmin = 6;
	private $_sgidNormal = 7;
	private $_sgidGuest = 8;
	private $_cgidAdmin = 5;
	private $_cgidOperator = 6;
	private $_cgidVoice = 7;
	private $_cgidGuest = 8;
	private	$_cs_lang = null;

	/**
	 */
	public function __construct($version = null, $charset = null)
	{
		if (!is_null($version))
			$this->version($version);
		if (!is_null($charset))
			$this->charset($charset);
		$this->_cs_lang = cs_translate('teamspeak');
	} // function __construct

	/**
	 */
	public function __destruct()
	{
		/* destruct tss class */
		unset($this->_tssclass);
	} // function __destruct

	/**
	 * Set the version of TeamSpeak we are now going to use
	 * 
	 * @param	int	$version	either VERSION_TS2 or VERSION_TS3, default VERSION_TS2.
	 */
	public function version($version = 0)
	{
		if (!is_null($this->_tssclass))
		{
			if ($this->_version != $version)
			{
				$this->logout();
				$this->disconnect();
				unset($this->_tssclass);
			}
			else
			{
				/* just reset */
				$this->reset();
				return;
			}
		}
		$this->_version = $version;
		switch ($this->_version)
		{
		default:
		case self::VERSION_TS2:
			require_once('mods/teamspeak/classes/cyts.class.php');
			$this->_tssclass = new cyts();
			break;
		case self::VERSION_TS3:
			require_once('mods/teamspeak/classes/ts3admin.0.1.5.class.php');
			$this->_tssclass = new ts3admin();
			break;
		}
	}

	/**
	 * Set the charset of TeamSpeak we are now going to use
	 * 
	 * @param	string	$charset	either 'ISO-8859-1' or 'UTF-8', default 'ISO-8859-1'.
	 */
	public function charset($charset)
	{
		$this->_charset = $charset;
	} // function charset

	/**
	 * Reset
	 */
	public function reset()
	{
		if (!is_null($this->_tssclass))
			$this->_tssclass->reset();
		$this->clear();
	} // function reset

	public function clear()
	{
		$this->_serverInfo = null;
		$this->_channelList = null;
		$this->_clientList = null;
		$this->_clientDbList = null;
		$this->_sgidAdmin = self::DEFAULT_SERVER_GROUP_ADMIN;
		$this->_sgidNormal = self::DEFAULT_SERVER_GROUP_NORMAL;
		$this->_sgidGuest = self::DEFAULT_SERVER_GROUP_GUEST;
		$this->_cgidAdmin = self::DEFAULT_CHANNEL_GROUP_ADMIN;
		$this->_cgidOperator = self::DEFAULT_CHANNEL_GROUP_OPERATOR;
		$this->_cgidVoice = self::DEFAULT_CHANNEL_GROUP_VOICE;
		$this->_cgidGuest = self::DEFAULT_CHANNEL_GROUP_GUEST;
	}
	
	/**
	 * @param string $addr IP of server
	 * @param	int		 $tcp	 TCP port (default TS2 51234 / TS3 10011)
	 * @param int		 $udp	 UDP port default null
	 * @param	int		 $timeout Timeout in seconds
	 *
	 * @return boolean
	 */
	public function connect($addr, $tcp = null, $udp = null, $timeout = 2)
	{
		global $cs_main;
		
		$addr = cs_encode($addr, $cs_main['charset'], $this->_charset);

		/* clear any temporary variables */
		$this->clear();
		switch ($this->_version)
		{
		default:
		case self::VERSION_TS2:
			if (empty($tcp) || !ctype_digit($tcp))
				$tcp = self::TCP_DEFAULT_TS2;
			if (!empty($udp) && !ctype_digit($udp))
				$udp = self::UDP_DEFAULT_TS2;
			if (empty($udp))
				$udp = false;
			return $this->_tssclass->connect($addr, $tcp, $udp, $timeout);
			break;
		case self::VERSION_TS3:
			if (empty($tcp) || !ctype_digit($tcp))
				$tcp = self::TCP_DEFAULT_TS3;
			if (!empty($udp) && !ctype_digit($udp))
				$udp = self::UDP_DEFAULT_TS3;
			if ($this->_tssclass->connect($addr, $tcp, $timeout))
				if ($this->_tssclass->selectServerByPort($udp))
				{
					/* try to determine the admin, normal and guest server groups for this virtual server */
					if ($this->initServerGroups() && $this->initChannelGroups())
						return true;
				}
			break;
		}
		return false;
	} // function connect
	
	/**
   * disconnect
   */
	public function disconnect()
	{
		/* no difference yet */
		return $this->_tssclass->disconnect();
	}

	/**
	 * Select a virtual server
	 * 
	 * @param	int	$serverId This is the UDP port 
	 */
	public function selectServer($udp)
	{
		switch ($this->_version)
		{
		default:
		case self::VERSION_TS2:
			return $this->_tssclass->select($udp);
			break;
		case self::VERSION_TS3:
			return $this->_tssclass->selectServerByPort($udp);
			break;
		}
	}
	
	/**
	 * Login to get extra privileges
	 *
	 * @return boolean
   */
	public function login($user, $pass)
	{
		global $cs_main;
		
		$user = cs_encode($user, $cs_main['charset'], $this->_charset);
		$pass = cs_encode($pass, $cs_main['charset'], $this->_charset);
		/* no difference yet */
		return $this->_tssclass->login($user, $pass);
	} // function login

	/**
	 * Login as superadmin
	 *
	 * @return boolean
   */
	public function loginSuperAdmin($user, $pass)
	{
		global $cs_main;
		
		$user = cs_encode($user, $cs_main['charset'], $this->_charset);
		$pass = cs_encode($pass, $cs_main['charset'], $this->_charset);
		switch ($this->_version)
		{
		default:
		case self::VERSION_TS2:
			return $this->_tssclass->slogin($user, $pass);
			break;
		case self::VERSION_TS3:
			return $this->_tssclass->login($user, $pass);
			break;
		}
	} // function loginSuperAdmin

	/**
	 * Logout
	 *
	 * @return boolean
   */
	public function logout()
	{
		switch ($this->_version)
		{
		default:
		case self::VERSION_TS2:
			return false;
			break;
		case self::VERSION_TS3:
			return $this->_tssclass->logout();
			break;
		}
	} // function login

	/**
   * Get general server info
	 *
   * @return array
   */
  public function serverInfo()
	{
		if (is_array($this->_serverInfo))
			return $this->_serverInfo;
		switch ($this->_version)
		{
		default:
		case self::VERSION_TS2:
			$this->_serverInfo = $this->_tssclass->info_serverInfo();
			/* some conversion */
			$this->_serverInfo['virtualserver_id'] = $this->_serverInfo['server_id'];
			$this->_serverInfo['virtualserver_channelsonline'] = $this->_serverInfo['server_currentchannels'];
			$this->_serverInfo['virtualserver_maxclients'] = $this->_serverInfo['server_maxusers'];
			$this->_serverInfo['virtualserver_name'] = $this->_serverInfo['server_name'];
			$this->_serverInfo['virtualserver_flag_password'] = $this->_serverInfo['server_password'];
			$this->_serverInfo['virtualserver_platform'] = $this->_serverInfo['server_platform'];
			$this->_serverInfo['virtualserver_clientsonline'] = $this->_serverInfo['server_currentusers'];
			$this->_serverInfo['virtualserver_queryclientsonline'] = 0;
			$this->_serverInfo['virtualserver_clan_server'] = $this->_serverInfo['server_clan_server'];
			$this->_serverInfo['connection_bytes_sent_total'] = $this->_serverInfo['server_bytessend'];
			$this->_serverInfo['connection_bytes_received_total'] = $this->_serverInfo['server_bytesreceived'];
			break;
		case self::VERSION_TS3:
			$this->_serverInfo = $this->_tssclass->serverInfo();
			$this->_serverInfo['virtualserver_clan_server'] = 0;
			break;
		}
		return $this->_serverInfo;
	} // function serverInfo

	/**
	 * Get server version
	 *
	 * @return string
	 */
	public function serverVersion()
	{
		switch ($this->_version)
		{
		default:
		case self::VERSION_TS2:
			return $this->_tssclass->fastcall('ver');
			break;
		case self::VERSION_TS3:
//			return $this->_tssclass->version();
			$serverInfo = $this->serverInfo();
			if (isset($serverInfo['virtualserver_version']))
				return $serverInfo['virtualserver_version'];
			break;
		}
		return 'unknown';
	} // function serverVersion

	public function serverUptime()
	{
		switch ($this->_version)
		{
		default:
		case self::VERSION_TS2:
			$serverInfo = $this->serverInfo();
			if (isset($serverInfo['server_uptime']))
				return $serverInfo['server_uptime'];
			break;
		case self::VERSION_TS3:
			$serverInfo = $this->serverInfo();
			if (isset($serverInfo['virtualserver_uptime']))
				return $serverInfo['virtualserver_uptime'];
			break;
		}
		return 0;
	}
	
	public function getCodec($codec)
	{
		switch ($this->_version)
		{
		default:
		case self::VERSION_TS2:
			return $this->_tssclass->info_getCodec($codec);
			break;
		case self::VERSION_TS3:
			/* according to specs:
			 * CODEC_SPEEX_NARROWBAND = 0, // 0: speex narrowband (mono, 16bit, 8kHz)
			 * CODEC_SPEEX_WIDEBAND, // 1: speex wideband (mono, 16bit, 16kHz)
			 * CODEC_SPEEX_ULTRAWIDEBAND, // 2: speex ultra-wideband (mono, 16bit, 32kHz)
			 * CODEC_CELT_MONO // 3: celt mono (mono, 16bit, 48kHz)
			 */
			switch ($codec)
			{
			case 0: return 'Speex narrowband (mono, 16bit, 8kHz)';
			case 1: return 'Speex wideband (mono, 16bit, 16kHz)';
			case 2: return 'Speex ultra-wideband (mono, 16bit, 32kHz)';
			case 3: return 'Celt mono (mono, 16bit, 48kHz)';
			default:
				return '???';
			}
			break;
		}
	}

	/**
   */
  public function clientList()
	{
		if (is_array($this->_clientList))
			return $this->_clientList;
		$time = time();
		switch ($this->_version)
		{
		default:
		case self::VERSION_TS2:
			$this->_clientList = $this->_tssclass->info_playerList();
			foreach ($this->_clientList as $key => $client)
			{
				// 'p_id', 'c_id', 'ps', 'bs', 'pr', 'br', 'pl', 'ping', 'logintime', 'idletime', 'cprivs', 'pprivs', 'pflags', 'ip', 'nick', 'loginname'
				$this->_clientList[$key]['clid'] = $client['p_id']; // 1
				$this->_clientList[$key]['cid'] = $client['c_id']; // 2
//				$this->_clientList[$key][''] = $client['ps']; // 3
//				$this->_clientList[$key][''] = $client['bs']; // 4
//				$this->_clientList[$key][''] = $client['pr']; // 5
//				$this->_clientList[$key][''] = $client['br']; // 6
//				$this->_clientList[$key][''] = $client['pl']; // 7
				$this->_clientList[$key]['client_ping'] = $client['ping']; // 8
				$this->_clientList[$key]['client_lastconnected'] = $time - $client['logintime']; // 9
//				$this->_clientList[$key][''] = $player['idletime']; // 10
				$this->_clientList[$key]['client_flags_channel'] = $client['cprivs']; // 11
				$this->_clientList[$key]['client_flags_global'] = $client['pprivs']; // 12
				$this->_clientList[$key]['client_flags_status'] = $client['pflags']; // 13
//				$this->_clientList[$key][''] = $player['ip']; // 14
				$this->_clientList[$key]['client_nickname'] = $client['nick']; // 15
				$this->_clientList[$key]['client_login_name'] = $client['loginname']; // 16
				$this->_clientList[$key]['client_type'] = self::TYPE_NORMAL_CLIENT; // normal connecting client
			}
			break;
		case self::VERSION_TS3:
			$this->_clientList = $this->_tssclass->clientList();
//		  'clid' => '1',
//		  'cid' => '1',
//		  'client_database_id' => '1',
//		  'client_nickname' => 'nick',
//		  'client_type' => '0',
			foreach ($this->_clientList as $key => $client)
			{
				$cInfo = $this->clientInfo($client['clid']); // no data probably means this is a fake user
				if ($cInfo !== false)
				{
//				  'client_unique_identifier' => '',
//				  'client_nickname' => '',
//				  'client_version' => '',
//				  'client_platform' => '',
//				  'client_input_muted' => '0',
//				  'client_output_muted' => '0',
//				  'client_outputonly_muted' => '0',
//				  'client_input_hardware' => '1',
//				  'client_output_hardware' => '1',
//				  'client_default_channel' => '',
//				  'client_meta_data' => '',
//				  'client_is_recording' => '0',
//				  'client_login_name' => '',
//				  'client_database_id' => '14',
//				  'client_channel_group_id' => '8',
//				  'client_servergroups' => '7',
//				  'client_created' => '1261308099',
//				  'client_lastconnected' => '1262354177',
//				  'client_totalconnections' => '20',
//				  'client_away' => '0',
//				  'client_away_message' => '',
//				  'client_type' => '0',
//				  'client_flag_avatar' => '',
//				  'client_talk_power' => '0',
//				  'client_talk_request' => '0',
//				  'client_talk_request_msg' => '',
//				  'client_description' => '',
//				  'client_is_talker' => '0',
//				  'client_month_bytes_uploaded' => '0',
//				  'client_month_bytes_downloaded' => '44978',
//				  'client_total_bytes_uploaded' => '0',
//				  'client_total_bytes_downloaded' => '44978',
//				  'client_is_priority_speaker' => '0',
//				  'client_unread_messages' => '0',
//				  'client_nickname_phonetic' => '',
//				  'client_needed_serverquery_view_power' => '75',
//				  'client_base64HashClientUID' => '',
//				  'connection_filetransfer_bandwidth_sent' => '0',
//				  'connection_filetransfer_bandwidth_received' => '0',
//				  'connection_packets_sent_total' => '629359',
//				  'connection_bytes_sent_total' => '61816845',
//				  'connection_packets_received_total' => '78305',
//				  'connection_bytes_received_total' => '4638292',
//				  'connection_bandwidth_sent_last_second_total' => '81',
//				  'connection_bandwidth_sent_last_minute_total' => '1438',
//				  'connection_bandwidth_received_last_second_total' => '83',
//				  'connection_bandwidth_received_last_minute_total' => '83',
					$this->_clientList[$key]['client_ping'] = 0; // TODO
					$this->_clientList[$key]['client_flags_channel'] = 0;
					if ($cInfo['client_channel_group_id'] == $this->_cgidAdmin)
						$this->_clientList[$key]['client_flags_channel'] += self::FLAG_PLAYER_CHANNEL_CHANNEL_ADMIN;
					if ($cInfo['client_channel_group_id'] == $this->_cgidOperator)
						$this->_clientList[$key]['client_flags_channel'] += self::FLAG_PLAYER_CHANNEL_OPERATOR;
					if ($cInfo['client_channel_group_id'] == $this->_cgidVoice)
						$this->_clientList[$key]['client_flags_channel'] += self::FLAG_PLAYER_CHANNEL_VOICE;
					
					$this->_clientList[$key]['client_flags_global'] = 0;
					$serverGroups = explode(',', $cInfo['client_servergroups']);
					$isAdmin = false;
					$isNormal = false;
					foreach ($serverGroups as $sKey => $serverGroup)
					{
						if (intval($serverGroup) == $this->_sgidAdmin)
						{
							$isAdmin = true;
							$isNormal = true;
						}
						else if (intval($serverGroup) == $this->_sgidNormal)
							$isNormal = true;
					}
					if ($isAdmin == true)
						$this->_clientList[$key]['client_flags_global'] += self::FLAG_PLAYER_GLOBAL_SERVER_ADMIN;
					if ($isNormal == true)
						$this->_clientList[$key]['client_flags_global'] += self::FLAG_PLAYER_GLOBAL_REGISTERED;
					
					$this->_clientList[$key]['client_flags_status'] = 0;
					if ($cInfo['client_away'] == '1')
						$this->_clientList[$key]['client_flags_status'] += self::FLAG_PLAYER_STATUS_AWAY;
					if ($cInfo['client_is_priority_speaker'] == '1')
						$this->_clientList[$key]['client_flags_status'] += self::FLAG_PLAYER_STATUS_CHANNEL_COMMANDER;
					if ($cInfo['client_input_muted'] == '1')
						$this->_clientList[$key]['client_flags_status'] += self::FLAG_PLAYER_STATUS_MUTE_MICROPHONE;
					if ($cInfo['client_output_muted'] == '1')
						$this->_clientList[$key]['client_flags_status'] += self::FLAG_PLAYER_STATUS_MUTE_SPEAKERS_HEADPHONE;
					if ($cInfo['client_is_recording'] == '1')
						$this->_clientList[$key]['client_flags_status'] += self::FLAG_PLAYER_STATUS_RECORDING;
					if ($cInfo['client_is_talker'] == '1')
						$this->_clientList[$key]['client_flags_status'] += self::FLAG_PLAYER_STATUS_IS_TALKER;
					if ($cInfo['client_talk_request'] == '1')
						$this->_clientList[$key]['client_flags_status'] += self::FLAG_PLAYER_STATUS_VOICE_REQUEST;
					$this->_clientList[$key]['client_lastconnected'] = $cInfo['client_lastconnected'];
				}
				else
				{
					$this->_clientList[$key]['client_flags_channel'] = 0;
					$this->_clientList[$key]['client_flags_global'] = 0;
					$this->_clientList[$key]['client_flags_status'] = 0;
					$this->_clientList[$key]['client_lastconnected'] = $time;
				}
			}
			break;
		}
		return $this->_clientList;
	} // function clientList

	/**
	 * Get all clients from database
	 * 
	 * @param	int $serverAdminGid	For TS3 only, if the serveradmin group does not have standard ID 6.
   */
  public function clientDbList()
	{
		if (is_array($this->_clientDbList))
			return $this->_clientDbList;
		switch ($this->_version)
		{
		default:
		case self::VERSION_TS2:
			$this->_clientDbList = $this->_tssclass->admin_dbUserList();
			foreach ($this->_clientDbList as $key => $client)
			{
				$this->_clientDbList[$key]['cldbid'] = $client[1]; // user id
				$this->_clientDbList[$key]['client_unique_identifier'] = '';
				$this->_clientDbList[$key]['client_privileges'] = $client[2]; // is_admin
				$this->_clientDbList[$key]['client_created'] = strtotime($client[3]); // created
				$this->_clientDbList[$key]['client_lastconnected'] = strtotime($client[4]); // last connected
				$this->_clientDbList[$key]['client_login_name'] = $client[5]; // username
				$this->_clientDbList[$key]['client_description'] = '';
			}
			break;
		case self::VERSION_TS3:
			$start = 0;
			$step = 100; // get every 100 records
			$count = 0;
			$this->_clientDbList = null;
			$tempList = $this->_tssclass->clientDbList('start='.$start.' duration='.$step);
			while ($tempList !== false)
			{
				if (!is_array($this->_clientDbList))
					$this->_clientDbList = array();
				foreach ($tempList as $client)
				{
					$this->_clientDbList[$count] = $client;
					$serverGroups = $this->_tssclass->serverGroupsByClientID($client['cldbid']);
					$isAdmin = false;
					foreach ($serverGroups as $serverGroup)
					{
						if (intval($serverGroup['sgid']) == $this->_sgidAdmin)
							$isAdmin = true;
					}
					// $this->_clientDbList[$count]['client_login_name'] = ''; // TODO
					$this->_clientDbList[$count]['client_login_name'] = $client['client_nickname']; // TODO
					$this->_clientDbList[$count]['client_privileges'] = ($isAdmin == true ? '-1' : '0');
					$count++;
				}
				$start += $step;
				$tempList = $this->_tssclass->clientDbList('start='.$start.' duration='.$step);
			}
			break;
		}
		return $this->_clientDbList;
	} // function clientDbList

	/**
	 * Create client DB account on server
	 * 
	 * @param	string $user
	 * @param	string $pass
	 * @param	string $asAdmin	false for user, true for admin
	 * @param	int		 $cs_id	ClanSphere users_id (optional for TS2, needed for TS3)
	 * 
	 * @return	boolean	true for TS2, token string for TS3 on success, false on failure
	 */
	public function clientDbCreate($user, $pass, $asAdmin = false, $cs_id = 0)
	{
		global $cs_main;
		
		$user = cs_encode($user, $cs_main['charset'], $this->_charset);
		$pass = cs_encode($pass, $cs_main['charset'], $this->_charset);
		switch ($this->_version)
		{
		default:
		case self::VERSION_TS2:
			return $this->_tssclass->admin_dbUserAdd($user, $pass, $asAdmin);
			break;
		case self::VERSION_TS3:
			if ($asAdmin)
			{
				$sgid = $this->_sgidAdmin;
				$description = 'ClanSphere Create Admin #'.$cs_id;
			}
			else
			{
				$sgid = $this->_sgidNormal;
				$description = 'ClanSphere Create Client #'.$cs_id;
			}
			$tokenList = $this->_tssclass->tokenList();
			/* first try to find a still unused token */
			foreach ($tokenList as $key => $token) 
			{
				if ($token['token_type'] == 0 && $token['token_id1'] == $sgid
						&& $token['token_id2'] == 0 && $token['token_description'] == $description)
				{
					return $token;
				}
			}
			/* no token found, create new one */
			$fields = array('cs_id' => $cs_id, 'cs_nick' => $user);
			/* since 3.0.0-beta15 */
			// tokenadd tokentype=0 tokenid1=$sgid tokenid2=0 tokendescription=XXX tokencustomset=YYYY
			return $this->_tssclass->tokenAddCustomSet(self::TOKEN_SERVER_GROUP, $sgid, 0, $fields, $description);
			// tokenadd tokentype=0 tokenid1=$sgid tokenid2=0
//			return $this->_tssclass->tokenAdd(self::TOKEN_SERVER_GROUP, $sgid, 0);
//			return false;
			break;
		}
	} // function clientDbCreate

	/**
	 * Delete client DB account from server
	 * 
	 * @param	int $cldbid	ID of the client in the database
	 * 
	 * @return	boolean	true on success, false on failure
	 */
	public function clientDbDelete($cldbid)
	{
		switch ($this->_version)
		{
		default:
		case self::VERSION_TS2:
			return $this->_tssclass->admin_dbUserDel($cldbid);
			break;
		case self::VERSION_TS3:
			return $this->_tssclass->clientDbDelete($cldbid);
			break;
		}
	} // function clientDbDelete
	
	/**
	 * Find a client DB account on the server
	 *
	 * @param	string	$login_name	TS login_name (CS users_nick) 
	 * @param	int			$cs_id			ClanSphere users_id
	 * @param	string	$ident			TS3: Ident of custom field to search for
	 *
	 * @return	array	array of found clients
	 */
	public function clientDbFind($user, $cs_id = 0, $ident = 'cs_id')
	{
		global $cs_main;
		
		$user = cs_encode($user, $cs_main['charset'], $this->_charset);

		switch ($this->_version)
		{
		default:
		case self::VERSION_TS2:
			// 0 =>
			// array (
			//   0 => '1	"admin"	-1	16-07-2008 18:09:21	26-10-2009 16:47:56',
    	// 	1 => '1',
    	// 	2 => 'admin',
    	// 	3 => '-1',
    	// 	4 => '16-07-2008 18:09:21',
    	// 	5 => '26-10-2009 16:47:56',
  		// ),
			$results = $this->_tssclass->sadmin_dbFindPlayer($user);
			if ($results !== false && count($results))
			{
				foreach ($results as $key => $client)
				{
					$results[$key]['cldbid'] = $client[1]; // user id
					$results[$key]['client_login_name'] = $client[2]; // username
					$results[$key]['client_privileges'] = $client[3]; // is_admin
					$results[$key]['client_created'] = strtotime($client[4]); // created
					$results[$key]['client_lastconnected'] = strtotime($client[5]); // last connected
				}
			}
			else
				$results = array();
			return $results;
			break;
		case self::VERSION_TS3:
			/* since 3.0.0-beta15 */
			$results = $this->_tssclass->customSearch($ident, $cs_id);
			if ($results !== false && count($results) > 0)
			{
				$list = $this->clientDbList();
				foreach ($results as $key => $client)
				{
					$results[$key]['client_login_name'] = ''; // TODO
					$results[$key]['client_privileges'] = '0';
					$results[$key]['client_created'] = 0; // created
					$results[$key]['client_lastconnected'] = 0; // last connected
					foreach ($list as $dbclient)
					{
						if ($client['cldbid'] == $dbclient['cldbid'])
						{
							$results[$key]['client_login_name'] = $dbclient['client_login_name']; // TODO
							$results[$key]['client_privileges'] = $dbclient['client_privileges'];
							$results[$key]['client_created'] = $dbclient['client_created']; // created
							$results[$key]['client_lastconnected'] = $dbclient['client_lastconnected']; // last connected
							break;
						}
					}
				}
			}
			else
				$results = array();
			return $results;
			break;
		}
	}

	/**
   */
  public function clientInfo($clientId)
	{
		switch ($this->_version)
		{
		default:
		case self::VERSION_TS2:
			return $this->_tssclass->info_playerInfo($clientId);
			break;
		case self::VERSION_TS3:
//		  'client_unique_identifier' => '',
//		  'client_nickname' => 'nick',
//		  'client_version' => '',
//		  'client_platform' => '',
//		  'client_input_muted' => '0',
//		  'client_output_muted' => '0',
//		  'client_outputonly_muted' => '0',
//		  'client_input_hardware' => '1',
//		  'client_output_hardware' => '1',
//		  'client_default_channel' => '',
//		  'client_meta_data' => '',
//		  'client_is_recording' => '0',
//		  'client_login_name' => '',
//		  'client_database_id' => '14',
//		  'client_channel_group_id' => '8',
//		  'client_servergroups' => '7',
//		  'client_created' => '1261308099',
//		  'client_lastconnected' => '1262354177',
//		  'client_totalconnections' => '20',
//		  'client_away' => '0',
//		  'client_away_message' => '',
//		  'client_type' => '0',
//		  'client_flag_avatar' => '',
//		  'client_talk_power' => '0',
//		  'client_talk_request' => '0',
//		  'client_talk_request_msg' => '',
//		  'client_description' => '',
//		  'client_is_talker' => '0',
//		  'client_month_bytes_uploaded' => '0',
//		  'client_month_bytes_downloaded' => '44978',
//		  'client_total_bytes_uploaded' => '0',
//		  'client_total_bytes_downloaded' => '44978',
//		  'client_is_priority_speaker' => '0',
//		  'client_unread_messages' => '0',
//		  'client_nickname_phonetic' => '',
//		  'client_needed_serverquery_view_power' => '75',
//		  'client_base64HashClientUID' => '',
//		  'connection_filetransfer_bandwidth_sent' => '0',
//		  'connection_filetransfer_bandwidth_received' => '0',
//		  'connection_packets_sent_total' => '629359',
//		  'connection_bytes_sent_total' => '61816845',
//		  'connection_packets_received_total' => '78305',
//		  'connection_bytes_received_total' => '4638292',
//		  'connection_bandwidth_sent_last_second_total' => '81',
//		  'connection_bandwidth_sent_last_minute_total' => '1438',
//		  'connection_bandwidth_received_last_second_total' => '83',
//		  'connection_bandwidth_received_last_minute_total' => '83',
			return $this->_tssclass->clientInfo($clientId);
			break;
		}
	} // function clientInfo

	/**
	 * 
	 */
	public function clientFlagsGlobal($flags, $cflags, $mode = 2, $size=14)
	{
		$flags = intval($flags);
		$flag = array();
		switch ($this->_version)
		{
		default:
		case self::VERSION_TS2:
			// SA
			$flag['player_flag_serveradmin'] = (($flags & self::FLAG_PLAYER_GLOBAL_SERVER_ADMIN) == self::FLAG_PLAYER_GLOBAL_SERVER_ADMIN) ? 1 : 0;
			// AR
			$flag['player_flag_allow_registration'] = (($flags & self::FLAG_PLAYER_GLOBAL_ALLOW_REGISTRATION) == self::FLAG_PLAYER_GLOBAL_ALLOW_REGISTRATION) ? 1 : 0;
			// R
			$flag['player_flag_registered'] = (($flags & self::FLAG_PLAYER_GLOBAL_REGISTERED) == self::FLAG_PLAYER_GLOBAL_REGISTERED) ? 1 : 0;
			// IU
			$flag['player_flag_internal_use'] = (($flags & self::FLAG_PLAYER_GLOBAL_INTERNAL_USE) == self::FLAG_PLAYER_GLOBAL_INTERNAL_USE) ? 1 : 0;
			// ST
			$flag['player_flag_sticky'] = (($flags & self::FLAG_PLAYER_GLOBAL_STICKY) == self::FLAG_PLAYER_GLOBAL_STICKY) ? 1 : 0;

			/* player channel stuff */
			// CA
			$flag['player_flag_channel_channel_admin'] = (($cflags & self::FLAG_PLAYER_CHANNEL_CHANNEL_ADMIN) == self::FLAG_PLAYER_CHANNEL_CHANNEL_ADMIN) ? 1 : 0;
			// O
			$flag['player_flag_channel_operator'] = (($cflags & self::FLAG_PLAYER_CHANNEL_OPERATOR) == self::FLAG_PLAYER_CHANNEL_OPERATOR) ? 1 : 0;
			// V
			$flag['player_flag_channel_voice'] = (($cflags & self::FLAG_PLAYER_CHANNEL_VOICE) == self::FLAG_PLAYER_CHANNEL_VOICE) ? 1 : 0;
			// AO
			$flag['player_flag_channel_auto_operator'] = (($cflags & self::FLAG_PLAYER_CHANNEL_AUTO_OPERATOR) == self::FLAG_PLAYER_CHANNEL_AUTO_OPERATOR) ? 1 : 0;
			// AV
			$flag['player_flag_channel_auto_voice'] = (($cflags & self::FLAG_PLAYER_CHANNEL_AUTO_VOICE) == self::FLAG_PLAYER_CHANNEL_AUTO_VOICE) ? 1 : 0;
			break;
		case self::VERSION_TS3:
			// SA
			$flag['player_flag_serveradmin'] = (($flags & self::FLAG_PLAYER_GLOBAL_SERVER_ADMIN) == self::FLAG_PLAYER_GLOBAL_SERVER_ADMIN) ? 1 : 0;
			// AR
			$flag['player_flag_allow_registration'] = (($flags & self::FLAG_PLAYER_GLOBAL_ALLOW_REGISTRATION) == self::FLAG_PLAYER_GLOBAL_ALLOW_REGISTRATION) ? 1 : 0;
			// R
			$flag['player_flag_registered'] = (($flags & self::FLAG_PLAYER_GLOBAL_REGISTERED) == self::FLAG_PLAYER_GLOBAL_REGISTERED) ? 1 : 0;
			// ???
			$flag['player_flag_internal_use'] = (($flags & self::FLAG_PLAYER_GLOBAL_INTERNAL_USE) == self::FLAG_PLAYER_GLOBAL_INTERNAL_USE) ? 1 : 0;
			// ST???
			$flag['player_flag_sticky'] = (($flags & self::FLAG_PLAYER_GLOBAL_STICKY) == self::FLAG_PLAYER_GLOBAL_STICKY) ? 1 : 0;

			/* player channel stuff */
			// CA
			$flag['player_flag_channel_channel_admin'] = (($cflags & self::FLAG_PLAYER_CHANNEL_CHANNEL_ADMIN) == self::FLAG_PLAYER_CHANNEL_CHANNEL_ADMIN) ? 1 : 0;
			// O
			$flag['player_flag_channel_operator'] = (($cflags & self::FLAG_PLAYER_CHANNEL_OPERATOR) == self::FLAG_PLAYER_CHANNEL_OPERATOR) ? 1 : 0;
			// V
			$flag['player_flag_channel_voice'] = (($cflags & self::FLAG_PLAYER_CHANNEL_VOICE) == self::FLAG_PLAYER_CHANNEL_VOICE) ? 1 : 0;
			// AO
			$flag['player_flag_channel_auto_operator'] = (($cflags & self::FLAG_PLAYER_CHANNEL_AUTO_OPERATOR) == self::FLAG_PLAYER_CHANNEL_AUTO_OPERATOR) ? 1 : 0;
			// AV
			$flag['player_flag_channel_auto_voice'] = (($cflags & self::FLAG_PLAYER_CHANNEL_AUTO_VOICE) == self::FLAG_PLAYER_CHANNEL_AUTO_VOICE) ? 1 : 0;
			break;
		}
		switch ($mode)
		{
		default: 
			break;
		case self::FLAG_PLAYER_MODE_GLOBAL_ARRAY:
			return $flag;
			break;
		case self::FLAG_PLAYER_MODE_GLOBAL_TEXT:
			$flagString = ($flag['player_flag_registered'] == 1) ? 'R' : '';
			$flagString .= ($flag['player_flag_allow_registration'] == 1) ? ' AR' : '';
			$flagString .= ($flag['player_flag_serveradmin'] == 1) ? ' SA' : '';
			$flagString .= ($flag['player_flag_internal_use'] == 1) ? ' IU' : '';
			$flagString .= ($flag['player_flag_sticky'] == 1) ? ' ST' : '';
			$flagString .= ($flag['player_flag_channel_channel_admin'] == 1) ? ' CA' : '';
			$flagString .= ($flag['player_flag_channel_operator'] == 1) ? ' O' : '';
			$flagString .= ($flag['player_flag_channel_voice'] == 1) ? ' V' : '';
			$flagString .= ($flag['player_flag_channel_auto_operator'] == 1) ? ' AO' : '';
			$flagString .= ($flag['player_flag_channel_auto_voice'] == 1) ? ' AV' : '';
			return (empty($flagString) ? '(U)' : '('.$flagString.')');
			break;
		case self::FLAG_PLAYER_MODE_GLOBAL_IMAGE:
			switch ($this->_version)
			{
			case self::VERSION_TS2:
				return $this->clientFlagsGlobal($flags, $cflags, self::FLAG_PLAYER_MODE_GLOBAL_TEXT, $size);
				break;
			case self::VERSION_TS3:
				$images = '';
				$images .= ($flag['player_flag_channel_operator'] == 1) ? cs_html_img('mods/teamspeak/images/'.$this->_version.'/player_global_channeloperator.png',$size,$size,'title="'.$this->_cs_lang['flag_p_o'].'"') : '';
				$images .= ($flag['player_flag_channel_channel_admin'] == 1) ? cs_html_img('mods/teamspeak/images/'.$this->_version.'/player_global_channelgroup.png',$size,$size,'title="'.$this->_cs_lang['flag_p_ca'].'"') : '';
				$images .= ($flag['player_flag_serveradmin'] == 1) ? cs_html_img('mods/teamspeak/images/'.$this->_version.'/player_global_servergroup.png',$size,$size,'title="'.$this->_cs_lang['flag_p_sa'].'"') : '';
				return $images;
				break;
			}
			break;
		}
		return $flag;
	} // function clientFlagsGlobal

	/**
	 * 
	 */
	public function clientFlagsStatus($flags, $mode = 2, $size = 14)
	{
		$flags = intval($flags);
		$flag = array();
		switch ($this->_version)
		{
		default:
		case self::VERSION_TS2:
			// CC
			$flag['player_status_channel_commander'] = (($flags & self::FLAG_PLAYER_STATUS_CHANNEL_COMMANDER) == self::FLAG_PLAYER_STATUS_CHANNEL_COMMANDER) ? 1 : 0;
			// VR
			$flag['player_status_voice_request'] = (($flags & self::FLAG_PLAYER_STATUS_VOICE_REQUEST) == self::FLAG_PLAYER_STATUS_VOICE_REQUEST) ? 1 : 0;
			// NW
			$flag['player_status_no_whispers'] = (($flags & self::FLAG_PLAYER_STATUS_NO_WHISPERS) == self::FLAG_PLAYER_STATUS_NO_WHISPERS) ? 1 : 0;
			// AW
			$flag['player_status_away'] = (($flags & self::FLAG_PLAYER_STATUS_AWAY) == self::FLAG_PLAYER_STATUS_AWAY) ? 1 : 0;
			// MM
			$flag['player_status_mute_microphone'] = (($flags & self::FLAG_PLAYER_STATUS_MUTE_MICROPHONE) == self::FLAG_PLAYER_STATUS_MUTE_MICROPHONE) ? 1 : 0;
			// SM
			$flag['player_status_mute_speakers_headset'] = (($flags & self::FLAG_PLAYER_STATUS_MUTE_SPEAKERS_HEADPHONE) == self::FLAG_PLAYER_STATUS_MUTE_SPEAKERS_HEADPHONE) ? 1 : 0;
			// RC
			$flag['player_status_recording'] = (($flags & self::FLAG_PLAYER_STATUS_RECORDING) == self::FLAG_PLAYER_STATUS_RECORDING) ? 1 : 0;
			// IT
			$flag['player_status_is_talker'] = (($flags & self::FLAG_PLAYER_STATUS_IS_TALKER) == self::FLAG_PLAYER_STATUS_IS_TALKER) ? 1 : 0;
			break;
		case self::VERSION_TS3:
			// CC
			$flag['player_status_channel_commander'] = (($flags & self::FLAG_PLAYER_STATUS_CHANNEL_COMMANDER) == self::FLAG_PLAYER_STATUS_CHANNEL_COMMANDER) ? 1 : 0;
			// VR
			$flag['player_status_voice_request'] = (($flags & self::FLAG_PLAYER_STATUS_VOICE_REQUEST) == self::FLAG_PLAYER_STATUS_VOICE_REQUEST) ? 1 : 0;
			// NW
			$flag['player_status_no_whispers'] = (($flags & self::FLAG_PLAYER_STATUS_NO_WHISPERS) == self::FLAG_PLAYER_STATUS_NO_WHISPERS) ? 1 : 0;
			// AW
			$flag['player_status_away'] = (($flags & self::FLAG_PLAYER_STATUS_AWAY) == self::FLAG_PLAYER_STATUS_AWAY) ? 1 : 0;
			// MM
			$flag['player_status_mute_microphone'] = (($flags & self::FLAG_PLAYER_STATUS_MUTE_MICROPHONE) == self::FLAG_PLAYER_STATUS_MUTE_MICROPHONE) ? 1 : 0;
			// SM
			$flag['player_status_mute_speakers_headset'] = (($flags & self::FLAG_PLAYER_STATUS_MUTE_SPEAKERS_HEADPHONE) == self::FLAG_PLAYER_STATUS_MUTE_SPEAKERS_HEADPHONE) ? 1 : 0;
			// RC
			$flag['player_status_recording'] = (($flags & self::FLAG_PLAYER_STATUS_RECORDING) == self::FLAG_PLAYER_STATUS_RECORDING) ? 1 : 0;
			// IT
			$flag['player_status_is_talker'] = (($flags & self::FLAG_PLAYER_STATUS_IS_TALKER) == self::FLAG_PLAYER_STATUS_IS_TALKER) ? 1 : 0;
			break;
		}
		switch ($mode)
		{
		default:
			break;
		case self::FLAG_PLAYER_MODE_STATUS_ARRAY:
			return $flag;
			break;
		case self::FLAG_PLAYER_MODE_STATUS_TEXT:
			$flagString = ($flag['player_status_channel_commander'] == 1) ? 'CC' : '';
			$flagString .= ($flag['player_status_voice_request'] == 1) ? 'VR' : '';
			$flagString .= ($flag['player_status_no_whispers'] == 1) ? 'NW' : '';
			$flagString .= ($flag['player_status_away'] == 1) ? 'AW' : ''; // ???
			$flagString .= ($flag['player_status_mute_microphone'] == 1) ? 'MM' : '';
			$flagString .= ($flag['player_status_mute_speakers_headset'] == 1) ? 'SM' : '';
			$flagString .= ($flag['player_status_recording'] == 1) ? 'RC' : '';
			$flagString .= ($flag['player_status_is_talker'] == 1) ? 'IT' : '';
			return $flagString;
			break;
		case self::FLAG_PLAYER_MODE_STATUS_IMAGE:
			if ($flag['player_status_channel_commander'] == 1)
				return cs_html_img('mods/teamspeak/images/'.$this->_version.'/player_channelcommander.png',$size,$size, 'title="'.$this->_cs_lang['flag_p_cc'].'"');
//			if ($flag['player_status_voice_request'] == 1)
//				return cs_html_img('mods/teamspeak/images/'.$this->_version.'/player_voicerequest.png',$size,$size, 'title="'.$this->_cs_lang['flag_p_vr'].'"');
//			if ($flag['player_status_no_whispers'] == 1)
//				return cs_html_img('mods/teamspeak/images/'.$this->_version.'/player_nowhispers.png',$size,$size, 'title="'.$this->_cs_lang['flag_p_nw'].'"');
			if ($flag['player_status_away'] == 1)
				return cs_html_img('mods/teamspeak/images/'.$this->_version.'/player_away.png',$size,$size, 'title="'.$this->_cs_lang['flag_p_aw'].'"');
			if ($flag['player_status_mute_microphone'] == 1)
				return cs_html_img('mods/teamspeak/images/'.$this->_version.'/player_mutemicrophone.png',$size,$size, 'title="'.$this->_cs_lang['flag_p_mm'].'"');
			if ($flag['player_status_mute_speakers_headset'] == 1)
				return cs_html_img('mods/teamspeak/images/'.$this->_version.'/player_mutespeakers.png',$size,$size, 'title="'.$this->_cs_lang['flag_p_sm'].'"');
//			if ($flag['player_status_recording'] == 1)
//				return cs_html_img('mods/teamspeak/images/'.$this->_version.'/player_recording.png',$size,$size, 'title="'.$this->_cs_lang['flag_p_rc'].'"');
			if ($flag['player_status_is_talker'] == 1)
				return cs_html_img('mods/teamspeak/images/'.$this->_version.'/player_istalker.png',$size,$size, 'title="'.$this->_cs_lang['flag_p_it'].'"');
			return cs_html_img('mods/teamspeak/images/'.$this->_version.'/player_normal.png',$size,$size);
			break;
		}
		return $flag;
	} // function clientFlagsStatus
	
	
	/**
	 * Kick a client from a channel
	 *
	 * @param	int	$clientId
	 * @param	int	$from				TS2: from server, TS3: 5 = kick from server, 4 = kick from channel
	 * @param	string	$reason		Kick reason
	 */
	public function clientKick($clientId, $from = 5, $reason = '')
	{
	  switch ($this->_version)
    {
    default:
    case self::VERSION_TS2:
    	switch ($from)
    	{
    	default:
    		return false;
    	case self::REASON_KICK_CHANNEL:
    		return $this->_tssclass->admin_kickFromChannel($clientId);
    		break;
    	case self::REASON_KICK_SERVER:
				if (empty($reason))
	      	return $this->_tssclass->admin_remove($clientId);
				else
					return $this->_tssclass->admin_kick($clientId, $reason);
	      break;
    	}
    	break;
    case self::VERSION_TS3:
    	switch ($from)
    	{
    	default:
    		return false;
    	case self::REASON_KICK_CHANNEL:
	      return $this->_tssclass->clientKick($clientId, self::REASON_KICK_CHANNEL, $reason);
    		break;
    	case self::REASON_KICK_SERVER:
	      return $this->_tssclass->clientKick($clientId, self::REASON_KICK_SERVER, $reason);
	      break;
    	}
      break;
    }
	} // function clientKick

	/**
	 * Return all set flags
	 */
	public function channelFlags($flags, $mode = 2, $size = 14)
	{
		$flags = intval($flags);
		$flag = array();
		switch ($this->_version)
		{
		default:
		case self::VERSION_TS2:
			// U/R ATTENTION TS2: REVERSED VALUE!!!
			$flag['channel_flag_permanent'] = (($flags & self::FLAG_CHANNEL_PERMANENT) == self::FLAG_CHANNEL_PERMANENT) ? 0 : 1;
			// M
			$flag['channel_flag_moderated'] = (($flags & self::FLAG_CHANNEL_MODERATED) == self::FLAG_CHANNEL_MODERATED) ? 1 : 0;
			// P
			$flag['channel_flag_password'] = (($flags & self::FLAG_CHANNEL_PASSWORD) == self::FLAG_CHANNEL_PASSWORD) ? 1 : 0;
			// S
			$flag['channel_flag_subchannels'] = (($flags & self::FLAG_CHANNEL_SUBCHANNELS) == self::FLAG_CHANNEL_SUBCHANNELS) ? 1 : 0;
			// D
			$flag['channel_flag_default'] = (($flags & self::FLAG_CHANNEL_DEFAULT) == self::FLAG_CHANNEL_DEFAULT) ? 1 : 0;
			// does not exist
			$flag['channel_flag_semi_permanent'] = 0;
			break;
		case self::VERSION_TS3:
			// R
			$flag['channel_flag_permanent'] = (($flags & self::FLAG_CHANNEL_PERMANENT) == self::FLAG_CHANNEL_PERMANENT) ? 1 : 0;
			// does not exist
			$flag['channel_flag_moderated'] =  (($flags & self::FLAG_CHANNEL_MODERATED) == self::FLAG_CHANNEL_MODERATED) ? 1 : 0;
			// P
			$flag['channel_flag_password'] = (($flags & self::FLAG_CHANNEL_PASSWORD) == self::FLAG_CHANNEL_PASSWORD) ? 1 : 0;
			// S
			$flag['channel_flag_subchannels'] = 0;
			// D
			$flag['channel_flag_default'] = (($flags & self::FLAG_CHANNEL_DEFAULT) == self::FLAG_CHANNEL_DEFAULT) ? 1 : 0;
			// r
			$flag['channel_flag_semi_permanent'] = (($flags & self::FLAG_CHANNEL_SEMI_PERMANENT) == self::FLAG_CHANNEL_SEMI_PERMANENT) ? 1 : 0;
			break;
		}
		switch ($mode)
		{
		default:
			break;
		case self::FLAG_CHANNEL_MODE_ARRAY:
			return $flag;
			break;
		case self::FLAG_CHANNEL_MODE_CHANNEL_STATES_TEXT:
			$flagString = ($flag['channel_flag_permanent'] == 1) ? 'R' : '';
			$flagString .= ($flag['channel_flag_semi_permanent'] == 1) ? 'r' : '';
			$flagString .= ($flag['channel_flag_permanent'] == 0 && $flag['channel_flag_semi_permanent'] == 0) ? 'U' : '';
			$flagString .= ($flag['channel_flag_moderated'] == 1) ? 'M' : '';
			$flagString .= ($flag['channel_flag_password'] == 1) ? 'P' : '';
			$flagString .= ($flag['channel_flag_subchannels'] == 1) ? 'S' : '';
			$flagString .= ($flag['channel_flag_default'] == 1) ? 'D' : '';
			return '('.$flagString.')';
			break;
		case self::FLAG_CHANNEL_MODE_CHANNEL_STATES_IMAGE:
			switch ($this->_version)
			{
			case self::VERSION_TS2:
				return $this->channelFlags($flags, self::FLAG_CHANNEL_MODE_CHANNEL_STATES_TEXT, $size);
				break;
			case self::VERSION_TS3:
				$images =  '';
				$images .= ($flag['channel_flag_password'] == 1) ? cs_html_img('mods/teamspeak/images/'.$this->_version.'/channel_state_password.png',$size,$size,'title="'.$this->_cs_lang['flag_c_p'].'"') : '';
			  $images .= ($flag['channel_flag_moderated'] == 1) ? cs_html_img('mods/teamspeak/images/'.$this->_version.'/channel_state_moderated.png',$size,$size,'title="'.$this->_cs_lang['flag_c_m'].'"') : '';
				$images .= ($flag['channel_flag_default'] == 1) ? cs_html_img('mods/teamspeak/images/'.$this->_version.'/channel_state_default.png',$size,$size,'title="'.$this->_cs_lang['flag_c_d'].'"') : '';
				return $images;
				break;
			}
			break;
		case self::FLAG_CHANNEL_MODE_CHANNEL_IMAGE:
			if ($flag['channel_flag_password'] == 1)
				return cs_html_img('mods/teamspeak/images/'.$this->_version.'/channel_password.png',$size,$size,'title="'.$this->_cs_lang['flag_c_p'].'"');
			return cs_html_img('mods/teamspeak/images/'.$this->_version.'/channel.png',$size,$size);
			break;
		}
		return $flag;
	} // function channelFlags
	
	/**
   */
  public function channelList()
	{
		if (is_array($this->_channelList))
			return $this->_channelList;
		switch ($this->_version)
		{
		default:
		case self::VERSION_TS2:
			$this->_channelList = $this->_tssclass->info_channelList();
			foreach ($this->_channelList as $key => $channel)
			{
				$this->_channelList[$key]['cid'] = $channel['id']; // 1
				$this->_channelList[$key]['channel_codec'] = $channel['codec']; // 2
				$this->_channelList[$key]['pid'] = ($channel['parent'] == '-1' ? '0' : $channel['parent']); // 3
				$this->_channelList[$key]['channel_order'] = $channel['order']; // 4
				$this->_channelList[$key]['channel_maxclients'] = $channel['maxusers']; // 5
				$this->_channelList[$key]['channel_name'] = $channel['name']; // 6
				$this->_channelList[$key]['channel_flags'] = $channel['flags']; // 7: flags
				$this->_channelList[$key]['channel_flag_password'] = $channel['password'];	// 8
				$this->_channelList[$key]['channel_topic'] = $channel['topic']; // 9
				$this->_channelList[$key]['channel_description'] = ''; // TODO: we can fetch this via sadmin_channelInfo
			}
			break;
		case self::VERSION_TS3:
			$this->_channelList = $this->_tssclass->channelList();
			foreach ($this->_channelList as $key => $channel)
			{
				$cInfo = $this->channelInfo($channel['cid']);
				$this->_channelList[$key]['channel_codec'] = $cInfo['channel_codec'];
				$this->_channelList[$key]['channel_maxclients'] = $cInfo['channel_maxclients'];
				$this->_channelList[$key]['channel_flags'] = $cInfo['channel_flags'];
				$this->_channelList[$key]['channel_flag_password'] = $cInfo['channel_flag_password'];
				$this->_channelList[$key]['channel_topic'] = $cInfo['channel_topic'];
				$this->_channelList[$key]['channel_description'] = $cInfo['channel_description'];
			}
			break;
		}
		return $this->_channelList;
	} // function channelList

	/**
	 * @param	int	$channelId
   */
  public function channelInfo($channelId)
	{
		switch ($this->_version)
		{
		default:
		case self::VERSION_TS2:
			$cInfo = $this->_tssclass->info_channelInfo($channelId);
//		  'id' => '35', // 1
			$cInfo['channel_codec'] = $cInfo['codec']; // 2
//		  'parent' => '9', // 3
			$cInfo['channel_order'] = $cInfo['order']; // 4
			$cInfo['channel_maxclients'] = $cInfo['maxusers']; // 5
			$cInfo['channel_name'] = $cInfo['name']; // 6
			$cInfo['channel_flags'] = $cInfo['flags']; // 7
			$cInfo['channel_flag_password'] = $cInfo['password']; // 8
			$cInfo['channel_topic'] = $cInfo['topic']; // 9
			return $cInfo;
			break;
		case self::VERSION_TS3:
//		  'channel_name' => '',
//		  'channel_topic' => '',
//		  'channel_description' => '',
//		  'channel_password' => '',
//		  'channel_codec' => '1',
//		  'channel_codec_quality' => '7',
//		  'channel_maxclients' => '-1',
//		  'channel_maxfamilyclients' => '-1',
//		  'channel_order' => '0',
//		  'channel_flag_permanent' => '1',
//		  'channel_flag_semi_permanent' => '0',
//		  'channel_flag_default' => '0',
//		  'channel_flag_password' => '0',
//		  'channel_flag_maxclients_unlimited' => '1',
//		  'channel_flag_maxfamilyclients_unlimited' => '0',
//		  'channel_flag_maxfamilyclients_inherited' => '1',
//		  'channel_filepath' => 'files/virtualserver_1/channel_18',
//		  'channel_needed_talk_power' => '0',
//		  'channel_forced_silence' => '0',
//		  'channel_name_phonetic' => '',
			$cInfo = $this->_tssclass->channelInfo($channelId);
			$cInfo['channel_flags'] = 0; // 7: flags
			if ($cInfo['channel_flag_permanent'] == '1')
				$cInfo['channel_flags'] += self::FLAG_CHANNEL_PERMANENT;
			if (intval($cInfo['channel_needed_talk_power']) > 0)
				$cInfo['channel_flags'] += self::FLAG_CHANNEL_MODERATED;
			if ($cInfo['channel_flag_semi_permanent'] == '1')
				$cInfo['channel_flags'] += self::FLAG_CHANNEL_SEMI_PERMANENT;
			if ($cInfo['channel_flag_password'] == '1')
				$cInfo['channel_flags'] += self::FLAG_CHANNEL_PASSWORD;
			if ($cInfo['channel_flag_default'] == '1')
				$cInfo['channel_flags'] += self::FLAG_CHANNEL_DEFAULT;
			return $cInfo;
			break;
		}
	} // function channelInfo

	/**
	 * Get all clients in a channel
	 * 
	 * @param	int		$channelId
	 * @param	boolean	$all	If true, also get the query clients
	 */
	public function channelClients($channelId, $all = false)
	{
		$clients = $this->clientList();
		$channelClients = array();
		foreach ($clients as $client)
		{
			if ($client['cid'] == $channelId)
			{
				if (!$all && $client['client_type'] == self::TYPE_QUERY_CLIENT)
					continue;
				$channelClients[] = $client;
			}
		}
		if (count($channelClients) == 0)
			return false;
		return $channelClients;
	}

	/**
	 * For TS3 the server admin groups are different for each virtual server
	 * This function tries to determine the defined server groups
	 * 
	 * If this somehow does not give the correct server groups,
	 * try to override them via tssServerGroups() after you have called connect().
	 * 
   * @return boolean	currently always returns true
	 */
	public function initServerGroups()
	{
		if ($this->_version == self::VERSION_TS2)
			return true;
		$serverGroups = $this->_tssclass->serverGroupList();
		if ($serverGroups !== false)
		foreach ($serverGroups as $serverGroup)
		{
			/* Array
 			 * {
 			 *  [sgid] => 3
 			 *  [name] => Server Admin
 			 *  [type] => 0
 			 *  [iconid] => 300
 			 *  [savedb] => 1
 			 * }
			 */
			if ($serverGroup['type'] == self::PERM_GROUP_DB_TYPE_REGULAR AND $serverGroup['iconid'] == 300 AND $serverGroup['savedb'] == 1) // AND $serverGroup['name'] == 'Server Admin'
			{
				// echo 'ADMIN '.intval($serverGroup['sgid']).'<br>';
				$this->_sgidAdmin = intval($serverGroup['sgid']);
			}
			else if ($serverGroup['type'] == self::PERM_GROUP_DB_TYPE_REGULAR AND $serverGroup['iconid'] == 0 AND $serverGroup['savedb'] == 1) // AND $serverGroup['name'] == 'Normal'
			{
				// echo 'NORMAL '.intval($serverGroup['sgid']).'<br>';
				$this->_sgidNormal = intval($serverGroup['sgid']);
			}
			else if ($serverGroup['type'] == self::PERM_GROUP_DB_TYPE_REGULAR AND $serverGroup['iconid'] == 0 AND $serverGroup['savedb'] == 0) // AND $serverGroup['name'] == 'Guest'
			{
				// echo 'GUEST '.intval($serverGroup['sgid']).'<br>';
				$this->_sgidGuest = intval($serverGroup['sgid']);
			}
		}
		return true;
	} // function initServerGroups

	public function initChannelGroups()
	{
		if ($this->_version == self::VERSION_TS2)
			return true;
		$channelGroups = $this->_tssclass->channelGroupList();
		if ($channelGroups !== false)
		foreach ($channelGroups as $channelGroup)
		{
			/* channelgrouplist
			 * cgid=1 name=Channel\sAdmin type=0 iconid=100 savedb=1
			 * cgid=2 name=Operator type=0 iconid=200 savedb=1
			 * cgid=3 name=Voice type=0 iconid=0 savedb=0
			 * cgid=4 name=Guest type=0 iconid=0 savedb=0
			 * cgid=5 name=Channel\sAdmin type=1 iconid=100 savedb=1
			 * cgid=6 name=Operator type=1 iconid=200 savedb=1
			 * cgid=7 name=Voice type=1 iconid=0 savedb=0
			 * cgid=8 name=Guest type=1 iconid=0 savedb=0
			 */
			if ($channelGroup['type'] == self::PERM_GROUP_DB_TYPE_REGULAR AND $channelGroup['iconid'] == 100 AND $channelGroup['savedb'] == 1) // AND $channelGroup['name'] == 'Channel Admin'
			{
//				 echo 'ADMIN '.intval($channelGroup['cgid']).'<br>';
				$this->_cgidAdmin = intval($channelGroup['cgid']);
			}
			else if ($channelGroup['type'] == self::PERM_GROUP_DB_TYPE_REGULAR AND $channelGroup['iconid'] == 200 AND $channelGroup['savedb'] == 1) // AND $channelGroup['name'] == 'Operator'
			{
//				 echo 'OPERATOR '.intval($channelGroup['cgid']).'<br>';
				$this->_cgidOperator = intval($channelGroup['cgid']);
			}
			else if ($channelGroup['type'] == self::PERM_GROUP_DB_TYPE_REGULAR AND $channelGroup['iconid'] == 0 AND $channelGroup['savedb'] == 0 AND $channelGroup['name'] == 'Voice')
			{
//				 echo 'VOICE '.intval($channelGroup['cgid']).'<br>';
				$this->_cgidVoice = intval($channelGroup['cgid']);
			}
			else if ($channelGroup['type'] == self::PERM_GROUP_DB_TYPE_REGULAR AND $channelGroup['iconid'] == 0 AND $channelGroup['savedb'] == 0 AND $channelGroup['name'] == 'Guest')
			{
//				 echo 'GUEST '.intval($channelGroup['cgid']).'<br>';
				$this->_cgidGuest = intval($channelGroup['cgid']);
			}
		}
		return true;
	} // function initChannelGroups
	
	/**
	 * Set or get the currently defined server groups for admin, normal and guest
	 * 
	 * @param	array	$groups	null or groups array of group ID's in order admin, normal, guest
	 * 
	 * @return array groups array of group ID's in order admin, normal, guest
	 */
	public function tssServerGroups($groups = null)	
	{
		if (is_array($groups))
		{
			$this->_sgidAdmin = intval($groups[0]);
			$this->_sgidNormal = intval($groups[1]);
			$this->_sgidGuest = intval($groups[2]);
		}
		return array(0 => $this->_sgidAdmin, 1 => $this->_sgidNormal, 2 => $this->_sgidGuest);
	} // function tssServerGroups
	
	/**
   * Returns TS server version
	 *
   * @return tss::VERSION_TS2 or tss::VERSION_TS3
   */
	public function tssVersion()
	{
		return $this->_version;
	} // function tssVersion

	/**
   * If you want to do some specific stuff, get the class
	 *
	 * @return cyts or ts3admin class object or null
   */
	public function tssClass()
	{
		return $this->_tssclass;
	} // function tssClass
	
	/**
	 * Get the protocol for this version of the teamspeak server
   */
  public function protocol()
	{
		switch ($this->_version)
		{
		default:
		case self::VERSION_TS2:
			return 'teamspeak';
			break;
		case self::VERSION_TS3:
			return 'ts3server';
			break;
		}
	} // function protocol
	
	//
	// PHGSTATS COMPATIBILITY
	//
	
	public function getstream($addr, $udp = null, $tcp = null, $timeout = 2)
	{
		
		return $this->connect($addr, $tcp, $udp, $timeout);
	} // function getstream
	
	public function getrules($phgdir)
	{
		$srv_rules['sets'] = false;

		$srv_rules['mapname'] = 'Teamspeak'.($this->_version == self::VERSION_TS2 ? '2' : '3');
		$srv_rules['map_path'] = 'maps/ts';
		$srv_rules['map_default'] = 'default.jpg';

		$sInfo = $this->serverInfo();
		// ts setting pics
		$sets['pass'] = cs_html_img('mods/servers/privileges/pass.gif',0,0,0,'Pass');
		// server hostname
		$srv_rules['hostname'] = cs_encode($sInfo['virtualserver_name'], $this->_charset);
		// server version
		$srv_rules['version'] = cs_encode($sInfo['virtualserver_version'], $this->_charset);
		$srv_rules['gamename'] = 'Teamspeak';
		// server channels
		$srv_rules['channels'] = $sInfo['virtualserver_channelsonline'];

		// response time TODO
		$srv_rules['response'] = '-';

		// server type
		if ($sInfo['virtualserver_clan_server'] == 1)
		{
			$srv_rules['gametype'] = 'Clanserver';
		}
		else
		{
			$srv_rules['gametype'] = 'Publicserver';
		}

		// server password
		$srv_rules['needpass'] = false;
		if ($sInfo['virtualserver_flag_password'] == 1)
			$srv_rules['needpass'] = true;

		// players
		$srv_rules['nowplayers'] = ($sInfo['virtualserver_clientsonline'] - $sInfo['virtualserver_queryclientsonline']);
		$srv_rules['maxplayers'] = $sInfo['virtualserver_maxclients'];

		if ($sInfo['virtualserver_flag_password'] == 1)
		{
			$srv_rules['sets'] .= $sets['pass'];
		}
		else
		{
			$srv_rules['sets'] = '-';
		}
		// return all server rules
		return $srv_rules;
	} // function getrules
	
	public function getplayers_head()
	{
		global $cs_lang;
		$head[]['name'] = $cs_lang['id'];
		$head[]['name'] = $cs_lang['name'];
		$head[]['name'] = $cs_lang['channel'];
		$head[]['name'] = $cs_lang['privileg'];
		$head[]['name'] = $cs_lang['ping'];
		return $head;
	} // function getplayers_head
	
	public function getplayers()
	{
		$players = 0;
		$tdata = '';

		$player = $this->clientList();

		$run=0;
		foreach ($player as $player_info)
		{
			/* don't show serverquery clients */
			if ($player_info['client_type'] == self::TYPE_QUERY_CLIENT)
				continue;
			$tdata[$run][0] = '<td class="centerb">' . $player_info['clid'] . '</td>';
			$tdata[$run][0] .= '<td class="centerb">' . $player_info['client_nickname'] . '</td>';
			$channel = $this->channelInfo( $player_info['cid']);
			$tdata[$run][0] .= '<td class="centerb">' . cs_encode($channel['channel_name'], $this->_charset) . '</td>';
			$tdata[$run][0] .= '<td class="centerb">' . $this->clientFlagsGlobal($player_info['client_flags_global'], $player_info['client_flags_channel'], self::FLAG_PLAYER_MODE_GLOBAL_TEXT) . '</td>';
			$tdata[$run][0] .= '<td class="centerb">' . $player_info['client_ping'] . '</td>';
			$run++;
		}
			
		if ($run == 0)
		{
			return array();
		}
		return $tdata;
	} // function getplayers
	
	//
	// END PHGSTATS COMPATIBILITY
	//
} // class tss

