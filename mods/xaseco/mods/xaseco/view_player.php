<?php
$cs_lang = cs_translate('xaseco');

$cs_options = cs_sql_option(__FILE__,'xaseco');

require_once('mods/xaseco/classes/tmfcolorparser.inc.php');
require_once('mods/xaseco/functions.php');

$id = intval($_GET['id']);

$prefix = 'xaseco_';

$results = cs_sql_select(__FILE__, 'xaseco_players', '*', 'Id = '.$id, 0, 0, 1);

if ($results === false)
{
	echo $cs_lang['no_player'];
	return;
}
$tmnf = new TMFColorParser($cs_options['bgcolor']);

$player = $results;

$results = cs_sql_select(__FILE__, 'xaseco_records ar LEFT JOIN {pre}_xaseco_challenges ac ON ac.Id = ar.ChallengeId', 'ar.Date AS date, ar.Score AS score, ar.ChallengeId AS challengeid, ac.Name as name', 'ar.PlayerId = '.$id, 'ar.Date ASC, ar.Score ASC', 0, 0);

$xaseco = array();
$rank = 1;
if ($results !== false)
{
	foreach ($results as $result)
	{
		$xaseco[] = array(
			'link' => cs_url('xaseco', 'view_challenge', 'id='.$result['challengeid']),
			'name' => $tmnf->toHTML(cs_encode(htmlspecialchars($result['name'], ENT_NOQUOTES, 'UTF-8'), 'UTF-8'), false, true),
			'score' => cs_xaseco_time_played(floor($result['score'] / 1000), $cs_lang['days']).sprintf('.%02d', floor(($result['score'] % 1000) / 10)),
			'date' => $result['date']
		);
	}
}

$data = array();
$data['head']['mod'] = $cs_lang['mod_name'];
$data['head']['action'] = $cs_lang['list'];
$data['head']['link_challenges'] = cs_url('xaseco', 'list_challenges');
$data['head']['link_players'] = cs_url('xaseco', 'list');
$data['head']['name'] = $tmnf->toHTML(cs_encode(htmlspecialchars($player['NickName'], ENT_NOQUOTES, 'UTF-8'), 'UTF-8'), false, true);

if (count($xaseco))
{
	$data['xaseco'] = $xaseco;
	echo cs_subtemplate(__FILE__,$data,'xaseco','view_player');
}
else
	echo $cs_lang['no_records'];
?>
