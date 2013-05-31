<?php
/*
 * WebSpell to ClanSphere converter class
 */

class ClanSphere_Convert_Webspell
{
	/*
	 * Webspell version
	 * 0 = 4.01
	 * 1 = 4.02
	 */
	protected $_ws_version = 0;
	/*
   * configuration settting for the convert:
   * db_webspell = db connection data webspell
	 *   user, pass, host, type, charset
	 */
	protected $_settings = null;
	/*
   * db class to WebSpell
	 */
	protected $_db = null;
	/*
   * perform a fake run
   */
  protected $_fake = false;
	/*
	 *
	 */
  protected $_quiet = true;
	/*
   * perform a fake run
   */
  protected $_url = 'http://localhost/';
 	/*
   * error messages
   */
  protected $_errors = array();
  protected $_errornum = 0;
	/*
   * statistics
   */
  protected $_statistics = array('users' => 0, 'squads' => 0, 'members' => 0,
																 'games' => 0, 'wars' => 0, 'clans' => 0,
																 'news' => 0, 'board' => 0, 'categories' => 0,
																 'threads' => 0);
  /*
   * image extensions
   */
	protected $_extensions = array(1 => '.gif', 2 => '.jpg', 3 => '.png');
	/*
	/*
   * incoming charset 
   */
	protected $_charset = 'ISO-8859-15';
	/*
   *
   */
  protected $_prefix = 'ws_';
  /*
   * 
   */
  protected $_hidden = '';

	/*
   * The admin user id for both systems
   */

	const USER_ADMIN = 1;
	/*
   * user mapping PREFIX_user.userID => {pre}_users.users_id
	 * initially map the admins
   */
	protected $_usermap = array(1 => 1);
	/* users_nick => users_id */
	protected $_usernick = array();
	/* users_email => users_email */
	protected $_useremail = array();
	protected $_countries = array();

	/* squad mapping */
	protected $_squadmap = array();
	/* squads_name => squads_id */
	protected $_squadname = array();

	/* member mapping */
	protected $_membermap = array();

	/* game mapping */
	protected $_gamemap = array();
	/* games_name => games_id */
	protected $_gamename = array();
	/* tag => games_id */
	protected $_gametag = array();

	/* war mapping */
	protected $_warmap = array();

	/* clan mapping */
	/* clans_name => clans_id */
	protected $_clanname = array();

	/* news mapping */
	protected $_newsmap = array();
	protected $_abcode = array(0 => 0, 1 => 0, 2 => 0, 3 => 0, 4 => 0);
	
	/* categories mapping */
	protected $_categoryname = array('news' => array(), 'board' => array());

	/* board info */
	protected $_lastthreadinfo = array(
		'board_last_time' => 0,
		'board_last_user' => '',
		'board_last_userid' => 0,
		'board_last_thread' => '',
		'board_last_threadid' => 0,
	);
	
	/* time of conversion */
	protected $_time = 0;
	
	public function __construct($settings = null)
	{
		$this->_time = cs_time();
		$this->_settings = $settings;
		/* check for fake/test mode setting */
		if (isset($this->_settings['fake']))
			$this->_fake = $this->_settings['fake'];
		if (isset($this->_settings['db']['db_prefix']))
			$this->_prefix = $this->_settings['db']['db_prefix'];
		if (isset($this->_settings['db']['db_charset']))
			$this->_charset = $this->_settings['db']['db_charset'];
		switch ($this->_settings['db']['db_type'])
		{
		default:
		case 'mysql':
			require_once('mods/convert/classes/ConvertDbMysql.php');
			$this->_db = new ClanSphere_Convert_Db_Mysql($this->_settings['db']);
			break;
		}
		if (isset($this->_settings['url']))
			$this->_url = $this->_settings['url'];
		if (!empty($this->_url) && strlen($this->_url) >= 2)
		{
			/* add trailing slash if not present */
			if (substr($this->_url, strlen($this->_url)-1, 1) != '/')
				$this->_url .= '/';
		}
		include('lang/German/countries.php');
		$this->_countries = $cs_country;
		/* we want these to be hidden by default, user privacy */
		$hidden = array();
		$hidden[] = 'users_name';
		$hidden[] = 'users_surname';
		$hidden[] = 'users_height';
		$hidden[] = 'users_age';
		$hidden[] = 'users_postalcode';
		$hidden[] = 'users_place';
		$hidden[] = 'users_adress';
		$hidden[] = 'users_icq';
		$hidden[] = 'users_msn';
		$hidden[] = 'users_skype';
		$hidden[] = 'users_phone';
		$hidden[] = 'users_mobile';
		$hidden[] = 'users_email';
		$this->_hidden = implode(',', $hidden);
	} // constructor

	public function convertNews()
	{
		global $cs_main;
		
		if (!$this->_db->connected())
		{
			$this->error('Convert news: no db');
			return;
		}

		$cs_option = cs_sql_option(__FILE__, 'news');
		$this->_abcode = explode(',', $cs_option['abcode']);

		$this->_statistics['news'] = 0;
		/* first map all news categores of the current news */
		$currentCategories = cs_sql_select(__FILE__, 'categories', 'categories_id, categories_name', 'categories_mod = \'news\'', 0, 0, 0);
		if (count($currentCategories) > 0)
		{
			foreach ($currentCategories as $currentCategory)
			{
				$this->_categoryname['news'][mb_strtolower($currentCategory['categories_name'], $cs_main['charset'])] = $currentCategory['categories_id'];
			}
		}

		/* select all WS news */
		$query = 'SELECT nw.*, nr.rubric AS rubricname FROM '.$this->_prefix.'news nw LEFT JOIN '.$this->_prefix.'news_rubrics nr ON nr.rubricID = nw.rubric WHERE 1 = 1 ORDER BY nw.newsID ASC';
		$result = $this->_db->db_query($query);
		if ($result === false)
		{
			$this->error('Convert news: query ['.$query.'] failed');
			return;
		}

		/* try to insert all WS news */
		while (($record = $this->_db->db_fetch_assoc($result)) !== false)
		{
			$newNews = $this->getNews($record);
			if (is_array($newNews))
			{
				if ($this->_fake)
					$newNewsId = $record['newsID'];
				else
				{
					cs_sql_insert(__FILE__, 'news', array_keys($newNews), array_values($newNews));
					$newNewsId = cs_sql_insertid(__FILE__);
				}
				/* add to member map */
				$this->_newsmap[$record['newsID']] = $newNewsId;
				$this->getNewsImages($newNewsId, $record['newsID'], $record);
				$this->_statistics['news']++;
			}
			unset($record);
		}
		$this->_db->db_free_result($result);
	} // function convertNews
	
	public function convertWars()
	{
		global $cs_main;

		if (!$this->_db->connected())
		{
			$this->error('Convert wars: no db');
			return;
		}

		$this->_statistics['wars'] = 0;
		$this->_statistics['clans'] = 0;
		/* first map all clan names of the current clans */
		$currentClans = cs_sql_select(__FILE__, 'clans', 'clans_id, clans_name', '1 = 1', 0, 0, 0);
		if (count($currentClans) > 0)
		{
			foreach ($currentClans as $currentClan)
			{
				$this->_clanname[mb_strtolower($currentClan['clans_name'], $cs_main['charset'])] = $currentClan['clans_id'];
			}
		}

		/* select all WS wars */
		$query = 'SELECT * FROM '.$this->_prefix.'clanwars WHERE 1 = 1 ORDER BY cwID ASC';
		$result = $this->_db->db_query($query);
		if ($result === false)
		{
			$this->error('Convert wars: query ['.$query.'] failed');
			return;
		}

		/* try to insert all WS wars */
		while (($record = $this->_db->db_fetch_assoc($result)) !== false)
		{
			$newWar = $this->getWar($record);
			if (is_array($newWar))
			{
				if ($this->_fake)
					$newWarId = $record['cwID'];
				else
				{
					cs_sql_insert(__FILE__, 'wars', array_keys($newWar), array_values($newWar));
					$newWarId = cs_sql_insertid(__FILE__);
				}
				/* add to member map */
				$this->_warmap[$record['cwID']] = $newWarId;
				$this->getWarImages($newWarId, $record['cwID'], $record); // add screens
				$this->getWarPlayers($newWarId, $record['cwID'], $record); // add players
				$this->_statistics['wars']++;
			}
			unset($record);
		}
		$this->_db->db_free_result($result);
	} // function convertWars

