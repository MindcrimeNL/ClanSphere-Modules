<?php
// ClanSphere 2010 - www.clansphere.net
// $Id: center.php 4501 2010-08-31 22:11:01Z hajo $

$cs_lang = cs_translate('cups');

include_once 'mods/cups/defines.php';

$get_memberships = cs_sql_select(__FILE__,'members','squads_id','users_id = \''.$account['users_id'].'\'',0,0,0);
$condition = '(cs.squads_id = \''.$account['users_id'].'\' AND cp.cups_system = \'users\')';
$matchcond = '';

if (!empty($get_memberships))
{

  $x = 0;
  $condition .= ' OR (cp.cups_system = \'teams\' AND (';
  
  foreach($get_memberships AS $membership) {
    $x++;

    if ($x != 1) {
      $condition .= ' OR ';
      $matchcond .= ' OR ';
    }

    $condition .= 'squads_id = '.$membership['squads_id'];
    $matchcond .= 'squad1_id = '.$membership['squads_id'].' OR ';
    $matchcond .= 'squad2_id = '.$membership['squads_id'];
    $squads[] = $membership['squads_id'];
  }
  $condition .= '))';
}

$data = array();

$tables = 'cupsquads cs INNER JOIN {pre}_cups cp ON cs.cups_id = cp.cups_id';
$cells  = 'cs.cupsquads_checkedin AS cupsquads_checkedin, cs.cups_id AS cups_id, cp.games_id AS games_id, cp.cups_name AS cups_name, ';
$cells .= 'cp.cups_system AS cups_system, cs.squads_id AS squads_id, cp.cups_start AS cups_start, cp.cups_checkin AS cups_checkin';
$data['cups'] = cs_sql_select(__FILE__,$tables,$cells,$condition,0,0,0);
$cups_count = count($data['cups']);

$data['lang']['take_part_in_cups'] = sprintf($cs_lang['take_part_in_cups'],$cups_count);

$conds = array();
$gencond = 'cupmatches_accepted1 = 0 AND cupmatches_accepted2 = 0 AND ';
$conds['teams'] = $gencond.'(' . $matchcond . ')';
$conds['users'] = $gencond.'(squad1_id = "'.$account['users_id'] .'" OR squad2_id = "'.$account['users_id'].'")';

for ($i = 0; $i < $cups_count; $i++) {
  $data['cups'][$i]['if']['gameicon_exists'] = file_exists('uploads/games/' . $data['cups'][$i]['games_id'] . '.gif') ? true : false;
  
  $cond = $conds[$data['cups'][$i]['cups_system']] . ' AND cups_id = "' . $data['cups'][$i]['cups_id'] . '"';
  $matchid = cs_sql_select(__FILE__, 'cupmatches', 'cupmatches_id', $cond, 'cupmatches_round ASC');
	if (empty($matchid))
	{
		$time = cs_time();
		if ($data['cups'][$i]['cups_start'] > $time && $data['cups'][$i]['cups_checkin'] <= $time)
		{
			if (empty($data['cups'][$i]['cupsquads_checkedin']))
  			$data['cups'][$i]['nextmatch'] = cs_link($cs_lang['checkin'],'cups','checkin','id='.$data['cups'][$i]['cups_id']);
			else
  			$data['cups'][$i]['nextmatch'] = cs_link($cs_lang['checkout'],'cups','checkin','checkout=1&id='.$data['cups'][$i]['cups_id']);
		}
		else
  		$data['cups'][$i]['nextmatch'] = $cs_lang['no_match_upcoming'];
	}
	else
  	$data['cups'][$i]['nextmatch'] = cs_link($cs_lang['show'],'cups','match','id='.$matchid['cupmatches_id']);
}

echo cs_subtemplate(__FILE__, $data, 'cups', 'center');
