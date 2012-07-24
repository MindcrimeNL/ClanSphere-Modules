<?php
global $cs_main;
$cs_lang = cs_translate('teamspeak');

$data = array();
$data['link']['teamspeak_manage'] = cs_link($cs_lang['head_manage'],'teamspeak','serverlist');

if(isset($_POST['submit'])) {

    $error = 0;
	$errormsg = '';

	$cs_teamspeak['teamspeak_ip'] = $_POST['teamspeak_ip'];
	$cs_teamspeak['teamspeak_version'] = intval($_POST['teamspeak_version']);
	$cs_teamspeak['teamspeak_udp'] = intval($_POST['teamspeak_udp']);
	$cs_teamspeak['teamspeak_tcp'] = intval($_POST['teamspeak_tcp']);
	$cs_teamspeak['teamspeak_admin'] = $_POST['teamspeak_admin'];
	$cs_teamspeak['teamspeak_adminpw'] = $_POST['teamspeak_adminpw'];
	$cs_teamspeak['teamspeak_sadmin'] = $_POST['teamspeak_sadmin'];
	$cs_teamspeak['teamspeak_sadminpw'] = $_POST['teamspeak_sadminpw'];
	$cs_teamspeak['teamspeak_register'] = intval($_POST['teamspeak_register']);
	$cs_teamspeak['teamspeak_access'] = intval($_POST['teamspeak_access']);
	$cs_teamspeak['teamspeak_charset'] = $_POST['teamspeak_charset'];

	if(empty($cs_teamspeak['teamspeak_ip'])) {
    	$error++;
	    $errormsg .= $cs_lang['no_ip'] . cs_html_br(1);
	}
	if(empty($cs_teamspeak['teamspeak_udp'])) {
    	$error++;
	    $errormsg .= $cs_lang['no_udp'] . cs_html_br(1);
	}
	if(empty($cs_teamspeak['teamspeak_tcp'])) {
	    $error++;
	    $errormsg .= $cs_lang['no_tcp'] . cs_html_br(1);
	}
	if(!in_array($cs_teamspeak['teamspeak_version'], array(0, 1))) {
	    $error++;
	    $errormsg .= $cs_lang['no_version'] . cs_html_br(1);
	}
	if(empty($cs_teamspeak['teamspeak_admin'])) {
	    $error++;
	    $errormsg .= $cs_lang['no_admin'] . cs_html_br(1);
	}
	if(empty($cs_teamspeak['teamspeak_adminpw'])) {
	    $error++;
	    $errormsg .= $cs_lang['no_adminpw'] . cs_html_br(1);
	}
}
else { 
	$cs_teamspeak['teamspeak_ip'] = '127.0.0.1';
	$cs_teamspeak['teamspeak_version'] = 0;
	$cs_teamspeak['teamspeak_udp'] = '8767';
	$cs_teamspeak['teamspeak_tcp'] = '51234';
	$cs_teamspeak['teamspeak_admin'] = '';
	$cs_teamspeak['teamspeak_adminpw'] = '';
	$cs_teamspeak['teamspeak_sadmin'] = '';
	$cs_teamspeak['teamspeak_sadminpw'] = '';
	$cs_teamspeak['teamspeak_register'] = 0;
	$cs_teamspeak['teamspeak_access'] = 1;
	$cs_teamspeak['teamspeak_charset'] = 'ISO-8859-1';
}

if(!isset($_POST['submit'])) {
  $data['head']['body'] = $cs_lang['body_create'];
}
elseif(!empty($error)) {
  $data['head']['body'] = $errormsg;
}

if(!empty($error) OR !isset($_POST['submit'])) {

	$data['teamspeak']['teamspeak_ip'] = $cs_teamspeak['teamspeak_ip'];
	$data['teamspeak']['teamspeak_version'] = $cs_teamspeak['teamspeak_version'];
	$data['teamspeak']['teamspeak_version_0'] = $cs_teamspeak['teamspeak_version'] == 0 ? ' selected' : '';
	$data['teamspeak']['teamspeak_version_1'] = $cs_teamspeak['teamspeak_version'] == 1 ? ' selected' : '';
	$data['teamspeak']['teamspeak_udp'] = $cs_teamspeak['teamspeak_udp'];
	$data['teamspeak']['teamspeak_tcp'] = $cs_teamspeak['teamspeak_tcp'];
	$data['teamspeak']['teamspeak_admin'] = $cs_teamspeak['teamspeak_admin'];
	$data['teamspeak']['teamspeak_adminpw'] = $cs_teamspeak['teamspeak_adminpw'];
	$data['teamspeak']['teamspeak_sadmin'] = $cs_teamspeak['teamspeak_sadmin'];
	$data['teamspeak']['teamspeak_sadminpw'] = $cs_teamspeak['teamspeak_sadminpw'];
	$data['teamspeak']['teamspeak_charset_iso8859_1'] = $cs_teamspeak['teamspeak_charset'] == 'ISO-8859-1' ? ' selected' : '';
	$data['teamspeak']['teamspeak_charset_utf_8'] = $cs_teamspeak['teamspeak_charset'] == 'UTF-8' ? ' selected' : '';
	$access = '<select name="teamspeak_register">';
	for ($i = 0; $i <= 5; $i++)
	{
		$access .= '<option value="'.$i.'"'.($i == $cs_teamspeak['teamspeak_register'] ? ' selected' : '').'>'.$cs_lang['lev_'.$i].'</option>';
	}
	$access .= '</select>';
	$data['select']['teamspeak_register'] = $access;
	$access = '<select name="teamspeak_access">';
	for ($i = 1; $i <= 5; $i++)
	{
		$access .= '<option value="'.$i.'"'.($i == $cs_teamspeak['teamspeak_access'] ? ' selected' : '').'>'.$cs_lang['lev_'.$i].'</option>';
	}
	$access .= '</select>';
	$data['select']['teamspeak_access'] = $access;
	
	echo cs_subtemplate(__FILE__,$data,'teamspeak','serveradd');
}
else {
  $cs_teamspeak['teamspeak_adminpw'] = base64_encode(cs_crypt($cs_teamspeak['teamspeak_adminpw'], $cs_main['crypt_key']));
  $cs_teamspeak['teamspeak_sadminpw'] = base64_encode(cs_crypt($cs_teamspeak['teamspeak_sadminpw'], $cs_main['crypt_key']));

  $teamspeak_cells = array_keys($cs_teamspeak);
  $teamspeak_save = array_values($cs_teamspeak);
  cs_sql_insert(__FILE__,'teamspeak',$teamspeak_cells,$teamspeak_save);

  cs_redirect($cs_lang['create_server_done'],'teamspeak');
}


?>
