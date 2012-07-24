<?php

$cs_lang = cs_translate('coins');
$cs_option = cs_sql_option(__FILE__, 'coins');

$cs_get = cs_get('id');
$cs_post = cs_post('id');

$coins_id = (empty($cs_post['id']) ? $cs_get['id'] : $cs_post['id']);

$coin_mods = array_map('trim', explode(',', strtolower($cs_option['coin_mods'])));
$error = '';
$data = array();
$data['head']['msg'] = cs_getmsg();

if (isset($cs_post['submit']))
{
	foreach ($coin_mods as $coin_mod)
	{
		$cs_coins['coins_'.$coin_mod.'_received'] = (float) $cs_post['coins_'.$coin_mod.'_received'];
		$cs_coins['coins_'.$coin_mod.'_used'] = (float) $cs_post['coins_'.$coin_mod.'_used'];
	}
	$cs_coins['coins_total'] = (float) $cs_post['coins_total'];
}
else
{
	$cs_coins = cs_sql_select(__FILE__, 'coins', '*', 'coins_id = '.$coins_id, 0, 0, 1);
}

if (!empty($error) OR !isset($_POST['submit']))
{
	$data['coins'] = $cs_coins;
	$data['coins']['startcoins'] = $cs_option['startcoins'];
	$run = 0;
	foreach ($coin_mods as $coin_mod)
	{
		$data['mods'][$run]['module'] = ucfirst($coin_mod);
		$data['mods'][$run]['field_name_received'] = 'coins_'.$coin_mod.'_received';
		$data['mods'][$run]['field_value_received'] = $cs_coins['coins_'.$coin_mod.'_received'];
		$data['mods'][$run]['field_name_used'] = 'coins_'.$coin_mod.'_used';
		$data['mods'][$run]['field_value_used'] = $cs_coins['coins_'.$coin_mod.'_used'];
		$run++;
	}
	$data['coins']['coins_total'] = number_format($data['coins']['coins_total'], $cs_option['coin_decimals'], '.', '');
	echo cs_subtemplate(__FILE__, $data, 'coins', 'edit');
}
else
{
  $coins_cells = array_keys($cs_coins);
  $coins_save = array_values($cs_coins);
  cs_sql_update(__FILE__,'coins',$coins_cells,$coins_save,$coins_id);
  
  cs_redirect($cs_lang['changes_done'], 'coins') ;
}
?>
