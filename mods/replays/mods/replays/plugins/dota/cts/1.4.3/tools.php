<?php
/******************************************************************************
Last revision:
- Author: Seven
- Email: zabkar@gmail.com  (Subject CDP)
- Date: 10.11.2010 (1.4.1)
Modified by Mindcrime (mindcrime@gab-clan.org) for ClanSphere/PHP5 plugin:
- Cleanup warnings
- Cleanup functions
******************************************************************************/

require_once('config.php');

/**
* Support function for determening the Dota version and appropriate XML file
* based on the map name extracted from the replay data
*
* @param mixed $mapName - Map name extracted from the replay
* @param mixed $dota_major - Address to return the Dota Major version number to
* @param mixed $dota_minor - Address to return the Dota Minor version number to
*
* @return Returns the filename of the XML file to parse the replay with
*/
function dota_getMapName($mapName, &$dota_major, &$dota_minor) {
    $map_file = substr($mapName, strrpos($mapName, "\\") + 1);
    $map_file = substr($map_file, 0, -4); 
    
    // Check if map version is valid
    preg_match("/([0-9]{1,1})\.([0-9]{1,2})([a-zA-Z]{0,1})/", $map_file, $matches);
    if( count($matches) < 4 ) {
        // Use default
        return DOTA_REPLAY_DEFAULT_XML_MAP; 
    }   
    else {
        $dota_major = $matches[1];
        $dota_minor = $matches[2];
        $dota_subver = $matches[3];
    
        // Check if an appropriate .xml file with subversion exists ( ie. dota.allstars.v6.60b.xml )
        if ( file_exists(DOTA_REPLAY_MAPS_FOLDER.PATH_SEPARATOR.DOTA_REPLAY_XML_MAP_BASE_NAME.$dota_major.'.'.$dota_minor.$dota_subver.'.xml') ) {
            // Use it 
            return DOTA_REPLAY_XML_MAP_BASE_NAME.$dota_major.'.'.$dota_minor.$dota_subver.'.xml';  
        }
        // Check if an appropriate .xml file without subversion exists  ( ie. dota.allstars.v6.60.xml )
        else if ( file_exists(DOTA_REPLAY_MAPS_FOLDER.PATH_SEPARATOR.DOTA_REPLAY_XML_MAP_BASE_NAME.$dota_major.'.'.$dota_minor.'.xml') ) {
             // Use it
             return DOTA_REPLAY_XML_MAP_BASE_NAME.$dota_major.'.'.$dota_minor.'.xml';   
        }
        // If no file is found use the default, but only allow 6.59 or newer.
        else if ( $dota_major < 6 || ( $dota_major == 6 && $dota_minor < 59 ) ) {
            echo 'Unsupported version of Dota.';
            return false;
        }
        else {
            // Use default
            return DOTA_REPLAY_DEFAULT_XML_MAP;
        }
    }
}

/**
* @desc Class for handling activated / used Heroes
*/
class DotaActivatedHero {
    private $Data;
    private $Skills;
    // Used to for evading duplicated actions
    private $lastSkilledTime = 0;
    // Used for limiting skill levels
    private $limitStats = 0;
    
    
    /**
    * Constructor
    * @param mixed $heroData
    */
    public function __construct($heroData) {
        $this->Data = $heroData;
        $this->Skills = array();
    }
    
    /**
    * Add learned skill to hero
    * 
    * @param mixed $skill - Skill Data 
    * @param mixed $time - Time learned ( non converted in miliseconds )
    */
    public function setSkill($skill, $time) {
        
        // TODO - Handle duplication / Level limit / Skill limit etc
        // Level limit Common skills A0NR and Aamk
        if($this->limitStats >= 10 && ($skill->getId() == "Aamk" || $skill->getId() == "A0NR")) {
            return;
        }

        // Handling duplication
        if($time - $this->lastSkilledTime < DOTA_DUPLICATE_SKILLING_TIME_LIMIT)
            return;

        $this->lastSkilledTime = $time;

        
        // Limit learned skills to 25
        if ( count ( $this->Skills ) >= 25 ) {
            return;    
        }
        
        // Add skill  
        $this->Skills[$time] = $skill;

        if($skill->getId() == "Aamk" || $skill->getId() == "A0NR") {
            $this->limitStats++;
        }
    }
    
    /**
    * @return Skills[time in miliseconds] = XML Skill data
    */
    public function getSkills() {
        return $this->Skills;
    }
    
    /**
    * Get Hero ID
    * @return String Hero ID
    */
    public function getId() {
        return $this->Data->getId();
    }
    
    public function getName() {
        return $this->Data->getName();
    }
    