	public function convertGames()
	{
		if (!$this->_db->connected())
		{
			$this->error('Convert games: no db');
			return;
		}

		$this->_statistics['games'] = 0;
		/* first map all game names of the current games */
		$currentGames = cs_sql_select(__FILE__, 'games', 'games_id, games_name', '1 = 1', 0, 0, 0);
		if (count($currentGames) > 0)
		{
			foreach ($currentGames as $currentGame)
			{
				$this->_gamename[$currentGame['games_name']] = $currentGame['games_id'];
			}
		}

		/* select all WS wars */
		$query = 'SELECT * FROM '.$this->_prefix.'games WHERE 1 = 1 ORDER BY gameID ASC';
		$result = $this->_db->db_query($query);
		if ($result === false)
		{
			$this->error('Convert games: query ['.$query.'] failed');
			return;
		}

		/* try to insert all WS games */
		while (($record = $this->_db->db_fetch_assoc($result)) !== false)
		{
			$newGame = $this->getGame($record);
			if (is_array($newGame))
			{
				if ($this->_fake)
					$newGameId = $record['gameID'];
				else
				{
					cs_sql_insert(__FILE__, 'games', array_keys($newGame), array_values($newGame));
					$newGameId = cs_sql_insertid(__FILE__);
				}
				/* add to member map */
				$this->_gamemap[$record['gameID']] = $newGameId;
				$this->_gamename[$newGame['games_name']] = $newGameId;
				$this->_gametag[$record['tag']] = $newGameId;
				/* try to get image */
				$this->getGameImage($newGameId, $record['gameID'], $record);
				$this->_statistics['games']++;
			}
			unset($record);
		}
		$this->_db->db_free_result($result);
	} // function convertGames

	public function convertMembers()
	{
		if (!$this->_db->connected())
		{
			$this->error('Convert members: no db');
			return;
		}

		$this->_statistics['members'] = 0;

		/* select all WS squads */
		$query = 'SELECT * FROM '.$this->_prefix.'squads_members WHERE 1 = 1 ORDER BY sqmID ASC';
		$result = $this->_db->db_query($query);
		if ($result === false)
		{
			$this->error('Convert members: query ['.$query.'] failed');
			return;
		}
		
		/* try to insert all WS members */
		while (($record = $this->_db->db_fetch_assoc($result)) !== false)
		{
			$newMember = $this->getMember($record);
			if (is_array($newMember))
			{
				if (!$this->_fake)
				{
					cs_sql_insert(__FILE__, 'members', array_keys($newMember), array_values($newMember));
					$newMemberId = cs_sql_insertid(__FILE__);
					/* update access for this user to level 3 (member) */
					cs_sql_update(__FILE__, 'users', array('users_access'), array(3), $this->_usermap[$record['userID']]);
				}
				/* add to member map */
				$this->_membermap[$this->_squadmap[$record['squadID']]][] = $this->_usermap[$record['userID']];
				$this->_statistics['members']++;
			}
			unset($record);
		}
		$this->_db->db_free_result($result);

	} // function convertMembers
	
	/*
	 * 
	 */
	public function convertSquads()
	{
		if (!$this->_db->connected())
		{
			$this->error('Convert squads: no db');
			return;
		}

		$this->_statistics['squads'] = 0;
		/* first map all squad names of the current squads */
		$currentSquads = cs_sql_select(__FILE__, 'squads', 'squads_id, squads_name', '1 = 1', 0, 0, 0);
		if (count($currentSquads) > 0)
		{
			foreach ($currentSquads as $currentSquad)
			{
				$this->_squadname[$currentSquad['squads_name']] = $currentSquad['squads_id'];
			}
		}
		
		/* select all WS squads */
		$query = 'SELECT * FROM '.$this->_prefix.'squads WHERE 1 = 1 ORDER BY squadID ASC';
		$result = $this->_db->db_query($query);
		if ($result === false)
		{
			$this->error('Convert squads: query ['.$query.'] failed');
			return;
		}

		/* try to insert all WS squads */
		while (($record = $this->_db->db_fetch_assoc($result)) !== false)
		{
			$newSquad = $this->getSquad($record);
			if (is_array($newSquad))
			{
				if ($this->_fake)
					/* fake the user id, we need this for other stuff */
					$newSquadId = $record['squadID'];
				else
				{
					cs_sql_insert(__FILE__, 'squads', array_keys($newSquad), array_values($newSquad));
					$newSquadId = cs_sql_insertid(__FILE__);
				}
				/* keep track of the user id mapping */
				$this->_squadmap[$record['squadID']] = $newSquadId;
				/* try to get image */
				$this->getSquadImage($this->_squadmap[$record['squadID']], $record['squadID'], $record);
				$this->_squadname[$newSquad['squads_name']] = $newSquadId;
				$this->_statistics['squads']++;
			}
			unset($record);
		}
		$this->_db->db_free_result($result);

	} // function convertSquads
	
	/*
	 * @return void
	 */
	public function convertUsers()
	{
		if (!$this->_db->connected())
		{
			$this->error('Convert users: no db');
			return;
		}
		
		$this->_statistics['users'] = 0;
		/* first map all nicks and emails of the current users */
		$currentUsers = cs_sql_select(__FILE__, 'users', 'users_id, users_nick, users_email', '1 = 1', 0, 0, 0);
		if (count($currentUsers) > 0)
		{
			foreach ($currentUsers as $currentUser)
			{
				$this->_usernick[$currentUser['users_nick']] = $currentUser['users_id'];
				$this->_useremail[$currentUser['users_email']] = $currentUser['users_id'];
			}
		}
		
		/* select all WS users */
		$query = 'SELECT * FROM '.$this->_prefix.'user WHERE userID > 1 ORDER BY userID ASC';
		$result = $this->_db->db_query($query);
		if ($result === false)
		{
			$this->error('Convert users: query ['.$query.'] failed');
			return;
		}
		/* try to insert all WS users */
		while (($record = $this->_db->db_fetch_assoc($result)) !== false)
		{
			$newUser = $this->getUser($record);
			if (is_array($newUser))
			{
				if ($this->_fake)
					/* fake the user id, we need this for other stuff */
					$this->_usermap[$record['userID']] = $record['userID'];
				else
				{
					cs_sql_insert(__FILE__, 'users', array_keys($newUser), array_values($newUser));
					$newUserId = cs_sql_insertid(__FILE__);
					/* keep track of the user id mapping */
					$this->_usermap[$record['userID']] = $newUserId;
				}
				$this->_usernick[$newUser['users_nick']] = $this->_usermap[$record['userID']];
				$this->_useremail[$newUser['users_email']] = $this->_usermap[$record['userID']];
				/* try to get images */
				$this->getUserImages($this->_usermap[$record['userID']], $record['userID'], $record);
				$this->_statistics['users']++;
			}
			unset($record);
		}
		$this->_db->db_free_result($result);
	} // function converUsers

