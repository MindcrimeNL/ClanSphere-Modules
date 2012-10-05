<?php
/******************************************************************************
Last revision:
- Author: Seven
- Email: zabkar@gmail.com  (Subject CDP)
- Date: 10.11.2010 (1.4.1)
------------------------------------------------------------------------------
Based on the works of:
- Julas, Rush4Hire, esby and Rachmadi
******************************************************************************/

require_once('config.php');
require_once('tools.php');
require_once('modes.php'); 
require_once('xml_parser.php'); 

// Parsed information of the current map.
$xml_data = '';
global $xml_data;



class replay_dota {
  public $fp, $data, $leave_unknown, $continue_game, $referees, $time, $pause, $leaves, $errors, $header, $game,  $players, $chat, $filename, $parse_actions, $parse_chat;
  public $max_datablock = DOTA_REPLAY_MAX_DATABLOCK;
  
  // Used for CM mode
  public $inPickMode;
  // Used for CM mode    
  public $bans, $picks;
   // Used for CM mode
  public $picks_num, $bans_num;
  // Info on observers 
  public $observers; 
  // Used for hero Swapping    
  public $SwapHeroes; 
  // Maps Slot ID to Player ID
  public $SlotToPlayerMap; 
  // Statistics
  public $stats; 
  // Holds extra information for replay displaying
  public $extra; 
  
  public $previousPick;
  
  // Activated Heroes, used for tracking hero assignment.
  public $ActivatedHeroes;
  
	// Keep track if we encountered errors
	private $_hasErrors = false;

  // Store heroes picked before 0:15 dota player IDs are sent out
  public $preAnnouncePick = array();
  // Store skills set before 0:15
  public $preAnnounceSkill = array();
  
  // Stores the mode, used for picking / banning so far
  public $dotaMode;

  // Stores player names based on WC3 IDs
  // TYPE: array
  public $wc3idToNames = array();
  
  // Stores player's WC3 IDs based on dota IDs
  // TYPE: array
  public $dotaIdToWc3id = array();
  
  // Map 'game' Dota IDs to 'internal' Dota IDs
  // TYPE: array
  public $translatedDotaID = array();
  
/**
* @desc Used for compact preview
* @param ID of the team 0 for Sentinel, 1 for Scourge  
*/
public function print_team_heroes($team) {
    
    if(!isset($this->game) || !isset($this->teams)) return 0;
    
    $team_str = ( $team == 0 ? 'sentinel' : 'scourge' );
    
    foreach($this->teams[$team] as $pid=>$player) {
        if ( $this->stats[$player['dota_id']]->getHero() == false ) continue;
        
        $hero = $this->stats[$player['dota_id']]->getHero()->getData();
   
        echo '<img src="'.$hero->getArt().'" width="48px" height="48px" alt="Hero" title="'.$hero->getName().'" class="'.$team_str.'BanHero" />';
    }      
} // function print_team_heroes 

/**
* @desc Constructor
* @param Replay filepath
* @param Set to false disable action parsing
* @param Set to false disable chat parsing
*/
public function __construct($filename, $parse_actions=true, $parse_chat=true) {

    if ( DOTA_REPLAY_DEBUG_ON ) {
        echo "Operating system: ".PHP_OS."<br />";
        echo "Memory limit: ".ini_get("memory_limit")."<br />";
    }
    
    // Temporarily increase memory limit
    @$mem_limit = ini_get("memory_limit");
    if ( substr( $mem_limit, 0, -1 ) < 32 ) {
        @ini_set("memory_limit","32M");   
    }
    

    $GLOBALS['xml_data'] = new dota_xml_data();
    
    // Used for picking in CM mode
    $this->inPickMode = false;
    $this->bans = array();
    $this->picks = array();
    $this->picks_num = 0;
    $this->bans_num = 0;
    
    // Observers
    $this->observers = array();
    // Statistics
    $this->stats = array();
    // WC3 Slots to Dota Player ID map
    $this->SlotToPlayerMap = array();
    // Used for handling Hero Swapping
    $this->SwapHeroes = array();
    // Used for tracking Activated Heroes
    $this->ActivatedHeroes = array();
    
    
    $this->parse_actions = $parse_actions;
    $this->parse_chat = $parse_chat;
    $this->filename = $filename;
	$this->game['player_count'] = 0;
	
    
    
    if (!$this->fp = fopen($filename, 'rb')) {
			$this->setError($this->filename.': Can\'t read replay file');
			return;
    }
    // Lock the replay for reading
    flock($this->fp, 1);

	if (!$this->_parseheader())
		return;
	if (!$this->_parsedata())
		return;
	$this->_cleanup();

    // Unlock the replay
    flock($this->fp, 3);
	fclose($this->fp);
	
    // Cleanup
    unset($this->fp);
    unset($this->data);
    unset($this->players);
    unset($this->referees);
	unset($this->time);
	unset($this->pause);
	unset($this->leaves);
	unset($this->max_datablock);
	unset($this->ability_delay);
	unset($this->leave_unknown);
	unset($this->continue_game);
  } // function __construct

  public function hasErrors()
  {
		return $this->_hasErrors;
  } // function hasErrors

  public function setError($error)
  {
		$this->_hasErrors = true;
		$this->errors[0] = $error;
  } // function setError

  public function getErrors()
  {
		return $this->errors;
  } // function getErrors

    /**
    * @desc 2.0 Header parsing
    */
  protected function _parseheader() {
		$data = fread($this->fp, 48);
		if (!$this->header = @unpack('a28intro/Lheader_size/Lc_size/Lheader_v/Lu_size/Lblocks', $data)) {
			$this->setError('Not a replay file');
			return false;
		}

		if ($this->header['header_v'] == 0) {
          $data = fread($this->fp, 16);
		  $this->header = array_merge($this->header, unpack('Sminor_v/Smajor_v/Sbuild_v/Sflags/Llength/Lchecksum', $data));
		  $this->header['ident'] = 'WAR3';
		} 
        elseif ($this->header['header_v']==1) {
          $data = fread($this->fp, 20);
		  $this->header = array_merge($this->header, unpack('a4ident/Lmajor_v/Sbuild_v/Sflags/Llength/Lchecksum', $data));
		  $this->header['minor_v'] = 0;
		  $this->header['ident'] = strrev($this->header['ident']);
		}
		return true;
	} // function _parseheader
    
    /**
    * @desc Block parsing
    */
    protected function _parsedata() {
        fseek($this->fp, $this->header['header_size']);
        $blocks_count = $this->header['blocks'];
        
		for ($i=0; $i < $blocks_count; $i++) {
 	         // 3.0 [Data block header]
			$block_header = @unpack('Sc_size/Su_size/Lchecksum', fread($this->fp, 8));
            
            $temp = fread($this->fp, $block_header['c_size']);
		    
            // First try uncompressing using the header / tail data, for non-WC3 generated replays
            if ( $temp_gzun = @gzuncompress($temp) ) {
                    $this->data .= $temp_gzun;    
            }
             // If that fails assume we're dealing with a WC3 generated replay and use inflate ignoring header / tail info.
             else {      
                    $temp = substr($temp, 2, -4);
                    $temp{0} = chr(ord($temp{0}) + 1);

                    if ($temp = gzinflate($temp)) {
                        $this->data .= $temp;
                    }
                    else {
											$this->setError($this->filename.': Incomplete replay file. Block id: '.$i);
											return false;
                    }
              }
              
		      // 4.0 [Decompressed data]
              // 4.0.1 - The first block contains Game and Player information 
              if ($i == 0) {
                    $this->data = substr($this->data, 4);
                    $this->_loadplayer();
                    $this->extra['parsed'] = true;
                    
                    if ( $this->_loadgame() == 'fail' )  {
                        $this->extra['parsed'] = false;
												$this->setError($this->filename.': loadgame "fail"');
                        return false;
                    }
                    
              } 
              else if ($blocks_count - $i < 2) {
                    $this->max_datablock = 0;
              }

              if ($this->parse_chat || $this->parse_actions) {
                    if (!$this->_parseblocks())
											return false;
              } 
              else {
                break;
              }
		}
			return true;
    } // function _parsedata