    /**
    * Return XML data for Hero
    * @return data - XML Data for Hero
    */
    public function getData() {
        return $this->Data;
    }
    
    /**
    * @return int Level
    */
    public function getLevel() {
        return count ( $this->Skills);
    }
} // class DotaActivatedHero

/**
* @desc Class for storing Player's end game statistics  
*/
class DotaPlayerStats {
    private $PID;
      
    public $HeroKills;
    public $Deaths;
    public $CreepKills;
    public $CreepDenies;    
    public $Assists;
    public $EndGold;
    public $Neutrals;
    
    private $DelayedSkills;
    
    public $Inventory;      // Array with 6 elements, for the 6 inventory slots
    
    private $Hero;    // Class ActivatedHero
    
    public $AA_Total;
    public $AA_Hit;
    public $HA_Total;
    public $HA_Hit;

    // Dota broadcasts hero levels, so this is used to check for skill duplications and similiar incidents.
    private $levelCap = 1;

    /**
    * @desc Constructor
    * @param Player's ID    
    */
    public function __construct($PID) {
        $this->PID = $PID;
        $this->Inventory = array();
        $this->Hero = false; 
        
        $this->DelayedSkills = array();
    }

    public function setAA_Total($var) {
        $this->AA_Total = $var;
    }
    public function setAA_Hits($var) {
        $this->AA_Hits = $var;
    }
    public function setHA_Total($var) {
        $this->HA_Total = $var;
    }
    public function setHA_Hits($var) {
        $this->HA_Hits = $var;
    }
    
    /**
    * 
    * @desc Set Hero
    * @param mixed Hero - ActivatedHero class
    */
    public function setHero($hero) {
        $this->Hero = $hero;
    }
    
    /**
    * Return hero data - ActivatedHero class
    */
    public function getHero() {
        if(!isset($this->Hero)) {
            return false;
        }
        return $this->Hero;
    }
    
    /**
    * Get the current level cap
    * @returns Integer - Current level cap
    */
    public function getLevelCap() {
        return $this->levelCap;
    }
    /**
    * Set the level cap
    * @param $levelCap - Level cap to set
    */
    public function setLevelCap($levelCap) {
        $this->levelCap = $levelCap;
    }

    /**
    * @return boolean TRUE if hero is set, FALSE otherwise
    */
    public function isSetHero() {
        if ( $this->Hero === false) {
            return false;
        }
        return true;
    }
    
    /**
    * Queue up skills, to be added later
    * 
    * @param mixed $skill_data Skill Data
    * @param mixed $time Replay time in miliseconds
    * @param String $heroId Hero ID
    */
    public function addDelayedSkill( $skill_data, $time, $heroId ) {
        $this->DelayedSkills[] = array( 'skill_data' => $skill_data, 'time' => $time, 'heroId' => $heroId );
    }
    
    /**
    * Process delayed skills
    * 
    */
    public function processDelayedSkills() {
        if ( count( $this->DelayedSkills) > 0 ) {
            
            foreach( $this->DelayedSkills as $element ) {
                // if ( !is_object($this->Hero) ) continue;

                if ( $this->Hero->getId() == $element['heroId'] ) {
                   $this->Hero->setSkill ( $element['skill_data'], $element['time'] );
                }
                // TODO: Otherwise added the skill to the appropriate activated hero
            }
        }
    }
  } // class DotaPlayerStats
  
  /**
  * @desc Converts the player's color to the proper Dota ID
  * @param Player's color
  */
  function dota_getDotaId($color) {
      switch ($color) {
          case "blue":
            return 1;
          case "teal":
            return 2;
           case "purple":
            return 3;
           case "yellow":
            return 4;
           case "orange":
            return 5;
           case "pink":
            return 6;
           case "gray":
            return 7;
           case "lightblue":
            return 8;
           case "darkgreen":
            return 9;
           case "brown":
            return 10;
           default:
            return 0;
      } 
  }
  
  /**
  * @desc Converts the internal Dota ID mapping to the Game one
  * assuming that each team has 6 IDs reserved, team 2 starts
  * at ID 7 internally, but maps it to 6 as far as the players
  * game is concerned
  * 
  * @param Internal DOTA ID (1-12)
  */
  function dota_getGameDotaId($internalDotaID) {
      switch ($internalDotaID) {
          // Team 1
          case 1:
            return 1;
          case 2:
            return 2;
           case 3:
            return 3;
           case 4:
            return 4;
           case 5:
            return 5;
           // Team 2 
           case 7:
            return 6;
           case 8:
            return 7;
           case 9:
            return 8;
           case 10:
            return 9;
           case 11:
            return 10;
           default:
            return 0;
      } 
  }
  