	/*
	 * @return void
	 */
	public function convertForums()
	{
		global $cs_main;
		
		if (!$this->_db->connected())
		{
			$this->error('Convert users: no db');
			return;
		}

		$fcats = array();

		/* first map all forum categores of the current board */
		$currentCategories = cs_sql_select(__FILE__, 'categories', 'categories_id, categories_name', 'categories_mod = \'board\'', 0, 0, 0);
		if (count($currentCategories) > 0)
		{
			foreach ($currentCategories as $currentCategory)
			{
				$this->_categoryname['board'][mb_strtolower($currentCategory['categories_name'], $cs_main['charset'])] = $currentCategory['categories_id'];
			}
		}

		/* select all WS forum categories first */
		$query = 'SELECT * FROM '.$this->_prefix.'forum_categories ORDER BY name ASC';
		$result = $this->_db->db_query($query);
		if ($result === false)
		{
			$this->error('Convert forums: query ['.$query.'] failed');
			return;
		}
		
		/* try to insert all WS forum categories */
		while (($record = $this->_db->db_fetch_assoc($result)) !== false)
		{
			$catname = $this->convert($record['name'], 'string', '80');
			/* we can't have duplicate forum category names */
			if (isset($this->_categoryname['board'][mb_strtolower($catname, $cs_main['charset'])]))
				$catname = $this->convert($record['catID'].'-'.$record['name'], 'string', '80');
			/* add category to CS and keep track of the new ID */
			$fcats[$record['catID']] = $this->addCategory('board', $catname, intval($record['sort']), (intval($record['intern']) == 1 ? 3 : 0));
			unset($record);
		}
		$this->_db->db_free_result($result);
		
		/* select all WS forum boards */
		$query = 'SELECT * FROM '.$this->_prefix.'forum_boards ORDER BY category ASC, name ASC';
		$result = $this->_db->db_query($query);
		if ($result === false)
		{
			$this->error('Convert forums: query ['.$query.'] failed');
			return;
		}
		/* try to insert all WS forum boards */
		while (($record = $this->_db->db_fetch_assoc($result)) !== false)
		{
			$newBoard = $this->getBoard($record, $fcats);
			if (is_array($newBoard))
			{
				if ($this->_fake)
					$newBoardId = $record['boardID'];
				else
				{
					cs_sql_insert(__FILE__, 'board', array_keys($newBoard), array_values($newBoard));
					$newBoardId = cs_sql_insertid(__FILE__);
				}
				/* insert all topics (and posts) and announcements of this board */
				$this->_lastThreadInfo = array(
					'board_last_time' => 0,
					'board_last_user' => '',
					'board_last_userid' => 0,
					'board_last_thread' => '',
					'board_last_threadid' => 0,
				);
				$this->getBoardTopics($newBoardId, $record, $fcats);
				
				if (!$this->_fake)
				{
					cs_sql_update(__FILE__, 'board', array_keys($this->_lastThreadInfo), array_values($this->_lastThreadInfo), $newBoardId);
				}
				$this->_statistics['board']++;
			}
			unset($record);
		}
		$this->_db->db_free_result($result);
		
	} // function convertForums
		
	/*
   *
   */
	public function connect()
	{
		$result = $this->_db->db_connect();
		$errors = $this->_db->getErrors();
		foreach ($errors as $error)
		{
			$this->error($error['message']);
		}
		$this->_db->clearErrors();
		return $result;
	} // function connect

	/*
	 * 
	 */
	public function disconnect()
	{
		$result = $this->_db->db_disconnect();
		$errors = $this->_db->getErrors();
		foreach ($errors as $error)
		{
			$this->error($error['message']);
		}			
		$this->_db->clearErrors();
		return $result;
	} // function disconnect
	
	/*
	 * @return int
	 */
	public function getPictureExtension($picture_name, $allowed = array(1,2,3))
	{
		$pos = strrpos($picture_name, '.');
		if ($pos === false)
		{
			return 0;
		}
		$picture_extension = strtolower(substr($picture_name, $pos));
		switch ($picture_extension)
		{
		case '.gif':
			if (in_array(1, $allowed))
				return 1;
			break;
		case '.jpeg':
		case '.jpg':
			if (in_array(2, $allowed))
				return 2;
		case '.png':
			if (in_array(3, $allowed))
				return 3;
			break;
		}
		return 0;
	} // function getPictureExtension

	public function addClan($record)
	{
		global $cs_main;

		$clans_url = $this->convert($record['opphp'], 'string', 80);
		if (mb_strtolower(mb_substr($clans_url, 0, 7, $cs_main['charset']), $cs_main['charset']) == 'http://')
			$clans_url = substr($clans_url, 7);
		$clan_map = array(
			'users_id' => 1, // admin
			'clans_name' => $this->convert($record['opponent'], 'string', 200),
			'clans_short' => $this->convert($record['opptag'], 'string', 20),
			'clans_tag' => $this->convert($record['opptag'], 'string', 40),
			'clans_tagpos' => 0,
			'clans_country' => $this->convert($record['oppcountry'], 'country'),
			'clans_url' => $clans_url,
			'clans_since' => '',
			'clans_picture' => '',
			'clans_pwd' => '',
		);
		
		if ($this->_fake)
			$newClanId = $this->_statistics['clans'] + 1;
		else
		{
			cs_sql_insert(__FILE__, 'clans', array_keys($clan_map), array_values($clan_map));
			$newClanId = cs_sql_insertid(__FILE__);
		}
		$this->_clanname[mb_strtolower($clan_map['clans_name'], $cs_main['charset'])] = $newClanId;
		$this->_statistics['clans']++;
		
		return $newClanId;

	} // function addClan

	public function addCategory($mod, $name, $order = 0, $access = 0)
	{
		global $cs_main;
		
		if ($this->_fake)
			$newCategoriesId = $this->_statistics['categories'] + 1;
		else
		{
			if (!empty($this->_categoryname[$mod][mb_strtolower($name, $cs_main['charset'])]))
			{
				return $this->_categoryname[$mod][mb_strtolower($name, $cs_main['charset'])];
			}
			else
			{
				cs_sql_insert(__FILE__, 'categories', array('categories_mod', 'categories_name', 'categories_order', 'categories_access'), array($mod, $name, $order, $access));
				$newCategoriesId = cs_sql_insertid(__FILE__);
			}
		}
		$this->_categoryname[$mod][mb_strtolower($name, $cs_main['charset'])] = $newCategoriesId;
		$this->_statistics['categories']++;
		
		return $newCategoriesId;
	} // function addCategory
	
	public function getBoardTopicPosts($newBoardId, $newThreadId, $brecord, $trecord, $fcats)
	{
		global $cs_main;
		
		$query = 'SELECT * FROM '.$this->_prefix.'forum_posts WHERE boardID = '.intval($brecord['boardID']).' AND topicID = '.intval($trecord['topicID']).' ORDER BY date ASC';
		$result = $this->_db->db_query($query);
		if ($result === false)
		{
			$this->error('Convert board topic posts: query ['.$query.'] failed');
			return;
		}
		$total_posts = 0;
		while (($record = $this->_db->db_fetch_assoc($result)) !== false)
		{
			/* check if poster was not mapped */
			if (!isset($this->_usermap[$record['poster']]))
			{
					if (!$this->_quiet)
						$this->error('Poster for WS post #'.$trecord['topicID'].' with userID #'.$record['poster'].' not found. Mapping to admin #1');
					$poster = 1;
			}
			else
				$poster = $this->_usermap[$record['poster']];

			$total_posts++;
			if ($total_posts == 1)		
			{
				/* first post is the topic post, update thread info an skip it */
				$thread = array();
				$thread['threads_text'] = $this->bbToAbcode($this->convert($record['message'], 'clob', 65000), 'board');
				if (!$this->_fake)
				{
					cs_sql_update(__FILE__, 'threads', array_keys($thread), array_values($thread), $newThreadId);
				}
				continue;
			}

			$post_map = array(
				'users_id' => $poster,
				'comments_fid' => $newThreadId,
				'comments_mod' => 'board',
				'comments_ip' => '',
				'comments_time' => $this->convert($record['date'], 'integer', 14),
				'comments_text' => $this->bbToAbcode($this->convert($record['message'], 'clob', 65000), 'board'),
				'comments_edit' => '',
				'comments_guestnick' => '',
			);
			
			if (!$this->_fake)
			{
		  	cs_sql_insert(__FILE__,'comments',array_keys($post_map),array_values($post_map));
			}
			unset($record);
		}
		
		$this->_db->db_free_result($result);
	} // function getBoardTopicPosts
	
