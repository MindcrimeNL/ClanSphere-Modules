<?php
/* from reshine */
// Set to true for verbose debug output
define('DOTA_REPLAY_DEBUG_ON', false);

// Used for CM-mode picking, for now
define('DOTA_REPLAY_NUM_OF_BANS', 8);
define('DOTA_REPLAY_NUM_OF_PICKS', 10);

// to know when there is a need to load next block
define('DOTA_REPLAY_MAX_DATABLOCK', 1500);
// for preventing duplicated actions
define('DOTA_REPLAY_ACTION_DELAY', 1000);

/* from tools */
// This is the base name for map XML files, 
// for instance a proper filename would be dota.allstars.v6.59.xml, 
// thus making the BASE name dota.allstars.v 
define('DOTA_REPLAY_XML_MAP_BASE_NAME', 'dota.allstars.v');

// Map folder, no trailing slash
define('DOTA_REPLAY_MAPS_FOLDER', 'mods/replays/plugins/dota/cts/1.4.1/maps');

// Default .xml file - make sure it exists.
define('DOTA_REPLAY_DEFAULT_XML_MAP', 'dota.allstars.v6.68.xml'); 

// Verbose debug output
define('DOTA_REPLAY_DEBUG_ON_TOOLS', false);

// Define in MS the minimal amount of time between two consecutive skill additions
define('DOTA_DUPLICATE_SKILLING_TIME_LIMIT', 200);

?>
