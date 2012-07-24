<?php 

global $cs_main;
$teamspeakid = (isset($_POST['teamspeakid'])) ? intval($_POST['teamspeakid']) : (isset($_GET['teamspeakid']) ? intval($_GET['teamspeakid']) : 0);
if (!empty($teamspeakid))
{
	$select = 'teamspeak_id, teamspeak_access, teamspeak_version, teamspeak_ip, teamspeak_udp, teamspeak_tcp, teamspeak_admin, teamspeak_adminpw, teamspeak_sadmin, teamspeak_sadminpw, teamspeak_active, teamspeak_charset, teamspeak_register';
	$where = 'teamspeak_id  = ' . $teamspeakid;
	$cs_teamspeak = cs_sql_select(__FILE__,'teamspeak',$select,$where);
} else {
	$select = 'teamspeak_access, teamspeak_version, teamspeak_ip, teamspeak_udp, teamspeak_tcp, teamspeak_admin, teamspeak_adminpw, teamspeak_sadmin, teamspeak_sadminpw, teamspeak_active, teamspeak_charset, teamspeak_register';
	$where = 'teamspeak_active = 1';
	$cs_teamspeak = cs_sql_select(__FILE__,'teamspeak',$select,$where);
}

$adr = $cs_teamspeak['teamspeak_ip'];    //Server-Address oder IP
$ver = $cs_teamspeak['teamspeak_version']; //
$udp = $cs_teamspeak['teamspeak_udp'];    //UDP-Port (default 8767, 9987)
$tcp = $cs_teamspeak['teamspeak_tcp'];    //TCP-Queryport (default 51234, 10011) 

// Admin User Daten:
$useradmin = $cs_teamspeak['teamspeak_admin'];
$userpw = cs_crypt(base64_decode($cs_teamspeak['teamspeak_adminpw']), $cs_main['crypt_key']);

// Superadmin User Daten
$suseradmin = $cs_teamspeak['teamspeak_sadmin'];
$suserpw = cs_crypt(base64_decode($cs_teamspeak['teamspeak_sadminpw']), $cs_main['crypt_key']);
$teamspeakcharset = $cs_teamspeak['teamspeak_charset'];
/* no access to register if disallowed or not enough access */
$teamspeakregister = ($cs_teamspeak['teamspeak_register'] == 0 || $account['access_teamspeak'] < $cs_teamspeak['teamspeak_register'] ? false : true);
/* no access to view if not enough access */
$teamspeakaccess = ($account['access_teamspeak'] < $cs_teamspeak['teamspeak_access'] ? false : true);

require_once('mods/teamspeak/functions.php');
?> 