	public function getBoardTopics($newBoardId, $brecord, $fcats)
	{
		global $cs_main;
		
		$query = 'SELECT * FROM '.$this->_prefix.'forum_topics WHERE boardID = '.intval($brecord['boardID']);
		$result = $this->_db->db_query($query);
		if ($result === false)
		{
			$this->error('Convert board topics: query ['.$query.'] failed');
			return;
		}
		/* try to insert all WS forum topics */
		$total_topics = 0;
		$total_posts = 0;
		while (($record = $this->_db->db_fetch_assoc($result)) !== false)
		{
			/* check if poster was not mapped */
			if (!isset($this->_usermap[$record['userID']]))
			{
					if (!$this->_quiet)
						$this->error('Poster for WS topic #'.$record['topicID'].' with userID #'.$record['userID'].' not found. Mapping to admin #1');
					$poster = 1;
			}
			else
				$poster = $this->_usermap[$record['userID']];
			if (!isset($this->_usermap[$record['lastposter']]))
			{
					if (!$this->_quiet)
						$this->error('Last poster for WS topic #'.$record['topicID'].' with userID #'.$record['lastposter'].' not found. Mapping to admin #1');
					$lastposter = 1;
			}
			else
				$lastposter = $this->_usermap[$record['lastposter']];
			$thread_map = array(
				'board_id' => $newBoardId,
				'users_id' => $poster,
				'threads_headline' => $this->convert($record['topic'], 'string', 200),
				'threads_text' => '', /* to be filled in later, it's the first forum post */
				'threads_time' => $this->convert($record['date'], 'integer', 14),
				'threads_view' => $this->convert($record['views'], 'integer', 8),
				'threads_important' => 0,
				'threads_close' => ($record['closed'] == 1 ? -1 : 0),
				'threads_edit' => 0,
				'threads_last_user' => $lastposter,
				'threads_last_time' => $this->convert($record['lastdate'], 'integer', 14),
				'threads_comments' => $this->convert($record['replys'], 'integer', 8), /* number of comments exclusive original topic/thread */
				'threads_ghost' => 0,
				'threads_ghost_board' => 0,
				'threads_ghost_thread' => 0,
			);

			$total_topics++;
			$total_posts += $this->convert($record['replys'], 'integer', 8);
			
			if ($this->_fake)
				$newThreadId = $record['topicID'];
			else
			{
				cs_sql_insert(__FILE__, 'threads', array_keys($thread_map), array_values($thread_map));				
				$newThreadId = cs_sql_insertid(__FILE__);
			}
			$this->_statistics['threads']++;

			if ($thread_map['threads_last_time'] > $this->_lastThreadInfo['board_last_time'])
			{
				$this->_lastThreadInfo['board_last_time'] = $thread_map['threads_last_time'];
				$userNick = array_search($thread_map['threads_last_user'], $this->_usernick);
				$this->_lastThreadInfo['board_last_user'] = ($userNick === false ? '???' : $userNick);
				$this->_lastThreadInfo['board_last_userid'] = $thread_map['threads_last_user'];
				$this->_lastThreadInfo['board_last_thread'] = $thread_map['threads_headline'];
				$this->_lastThreadInfo['board_last_threadid'] = $newThreadId;
			}
			
			/* insert all posts of this board */
			$this->getBoardTopicPosts($newBoardId, $newThreadId, $brecord, $record, $fcats);

			unset($record);
		}
		$this->_db->db_free_result($result);
		
		$query = 'SELECT * FROM '.$this->_prefix.'forum_announcements WHERE boardID = '.intval($brecord['boardID']);
		$result = $this->_db->db_query($query);
		if ($result === false)
		{
			$this->error('Convert board announcements: query ['.$query.'] failed');
			return;
		}
		/* try to insert all WS forum announcements */
		while (($record = $this->_db->db_fetch_assoc($result)) !== false)
		{
			/* check if poster was not mapped */
			if (!isset($this->_usermap[$record['userID']]))
			{
					if (!$this->_quiet)
						$this->error('Poster for WS announcement #'.$record['announceID'].' with userID #'.$record['userID'].' not found. Mapping to admin #1');
					$poster = 1;
			}
			else
				$poster = $this->_usermap[$record['userID']];
			$thread_map = array(
				'board_id' => $newBoardId,
				'users_id' => $poster,
				'threads_headline' => $this->convert($record['topic'], 'string', 200),
				'threads_text' => $this->bbToAbcode($this->convert($record['announcement'], 'clob', 65000), 'board'),
				'threads_time' => $this->convert($record['date'], 'integer', 14),
				'threads_view' => 0,
				'threads_important' => 1,
				'threads_close' => -1,
				'threads_edit' => 0,
				'threads_last_user' => 0,
				'threads_last_time' => 0,
				'threads_comments' => 0,
				'threads_ghost' => 0,
				'threads_ghost_board' => 0,
				'threads_ghost_thread' => 0,
			);

			$total_topics++;
			
			if ($this->_fake)
				$newThreadId = $record['announceID'];
			else
			{
				cs_sql_insert(__FILE__, 'threads', array_keys($thread_map), array_values($thread_map));				
				$newThreadId = cs_sql_insertid(__FILE__);
			}
			$this->_statistics['threads']++;
			
			unset($record);
		}
		$this->_db->db_free_result($result);

		/* update board info */
		if (!$this->_fake)
		{
			cs_sql_update(__FILE__, 'board', array('board_threads', 'board_comments'), array($total_topics, $total_posts), $newBoardId);
		}

	} // function getBoardTopics
	
	public function getBoard($record, $fcats)
	{
		global $cs_main;
		
		$board_map = array(
			'categories_id' => $fcats[$record['category']],
			'users_id' => 1, // the admin always created the board
			'squads_id' => 0,
			'board_access' => (intval($record['intern']) == 1 ? 3 : 0),
			'board_name' => $this->convert($record['name'], 'string', 80),
			'board_text' => $this->convert($record['info'], 'string', 200),
			'board_time' => $this->_time,
			'board_pwd' => '',
			'board_threads' => 0,
			'board_comments' => 0,
			'board_order' => intval($record['sort']),
			'board_read' => 0,
			'board_last_time' => 0,
			'board_last_user' => '',
			'board_last_userid' => 0,
			'board_last_thread' => '',
			'board_last_threadid' => 0,
		);

		return $board_map;
	} // function getBoard
	
	public function getNewsImages($news_id, $newsID, $record)
	{
		$screenurl = $this->_url.'images/news-pics/';

		$count = 1;
		if (!empty($record['screens']) && file_exists('uploads/news'))
		{
			$screens= explode('|', $record['screens']);
			$pictures = array();
			foreach ($screens as $screen)
			{
				if (empty($screen))
					continue;
				$ext = $this->getPictureExtension($screen);
				if ($ext > 0)
				{
					$screenpicture = $screenurl.$screen;
					$picture_extension = $this->_extensions[$ext];
					$picture_file = @file_get_contents($screenpicture);
					if ($picture_file !== false)
					{
						$new_picture_path = './uploads/news/picture-'.$news_id.'-'.$count.$picture_extension;
						if (!$this->_fake)
						{
							$new_picture_file = file_put_contents($new_picture_path, $picture_file);
							if ($new_picture_file !== false)
							{
								chmod($new_picture_path, 0664);
								/* create thumbnail */
								cs_resample($new_picture_path, 'uploads/news/thumb-'.$news_id.'-'.$count.$picture_extension, 80, 200);
								// no sql update needed yet
								$pictures[$count-1] = $news_id.'-'.$count.$picture_extension;
								$count++;
							}
							else
								if (!$this->_quiet)
									$this->error('Error writing screen file: '.$new_picture_path);
						}
					}
					else
						if (!$this->_quiet)
							$this->error('Screen for WS news #'.$newsID.' could not be found: '.$screenpicture);
				}
				else
					if (!$this->_quiet)
						$this->error('Screen for WS news #'.$newsID.' has invalid extension: '.$screen);
			}
		}
		if ($count > 1)
		{
			$news_pictures = implode("\n",$pictures);
			if (!$this->_fake)
				cs_sql_update(__FILE__, 'news', array('news_pictures'), array($news_pictures), $news_id);
		}
	} // function getNewsImages

