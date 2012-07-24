<?php

$cs_lang = cs_translate('coins');
$cs_option = cs_sql_option(__FILE__, 'coins');

$users_nick = (empty($_POST['users_nick']) ? (empty($_GET['users_nick']) ? '' : $_GET['users_nick']) : $_POST['users_nick']);

$data['search']['users_nick'] = '';
$data['search']['message'] = '';
if (!empty($_POST['submit']) && !empty($users_nick))
{
	$cs_user = cs_sql_select(__FILE__, 'users', 'users_id', 'users_nick = \''.cs_sql_escape($users_nick).'\'', 0, 0, 1);
	if (!empty($cs_user['users_id']))
	{
		if (!cs_coins_exists($cs_user['users_id']))
		{
			cs_coins_create($cs_user['users_id']);
		}
		cs_redirect($cs_lang['create_done'], 'coins', 'manage');
	}
	else
		$data['search']['message'] = $cs_lang['no_data'];
		
	$data['search']['users_nick'] = htmlentities($users_nick);
}

echo cs_subtemplate(__FILE__, $data, 'coins', 'create');