	/**
    * @desc 4.1 PlayerRecord
    */
	protected function _loadplayer() {
		$temp = unpack('Crecord_id/Cplayer_id', $this->data);
		$this->data = substr($this->data, 2);
		$player_id = $temp['player_id'];
		$this->players[$player_id]['player_id'] = $player_id;
		$this->players[$player_id]['initiator'] = dota_convert_bool(!$temp['record_id']);

		$this->players[$player_id]['name'] = '';
		for ($i=0; $this->data{$i}!="\x00"; $i++) {
            $this->players[$player_id]['name'] .= $this->data{$i};
        }
        // Save names for handling SP
        $this->wc3idToNames[$player_id] = $this->players[$player_id]['name'];
        
        
        // if it's FFA we need to give players some names
        if (!$this->players[$player_id]['name']) {
          $this->players[$player_id]['name'] = 'Player '.$player_id;
        }
		$this->data = substr($this->data, $i+1);

        // custom game 
		if (ord($this->data{0}) == 1) { 
			$this->data = substr($this->data, 2);
		}
        // ladder game   
        else if (ord($this->data{0}) == 8) { 
			$this->data = substr($this->data, 1);
			$temp = unpack('Lruntime/Lrace', $this->data);
			$this->data = substr($this->data, 8);
			$this->players[$player_id]['exe_runtime'] = $temp['runtime'];
			$this->players[$player_id]['race'] = dota_convert_race($temp['race']);
		}
		if ($this->parse_actions) {
		  $this->players[$player_id]['actions'][] = 0;
		}
        // calculating team for tournament replays from battle.net website 
		if (!$this->header['build_v']) { 
            $this->players[$player_id]['team'] = ($player_id-1)%2;
		}
		if(isset($this->game['player_count'])) {
			$this->game['player_count']++;
		} 
        else { 
			$this->game['player_count'] = 1; 
		}
	} // function _loadplayer

/**
* @desc Parse Game and Player information
*/
protected function _loadgame() {
    // 4.2 [GameName]
	$this->game['name'] = '';
	for ($i=0; $this->data{$i}!=chr(0); $i++) {
      $this->game['name'] .= $this->data{$i};
    }
	$this->data = substr($this->data, $i+2); // 0-byte ending the string + 1 unknown byte

    // 4.3 [Encoded String]
    $temp = '';

		$mask = 0;
    for ($i=0; $this->data{$i} != chr(0); $i++) {
      if ($i%8 == 0) {
        $mask = ord($this->data{$i});
      } else {
        $temp .= chr(ord($this->data{$i}) - !($mask & (1 << $i%8)));
      }
    }
    $this->data = substr($this->data, $i+1);

    // 4.4 [GameSettings]
    $this->game['speed'] = dota_convert_speed(ord($temp{0}));
    
    if (ord($temp{1}) & 1) {
      $this->game['visibility'] = dota_convert_visibility(0);
    } else if (ord($temp{1}) & 2) {
      $this->game['visibility'] = dota_convert_visibility(1);
    } else if (ord($temp{1}) & 4) {
      $this->game['visibility'] = dota_convert_visibility(2);
    } else if (ord($temp{1}) & 8) {
      $this->game['visibility'] = dota_convert_visibility(3);
    }
    $this->game['observers'] = dota_convert_observers(((ord($temp{1}) & 16) == true) + 2*((ord($temp{1}) & 32) == true));
    $this->game['teams_together'] = dota_convert_bool(ord($temp{1}) & 64);
    
    $this->game['lock_teams'] = dota_convert_bool(ord($temp{2}));
    
    $this->game['full_shared_unit_control'] = dota_convert_bool(ord($temp{3}) & 1);
    $this->game['random_hero'] = dota_convert_bool(ord($temp{3}) & 2);
    $this->game['random_races'] = dota_convert_bool(ord($temp{3}) & 4);
    if (ord($temp{3}) & 64) {
      $this->game['observers'] = dota_convert_observers(4);
    }

    $temp = substr($temp, 13); // 5 unknown bytes + checksum

    // 4.5 [Map&CreatorName]
    $temp = explode(chr(0), $temp);
	$this->game['creator'] = $temp[1];
	$this->game['map'] = $temp[0];
    
    // Get file name, check for an appropriate .xml file and parse it.
    $map_file = dota_getMapName($this->game['map'], $this->game['dota_major'], $this->game['dota_minor']);
    if ( $map_file === false ) {
        return 'fail';
    }
     
    $GLOBALS['xml_data']->parse_file(DOTA_REPLAY_MAPS_FOLDER.'/'.$map_file);

    // 4.6 [PlayerCount]
	$temp = unpack('Lslots', $this->data);
	$this->data = substr($this->data, 4);
	$this->game['slots'] = $temp['slots'];

	// 4.7 [GameType]
	$this->game['type'] = dota_convert_game_type(ord($this->data[0]));
    $this->game['private'] = dota_convert_bool(ord($this->data[1]));

    $this->data = substr($this->data, 8); // 2 bytes are unknown and 4.8 [LanguageID] is useless

    
    
    // 4.9 [PlayerList]
	while (ord($this->data{0}) == 0x16) {
			$this->_loadplayer();
			$this->data = substr($this->data, 4);
	}

     
    
		// 4.10 [GameStartRecord]
    $temp = unpack('Crecord_id/Srecord_length/Cslot_records', $this->data);
    $this->data = substr($this->data, 4);
    $this->game = array_merge($this->game, $temp);
    $slot_records = $temp['slot_records'];

    

    // 4.11 [SlotRecord]
    for ($i=0; $i<$slot_records; $i++) {
  		if ($this->header['major_v'] >= 7) {
  			$temp = unpack('Cplayer_id/x1/Cslot_status/Ccomputer/Cteam/Ccolor/Crace/Cai_strength/Chandicap', $this->data);
  			$this->data = substr($this->data, 9);
  		
        
        } else if ($this->header['major_v'] >= 3) {
  			$temp = unpack('Cplayer_id/x1/Cslot_status/Ccomputer/Cteam/Ccolor/Crace/Cai_strength', $this->data);
  			$this->data = substr($this->data, 8);
  		} else {
  			$temp = unpack('Cplayer_id/x1/Cslot_status/Ccomputer/Cteam/Ccolor/Crace', $this->data);
  			$this->data = substr($this->data, 7);
  		}
  		$temp['color_html'] = dota_convert_color_html($temp['color']);
  		$temp['color'] = dota_convert_color($temp['color']);
        $temp['race'] = dota_convert_race($temp['race']);
        $temp['ai_strength'] = dota_convert_ai($temp['ai_strength']);
        $temp['dota_id'] = dota_getDotaId($temp['color']);   /* Seven */
        // Used for handling SP mode
        $this->dotaIdToWc3id[dota_getDotaId($temp['color'])] = $temp['player_id'];
        
        // do not add empty slots 
        if ($temp['slot_status'] == 2) { 
            
            /* Observers */
            if($temp['team'] == 12) {
               $this->observers[$temp['player_id']] = array_merge($this->players[$temp['player_id']], $temp);
            }
            /* Players */
            else {    
                $this->players[$temp['player_id']] = array_merge($this->players[$temp['player_id']], $temp);
            }
            // Tome of Retraining
            $this->players[$temp['player_id']]['retraining_time'] = 0;
         }
    }
    
    

    // 4.12 [RandomSeed]
    $temp = unpack('Lrandom_seed/Cselect_mode/Cstart_spots', $this->data);
    $this->data = substr($this->data, 6);
    $this->game['random_seed'] = $temp['random_seed'];
    $this->game['select_mode'] = dota_convert_select_mode($temp['select_mode']);
    if ($temp['start_spots'] != 0xCC) { // tournament replays from battle.net website don't have this info
      $this->game['start_spots'] = $temp['start_spots'];
    }
} // function _loadgame

/**
* @desc 5.0 ReplayData parsing
*/
protected function _parseblocks() {
    $data_left = strlen($this->data);
    while ($data_left > $this->max_datablock) {
      
      $prev = (isset($block_id) ? $block_id : 1);
      $block_id = ord($this->data{0});
			
			
      switch ($block_id) {
 	        // TimeSlot block
	        case 0x1E:
			case 0x1F:
              $temp = unpack('x1/Slength/Stime_inc', $this->data);
              if ($this->pause != 1) {
                $this->time += $temp['time_inc'];
              }
              if ($temp['length'] > 2 && $this->parse_actions) {
                $this->_parseactions(substr($this->data, 5, $temp['length']-2), $temp['length']-2);
              }
              $this->data = substr($this->data, $temp['length']+3);
              $data_left -= $temp['length']+3;
			  break;
            // Player chat message (patch version >= 1.07)
            case 0x20:
              // before 1.03 0x20 was used instead 0x22
              if ($this->header['major_v'] > 2) {
                $temp = unpack('x1/Cplayer_id/Slength/Cflags/Smode', $this->data);
                if ($temp['flags'] == 0x20) {
                  $temp['mode'] = dota_convert_chat_mode($temp['mode']);
                  $temp['text'] = substr($this->data, 9, $temp['length']-6);
                } elseif ($temp['flags'] == 0x10) {
                  // those are strange messages, they aren't visible when
                  // watching the replay but they are present; they have no mode
                  $temp['text'] = substr($this->data, 7, $temp['length']-3);
                  unset($temp['mode']);
                }
                $this->data = substr($this->data, $temp['length']+4);
                $data_left -= $temp['length']+4;
                $temp['time'] = $this->time;
                $temp['player_name'] = $this->players[$temp['player_id']]['name'];
                $this->chat[] = $temp;
                break;
              }
            // unknown (Random number/seed for next frame)
            case 0x22:
              $temp = ord($this->data{1});
					    $this->data = substr($this->data, $temp+2);
					    $data_left -= $temp+2;
				      break;
            // unknown (startblocks)
			case 0x1A:
			case 0x1B:
			case 0x1C:
				$this->data = substr($this->data, 5);
				$data_left -= 5;
			  break;
            // unknown (very rare, appears in front of a 'LeaveGame' action)
            case 0x23:
              $this->data = substr($this->data, 11);
              $data_left -= 11;
              break;
            // Forced game end countdown (map is revealed)
            case 0x2F:
              $this->data = substr($this->data, 9);
              $data_left -= 9;
              break;
            // LeaveGame
            case 0x17:
              $this->leaves++;
              $temp = unpack('x1/Lreason/Cplayer_id/Lresult/Lunknown', $this->data);
              $this->players[$temp['player_id']]['time'] = $this->time;
              $this->players[$temp['player_id']]['leave_reason'] = $temp['reason'];
              $this->players[$temp['player_id']]['leave_result'] = $temp['result'];
              $this->data = substr($this->data, 14);
              $data_left -= 14;
              if ($this->leave_unknown) {
                $this->leave_unknown = $temp['unknown'] - $this->leave_unknown;
              }
              if ($this->leaves == $this->game['player_count']) {
                $this->game['saver_id'] = $temp['player_id'];
                $this->game['saver_name'] = $this->players[$temp['player_id']]['name'];
              }
              if ($temp['reason'] == 0x01) {
                switch ($temp['result']) {
                  case 0x01: $temp['mode'] = 'Quit'; $temp['time'] = $this->time; $temp['player_name'] = $this->players[$temp['player_id']]['name']; $temp['text']='Disconnect'; $this->chat[] = $temp; $this->players[$temp['player_id']]['leave_result'] = $temp['text']; break;
                  case 0x07: $temp['mode'] = 'Quit'; $temp['time'] = $this->time; $temp['player_name'] = $this->players[$temp['player_id']]['name']; $temp['text']="Left"; $this->chat[] = $temp;  $this->players[$temp['player_id']]['leave_result'] = $temp['text']; break;
                  case 0x08: $this->game['loser_team'] = $this->players[$temp['player_id']]['team']; $temp['mode'] = 'Quit'; $temp['time'] = $this->time; $temp['player_name'] = $this->players[$temp['player_id']]['name']; $temp['text']='Finished'; $this->chat[] = $temp;  $this->players[$temp['player_id']]['leave_result'] = $temp['text'];  break;//Lost
                  case 0x09: $this->game['winner_team'] = $this->players[$temp['player_id']]['team']; $temp['mode'] = 'Quit'; $temp['time'] = $this->time; $temp['player_name'] = $this->players[$temp['player_id']]['name']; $temp['text']='Finished'; $this->chat[] = $temp;  $this->players[$temp['player_id']]['leave_result'] = $temp['text'];  break;//Won
                  case 0x0A: $this->game['loser_team'] = 'tie'; $this->game['winner_team'] = 'tie'; $temp['mode'] = 'Quit'; $temp['time'] = $this->time; $temp['player_name'] = $this->players[$temp['player_id']]['name']; $temp['text']='Finished'; $this->chat[] = $temp;  $this->players[$temp['player_id']]['leave_result'] = $temp['text'];  break;//Draw
                  case 0x0B: $temp['mode'] = 'Quit'; $temp['time'] = $this->time; $temp['player_name'] = $this->players[$temp['player_id']]['name']; $temp['text']="Left"; $this->chat[] = $temp;  $this->players[$temp['player_id']]['leave_result'] = $temp['text'];  break;
                }
              } elseif ($temp['reason'] == 0x0C && isset($this->game['saver_id']) && $this->game['saver_id']) {
                switch ($temp['result']) {
                  case 0x01: $temp['mode'] = 'Quit'; $temp['time'] = $this->time; $temp['player_name'] = $this->players[$temp['player_id']]['name']; $temp['text']='Disconnect'; $this->chat[] = $temp;  $this->players[$temp['player_id']]['leave_result'] = $temp['text'];  break;//Saver Disconnect
                  case 0x07:
                    if ($this->leave_unknown > 0 && $this->continue_game) {
                      $this->game['winner_team'] = $this->players[$this->game['saver_id']]['team']; $temp['mode'] = 'Quit'; $temp['time'] = $this->time; $temp['player_name'] = $this->players[$temp['player_id']]['name']; $temp['text']='Finished'; $this->chat[] = $temp;  $this->players[$temp['player_id']]['leave_result'] = $temp['text'];  break;//Saver Won
                    } else {
                      $this->game['loser_team'] = $this->players[$this->game['saver_id']]['team']; $temp['mode'] = 'Quit'; $temp['time'] = $this->time; $temp['player_name'] = $this->players[$temp['player_id']]['name']; $temp['text']='Finished'; $this->chat[] = $temp;  $this->players[$temp['player_id']]['leave_result'] = $temp['text'];  break;//Saver Lost
                    }
                  case 0x08: $this->game['loser_team'] = $this->players[$this->game['saver_id']]['team']; $temp['mode'] = 'Quit'; $temp['time'] = $this->time; $temp['player_name'] = $this->players[$temp['player_id']]['name']; $temp['text']='Finished'; $this->chat[] = $temp;  $this->players[$temp['player_id']]['leave_result'] = $temp['text'];  break;//Saver Lost
                  case 0x09: $this->game['winner_team'] = $this->players[$this->game['saver_id']]['team']; $temp['mode'] = 'Quit'; $temp['time'] = $this->time; $temp['player_name'] = $this->players[$temp['player_id']]['name']; $temp['text']='Finished'; $this->chat[] = $temp;  $this->players[$temp['player_id']]['leave_result'] = $temp['text'];  break;//Saver Won
                  case 0x0B: // this isn't correct according to w3g_format but generally works...
                    if ($this->leave_unknown > 0) {
                      $this->game['winner_team'] = $this->players[$this->game['saver_id']]['team']; $temp['mode'] = 'Quit'; $temp['time'] = $this->time; $temp['player_name'] = $this->players[$temp['player_id']]['name']; $temp['text']='Finished'; $this->chat[] = $temp;  $this->players[$temp['player_id']]['leave_result'] = $temp['text'];  break;//Saver Won
                    }
                }
              } elseif ($temp['reason'] == 0x0C) {
                switch ($temp['result']) {
                  case 0x01: $temp['mode'] = 'Quit'; $temp['time'] = $this->time; $temp['player_name'] = $this->players[$temp['player_id']]['name']; $temp['text']='Disconnect'; $this->chat[] = $temp;  $this->players[$temp['player_id']]['leave_result'] = $temp['text'];  break;//Saver Disconnect
                  case 0x07: $this->game['loser_team'] = 99; $temp['mode'] = 'Quit'; $temp['time'] = $this->time; $temp['player_name'] = $this->players[$temp['player_id']]['name']; $temp['text']='Finished'; $this->chat[] = $temp;  $this->players[$temp['player_id']]['leave_result'] = $temp['text'];  break;//Saver Lost
                  case 0x08: $this->game['winner_team'] = $this->players[$temp['player_id']]['team']; $temp['mode'] = 'Quit'; $temp['time'] = $this->time; $temp['player_name'] = $this->players[$temp['player_id']]['name']; $temp['text']='Finished'; $this->chat[] = $temp;  $this->players[$temp['player_id']]['leave_result'] = $temp['text'];  break;//Saver Lost
                  case 0x09: $this->game['winner_team'] = 99; $temp['mode'] = 'Quit'; $temp['time'] = $this->time; $temp['player_name'] = $this->players[$temp['player_id']]['name']; $temp['text']='Finished'; $this->chat[] = $temp;  $this->players[$temp['player_id']]['leave_result'] = $temp['text'];  break;//Saver Won
                  case 0x0A: $this->game['loser_team'] = 'tie'; $this->game['winner_team'] = 'tie'; $temp['mode'] = 'Quit'; $temp['time'] = $this->time; $temp['player_name'] = $this->players[$temp['player_id']]['name']; $temp['text']='Finished'; $this->chat[] = $temp;  $this->players[$temp['player_id']]['leave_result'] = $temp['text'];  break;//Tie
                }
              }
              $this->leave_unknown = $temp['unknown'];
              break;
            case 0:
              $data_left = 0;
              break;
            default:
							$this->setError('Unhandled replay command block: 0x'.sprintf('%02X', $block_id).' (prev: 0x'.sprintf('%02X', $prev).', time: '.$this->time.') in '.$this->filename);
							return false;
          } // End switch
      }  // End while
		return true;
} // function _parseblocks

/**
* @desc Action parsing
* @param Action Block
* @param Data length
*/
protected function _parseactions($actionblock, $data_length) {
  $block_length = 0;
	$was_subgroup = false;

  while ($data_length) {
    if ($block_length) {
      $actionblock = substr($actionblock, $block_length);
    }
    $temp = unpack('Cplayer_id/Slength', $actionblock);
    $player_id = $temp['player_id'];
    $block_length = $temp['length']+3;
    $data_length -= $block_length;

    $was_deselect = false;
    $was_subupdate = false;

    $n = 3;
	
    // The main action block loop    
    while ($n < $block_length) {
        
        // Holds a history for the previous action 
        $prev = (isset($action) ? $action : 0);     
        $action = ord($actionblock{$n});
        

      switch ($action) {
        // Unit/building ability (no additional parameters)
        // here we detect the races, heroes, units, items, buildings,
        // upgrades

        case 0x10:
          $this->players[$player_id]['actions'][] = $this->time;

          if ($this->header['major_v'] >= 13) {
            $n++; // ability flag is one byte longer
          }
          $itemid = strrev(substr($actionblock, $n+2, 4));
          
          // For debugging only
          if(DOTA_REPLAY_DEBUG_ON) { 
               $temp['mode'] = "action";
               $temp['text'] = $itemid;
               $temp['time'] = $this->time;
               $temp['player_name'] = $this->players[$temp['player_id']]['name'];
               $this->chat[] = $temp;
          }
          
          $value = dota_convert_itemid($itemid);
          if (!$value) {
            if(isset($this->players[$player_id]['actions_details'][dota_convert_action('ability')])) {
            	$this->players[$player_id]['actions_details'][dota_convert_action('ability')]++;
            } else { 
	            $this->players[$player_id]['actions_details'][dota_convert_action('ability')] = 1; 
            }

            // Irrelevant to dota - handling Destroyers
            if (ord($actionblock{$n+2}) == 0x33 && ord($actionblock{$n+3}) == 0x02) {
              $name = substr(dota_convert_itemid('ubsp'), 2);
              $this->players[$player_id]['units']['order'][$this->time] = $this->players[$player_id]['units_multiplier'].' '.$name;
              $this->players[$player_id]['units'][$name] += $this->players[$player_id]['units_multiplier'];
              $name = substr(dota_convert_itemid('uobs'), 2);
              $this->players[$player_id]['units'][$name] -= $this->players[$player_id]['units_multiplier'];
            }
          } 
          else {
	          if(isset($this->players[$player_id]['actions_details'][dota_convert_action('buildtrain')])) {
	          	$this->players[$player_id]['actions_details'][dota_convert_action('buildtrain')]++;
	          } 
              else { 
	            $this->players[$player_id]['actions_details'][dota_convert_action('buildtrain')] = 1; 
	          }
	          if (!isset($this->players[$player_id]['race_detected']) || !$this->players[$player_id]['race_detected']) {
	            if ($race_detected = dota_convert_race($itemid)) {
	              $this->players[$player_id]['race_detected'] = $race_detected;
	            }
              }
            
            // Entity name (unique)  
            $name = $value->getName();          
            // Entity type (Hero, Skill, Ultimate, Stat, Item)
            $type = $value->getEntityType();    
            
            switch ($type) {
                case 'HERO':
                      // Picking in CM mode, we need to get heroes equal to the number of slots... - obs?
                      if($this->inPickMode) {
                           
                            // Handle duplicated actions
                            if(isset($this->previousPick) && $this->previousPick == $value->getName()) continue;
                            
                            $value->extra = $this->players[$player_id]['team'];  
                            
                            // 3-2 ban split CM Mode in versions 6.68+
                            if( ($this->game['dota_major'] == 6 && $this->game['dota_minor'] >= 68) || $this->game['dota_major'] > 6) {
                                // This action was triggered by CM banning, so ignore it
                                if(!$this->dotaMode->banPhaseComplete())
                                    break;
 
                                // We need to keep checking how many picks have been made,
                                // since we need to start another ban phase for the split CM mode
                                if($this->dotaMode->getBansPerTeam() == 3) {
                                    // We're done with phase 1, and waiting for 6 picks to be made to get to phase 2
                                    if($this->dotaMode->getNumPicked() >= 6) {
                                        break;
                                    }
                                }
 
                                // Pick hero
                                $this->dotaMode->addHeroToPicks($value);
                                $this->picks_num++;
 
                            }
                            // Otherwise we're dealing with the pre 6.68 CM mode of 4 bans
                            else {
                                // The hero was banned, don't add it to the picks.
                                if($this->bans_num < NUM_OF_BANS) break;


                            		// Add the picked hero to the array of picked heroes.
                            		$this->picks[] = $value;
                            		$this->picks_num++;
														}
                            
														// Save previous pick to avoid duplication issues
                            $this->previousPick = $value->getName();

                            // End of picking, unpause the game
                            if($this->picks_num >= DOTA_REPLAY_NUM_OF_PICKS) {
                                $this->pause = false;
                                $this->inPickMode = false;
                            }
                            
                            if(DOTA_REPLAY_DEBUG_ON) {
                                echo "Added hero to pool in CM mode ".$value->getName()." ID ".$value->getId()." for player ".$this->players[$player_id]['name']." at ".dota_convert_time($this->time)."<br />"; 
                            }
                            
                      }    
                  break;
                case 'SKILL':
                case 'ULTIMATE':
                case 'STAT':
                  
                  // Find the hero with the SkillID in RelatedTo
                  $heroId = $GLOBALS['xml_data']->SkillToHeroMap[$value->getId()];
                  $heroName   = $GLOBALS['xml_data']->HashMap[$heroId]->getName();
                  
                  $pid = $this->players[$player_id]['dota_id'];

                  if ( !isset ( $this->stats[$pid]) ) {                      
                      if ( DOTA_REPLAY_DEBUG_ON ) {
                        echo "<span style='color:red;'> Problematic player id: ".$pid." </span><br />";
                      }
                      $this->preAnnounceSkill[$pid] = array( 'skill' => $value, 'time' => $this->time, 'heroId' => $heroId);
                  }
                  else if ( !$this->stats[$pid]->isSetHero() ) {
                      // Player is skilling, but no hero set yet.
                      // Save the Skill Data and Time, and try to add the skill on cleanup
                      $this->stats[$pid]->addDelayedSkill( $value, $this->time, $heroId );
                  }
                  // If skill-to-hero is the same as player's hero or common attribute skill the hero
                  else if ( 
                            $value->getId() == 'A0NR' 
                            || $heroName == "Common" 
                            || $heroName == $this->stats[$pid]->getHero()->getName() 
                          ) {
                                
                       // Check if the current amount of stored skills is not greater then the level
                       // broadcast by Dota
                       // Only check if we're dealing with a new version, where the level was increased at least once
                       if($this->stats[$pid]->getLevelCap() >= 2) {
                           // TODO - THIS IS DEACTIVATED with "> 0 || 0 =="  AT THE MOMENT
                           // Limiting skilling by the Level broadcasting doesn't seem to be effective due to ID and possible timing issues
                           if( $this->stats[$pid]->getLevelCap() > 0 || 0 == $this->stats[$pid]->getHero()->getLevel() ) {
                               $this->stats[$pid]->getHero()->setSkill( $value, $this->time );
 
                               if( DOTA_REPLAY_DEBUG_ON ) {
                                     echo "Player ".$this->players[$player_id]['name']." skilling ".$heroName." at ".$this->time."  (PLAYER ID : ".$player_id.") - DOTA ID: ".$this->players[$player_id]['dota_id']." Translated: ".$this->dotaIdToWc3id[$this->players[$player_id]['dota_id']]." <br />";
                               }
                           }
                           else if( DOTA_REPLAY_DEBUG_ON ) {
                             echo "Player ".$this->players[$player_id]['name']." failed skilling ".$heroName." due to LEVEL CAP at ".$this->time."<br />";
                           }
                       }
                       else {

		                      $this->stats[$pid]->getHero()->setSkill( $value, $this->time );
                      
                      		if( DOTA_REPLAY_DEBUG_ON ) {
                       				echo "Player ".$this->players[$player_id]['name']." skilling ".$heroName."<br />";
                      		}
											 }
                  }
                  // Otherwise assume the player's skilling a Hero not owned by him
                  else {
                      if ( isset($this->ActivatedHeroes[$heroName]) ) {
                        $this->ActivatedHeroes[$heroName]->setSkill( $value, $this->time );
                      }
                      if ( DOTA_REPLAY_DEBUG_ON ) {
                        echo "Player ".$this->players[$player_id]['name']." skilling non-owned ".$heroName."<br />";
                      }
                  } 
                  
                  break;
                  
                case 'ITEM':
                  $this->players[$player_id]['items'][dota_convert_time($this->time)] = $value;
                  break;
                
                // Irrelevant to dota for now.
                case 'p':
                  // preventing duplicated upgrades
                  if ($this->time - $this->players[$player_id]['upgrades_time'] > DOTA_REPLAY_ACTION_DELAY || $itemid != $this->players[$player_id]['last_itemid']) {
                    $this->players[$player_id]['upgrades_time'] = $this->time;
                    $this->players[$player_id]['upgrades']['order'][$this->time] = $name;
                    if(isset($this->players[$player_id]['upgrades'][$name])) {
                    	$this->players[$player_id]['upgrades'][$name]++;
                    } else { 
	                    $this->players[$player_id]['upgrades'][$name] = 1; 
                    }
                  }
                  break;
                // Irrelevant to dota for now.
                case 'UNIT':
                  // preventing duplicated units
                  if (($this->time - $this->players[$player_id]['units_time'] > DOTA_REPLAY_ACTION_DELAY || $itemid != $this->players[$player_id]['last_itemid'])
                  // at the beginning of the game workers are queued very fast, so
                  // it's better to omit action delay protection
                  || (($itemid == 'hpea' || $itemid == 'ewsp' || $itemid == 'opeo' || $itemid == 'uaco') && $this->time - $this->players[$player_id]['units_time'] > 0)) {
                    $this->players[$player_id]['units_time'] = $this->time;
                    $this->players[$player_id]['units']['order'][$this->time] = $this->players[$player_id]['units_multiplier'].' '.$name;
                    $this->players[$player_id]['units'][$name] += $this->players[$player_id]['units_multiplier'];
                  }
                  break;
                // Irrelevant to dota for now.
                case 'BUILDING':
                  $this->players[$player_id]['buildings']['order'][$this->time] = $name;
                  if(isset($this->players[$player_id]['buildings'][$name])) {
                      $this->players[$player_id]['buildings'][$name]++;
                  } else { 
                      $this->players[$player_id]['buildings'][$name] = 1; 
                  }
                  break;
                case 'ERROR':
                	$this->errors[$this->time] = $this->players[$player_id]['name'].': Unknown SkillID: '.$value;
                	break;
                default:
                  $this->errors[$this->time] = $this->players[$player_id]['name'].': Unknown ItemID: '.$value;
                  break;
              }
              $this->players[$player_id]['last_itemid'] = $itemid;
            }

            $n+=14;
            break;

          // Unit/building ability (with target position)
          case 0x11:
          //// Was in the middle of working on the coordinants here... This is where I stopped.
// $temp1 = unpack('CPlayer/SLength/Caction/SAbilityFlags/LItemID/Lfooa/Lfoob/fx/fy/Lfooc/Lfood', $actionblock);
// if (!isset ){}$temp1['x']/8192*64;
// $y = $temp1['y']/8192*64;
// echo $x.'<br />'.$y.'<br /><br />';
                $this->players[$player_id]['actions'][] = $this->time;
                if ($this->header['major_v'] >= 13) {
                  $n++; // ability flag
                }
                if (ord($actionblock{$n+2}) <= 0x19 && ord($actionblock{$n+3}) == 0x00) { // basic commands
                  if(isset($this->players[$player_id]['actions_details'][dota_convert_action('basic')])) {
              	    $this->players[$player_id]['actions_details'][dota_convert_action('basic')]++;
                  } 
                  else { 
	                  $this->players[$player_id]['actions_details'][dota_convert_action('basic')] = 1; 
                  }
                } 
                else {
                  if(isset($this->players[$player_id]['actions_details'][dota_convert_action('ability')])) {
              	    $this->players[$player_id]['actions_details'][dota_convert_action('ability')]++;
                  } 
                  else { 
	                  $this->players[$player_id]['actions_details'][dota_convert_action('ability')] = 1; 
                  }
                }
                $value = strrev(substr($actionblock, $n+2, 4));
                if ($value = dota_convert_buildingid($value)) {
                  $this->players[$player_id]['buildings']['order'][$this->time] = $value;
                  if(isset($this->players[$player_id]['buildings'][$value])) {
              	    $this->players[$player_id]['buildings'][$value]++;
                  } 
                  else { 
	                  $this->players[$player_id]['buildings'][$value] = 1; 
                  }
                }
              $n+=22;
            break;

          // Unit/building ability (with target position and target object ID)
          case 0x12:

            $this->players[$player_id]['actions'][] = $this->time;
            if ($this->header['major_v'] >= 13) {
              $n++; // ability flag
            }
            if (ord($actionblock{$n+2}) == 0x03 && ord($actionblock{$n+3}) == 0x00) { // rightclick
              if(isset($this->players[$player_id]['actions_details'][dota_convert_action('rightclick')])) {
              	$this->players[$player_id]['actions_details'][dota_convert_action('rightclick')]++;
              } 
              else { 
	              $this->players[$player_id]['actions_details'][dota_convert_action('rightclick')] = 1; 
              }
            } 
            else if (ord($actionblock{$n+2}) <= 0x19 && ord($actionblock{$n+3}) == 0x00) { // basic commands
              if(isset($this->players[$player_id]['actions_details'][dota_convert_action('basic')])) {
              	$this->players[$player_id]['actions_details'][dota_convert_action('basic')]++;
              } 
              else { 
	              $this->players[$player_id]['actions_details'][dota_convert_action('basic')] = 1; 
              }
            } 
            else {
              if(isset($this->players[$player_id]['actions_details'][dota_convert_action('ability')])) {
              	$this->players[$player_id]['actions_details'][dota_convert_action('ability')]++;
              } 
              else { 
	              $this->players[$player_id]['actions_details'][dota_convert_action('ability')] = 1; 
              }
            }

            $n+=30;
            break;

          // Give item to Unit / Drop item on ground
          case 0x13:
            $this->players[$player_id]['actions'][] = $this->time;
            if ($this->header['major_v'] >= 13) {
              $n++; // ability flag
            }
            if(isset($this->players[$player_id]['actions_details'][dota_convert_action('item')])) {
            	$this->players[$player_id]['actions_details'][dota_convert_action('item')]++;
            } else { 
	            $this->players[$player_id]['actions_details'][dota_convert_action('item')] = 1; 
            }
            $n+=38;
            break;

          // Unit/building ability (with two target positions and two item IDs)
          case 0x14:
            $this->players[$player_id]['actions'][] = $this->time;
            if ($this->header['major_v'] >= 13) {
              $n++; // ability flag
            }
            if (ord($actionblock{$n+2}) == 0x03 && ord($actionblock{$n+3}) == 0x00) { // rightclick
              if(isset($this->players[$player_id]['actions_details'][dota_convert_action('rightclick')])) {
              	$this->players[$player_id]['actions_details'][dota_convert_action('rightclick')]++;
              } else { 
	              $this->players[$player_id]['actions_details'][dota_convert_action('rightclick')] = 1; 
              }
            } elseif (ord($actionblock{$n+2}) <= 0x19 && ord($actionblock{$n+3}) == 0x00) { // basic commands
              if(isset($this->players[$player_id]['actions_details'][dota_convert_action('basic')])) {
              	$this->players[$player_id]['actions_details'][dota_convert_action('basic')]++;
              } else { 
	              $this->players[$player_id]['actions_details'][dota_convert_action('basic')] = 1; 
              }
            } else {
              if(isset($this->players[$player_id]['actions_details'][dota_convert_action('ability')])) {
              	$this->players[$player_id]['actions_details'][dota_convert_action('ability')]++;
              } else { 
	              $this->players[$player_id]['actions_details'][dota_convert_action('ability')] = 1; 
              }
            }
              $n+=43;
            break;

          // Change Selection (Unit, Building, Area)
          case 0x16:
            $temp = unpack('Cmode/Snum', substr($actionblock, $n+1, 3));
            if ($temp['mode'] == 0x02 || !$was_deselect) {
                $this->players[$player_id]['actions'][] = $this->time;
                if(isset($this->players[$player_id]['actions_details'][dota_convert_action('select')])) {
            	    $this->players[$player_id]['actions_details'][dota_convert_action('select')]++;
                } 
                else { 
	                $this->players[$player_id]['actions_details'][dota_convert_action('select')] = 1; 
                }
            }
            $was_deselect = ($temp['mode'] == 0x02);
            
            $this->players[$player_id]['units_multiplier'] = $temp['num'];
            $n+=4 + ($temp['num'] * 8);
            break;

          // Assign Group Hotkey
          case 0x17:
            $this->players[$player_id]['actions'][] = $this->time;
            if(isset($this->players[$player_id]['actions_details'][dota_convert_action('assignhotkey')])) {
            	$this->players[$player_id]['actions_details'][dota_convert_action('assignhotkey')]++;
            } 
            else { 
	            $this->players[$player_id]['actions_details'][dota_convert_action('assignhotkey')] = 1; 
            }
            $temp = unpack('Cgroup/Snum', substr($actionblock, $n+1, 3));
            if(isset($this->players[$player_id]['hotkeys'][$temp['group']]['assigned'])) {
            	$this->players[$player_id]['hotkeys'][$temp['group']]['assigned']++;
            } 
            else { 
	            $this->players[$player_id]['hotkeys'][$temp['group']]['assigned'] = 1; 
            }
            $this->players[$player_id]['hotkeys'][$temp['group']]['last_totalitems'] = $temp['num'];

            $n+=4 + ($temp['num'] * 8);
            break;

          // Select Group Hotkey
          case 0x18:
            $this->players[$player_id]['actions'][] = $this->time;
            if(isset($this->players[$player_id]['actions_details'][dota_convert_action('selecthotkey')])) {
            	$this->players[$player_id]['actions_details'][dota_convert_action('selecthotkey')]++;
            } 
            else { 
	            $this->players[$player_id]['actions_details'][dota_convert_action('selecthotkey')] = 1; 
            }
            if(isset($this->players[$player_id]['hotkeys'][ord($actionblock{$n+1})]['used'])) {
            	$this->players[$player_id]['hotkeys'][ord($actionblock{$n+1})]['used']++;
            } 
            else { 
	            $this->players[$player_id]['hotkeys'][ord($actionblock{$n+1})]['used'] = 1; 
            }

            $this->players[$player_id]['units_multiplier'] = $this->players[$player_id]['hotkeys'][ord($actionblock{$n+1})]['last_totalitems'];
            $n+=3;
            break;

          // Select Subgroup
          case 0x19:
            // OR is for torunament reps which don't have build_v
            if ($this->header['build_v'] >= 6040 || $this->header['major_v'] > 14) {
              if ($was_subgroup) { // can't think of anything better (check action 0x1A)
                $this->players[$player_id]['actions'][] = $this->time;
                if(isset($this->players[$player_id]['actions_details'][dota_convert_action('subgroup')])) {
                	$this->players[$player_id]['actions_details'][dota_convert_action('subgroup')]++;
                } else { 
	                $this->players[$player_id]['actions_details'][dota_convert_action('subgroup')] = 1; 
                }
                
                // I don't have any better idea what to do when somebody binds buildings
                // of more than one type to a single key and uses them to train units
                $this->players[$player_id]['units_multiplier'] = 1;
              }
              $n+=13;
            } 
            else {
              if (ord($actionblock{$n+1}) != 0 && ord($actionblock{$n+1}) != 0xFF && !$was_subupdate) {
                $this->players[$player_id]['actions'][] = $this->time;
                if(isset($this->players[$player_id]['actions_details'][dota_convert_action('subgroup')])) {
                	$this->players[$player_id]['actions_details'][dota_convert_action('subgroup')]++;
                } else { 
	                $this->players[$player_id]['actions_details'][dota_convert_action('subgroup')] = 1; 
                }
              }
              $was_subupdate = (ord($actionblock{$n+1}) == 0xFF);
              $n+=2;
            }
            break;

          // some subaction holder?
          // version < 14b: Only in scenarios, maybe a trigger-related command
          case 0x1A:
            // OR is for torunament reps which don't have build_v
            if ($this->header['build_v'] >= 6040 || $this->header['major_v'] > 14) {
              $n+=1;
              $was_subgroup = ($prev == 0x19 || $prev == 0); //0 is for new blocks which start from 0x19
            } else {
              $n+=10;
            }
            break;

          // Only in scenarios, maybe a trigger-related command
          // version < 14b: Select Ground Item
          case 0x1B:
            // OR is for torunament reps which don't have build_v
            if ($this->header['build_v'] >= 6040 || $this->header['major_v'] > 14) {
              $n+=10;
            } else {
              $this->players[$player_id]['actions'][] = $this->time;
              $n+=10;
            }
            break;
            
          // Select Ground Item
          // version < 14b: Cancel hero revival (new in 1.13)
          case 0x1C:
            // OR is for torunament reps which don't have build_v
            if ($this->header['build_v'] >= 6040 || $this->header['major_v'] > 14) {
              $this->players[$player_id]['actions'][] = $this->time;
              $n+=10;
            } else {
              $this->players[$player_id]['actions'][] = $this->time;
              $n+=9;
            }
            break;

          // Cancel hero revival
          // Remove unit from building queue
          case 0x1D:
          case 0x1E:
            // OR is for torunament reps which don't have build_v
            if (($this->header['build_v'] >= 6040 || $this->header['major_v'] > 14) && $action != 0x1E) {
              $this->players[$player_id]['actions'][] = $this->time;
              $n+=9;
            } else {
              $this->players[$player_id]['actions'][] = $this->time;
              if(isset($this->players[$player_id]['actions_details'][dota_convert_action('removeunit')])) {
              	$this->players[$player_id]['actions_details'][dota_convert_action('removeunit')]++;
              } else { 
	              $this->players[$player_id]['actions_details'][dota_convert_action('removeunit')] = 1; 
              }
              $value = dota_convert_itemid(strrev(substr($actionblock, $n+2, 4)));
              $name = substr($value, 2);
              switch ($value{0}) {
                case 'u':
                  // preventing duplicated units cancellations
                  if ($this->time - $this->players[$player_id]['runits_time'] > DOTA_REPLAY_ACTION_DELAY || $value != $this->players[$player_id]['runits_value']) {
                    $this->players[$player_id]['runits_time'] = $this->time;
                    $this->players[$player_id]['runits_value'] = $value;
                    $this->players[$player_id]['units']['order'][$this->time] = '-1 '.$name;
                    $this->players[$player_id]['units'][$name]--;
                  }
                  break;
                case 'b':
                  $this->players[$player_id]['buildings'][$name]--;
                  break;
                case 'h':
                  $this->players[$player_id]['heroes'][$name]['revivals']--;
                  break;
                case 'p':
                  // preventing duplicated upgrades cancellations
                  if ($this->time - $this->players[$player_id]['rupgrades_time'] > DOTA_REPLAY_ACTION_DELAY || $value != $this->players[$player_id]['rupgrades_value']) {
                    $this->players[$player_id]['rupgrades_time'] = $this->time;
                    $this->players[$player_id]['rupgrades_value'] = $value;
                    $this->players[$player_id]['upgrades'][$name]--;
                  }
                  break;
              }
              $n+=6;
            }
            break;

          // Found in replays with patch version 1.04 and 1.05.
          case 0x21:
            $n+=9;
            break;

          // Change ally options
          case 0x50:
            $n+=6;
            break;

          // Transfer resources
          case 0x51:
            $n+=10;
            break;

          // Map trigger chat command
          // Mode can be detected here, as can the usage of -di, -fs, -ma etc.
          // TODO - Use this information
          case 0x60:
            $n+=9; // Two DWORDS + ID 
            $str = "";
            while ($actionblock{$n} != "\x00") {
                $str .= $actionblock{$n};
                $n++;
            }
            $n+=1;
            if ( DOTA_REPLAY_DEBUG_ON ) {
                echo "Trigger chat command: ".$str." at ".dota_convert_time($this->time)."<br />";
            }
            break;

          // ESC pressed
          case 0x61:
            $this->players[$player_id]['actions'][] = $this->time;
            if(isset($this->players[$player_id]['actions_details'][dota_convert_action('esc')])) {
            	$this->players[$player_id]['actions_details'][dota_convert_action('esc')]++;
            } 
            else { 
	            $this->players[$player_id]['actions_details'][dota_convert_action('esc')] = 1; 
            }
            $n+=1;
            break;

          // Scenario Trigger
          case 0x62:
            if ($this->header['major_v'] >= 7) {
              $n+=13;
            } 
            else {
              $n+=9;
            }
            break;

          // Enter select hero skill submenu for WarCraft III patch version <= 1.06
          case 0x65:
            $this->players[$player_id]['actions'][] = $this->time;
            if(isset($this->players[$player_id]['actions_details'][dota_convert_action('heromenu')])) {
            	$this->players[$player_id]['actions_details'][dota_convert_action('heromenu')]++;
            } else { 
	            $this->players[$player_id]['actions_details'][dota_convert_action('heromenu')] = 1; 
            }
            $n+=1;
            break;

          // Enter select hero skill submenu
          // Enter select building submenu for WarCraft III patch version <= 1.06
          case 0x66:
            $this->players[$player_id]['actions'][] = $this->time;
              if(isset($this->players[$player_id]['actions_details'][dota_convert_action('heromenu')])) {
              	$this->players[$player_id]['actions_details'][dota_convert_action('heromenu')]++;
              } 
              else { 
	              $this->players[$player_id]['actions_details'][dota_convert_action('heromenu')] = 1; 
              }
            $n+=1;
            break;

          // Enter select building submenu
          // Minimap signal (ping) for WarCraft III patch version <= 1.06
          case 0x67:
            if ($this->header['major_v'] >= 7) {
              $this->players[$player_id]['actions'][] = $this->time;
              if(isset($this->players[$player_id]['actions_details'][dota_convert_action('buildmenu')])) {
              	$this->players[$player_id]['actions_details'][dota_convert_action('buildmenu')]++;
              } 
              else { 
	              $this->players[$player_id]['actions_details'][dota_convert_action('buildmenu')] = 1; 
              }
              $n+=1;
            } else {
              $n+=13;
            }
            break;

          // Minimap signal (ping)
          // Continue Game (BlockB) for WarCraft III patch version <= 1.06
          case 0x68:
            $n+=13;
            break;

          // Continue Game (BlockB)
          // Continue Game (BlockA) for WarCraft III patch version <= 1.06
          case 0x69:
          // Continue Game (BlockA)
          case 0x6A:
            $this->continue_game = 1;
            $n+=17;
            break;

          // Pause game
          case 0x01:
            $this->pause = 1;
            $n+=1;
            break;

          // Resume game
          case 0x02:
            $this->pause = 0;
            $n+=1;
            break;

          // Increase game speed in single player game (Num+)
          case 0x04:
            $n+=1;
            break;
          // Decrease game speed in single player game (Num-)
          case 0x05:
            $n+=1;
            break;

          // Set game speed in single player game (options menu)
          case 0x03:
            $n+=2;
            break;

          // Save game           
          case 0x06:
            $i=1;
            while ($actionblock{$n} != "\x00") {
              $n++;
            }
            $n+=1;
            $temp['time'] = $this->time;
            $temp['mode'] = 'Save';
            $temp['text'] = 'Save game.';
            $temp['player_name'] = $this->players[$temp['player_id']]['name'];
            $this->chat[] = $temp;
            break;

          // Save game finished
          case 0x07:
            $n+=5;
            break;

          // Only in scenarios, maybe a trigger-related command
          case 0x75:
            $n+=2;
            break;

/*
- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
0x6B - SyncStoredInteger actions           [ n bytes ] [APM-]
- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
 n byte  - GameCache  - null terminated string - On the observeded replays it seems to be always the same: A trigger name or identifier? - 
 n byte  - MissionKey - null terminated string - Player slot as a string. This info can and will be overridden by action 0x70 in replay of dota 6.39b and 6.39.
 n byte  - Key        - null terminated string - stat identifier, so far:
        "1" : kills, 
        "2" : deaths,
        "3" : creep kills,
        "4" : creep denies,
        "5" : Assists,
        "6" : EndGold,
        "7" : Neutrals,
        "8_x" : Inventory
        "9" : Hero
 1 dword - Value      - stat value associated to each identifier category.
 */
 
          case 0x6B:
            $GameCache = "";
            $MissionKey = "";
            $Key = "";
            $value = "";

            /* Get GameCache */
            while ( $n < $block_length && $actionblock{$n} != "\x00") {
              $n++;
              $GameCache .= $actionblock{$n};       
            }
            $n+=1; 
            /* Get MissionKey */
            while ( $n < $block_length && $actionblock{$n} != "\x00") {
                $MissionKey .= $actionblock{$n};
              $n++;
            }
            $n+=1;
            /* Get Key */
            while ( $n < $block_length && $actionblock{$n} != "\x00") {
                $Key .= $actionblock{$n};
              $n++;
            }
            $n+=1;       
            
            // In the case of the Key being 8, we're dealing with items, so we get the Item information as an object
            // In the case of the Key being 9, we're dealing with heroes, so we get the Hero information as an object
            if ($Key{0} == "8" || $Key == "9" ) {                 
                $value = strrev(substr($actionblock, $n, 4));
                
                if( $value == "\0\0\0\0") {
                    $value = 0;
                }
                else {
                    $value = dota_convert_itemid($value);
                }
            }
            // Otherwise $value holds the raw string.
            else {
                $value = unpack("Lval", substr($actionblock, $n, 4));
            }
            
             
            // Handle mode / bans / pools / picks 
            if($MissionKey == "Data" ) {
                
                /*
                // Setting levels
                if("Level" == substr($Key, 0, 5)) {
                    $level = substr($Key, 5);
                    $c_pid = $value['val'];


                    if(isset($this->stats[$l_pid])) {
                        $this->stats[$l_pid]->setLevelCap($level);

                        if(DOTA_REPLAY_DEBUG_ON) {
                            echo "Set level cap of [".$level."] for player ID [".$renamedWC3PlayerID."] named ".$this->players[$renamedWC3PlayerID]['name']." l_pid [".$l_pid."] <br />";
                        }
                    }
                }
                */

                // Detect POTM Arrow Accuracy (post 6.68)
                if(substr($Key, 0, strlen("AA_Total")) == "AA_Total") {
                    $pid = substr($Key, strlen("AA_Total"));
                    $this->stats[$pid]->setAA_Total($value['val']);
                }
                // Detect POTM Arrow Accuracy (post 6.68)
                else if(substr($Key, 0, strlen("AA_Hits")) == "AA_Hits") {
                    $pid = substr($Key, strlen("AA_Hits"));
                    $this->stats[$pid]->setAA_Hits($value['val']);
                }
                // Detect Pudge Hook Accuracy (post 6.68)
                else if(substr($Key, 0, strlen("HA_Total")) == "HA_Total") {
                    $pid = substr($Key, strlen("HA_Total"));
                    $this->stats[$pid]->setHA_Total($value['val']);
                }
                // Detect Pudge Hook Accuracy (post 6.68)
                else if(substr($Key, 0, strlen("HA_Hits")) == "HA_Hits") {
                    $pid = substr($Key, strlen("HA_Hits"));
                    $this->stats[$pid]->setHA_Hits($value['val']);
                }

                // Detect mode
                if( strstr($Key, "Mode") !== false ) {
                    $shortMode = substr($Key, 4, 2);
                    
                    switch ( $shortMode ) {
                        case "cd":
                            $this->dotaMode = new DotaModeCD();
                            break;
                        case "cm":
                            $this->dotaMode = new DotaModeCM();
                            break;
                    }
                }
                
                
                // CD Mode - TODO
                // At the moment the CD Mode broadcasting seems to be broken in DOTA 6.66?
                // Can only find Sentinel packets in 0x6B packets
                if(isset($this->dotaMode) && $this->dotaMode->getShortName() == 'cd') {
                    /*
                    $entity_id = strrev(substr($actionblock, $n, 4));
                    $entity = dota_convert_itemid($entity_id);
                    
                    // CD - Constructing hero pool
                    if ( strstr($Key, 'Pool') !== false) {
                        $this->dotaMode->addHeroToPool($entity);
                    }
                    
                    // Gather bans
                    else if ( strstr($Key, 'Ban') !== false ) {
                        if( !$this->inPickMode ) {
                            $this->inPickMode = true;
                            $this->pause = true;        
                        }
                        
                        
                        if($Key == 'Ban1') {
                            $entity->extra = 0;           
                        }
                        else if ($Key == 'Ban7') {
                            $entity->extra = 1;    
                        }
                        
                        $this->dotaMode->addHeroToBans($entity);
                    }
                    
                    // Gather picks
                    else if ( strstr($Key, 'Pick') !== false ) {
                        if($Key == 'Pick1') {
                            $entity->extra = 0;           
                        }
                        else if ($Key == 'Pick7') {
                            $entity->extra = 1;    
                        }
                        
                        $this->dotaMode->addHeroToBans($entity);
                    }               
                    
                    // Unpause the game - TODO Nonstatic number
                    if ( $this->dotaMode->getNumPicked() >= 10 ) {
                        $this->inPickMode = false;
                        $this->pause = false;
                        
                        $this->bans = $this->dotaMode->getBans();
                        $this->picks = $this->dotaMode->getPicks();
                    }
                    */
                }                
                else if( strstr($Key, 'Ban') !== false ) {
                    // Detected CM mode 
                    if( !$this->inPickMode ) {
                        $this->inPickMode = true;
                        $this->pause = true;        
                    }
                    
                    $entity_id = strrev(substr($actionblock, $n, 4));
                    $entity = dota_convert_itemid($entity_id);
                    
                    if (isset($this->SlotToPlayerMap[$Key{3}]) ) {
                        $team_pid = $this->SlotToPlayerMap[$Key{3}];     
                    }
                    else {
                        $team_pid = $Key{3};
                    }
                    if($Key == 'Ban1') {
                        $entity->extra = 0;           
                    }
                    else if ($Key == 'Ban7') {
                        $entity->extra = 1;    
                    }
                    else {
                        $entity->extra = $this->players[$team_pid]['team'];
                    }  
                    
                    // 3-2 ban split CM Mode in versions 6.68+
                    if( ($this->game['dota_major'] == 6 && $this->game['dota_minor'] >= 68) || $this->game['dota_major'] > 6) {
                        // If we've already got all bans for phase 1 (6 bans) and we get a new ban action,
                        // then we need to start phase 2
                        if($this->dotaMode->banPhaseComplete()) {
                           $this->dotaMode->setBansPerTeam(5);
                        }

                        $this->dotaMode->addHeroToBans($entity);
                    }
                    else {

                    		$this->bans[] = $entity;
                    		$this->bans_num++; 
 		                }    
	            }
            }
            // Determine the winner if possible
            // 1 = Sentinel and 2 = Scourge
            else if("Global" == $MissionKey && "Winner" == $Key) {
                $this->extra['parsed_winner'] = ($value['val'] == 1 ? 'Sentinel' : 'Scourge');
            }

            // Handle hero assignment and stats collecting 
            if(is_numeric($MissionKey) && $MissionKey > -1 && $MissionKey < 13) {
                
                 // Map the Slot ID to the proper player ID (Wc3 - dota)
                 if (isset($this->SlotToPlayerMap[$MissionKey]) ) {
                    $pid = $this->SlotToPlayerMap[$MissionKey];     
                 }
                 else {
                        $pid = $MissionKey;
                 } 
                
                 // Set heroes for players, including swap & repick handling 
                 if($Key == 9) {
                     
                    // This is a failsafe for when a random hero is picked by the game in CM / CD? mode
                    if ( $this-> inPickMode ) {
                        $this->pause = false;
                        $this->inPickMode = false;
                    }
                     
                     
                    $x_pid = $pid;       
                    $x_hero = $value;
                    
                    // End game stats, no hero picked by player
                    if ( !is_object($x_hero) ) {
                        // Handle? - TODO
                    }
                    // If hero picked before player IDs are sent out
                    else if ( !isset($this->stats[$x_pid] )) {
                        $this->preAnnouncePick[$x_pid] = $x_hero;
                    }
                    
                    // Set hero for player if player's hero ain't set yet
                    else if ( !$this->stats[$x_pid]->isSetHero() ) {
                        // Assign as player's hero
                        $this->stats[$x_pid]->setHero( new DotaActivatedHero($x_hero) );
                        
                        // Add to Activated Heroes list
                        $this->ActivatedHeroes[$x_hero->getName()] = $this->stats[$x_pid]->getHero(); 
                    }
                    
                    // Player's either swapping, repicking or hero was hero-replaced at the end
                    else {
                       
                       // If the Hero's already been Activated either swapping or end game's taking place
                       if ( isset($this->ActivatedHeroes[$x_hero->getName()] ) ) {
                           // Swapping taking place
                           if ( $this->stats[$x_pid]->getHero()->getName() != $x_hero->getName() ) {
                                // Update ownership of previously Activated Hero
                                $this->stats[$x_pid]->setHero( $this->ActivatedHeroes[$x_hero->getName()] );
                                
                           }
                           // End game statistics
                           else {
                               // Todo
                           } 
                       }
                       
                       // Hero-replacement ( Ezalor, etc) or repicking's taking place
                       else {
                           // If the name matches we're dealing with a morphing ability, otherwise it's repicking                   
                           if ( $this->stats[$x_pid]->getHero()->getName() != $x_hero->getName() ) {

                                // Assign as player's new hero
                                $this->stats[$x_pid]->setHero( new DotaActivatedHero($x_hero) );
                        
                                // Add to Activated Heroes list
                                $this->ActivatedHeroes[$x_hero->getName()] = $this->stats[$x_pid]->getHero();
                            }
                       }
                    }
                } 
    
                
                
                // Stats collecting
                switch ($Key{0}) {
                    case "i":      // ID 
                        $pid = $value['val'];

                        /*
                        // We're dealing with a switch
                        if(isset($this->SlotToPlayerMap[$MissionKey]) && $this->SlotToPlayerMap[$MissionKey] != $pid) {
                            // Update the Dota_ID
                            $this->players[$MissionKey]['dota_id'] = $pid;
                            echo "CHANGING PID";
                        }
                        */

                        $this->SlotToPlayerMap[$MissionKey] = $pid;
                        
                        // For handling SP
                        $this->translatedDotaID[$pid] = $MissionKey;
                         
                        if(!isset($this->stats[$pid])) {
                            $this->stats[$pid] = new DotaPlayerStats($pid);
                            
                            // Check if there's any pending delayed hero for the player
                            if( isset( $this->preAnnouncePick[$MissionKey]) ) {
                                $x_hero = $this->preAnnouncePick[$MissionKey];
                                $this->stats[$pid]->setHero( new DotaActivatedHero($x_hero) );
                                
                                // Add to Activated Heroes list
                                $this->ActivatedHeroes[$x_hero->getName()] = $this->stats[$pid]->getHero();
                            }
                            // Check if there's any pending delayed skilling for the player
                            if(isset( $this->preAnnounceSkill[$pid])) {
                                $this->stats[$pid]->addDelayedSkill ( 
                                    $this->preAnnounceSkill[$pid]['skill'], 
                                    $this->preAnnounceSkill[$pid]['time'],
                                    $this->preAnnounceSkill[$pid]['heroId'] );
                                    
                                
                            }
                        }       
                        break; 
                    case "1":
                        $this->stats[$pid]->HeroKills = $value['val'];        
                        break;    
                    case "2":
                        $this->stats[$pid]->Deaths = $value['val'];
                        break;
                    case "3":
                        $this->stats[$pid]->CreepKills = $value['val'];
                        break;
                    case "4":
                        $this->stats[$pid]->CreepDenies = $value['val'];
                        break;
                    case "5":
                        $this->stats[$pid]->Assists = $value['val'];
                        break;
                    case "6":
                        $this->stats[$pid]->EndGold = $value['val'];
                        break;
                    case "7":
                        $this->stats[$pid]->Neutrals = $value['val'];
                        break;
                    // Inventory
                    case "8":
												if(isset($this->stats[$pid])) {
                        	$this->stats[$pid]->Inventory[$Key{2}] = $value;
												}
                        break;
                }
                
            }   
            
            
            
            
            if(DOTA_REPLAY_DEBUG_ON) {
                echo "<hr />Debug time: ".dota_convert_time($this->time)." <br />";
                echo "GameCache: ".$GameCache." <br />";
                echo "MissionKey: ".$MissionKey." <br />";
                echo "Key: ".$Key." <br />";
                echo "Value: ".(is_object($value) ? $value->getName() : $value['val'])." <br /><hr />";
            }
            
            
            $n+=4; // 1 dword aka value
          break;
            
 /* Add Seven - Most likely outdated after 59c*/
 /* Didn't exactly work out as described. using assumed 28 byte size for now...
 - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
0x70 - Unknown                               [ n bytes ] [APM-]
- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
 n byte  - unknown1 null terminated string - seems to be "dr.x" each time so far. See action 0x6B anyway.
 n byte  - unknown2 null terminated string
 n byte  - unknown3 null terminated string
 
 Notes:
  o Observed in dota replay version 6.39 and 6.39b - at end of the game. Not used anymore starting in 6.44.
  o See action 0x6B the unknown* of 0x6B match the unknown* of this action.
  o This action was used to determine the winner side based on unknown3 value 1=sentinel , 2=scourge.
 */        
          case 0x70:
               /* while ($actionblock{$n} != "\x00") {
                  $n++;
                }
                $n+=1; //First string
                
                while ($actionblock{$n} != "\x00") {
                  $n++;
                }
                $n+=1; //Second string
                
                while ($actionblock{$n} != "\x00") {
                  $n++;
                }
                $n+=1; //Third string
                 */  
                 $n+=28;
            break;
          
            
          default:
            $temp = '';

            for ($i=3; $i<$n; $i++) {
              $temp .= sprintf('%02X', ord($actionblock{$i})).' ';
            }         
               
            $temp .= '['.sprintf('%02X', ord($actionblock{$n})).'] ';
            $alen=strlen($actionblock);
            for ($i=1; $n+$i<$alen; $i++) {
              $temp .= sprintf('%02X', ord($actionblock{$n+$i})).' ';
            }
            
            $this->errors[$this->time] = $this->players[$player_id]['name'].': Unknown action: 0x'.sprintf('%02X', $action).', prev: 0x'.sprintf('%02X', $prev).', Player_id='.$this->players[$player_id]['name'].' dump: '.$temp;
            $n+=2;
//            echo $this->errors[$this->time];
        }
      }
      $was_deselect = ($action == 0x16);
      $was_subupdate = ($action == 0x19);

    }
  }