	/*
	 * @return array
	 */
	public function getNews($record)
	{
		global $cs_main;
		
		if (!isset($this->_usermap[$record['poster']]))
		{
				if (!$this->_quiet)
					$this->error('Poster for WS news #'.$record['newsID'].' with userID #'.$record['poster'].' not found. Mapping to admin #1');
				$poster = 1;
		}
		else
			$poster = $this->_usermap[$record['poster']];
		$rubric = $this->convert($record['rubricname'], 'string', '80');
		if (isset($this->_categoryname['news'][mb_strtolower($rubric, $cs_main['charset'])]))
			$categories_id = $this->_categoryname['news'][mb_strtolower($rubric, $cs_main['charset'])];
		else
			$categories_id = $this->addCategory('news', $rubric);
		
		switch ($this->_ws_version)
		{
		default:
		case 1:
			/* select all WS news */
			$query = 'SELECT * FROM '.$this->_prefix.'news_content WHERE newsID = '.$record['newsID'].' AND language = '.$this->_settings['news_language'].' LIMIT 0,1';
			$sresult = $this->_db->db_query($query);
			if ($sresult === false)
			{
				$this->error('Convert news: query ['.$query.'] failed');
			}
			else if (($recordContent = $this->_db->db_fetch_assoc($sresult)) !== false)
			{
				$headline = $this->convert($recordContent['headline'], 'string', 80);
				$text = $this->convert($recordContent['content'], 'clob', 65000);
				
				$this->_db->db_free_result($sresult);
			}
			else
			{
				$query = 'SELECT * FROM '.$this->_prefix.'news_content WHERE newsID = '.$record['newsID'].' LIMIT 0,1';
				$sresult = $this->_db->db_query($query);
				if ($sresult === false)
				{
					$this->error('Convert news: query ['.$query.'] failed');
				}
				else if (($recordContent = $this->_db->db_fetch_assoc($sresult)) !== false)
				{
					$headline = $this->convert($recordContent['headline'], 'string', 80);
					$text = $this->convert($recordContent['content'], 'clob', 65000);
					
					$this->_db->db_free_result($sresult);
				}
				else
				{
					$this->error('Convert news: failed obtaining headline and text for newsID #'.$record['newsID']);
					return null;
				}
			}
			break;
		case 0: 
			if ($record['lang1'] == $this->_settings['news_language'] || $record['lang2'] != $this->_settings['news_language'])
			{
				$headline = $this->convert($record['headline1'], 'string', 80);
				$text = $this->convert($record['content1'], 'clob', 65000);
			}
			else
			{
				$headline = $this->convert($record['headline2'], 'string', 80);
				$text = $this->convert($record['content2'], 'clob', 65000);
			}
			break;
		}

		$news_mirror = '';
		$news_mirror_name = '';
		if (!empty($record['link1']) && !empty($record['url1']))
		{
			$news_mirror .= "\n" . $this->convert($record['url1'], 'string', 80);
			$news_mirror_name .= "\n" . $this->convert($record['link1'], 'string', 80);
		}
		if (!empty($record['link2']) && !empty($record['url2']))
		{
			$news_mirror .= "\n" . $this->convert($record['url2'], 'string', 80);
			$news_mirror_name .= "\n" . $this->convert($record['link2'], 'string', 80);
		}
		if (!empty($record['link3']) && !empty($record['url3']))
		{
			$news_mirror .= "\n" . $this->convert($record['url3'], 'string', 80);
			$news_mirror_name .= "\n" . $this->convert($record['link3'], 'string', 80);
		}
		if (!empty($record['link4']) && !empty($record['url4']))
		{
			$news_mirror .= "\n" . $this->convert($record['url4'], 'string', 80);
			$news_mirror_name .= "\n" . $this->convert($record['link4'], 'string', 80);
		}

		$rtext = $this->bbToAbcode($text, 'news');
		if (!empty($cs_main['rte_html']))
		{
			/* we use CKEditor,  */
			$rtext = '[html]'.cs_secure($rtext, $this->_abcode[0], $this->_abcode[1], $this->_abcode[2], $this->_abcode[3], $this->_abcode[4]).'[/html]';
		}
		$news_map = array(
			'categories_id' => $categories_id,
			'users_id' => $poster,
			'news_time' => $this->convert($record['date'], 'integer', 14),
			'news_headline' => $headline,
		  'news_readmore' => '',
		  'news_text' => $rtext,
		  'news_readmore_active' => 0,
			'news_close' => ($record['comments'] == 1 ? 0 : 1),
			'news_public' => ($record['published'] == 1 ? 1 : 0),
			'news_attached' => 0,
			'news_pictures' => '', // not now, we need news_id for this
			'news_publishs_at' => 0,
			'news_mirror' => $news_mirror,
			'news_mirror_name' => $news_mirror_name,
		);
		unset($rtext);

		return $news_map;
 	} // function getNews
	
	public function getWarPlayers($wars_id, $cwID, $record)
	{
		$hometeam = $record['hometeam'];
		if ($this->_ws_version == 0)
		{
			$hometeam = serialize(explode('|', $record['hometeam']));
		}
		
		$players = unserialize($hometeam);
		foreach ($players as $userID)
		{
			if (!isset($this->_usermap[$userID]))
			{
				if (!$this->_quiet)
					$this->error('Player for WS war #'.$cwID.' with userID #'.$userID.' not found');
				continue;
			}
			$players_map = array(
				'users_id' => $this->_usermap[$userID],
				'wars_id' => $wars_id,
				'players_status' => '',
				'players_played' => 1,
				'players_time' => 0
			);
			if (!$this->_fake)
				cs_sql_insert(__FILE__, 'players', array_keys($players_map), array_values($players_map));
		}
	} // function getWarPlayers
	
	public function getWarImages($wars_id, $cwID, $record)
	{
		$screenurl = $this->_url.'images/clanwar-screens/';

		$count = 1;
		if (!empty($record['screens']) && file_exists('uploads/wars'))
		{
			$screens= explode('|', $record['screens']);
			$pictures = array();
			foreach ($screens as $screen)
			{
				if (empty($screen))
					continue;
				$ext = $this->getPictureExtension($screen);
				if ($ext > 0)
				{
					$screenpicture = $screenurl.$screen;
					$picture_extension = $this->_extensions[$ext];
					$picture_file = @file_get_contents($screenpicture);
					if ($picture_file !== false)
					{
						$new_picture_path = './uploads/wars/picture-'.$wars_id.'-'.$count.$picture_extension;
						if (!$this->_fake)
						{
							$new_picture_file = file_put_contents($new_picture_path, $picture_file);
							if ($new_picture_file !== false)
							{
								chmod($new_picture_path, 0664);
								/* create thumbnail */
								cs_resample($new_picture_path, 'uploads/wars/thumb-'.$wars_id.'-'.$count.$picture_extension, 80, 200);
								// no sql update needed yet
								$pictures[$count-1] = $wars_id.'-'.$count.$picture_extension;
								$count++;
							}
							else
								if (!$this->_quiet)
									$this->error('Error writing screen file: '.$new_picture_path);
						}
					}
					else
						if (!$this->_quiet)
							$this->error('Screen for WS war #'.$cwID.' could not be found: '.$screenpicture);
				}
				else
					if (!$this->_quiet)
						$this->error('Screen for WS war #'.$cwID.' has invalid extension: '.$screen);
			}
		}
		if ($count > 1)
		{
			$wars_pictures = implode("\n",$pictures);
			if (!$this->_fake)
				cs_sql_update(__FILE__, 'wars', array('wars_pictures'), array($wars_pictures), $wars_id);
		}
	} // function getWarImages
	
