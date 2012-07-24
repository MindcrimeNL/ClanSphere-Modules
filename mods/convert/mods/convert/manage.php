<?php
// Geh aB Clan 2010 - www.gab-clan.org
// $Id$

$cs_lang = cs_translate('convert');

/* currently Webspell only */
require_once('mods/convert/classes/ConvertWebspell.php');

$data = array();
$data['convert']['fake'] = 'checked';
$data['convert']['members'] = 'checked';
$data['convert']['wars'] = 'checked';
$data['convert']['news'] = 'checked';
$data['convert']['board'] = 'checked';
$data['convert']['cat_wars'] = 0;
$data['convert']['cat_games'] = 0;
$data['convert']['url'] = 'http://www.website.com/';
$data['convert']['user'] = '';
$data['convert']['pass'] = '';
$data['convert']['name'] = '';
$data['convert']['prefix'] = 'ws_';
$data['convert']['charset'] = 'ISO-8859-1';
$data['convert']['host'] = 'localhost';
$data['convert']['port'] = '3306';
$data['convert']['type'] = 'mysql';
$data['convert']['news_language'] = 'de';
$data['errors'] = array();
$data['statistics'] = array('users' => 0, 'squads' => 0, 'members' => 0,
														'games' => 0, 'wars' => 0, 'clans' => 0,
														'news' => 0, 'categories' => 0, 'board' => 0,
														'threads' => 0, 'boardmods' => 0);

if (!empty($_POST['submit']))
{
	$data['convert']['fake'] = (!empty($_POST['fake']) ? 'checked' : '');
	$data['convert']['members'] = (!empty($_POST['convert_members']) ? 'checked' : '');
	$data['convert']['wars'] = (!empty($_POST['convert_wars']) ? 'checked' : '');
	$data['convert']['cat_games'] = (!empty($_POST['cat_games']) ? (int) $_POST['cat_games'] : 0);
	$data['convert']['cat_wars'] = (!empty($_POST['cat_wars']) ? (int) $_POST['cat_wars'] : 0);
	$data['convert']['news'] = (!empty($_POST['convert_news']) ? 'checked' : '');
	$data['convert']['board'] = (!empty($_POST['convert_board']) ? 'checked' : '');
	$data['convert']['url'] = $_POST['url'];
	$data['convert']['prefix'] = $_POST['prefix'];
	$data['convert']['charset'] = $_POST['charset'];
	$data['convert']['user'] = $_POST['user'];
	$data['convert']['pass'] = $_POST['pass'];
	$data['convert']['name'] = $_POST['name'];
	$data['convert']['host'] = $_POST['host'];
	$data['convert']['port'] = (int) $_POST['port'];
	$data['convert']['type'] = $_POST['type'];
	$data['convert']['news_language'] = !empty($_POST['news_language']) ? $_POST['news_language'] : 'de';

	$settings = array();
	if (!empty($_POST['fake']))
	{
		$settings['fake'] = true;
	}
	if (!empty($_POST['url']))
	{
		$settings['url'] = $data['convert']['url'];
	}
	$settings['news_language'] = $data['convert']['news_language'];
	
	$settings['db']['db_prefix'] = $data['convert']['prefix'];
	$settings['db']['db_charset'] = $data['convert']['charset'];
	$settings['db']['db_user'] = $data['convert']['user'];
	$settings['db']['db_pass'] = $data['convert']['pass'];
	$settings['db']['db_name'] = $data['convert']['name'];
	$settings['db']['db_host'] = $data['convert']['host'];
	$settings['db']['db_port'] = $data['convert']['port'];
	$settings['db']['db_type'] = $data['convert']['type'];

	$settings['cat_games'] = $data['convert']['cat_games'];
	$settings['cat_wars'] = $data['convert']['cat_wars'];

	/* only WebSpell 4.x for now */
	$convert = new ClanSphere_Convert_Webspell($settings);
	/* check some conversion settings */
	$no_errors = true;
	if (!function_exists('mb_strtolower'))
	{
		$no_errors = false;
		$convert->error(html_entity_decode($cs_lang['mbstring_required']));
	}
	if (!empty($_POST['convert_wars']))
	{
		if (empty($_POST['convert_members']))
		{
			$no_errors = false;
			$convert->error(html_entity_decode($cs_lang['wars_needs_members']));
		}
		if (empty($data['convert']['cat_games']))
		{
			$no_errors = false;
			$convert->error(html_entity_decode($cs_lang['games_needs_category']));
		}
		if (empty($data['convert']['cat_wars']))
		{
			$no_errors = false;
			$convert->error(html_entity_decode($cs_lang['wars_needs_category']));
		}
	}
	if ($no_errors)
	{
		/* this might take some time */
		$met = ini_get('max_execution_time');
		ini_set('max_execution_time', 300);
		$convert->connect();
		$convert->convertUsers();
		if (!empty($_POST['convert_wars']))
		{
			$convert->convertGames(); // squads might need this too
		}
		if (!empty($_POST['convert_members']))
 	  {
			$convert->convertSquads();
			$convert->convertMembers();
 		}
		if (!empty($_POST['convert_wars']))
		{
			$convert->convertWars();
		}
		if (!empty($_POST['convert_news']))
		{
			$convert->convertNews();
		}
		if (!empty($_POST['convert_board']))
		{
			$convert->convertForums();
		}
		$convert->disconnect();
		ini_set('max_execution_time', $met);
		$version = $convert->getVersion();
		switch ($version)
		{
		case 0:	$convert->error('Detected Webspell Version: 4.01.xx');	break;
		case 1:	$convert->error('Detected Webspell Version: 4.02.xx');	break;
		}
		
	}
	$data['errors'] = $convert->getErrors();
	$data['statistics'] = $convert->getStatistics();
}

