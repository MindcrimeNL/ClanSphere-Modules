<?php
$cs_lang = cs_translate('teamspeak');

if (@fsockopen('udp://127.0.0.1', 1)) {
  $data['if']['fsock'] = false;
} else {
  $data['if']['fsock'] = true;
}


$data['manage']['options'] = cs_link($cs_lang['options'],'teamspeak','options');
$data['manage']['serverlist'] = cs_link($cs_lang['serverlist'],'teamspeak','serverlist');
$data['manage']['userchannelkick'] = cs_link($cs_lang['userchannelkick'],'teamspeak','userchannelkick');
$data['manage']['userkick'] = cs_link($cs_lang['userkick'],'teamspeak','userkick');
$data['manage']['userdblist'] = cs_link($cs_lang['userdblist'],'teamspeak','userdblist');

echo cs_subtemplate(__FILE__,$data,'teamspeak','manage');
?>
