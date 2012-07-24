<?php
$cs_lang = cs_translate('xaseco');

$cs_options = cs_sql_option(__FILE__,'xaseco');

require_once('mods/xaseco/classes/tmfcolorparser.inc.php');
require_once('mods/xaseco/functions.php');

$prefix = 'xaseco_';

$start = empty($_REQUEST['start']) ? 0 : intval($_REQUEST['start']);
$players_count = cs_sql_count(__FILE__,'xaseco_players', 0);
$results = cs_sql_select(__FILE__, 'xaseco_players', '*', 0, 'Wins DESC, TimePlayed DESC, Login DESC', $start, $account['users_limit']);

$xaseco = array();
if ($results !== false)
{
	$tmnf = new TMFColorParser($cs_options['bgcolor']);
	foreach ($results as $result)
	{
		$xaseco[] = array(
			'link' => cs_url('xaseco', 'view_player', 'id='.$result['Id']),
			'name' => $tmnf->toHTML(cs_encode(htmlspecialchars($result['NickName'], ENT_NOQUOTES, 'UTF-8'), 'UTF-8'), false, true),
			'wins' => $result['Wins'],
			'time_played' => cs_xaseco_time_played($result['TimePlayed'], $cs_lang['days'])
		);
	}
}

$data = array();
$data['head']['mod'] = $cs_lang['mod_name'];
$data['head']['action'] = $cs_lang['list'];
$data['lang']['total'] = $cs_lang['total'];
$data['head']['link_challenges'] = cs_url('xaseco', 'list_challenges');
$data['head']['link_players'] = cs_url('xaseco', 'list');
$data['head']['total'] = $players_count;                 
$data['head']['pages'] = cs_pages('xaseco', 'list', $players_count, $start, 0, 0, $account['users_limit'], 0);

if (count($xaseco))
{
	ksort($xaseco);
	$data['xaseco'] = $xaseco;
	echo cs_subtemplate(__FILE__,$data,'xaseco','list');
}
else
	echo $cs_lang['no_challenges'];
?>