/**
* @desc Returns the appropriate Entity object from the previously parsed XML Data.
* @param EntityID
*/
function dota_convert_itemid($value) {
    
    // When DOTA_REPLAY_DEBUG_ON_TOOLS is turned on true, we can capture unknown ID's
    if( DOTA_REPLAY_DEBUG_ON_TOOLS ) { 
        if ( ! isset ( $GLOBALS['xml_data']->HashMap[$value] ) ) {
            echo 'Unknown ID: ['.$value.']. <br />';  
        }
        else { 
            echo 'Known ID: ['.$value.']. <br />';
        }
    }
    
    if(empty($value) || !isset($GLOBALS['xml_data']->HashMap[$value])) {
        return false;
    }
   
    return $GLOBALS['xml_data']->HashMap[$value];    
}

/*
 * The following functions are based on Tedi Rachmadi's "RESHINE"
 */
function dota_convert_bool($value) {
  switch ($value) {
    case 0x00: $value = false; break;
    default: $value = true;
  }
  return $value;
}

function dota_convert_speed($value) {
  switch ($value) {
    case 0: $value = 'Slow'; break;
    case 1: $value = 'Normal'; break;
    case 2: $value = 'Fast'; break;
  }
  return $value;
}

function dota_convert_visibility($value) {
  switch ($value) {
    case 0: $value = 'Hide Terrain'; break;
    case 1: $value = 'Map Explored'; break;
    case 2: $value = 'Always Visible'; break;
    case 3: $value = 'Default'; break;
  }
  return $value;
}

function dota_convert_observers($value) {
  switch ($value) {
    case 0: $value = 'No Observers'; break;
    case 2: $value = 'Observers on Defeat'; break;
    case 3: $value = 'Full Observers'; break;
    case 4: $value = 'Referees'; break;
  }
  return $value;
}

function dota_convert_game_type($value) {
  switch ($value) {
    case 0x01: $value = 'Ladder 1vs1/FFA'; break;
    case 0x09: $value = 'Custom game'; break;
    case 0x0D: $value = 'Single player/Local game'; break;
    case 0x20: $value = 'Ladder team game (AT/RT)'; break;
    default: $value = 'unknown';
  }
  return $value;
}

function dota_convert_color($value) {
  switch ($value) {
        case 0: $value = 'red'; break;
        case 1: $value = 'blue'; break;
        case 2: $value = 'teal'; break;
        case 3: $value = 'purple'; break;
        case 4: $value = 'yellow'; break;
        case 5: $value = 'orange'; break;
        case 6: $value = 'green'; break;
        case 7: $value = 'pink'; break;
        case 8: $value = 'gray'; break;
        case 9: $value = 'lightblue'; break;
        case 10: $value = 'darkgreen'; break;
        case 11: $value = 'brown'; break;
        case 12: $value = 'observer'; break;
  }
  return $value;
}

function dota_convert_color_html($value) {
  switch ($value) {
		case 0: return '#D93636'; break;
		case 1: return '#608FBF'; break;
		case 2: return '#60BFA9'; break;
		case 3: return '#9B60BF'; break;
		case 4: return '#E4E573'; break;
		case 5: return '#CC8029'; break;
		case 6: return '#86E573'; break;
		case 7: return '#D96CB1'; break;
		case 8: return '#BFBFBF'; break;
		case 9: return '#79C2F2'; break;
		case 10: return '#59B365'; break;
		case 11: return '#BF8F60'; break;
		case 12: return '#FFFFFF'; break;
  }
  return $value;
}

function dota_convert_race($value) {
  switch ($value) {
    case 'ewsp': case 0x04: case 0x44: $value = 'Sentinel'; break;
    case 'uaco': case 0x08: case 0x48: $value = 'Scourge'; break;
    default: $value = 0; // do not change this line
  }
  return $value;
}

function dota_convert_ai($value) {
  switch ($value) {
      case 0x00: $value = "Easy"; break;
      case 0x01: $value = "Normal"; break;
      case 0x00: $value = "Insane"; break;
  }
  return $value;
}

function dota_convert_select_mode($value) {
  switch ($value) {
      case 0x00: $value = 'Team & race selectable'; break;
      case 0x01: $value = 'Team not selectable'; break;
      case 0x03: $value = 'Team & race not selectable'; break;
      case 0x04: $value = 'Race fixed to random'; break;
      case 0xcc: $value = 'Automated Match Making (ladder)'; break;
  }
  return $value;
}