$data['languages']['lang'] = '<select name="news_language">';
$data['languages']['lang'] .= '<option value="dk" '.($data['convert']['news_language'] == 'dk' ? 'selected' : '').'>Danish</option>';
$data['languages']['lang'] .= '<option value="nl" '.($data['convert']['news_language'] == 'nl' ? 'selected' : '').'>Dutch</option>';
$data['languages']['lang'] .= '<option value="uk" '.($data['convert']['news_language'] == 'uk' ? 'selected' : '').'>English</option>';
$data['languages']['lang'] .= '<option value="fr" '.($data['convert']['news_language'] == 'fr' ? 'selected' : '').'>French</option>';
$data['languages']['lang'] .= '<option value="fi" '.($data['convert']['news_language'] == 'fi' ? 'selected' : '').'>Finnish</option>';
$data['languages']['lang'] .= '<option value="de" '.($data['convert']['news_language'] == 'de' ? 'selected' : '').'>German</option>';
$data['languages']['lang'] .= '<option value="hu" '.($data['convert']['news_language'] == 'hu' ? 'selected' : '').'>Hungarian</option>';
$data['languages']['lang'] .= '<option value="it" '.($data['convert']['news_language'] == 'it' ? 'selected' : '').'>Italian</option>';
$data['languages']['lang'] .= '<option value="no" '.($data['convert']['news_language'] == 'no' ? 'selected' : '').'>Norwegian</option>';
$data['languages']['lang'] .= '<option value="es" '.($data['convert']['news_language'] == 'es' ? 'selected' : '').'>Spanish</option>';
$data['languages']['lang'] .= '<option value="se" '.($data['convert']['news_language'] == 'se' ? 'selected' : '').'>Swedish</option>';
$data['languages']['lang'] .= '</select>';
$data['databases']['type'] = '<select name="type">';
$data['databases']['type'] .= '<option value="mysql" '.($data['convert']['type'] == 'mysql' ? 'selected' : '').'>MySQL</option>';
$data['databases']['type'] .= '</select>';

require_once('mods/categories/functions.php');
$data['categories']['games'] = cs_categories_dropdown2('games', $data['convert']['cat_games'], 0, 'cat_games');
$data['categories']['wars'] = cs_categories_dropdown2('wars', $data['convert']['cat_wars'], 0, 'cat_wars');

echo cs_subtemplate(__FILE__,$data,'convert','manage');

?>
