<?php
$cs_lang = cs_translate('xaseco');

$cs_options = cs_sql_option(__FILE__,'xaseco');

require_once('mods/xaseco/classes/tmfcolorparser.inc.php');

$prefix = 'xaseco_';

$results = cs_sql_select(__FILE__, 'xaseco_challenges', '*', 0, 'Name ASC', 0, 0);

$xaseco = array();
if ($results !== false)
{
	$tmnf = new TMFColorParser($cs_options['bgcolor']);
	foreach ($results as $result)
	{
		$xaseco[] = array(
			'link' => cs_url('xaseco', 'view_challenge', 'id='.$result['Id']),
			'name' => $tmnf->toHTML(cs_encode(htmlspecialchars($result['Name'], ENT_NOQUOTES, 'UTF-8'), 'UTF-8'), false, true),
			'author' => $tmnf->toHTML(cs_encode(htmlspecialchars($result['Author'], ENT_NOQUOTES, 'UTF-8'), 'UTF-8'), false, true),
			'environment' => $result['Environment']
		);
	}
}

$data = array();
$data['head']['mod'] = $cs_lang['mod_name'];
$data['head']['action'] = $cs_lang['list'];
$data['head']['link_challenges'] = cs_url('xaseco', 'list_challenges');
$data['head']['link_players'] = cs_url('xaseco', 'list');

if (count($xaseco))
{
	ksort($xaseco);
	$data['xaseco'] = $xaseco;
	echo cs_subtemplate(__FILE__,$data,'xaseco','list_challenges');
}
else
	echo $cs_lang['no_challenges'];
?>