	public function getWar($record)
	{
		global $cs_main;

		if (!isset($this->_squadmap[$record['squad']]))
		{
			if (!$this->_quiet)
				$this->error('War not converted: WS cwID #'.$record['cwID'].': squad not found/converted with WS squadID #'.$record['squad']);
			return null;
		}
		if (!isset($this->_gametag[$record['game']]))
		{
			if (!$this->_quiet)
				$this->error('War not converted: WS cwID #'.$record['cwID'].': game not found/converted with WS game tag "'.$record['game'].'"');
			return null;
		}
		$clans_name = mb_strtolower($this->convert($record['opponent'], 'string', 200), $cs_main['charset']);
		if (!isset($this->_clanname[$clans_name]))
		{
			$clans_id = $this->addClan($clans_name, $record);
		}
		else
			$clans_id = $this->_clanname[$clans_name];

		$homescore = $record['homescore'];
		$oppscore = $record['oppscore'];
		$maps = $record['maps'];
		if ($this->_ws_version == 0)
		{
			$homescore = serialize(explode('||', $record['homescore']));
			$oppscore = serialize(explode('||', $record['oppscore']));
			$maps = serialize(explode('||', $record['maps']));
		}
		$homescr=array_sum(unserialize($homescore));
		$oppscr=array_sum(unserialize($oppscore));
		$mapstext = 'Maps: '.implode(',', unserialize($maps))."\n\n";
		
		$date = $this->convert($record['date'], 'integer', 14);
		$war_map = array(
			'games_id' => $this->_gametag[$record['game']],
			'categories_id' => $this->_settings['cat_wars'],
			'clans_id' => $clans_id,
			'squads_id' => $this->_squadmap[$record['squad']],
			'wars_date' => $date,
			'wars_status' => ($date < cs_time() ? 'played' : 'upcoming'),
			'wars_topmatch' => 0,
			'wars_score1' => $this->convert($homescr, 'integer', 6),
			'wars_score1' => $this->convert($oppscr, 'integer', 6),
			'wars_url' => $this->convert($record['linkpage'], 'string', 80),
			'wars_opponents' => $this->convert($record['oppteam'], 'string', 200),
			'wars_players1' => 0,
			'wars_players2' => 0,
			'wars_report' => $this->convert($mapstext, 'clob', 1000).$this->convert($record['report'], 'clob', 64000),
			'wars_report2' => '',
			'wars_close' => (intval($record['comments']) >= 1 ? 0 : 1),
			'wars_pictures' => '', // later, we need wars_id for this
		);

		return $war_map;

	} // function getWar

	public function getGameImage($games_id, $gameID, $record)
	{
		$iconurl = $this->_url.'images/games/';

		if (!empty($record['img']) && file_exists('uploads/games'))
		{
			$ext = $this->getPictureExtension($record['img'], array(1));
			if ($ext > 0)
			{
				$gameicon = $iconurl.$record['img'];
				$picture_extension = $this->_extensions[$ext];
				$picture_file = @file_get_contents($gameicon);
				if ($picture_file !== false)
				{
					$new_picture_path = './uploads/games/'.$games_id.$picture_extension;
					if (!$this->_fake)
					{
						$new_picture_file = file_put_contents($new_picture_path, $picture_file);
						if ($new_picture_file !== false)
						{
							chmod($new_picture_path, 0664);
							// no sql update needed
						}
						else
							if (!$this->_quiet)
								$this->error('Error writing game icon file: '.$new_picture_path);
					}
				}
				else
					if (!$this->_quiet)
						$this->error('Icon for WS game #'.$gameID.' could not be found: '.$gameicon);
			}
			else
				if (!$this->_quiet)
					$this->error('Icon for WS game #'.$gameID.' has invalid extension: '.$record['img']);
		}
	} // function getGameImage

	/*
   * @return array
   */
	public function getGame($record)
	{
		$games_name = $this->convert($record['name'], 'string', 80);
		if (array_key_exists($games_name, $this->_gamename))
		{
			if (!$this->_quiet)
				$this->error('Mapping WS game #'.$record['gameID'].' to existing CS game #'.$this->_gamename[$games_name].': game name "'.$games_name.'"'); 
			$this->_gamemap[$record['gameID']] = $this->_gamename[$games_name];
			return null;
		}

		$game_map = array(
			'categories_id' => $this->_settings['cat_games'],
			'games_name' => $games_name,
			'games_version' => '',
			'games_released' => '',
			'games_creator' => '',
			'games_url' => '',
			'games_usk' => '',
		);

		return $game_map;
	} // function getGame

	public function getMember($record)
	{
		if (!isset($this->_squadmap[$record['squadID']]))
		{
			if (!$this->_quiet)
				$this->error('Squad member not converted: squad not found/converted with WS squadID #'.$record['squadID']);
			return null;
		}
		$squads_id = $this->_squadmap[$record['squadID']];
		if (!isset($this->_usermap[$record['userID']]))
		{
			if (!$this->_quiet)
				$this->error('Squad member not converted: member not found/converted WS userID #'.$record['userID']);
			return null;
		}
		$users_id = $this->_usermap[$record['userID']];
		/* uniqueness check */
		if (!isset($this->_membermap[$squads_id]))
			$this->_membermap[$squads_id] = array();
		else
		{
			if (in_array($users_id, $this->_membermap[$squads_id]))
			{
				if (!$this->_quiet)
					$this->error('Squad member not converted: duplicate member WS userID #'.$record['userID'].' from WS squadID #'.$record['squadID']);
				return null;
			}
		}

		$member_map = array(
			'squads_id' => $squads_id,
			'users_id' => $users_id,
			'members_task' => $this->convert($record['position'], 'string', 80),
			'members_order' => $this->convert($record['sort'], 'integer', 4),
			'members_since' => '',
			'members_admin' => ($record['joinmember'] == 1 ? 1 : 0), // can administrate members in squad?
		);
		return $member_map;
	} // function getMember
	
	public function getSquadImage($squads_id, $squadID, $record)	
	{
		$iconurl = $this->_url.'images/squadicons/';

		if (!empty($record['icon']) && file_exists('uploads/squads'))
		{
			$ext = $this->getPictureExtension($record['icon']);
			if ($ext > 0)
			{
				$squadicon = $iconurl.$record['icon'];
				$picture_extension = $this->_extensions[$ext];
				$picture_file = @file_get_contents($squadicon);
				if ($picture_file !== false)
				{
					$new_picture_path = './uploads/squads/picture-'.$squads_id.$picture_extension;
					if (!$this->_fake)
					{
						$new_picture_file = file_put_contents($new_picture_path, $picture_file);
						if ($new_picture_file !== false)
						{
							chmod($new_picture_path, 0664);
							cs_sql_update(__FILE__, 'squads', array('squads_picture'), array('picture-'.$squads_id.$picture_extension), $squads_id);
						}
						else
							if (!$this->_quiet)
								$this->error('Error writing squad icon file: '.$new_picture_path);
					}
				}
				else
					if (!$this->_quiet)
						$this->error('Icon for WS squad #'.$squadID.' could not be found: '.$squadicon);
			}
			else
				if (!$this->_quiet)
					$this->error('Icon for WS squad #'.$squadID.' has invalid extension: '.$record['icon']);
		}
 	} // function getSquadImage
	
