<?php

$cs_lang = cs_translate('coins');

$options = cs_sql_option(__FILE__, 'coins');

$cs_coins = cs_coins_exists($account['users_id']);
if ($cs_coins === false)
{
	/* no, try to create one */
	$cs_coins = cs_coins_create($account['users_id']);
	if ($cs_coins === false)
		$cs_coins['coins_total'] = 0;
}
$data['user']['points'] = number_format($cs_coins['coins_total'], $options['coin_decimals']);
$data['head']['getmsg'] = cs_getmsg();

$mods = array_map('trim', explode(',', strtolower($options['coin_mods'])));
$data['if']['mod'] = false;
if (count($mods) > 0)
{
	$run = 0;
	foreach ($mods as $mod)
	{
		$info_file = 'mods/' . $mod . '/info.php';
		if (!file_exists($info_file))
			continue;
		if (!isset($cs_coins['coins_'.$mod.'_received']) || !isset($cs_coins['coins_'.$mod.'_used']))
			continue;
		include($info_file);

		$data['mods'][$run]['name'] = $mod_info['name'];
		$data['mods'][$run]['icon'] = empty($mod_info['icon']) ? '' : cs_icon($mod_info['icon']);
		$data['mods'][$run]['received'] = number_format($cs_coins['coins_'.$mod.'_received'], $options['coin_decimals']);
		$data['mods'][$run]['used'] = number_format($cs_coins['coins_'.$mod.'_used'], $options['coin_decimals']);

		$run++;
	}
	if ($run > 0)
		$data['if']['mod'] = true;
}
else
{
	$data['mods'] = array();
}

echo cs_subtemplate(__FILE__, $data, 'coins', 'center');
?>