  protected function _cleanup() {

    if(isset($this->dotaMode)) {
        // Legacy support for picks / bans
        $this->bans = (count($this->bans) > 0 ? $this->bans : $this->dotaMode->getBans());
        $this->picks = (count($this->picks) > 0 ? $this->picks : $this->dotaMode->getPicks());
    }

    $this->bans_num = count($this->bans);
    $this->picks_num = count($this->picks);


    // Process delayed skills
    foreach ( $this->stats as $player ) {
        $player->processDelayedSkills();
    }  
      
    $wc3idToTime = array();
    $wc3idToLeaveResult = array();
    $wc3idToItems = array();
    $wc3idToActions = array();

    // Construct leave results for handling switch
    foreach($this->players as $player) {
        if(!isset($player['player_id']) || !isset($player['dota_id']))
            continue;

        // Handle player left events
        if(isset($player['time'])) {
            $wc3idToTime[$player['player_id']] = $player['time'];
        }
        else {
            $wc3idToTime[$player['player_id']] = $player['time'] = $this->header['length'];
        }
        if(isset($player['leave_result'])) {
            $wc3idToLeaveResult[$player['player_id']] = $player['leave_result'];
        }
        else {
            $wc3idToLeaveResult[$player['player_id']] = "Finished";
        }

        $wc3idToItems[$player['player_id']] = isset($player['items']) ? $player['items'] : "";
        $wc3idToActionsDetails[$player['player_id']] = isset($player['actions_details']) ? $player['actions_details'] : "";
        $wc3idToActions[$player['player_id']] = isset($player['actions']) ? $player['actions'] : "";
    }

    // Player's time cleanup
	foreach ($this->players as $player) {
      if (!isset($player['player_id'])) continue;
      
      // Get player's WC3 ID
      $wc3pid = $player['player_id'];
      
      // Get 'game' dota ID of player
      if(!isset($player['dota_id'])) continue;
      
      $gameDotaId = $player['dota_id'];
      
      // Get 'internal' dota ID of player
      $intDotaId = $this->translatedDotaID[$gameDotaId];
      
      // Get base game ID based on internal ID
      $baseGameDotaId = dota_getGameDotaId($intDotaId);
      
      // Get the renamed WC3 player ID after shuffling
      $renamedWC3PlayerID = $this->dotaIdToWc3id[$baseGameDotaId];
      
      // Change the name
      $this->players[$wc3pid]['name'] = $this->wc3idToNames[$renamedWC3PlayerID];
      
      // For handling SWITCH and leave time / result / items and actions
      $this->players[$wc3pid]['time'] =  $wc3idToTime[$renamedWC3PlayerID];
      $this->players[$wc3pid]['leave_result'] = $wc3idToLeaveResult[$renamedWC3PlayerID];
      $this->players[$wc3pid]['items'] = $wc3idToItems[$renamedWC3PlayerID];
      $this->players[$wc3pid]['actions_details'] = $wc3idToActionsDetails[$renamedWC3PlayerID];
      $this->players[$wc3pid]['actions'] = $wc3idToActions[$renamedWC3PlayerID];

      if (!isset($player['time']) || !$player['time']) {
        $this->players[$player['player_id']]['time'] = $this->header['length'];
      }
	}

  
  // APM
  foreach ($this->players as $player) {
        if (!isset($player['player_id'])) 
            continue;

        $spa = $this->players[$player['player_id']]['actions'];

        $this->players[$player['player_id']]['apm'] = count($spa);

        $ni=30000;
        $ii=0;
        $apm = 0;
        $astr='';
        $temp_time = $this->players[$player['player_id']]['time'];
        $sum = 0;
        foreach ($spa as $atime) {
          $sum += strlen($atime);
          if ($atime < $ni) {
            $ii++;
            $apm++;
          } 
          else {
            while ($atime>$ni) {
              $astr.=','.$ii;
              $ii=0;
              $ni+=30000;
            }
          }
        }
        $this->players[$player['player_id']]['actions'] = substr($astr,1);
  }

        // splitting teams
        foreach ($this->players as $player_id=>$info) {
            if (isset($info['team'])) { // to eliminate zombie-observers caused by Waaagh!TV
		        $this->teams[$info['team']][$player_id] = $info;
		    }
        }
 } // function _cleanup
  
} // class replay_dota
?>