	public function getSquad($record)
	{
  	$squads_name = $this->convert($record['name'], 'string', 80);
		if (array_key_exists($squads_name, $this->_squadname))
		{
			if (!$this->_quiet)
				$this->error('Mapping WS squad #'.$record['squadID'].' to existing CS squad #'.$this->_squadname[$squads_name].': squad name "'.$squads_name.'"'); 
			$this->_squadmap[$record['squadID']] = $this->_squadname[$squads_name];
			return null;
		}
		
		$games_id = 0;
		if ($this->_ws_version == 1)
		{
			if (count($this->_gamename) > 0)
			{
				if (!empty($record['games']))
				{
					$games = explode(';', $record['games']);
					if (isset($games[0]) && isset($this->_gamename[$games[0]])) // just pick the first
					{
						$games_id = $this->_gamename[$games[0]];
					}
				}
			}
		}

		$squad_map = array(
		 	'clans_id' => 1, // the users clans
		 	'games_id' => $games_id,
		 	'squads_name' => $squads_name,
		 	'squads_picture' => '', // converted later, we need squads_id for this
		 	'squads_order' => $this->convert($record['sort'], 'integer', 4),		
			'squads_pwd' => '',
			'squads_own' => 1, // this squad is from the clan owning the page
			'squads_joinus' => 0, // 0 = ppl can ask to join
			'squads_fightus' => 0, // 0 = ppl can ask to challenge ($record['gamesquad'] == 1 ? 0 : 1),
			'squads_text' => $this->convert($record['info'], 'clob', 65000),
		);
//		$this->error('Squad: '.implode(',', $squad_map));
		return $squad_map;
	} // function getSquad
	
	/*
	 * 
	 */
	public function getUserImages($users_id, $userID, $record)
	{
		$avatarurl = $this->_url.'images/avatars/';
		$pictureurl = $this->_url.'images/userpics/';

		if (!empty($record['avatar']) && file_exists('uploads/board'))
		{
			$ext = $this->getPictureExtension($record['avatar']);
			if ($ext > 0)
			{
				$useravatar = $avatarurl.$record['avatar'];
				$picture_extension = $this->_extensions[$ext];
				$picture_file = @file_get_contents($useravatar);
				if ($picture_file !== false)
				{
					$new_picture_path = './uploads/board/avatar-'.$users_id.$picture_extension;
					if (!$this->_fake)
					{
						$new_picture_file = file_put_contents($new_picture_path, $picture_file);
						if ($new_picture_file !== false)
						{
							chmod($new_picture_path, 0664);
							cs_sql_update(__FILE__, 'users', array('users_avatar'), array('avatar-'.$users_id.$picture_extension), $users_id);
						}
						else
							if (!$this->_quiet)
								$this->error('Error writing picture file: '.$new_picture_path);
					}
				}
				else
					if (!$this->_quiet)
						$this->error('Avatar for WS user #'.$userID.' could not be found: '.$useravatar);
			}
			else
				if (!$this->_quiet)
					$this->error('Avatar for WS user #'.$userID.' has invalid extension: '.$record['avatar']);
		}
		if (!empty($record['userpic']) && file_exists('uploads/users'))
		{
			$ext = $this->getPictureExtension($record['userpic']);
			if ($ext > 0)
			{
				$userpicture = $pictureurl.$record['userpic'];
				$picture_extension = $this->_extensions[$ext];
				$picture_file = @file_get_contents($userpicture);
				if ($picture_file !== false)
				{
					$new_picture_path = './uploads/users/picture-'.$users_id.$picture_extension;
					if (!$this->_fake)
					{
						$new_picture_file = file_put_contents($new_picture_path, $picture_file);
						if ($new_picture_file !== false)
						{
							chmod($new_picture_path, 0664);
							cs_sql_update(__FILE__, 'users', array('users_picture'), array('picture-'.$users_id.$picture_extension), $users_id);
						}
						else
							if (!$this->_quiet)
								$this->error('Error writing picture file: '.$new_picture_path);
					}
				}
				else
					if (!$this->_quiet)
						$this->error('Picture for WS user #'.$userID.' could not be found: '.$userpicture);
			}
			else
				if (!$this->_quiet)
					$this->error('Picture for WS user #'.$userID.' has invalid extension: '.$record['userpic']);
		}
	} // function getUserImages
	
	/* 
   * @return array
	 */
	public function getUser($record)
	{
		/* check WS version, we need this for later */
		if (isset($record['email_hide']))
			$this->_ws_version = 1;

  	$users_email = $this->convert($record['email'], 'string', 255);
		if (array_key_exists($users_email, $this->_useremail))
		{
			if (!$this->_quiet)
				$this->error('Mapping WS user #'.$record['userID'].' to existing CS user #'.$this->_useremail[$users_email].': email "'.$users_email.'"'); 
			$this->_usermap[$record['userID']] = $this->_useremail[$users_email];
			return null;
		}
  	$users_nick = $this->convert($record['username'], 'string', 40);
		if (array_key_exists($users_nick, $this->_usernick))
		{
			if (!$this->_quiet)
				$this->error('User not converted: duplicate user nick "'.$users_nick.'": existing CS user #'.$this->_usernick[$users_nick].', current WS user #'.$record['userID'].': "'.$record['username'].'"');
			return null;
		}
		
		/* some version specific stuff */
		$banned = false;
		$users_lang = '';
		switch ($this->_ws_version)
		{
		default:
		case 1: // WS 4.2
			if ($record['banned'] == 'perm')
				$banned = true;
			else if (intval($record['banned']) > 0) // temporarily
				$banned = true;
			$users_lang = $this->convert($record['language'], 'language');
			break;
		case 0: // WS 4.1
			if ($record['banned'] == 1)
				$banned = true;
			break;
		}

		$user_map = array(
		 	'access_id' => 2, // user		
			'users_nick' => $users_nick,
			'users_pwd' => $this->convert($record['password'], 'string', 40),
			'users_name' => $this->convert($record['firstname'], 'string', 80),
			'users_surname' => $this->convert($record['lastname'], 'string', 80),
			'users_sex' => ($record['sex'] == 'm' ? 'male' : ($record['sex'] == 'f' ? 'female' : '')),
			'users_age' => $this->convert($record['birthday'], 'birthday'),
//			'users_height' => 0,
  		'users_lang' => $users_lang,
  		'users_country' => $this->convert($record['country'], 'country'),
//  		'users_postalcode' => '',
  		'users_place' => $this->convert($record['town'], 'string', 40),
  		'users_adress' => '',
 			'users_icq' => $this->convert($record['icq'], 'integer', 12),
//  		'users_msn' => '',
//  		'users_skype' => '',
			'users_email' => $users_email,
			'users_emailregister' => $users_email,
  		'users_url' => $this->convert($record['homepage'], 'string', 80),
//      'users_phone' => '',
//      'users_mobile' => '',
      'users_active' => ($banned === true ? 0 : 1),
//      'users_limit' => 20,
//      'users_view' => '',
  		'users_register' => $this->convert($record['registerdate'], 'integer', 14),
  		'users_laston' => $this->convert($record['lastlogin'], 'integer', 14),
      'users_picture' => '', // converted later, because we need the new users_id
      'users_avatar' => '', // converted later, because we need the new users_id
      'users_signature' => $this->convert($record['usertext'], 'clob', 65000),
  		'users_info' => $this->convert($record['about'], 'clob', 65000),
//      'users_timezone' => 0,
//      'users_dstime' => '',
  		'users_hidden' => $this->_hidden,
//      'users_regkey' => '0',
//      'users_homelimit' => 8,
//      'users_readtime' => 1209600,
  		'users_newsletter' => $this->convert($record['newsletter'], 'integer', 2),
//      'users_tpl' => '',
//      'users_theme' => '',
//      'users_invisible' =>  0,
//      'users_ajax' => 0,
      'users_delete' => ($banned === true ? 1 : 0),
//      'users_abomail' => 1,
//      'users_cookiehash' => '',
//      'users_cookietime' => '0',
		);
		
//		$this->error('User: '.implode(',', $user_map));
		return $user_map;
	} // function getUser