function dota_convert_chat_mode($value) {
  switch ($value) {
    case 0x00: $value = 0; break;   //All
    case 0x01: $value = 1; break;   //Allies
    case 0x02: $value = 2; break;   //Observers
    case 0xFE: $value = 3; break;   //The game has been paused.
    case 0xFF: $value = 4; break;   //The game has been resumed.
    default: $value -= 2;           // this is for private messages
  }
  return $value;
}

function dota_convert_buildingid($value) {
  // non-ASCII ItemIDs
  if (ord($value{0}) < 0x41 || ord($value{0}) > 0x7A) {
    return 0;
  }

  switch ($value) {
    case 'halt': $value = 'Altar of Kings'; break;
    case 'harm': $value = 'Workshop'; break;
    case 'hars': $value = 'Arcane Sanctum'; break;
    case 'hbar': $value = 'Barracks'; break;
    case 'hbla': $value = 'Blacksmith'; break;
    case 'hhou': $value = 'Farm'; break;
    case 'hgra': $value = 'Gryphon Aviary'; break;
    case 'hwtw': $value = 'Scout Tower'; break;
    case 'hvlt': $value = 'Arcane Vault'; break;
    case 'hlum': $value = 'Lumber Mill'; break;
    case 'htow': $value = 'Town Hall'; break;

    case 'etrp': $value = 'Ancient Protector'; break;
    case 'etol': $value = 'Tree of Life'; break;
    case 'edob': $value = 'Hunter\'s Hall'; break;
    case 'eate': $value = 'Altar of Elders'; break;
    case 'eden': $value = 'Ancient of Wonders'; break;
    case 'eaoe': $value = 'Ancient of Lore'; break;
    case 'eaom': $value = 'Ancient of War'; break;
    case 'eaow': $value = 'Ancient of Wind'; break;
    case 'edos': $value = 'Chimaera Roost'; break;
    case 'emow': $value = 'Moon Well'; break;

    case 'oalt': $value = 'Altar of Storms'; break;
    case 'obar': $value = 'Barracks'; break;
    case 'obea': $value = 'Beastiary'; break;
    case 'ofor': $value = 'War Mill'; break;
    case 'ogre': $value = 'Great Hall'; break;
    case 'osld': $value = 'Spirit Lodge'; break;
    case 'otrb': $value = 'Orc Burrow'; break;
    case 'orbr': $value = 'Reinforced Orc Burrow'; break;
    case 'otto': $value = 'Tauren Totem'; break;
    case 'ovln': $value = 'Voodoo Lounge'; break;
    case 'owtw': $value = 'Watch Tower'; break;

    case 'uaod': $value = 'Altar of Darkness'; break;
    case 'unpl': $value = 'Necropolis'; break;
    case 'usep': $value = 'Crypt'; break;
    case 'utod': $value = 'Temple of the Damned'; break;
    case 'utom': $value = 'Tomb of Relics'; break;
    case 'ugol': $value = 'Haunted Gold Mine'; break;
    case 'uzig': $value = 'Ziggurat'; break;
    case 'ubon': $value = 'Boneyard'; break;
    case 'usap': $value = 'Sacrificial Pit'; break;
    case 'uslh': $value = 'Slaughterhouse'; break;
    case 'ugrv': $value = 'Graveyard'; break;

    default: $value = 0;
  }
  return $value;
}

function dota_convert_action($value) {
  switch ($value) {
    case 'rightclick': $value = 'Right click'; break;
    case 'select': $value = 'Select / deselect'; break;
    case 'selecthotkey': $value = 'Select group hotkey'; break;
    case 'assignhotkey': $value = 'Assign group hotkey'; break;
    case 'ability': $value = 'Use ability'; break;
    case 'basic': $value = 'Basic commands'; break;
    case 'buildtrain': $value = 'Build / train'; break;
    case 'buildmenu': $value = 'Enter build submenu'; break;
    case 'heromenu': $value = 'Enter hero\'s abilities submenu'; break;
    case 'subgroup': $value = 'Select subgroup'; break;
    case 'item': $value = 'Give item / drop item'; break;
    case 'removeunit': $value = 'Remove unit from queue'; break;
    case 'esc': $value = 'ESC pressed'; break;
  }
  return $value;
}

function dota_conv_col2($value) {
  if ($value<6){return $value-1;}else{return $value-2;}
}

function dota_convert_time($value) {
  $output = sprintf('%02d', intval($value/60000)).':';
  $value = $value%60000;
  $output .= sprintf('%02d', intval($value/1000));

  return $output;
}

function dota_convert_yesno($value) {
  switch ($value) {
    case 0x00: $value = 'No'; break;
    default: $value = 'Yes';
  }
  return $value;
}  
  
?>