	public static function bbToAbcodeSize($matches)
	{
		return '[size='.(4*intval($matches[1])).']'.$matches[2].'[/size]';
	} // function bbToAbcodeSize

	public static function bbToAbcodeColor($matches)
	{
		$colorToHex = array(
			'skyblue' => '87CEEB',
			'royalblue' => '4169E1',
			'blue' => '0000FF',
			'darkblue' => '00008B',
			'orange' => 'FFA500',
			'orangered' => 'FF4500',
			'crimson' => 'DC143C',
			'red' => 'FF0000',
			'firebrick' => 'B22222',
			'darkred' => '8B0000',
			'green' => '00FF00',
			'limegreen' => '90EE90',
			'seagreen' => '2E8B57',
			'deeppink' => 'FF1493',
			'tomato' => 'FF6347',
			'coral' => 'FF7F50',
			'purple' => '800080',
			'indigo' => '4B0082',
			'burlywood' => 'DEB887',
			'sandybrown' => 'F4A460',
			'sienna' => 'A0522D',
			'chocolate' => 'D2691E',
			'teal' => '008080',
			'silver' => 'C0C0C0'
		);
		
		if ($matches[0]{0} == '#' || !array_key_exists(strtolower($matches[1]), $colorToHex))
			return $matches[0];
		return '[color=#'.$colorToHex[strtolower($matches[1])].']'.$matches[2].'[/color]';
	} // function bbColorToAbcodeColor
	
	/**
	 * Convert some bbcode to abcode
	 * 
	 * @param string	$text	The text to convert
	 * @param	string	$mode The mode (news, comments)
	 * 
	 * @return string
	 */
	public function bbToAbcode($text, $mode = 'news')
	{
		$m = 0;
		switch ($mode)
		{
		default: break;
		case 'comments': $m = 1; break; // less allowed in comments
		case 'board': $m = 2; break;
		case 'news': $m = 3; break;
		}
		// escape problem?
		$text = str_replace('\\"', '"', $text);
		// resize images to max of forum/comments
		if ($mode != 'news')
		{
			$text = preg_replace_callback("=\[img\](.*?)\[/img\]=si","cs_abcode_resize", $text);
			$text = preg_replace_callback("=\[img width\=(.*?) height\=(.*?)\](.*?)\[/img\]=si","cs_abcode_resize", $text);
		}
		// [size=X]Y[/size] => [size=X*10]Y[/size]
		if ($m > 0 && preg_match("#\[size=(.*?)\](.*?)\[/size\]#si", $text))
		{
			$text = preg_replace_callback("#\[size=(.*?)\](.*?)\[/size\]#si", array('ClanSphere_Convert_Webspell', 'bbToAbcodeSize'), $text);
		}
		// [color=text]Y[/color] => [color=#Z]Y[/color]
		if ($m > 0 && preg_match("#\[color=(.*?)\](.*?)\[/color\]#si", $text))
		{
			$text = preg_replace_callback("#\[color=(.*?)\](.*?)\[/color\]#si", array('ClanSphere_Convert_Webspell', 'bbToAbcodeColor'), $text);
		}
		// [align=X]Y[/align] => [X]Y[/X]
		if ($m > 1 && preg_match("#\[align=(.*?)\](.*?)\[/align\]#si", $text))
		{
			$text = preg_replace("#\[align=(.*?)\](.*?)\[/align\]#si", "[\\1]\\2[/\\1]", $text);
		}
		// [email]X[/email] => [mail]X[/mail]
		if ($m > 1 && preg_match("#\[email\](.*?)\[/email\]#si", $text))
		{
			$text = preg_replace("#\[email=\](.*?)\[/email\]#si", "[mail]\\1[/mail]", $text);
		}
		// [toggle=X]Y[/toggle] => [clip=]Y[/clip]
		if ($m > 1 && preg_match("#\[toggle=(.*?)\](.*?)\[/toggle\]#si", $text))
		{
			$text = preg_replace("#\[toggle=(.*?)\](.*?)\[/toggle\]#si", "[clip=\\1]\\2[/clip]", $text);
		}
		// [email=X]Y[/email] => [url=mailto:X]Y[/url]
		/* this will probably conflict with cs_secure sequence */
//		if ($m > 1 && preg_match("#\[email=(.*?)\](.*?)\[/email\]#si", $text))
//		{
//			$text = preg_replace("#\[email=(.*?)\](.*?)\[/email\]#si", "[url=mailto:\\1]\\2[/url]", $text);
//		}
		return $text;
	} // function bbToAbcode
	
	/*
   * @return mixed
	 */
	public function convert($data, $type = 'string', $param1 = 80)
	{
		global $cs_main;

		$newdata = $data;
		switch ($type)
		{
		
		case 'string':
			if ($this->_charset != strtoupper($cs_main['charset']))
				$newdata = iconv($this->_charset, $cs_main['charset'].'//TRANSLIT', $data);
			if (strlen($newdata) > $param1)
			{
				if (!$this->_quiet)
					$this->error('Convert: "'.$newdata.'" too long for string('.$param1.')');
				$newdata = substr($newdata, 0, $param1);
			}
			break;
		case 'birthday':
			$newdata = substr($data, 0, 10);
			if ($newdata == '0000-00-00')
				$newdata = '';
			break;
		case 'country':
			if (array_key_exists($data, $this->_countries))
				break;
			switch ($data)
			{
			case 'uk':
				$newdata = 'gb';
				break;
			default:
				$newdata = 'fam';
				break;
			}
			break;
		case 'language':
			switch ($data)
			{
//			case 'dk': $newdata = 'Danish'; break;
//			case 'nl': $newdata = 'Dutch'; break;
//			case 'fr': $newdata = 'French'; break;
//			case 'fi': $newdata = 'Finnish'; break;
//			case 'hu': $newdata = 'Hungarian'; break;
//			case 'it': $newdata = 'Italian'; break;
//			case 'no': $newdata = 'Norwegian'; break;
//			case 'es': $newdata = 'Spanish'; break;
//			case 'se': $newdata = 'Swedish'; break;
			default:
			case 'uk': $newdata = 'English'; break;
			case 'de': $newdata = 'German'; break;
			}
			break;
		case 'integer':
			$newdata = intval($data);
			if (strlen(''.$newdata) > $param1)
			{
				if (!$this->_quiet)
					$this->error('Convert: "'.$newdata.'" too long for integer('.$param1.')');
				$newdata = 0;
			}
			break;
		case 'float':
			$newdata = floatval($data);
			break;
		case 'clob';
			if ($this->_charset != strtoupper($cs_main['charset']))
				$newdata = iconv($this->_charset, $cs_main['charset'].'//TRANSLIT', $data);
			if (strlen($newdata) > $param1)
			{
				if (!$this->_quiet)
					$this->error('Convert: "'.$newdata.'" too long for clob('.$param1.')');
				$newdata = substr($newdata, 0, $param1);
			}
			break;
		}
		return $newdata;
	}

	/* 
   * @return void
	 */
	public function error($text)
	{
		$this->_errors[$this->_errornum++]['message'] = htmlentities($text);
	} // function error

	/* 
   * @return array
	 */
	public function getStatistics()
	{
		return $this->_statistics;
	} // function getStatistics

	/* 
   * @return array
	 */
	public function getErrors()
	{
		return $this->_errors;
	} // function getErrors

	public function getVersion()
	{
		return $this->_ws_version;
	}
} // class ClanSphere_Convert_WebSpell

?>
