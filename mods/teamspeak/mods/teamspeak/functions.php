<?php
if (!function_exists('teamspeak_sorter'))
{
	function teamspeak_sorter($a, $b)
	{
	  return strnatcasecmp($a['channel_order'], $b['channel_order']);
	} // function teamspeak_sorter
}

if (!function_exists('teamspeak_make_timestring'))
{
	function teamspeak_make_timestring($secs, $days)
	{
		$day = floor($secs / 86400);
		$dif = $secs - $day * 86400;
		$hrs = floor($dif / 3600);
		$hrs = (strlen($hrs) < 2) ? '0'.$hrs : $hrs;
		$dif = $dif - $hrs * 3600;
		$min = floor($dif / 60);
		$min = (strlen($min) < 2) ? '0'.$min : $min;
		$dif = $dif - $min * 60;
		$sec = $dif;
		$sec = (strlen($sec) < 2) ? '0'.$sec : $sec;
		return "{$day} {$days} {$hrs}:{$min}:{$sec}";
	} // function teamspeak_make_timestring
}

if (!function_exists('teamspeak_make_timestringuser'))
{
	function teamspeak_make_timestringuser($secs)
	{
		$day = floor($secs / 86400);
		$dif = $secs - $day * 86400;
		$hrs = floor($dif / 3600);
		$hrs = (strlen($hrs) < 2) ? '0'.$hrs : $hrs;
		$dif = $dif - $hrs * 3600;
		$min = floor($dif / 60);
		$min = (strlen($min) < 2) ? '0'.$min : $min;
		$dif = $dif - $min * 60;
		$sec = $dif;
		$sec = (strlen($sec) < 2) ? '0'.$sec : $sec;
		return "{$hrs}:{$min}:{$sec}";
	} // function teamspeak_make_timestringuser
}

if (!function_exists('teamspeak_show_channel'))
{
	/**
	 * Show a teamspeak channel with all its subchannels and users
	 */
	function teamspeak_show_channel($tss, $subchannels, $channel, $depth, $islast, $settings)
	{
		$indent = '';
		if ($depth > 0)
			$indent = cs_html_img('mods/teamspeak/images/transparant.gif', 16, $depth * 16);
		/* first count the number of users in this channel and its subchannels */ 
		$countusers = teamspeak_count_users($tss, $subchannels, $channel);
		if (!$settings['se'] && $countusers == 0)
		{
			// skipping empty (sub)channel
			return '';
		}
		$cs_lang = cs_translate('teamspeak');
		$imglarge = $settings['imgsize'];
		$output = '';
		$mainchanneloutput = '';
		$mainchanneloutput .= $indent;
		if (empty($channel['channel_topic']))
		{
			$channel['channel_topic'] = $cs_lang['notopic'];
		}
		$codecs = $tss->getCodec($channel['channel_codec']);
		$chaninfo = $cs_lang['channel'] . '<br\>'	. cs_secure(cs_encode($channel['channel_name'], $settings['charset']), 1) . '<br\><br\>'
								. $cs_lang['topic'] . '<br\>' . cs_secure(cs_encode($channel['channel_topic'], $settings['charset']), 1) . '<br\><br\>'
								. $cs_lang['codecs'] . '<br>' . cs_secure(cs_encode($codecs, $settings['charset']));
		if (!empty($channel['channel_description']))
			$chaninfo .= '<br\><br\>' . $cs_lang['desc'] . '<br\>' . cs_secure(cs_encode($channel['channel_description'], $settings['charset']), 1);
		/* show channel name and channel info */
		if ($settings['scf'])
			$mainchanneloutput .= $tss->channelFlags($channel['channel_flags'], tss::FLAG_CHANNEL_MODE_CHANNEL_IMAGE, $imglarge);
		else
			$mainchanneloutput .= $tss->channelFlags(0, tss::FLAG_CHANNEL_MODE_CHANNEL_IMAGE, $imglarge);
		if (!$settings['dl'])
			$mainchanneloutput .= '<a href="#" onMouseOver="teamspeak_submenu(\'teamspeak_channelinfo\',\''.str_replace(array('\'','&#039;','"'), array('\\\'', '\\\'', '&quote;'), $chaninfo).'\');">';
		$mainchanneloutput .= cs_secure(cs_encode($channel['channel_name'], $settings['charset']));
		if (!$settings['dl'])
			$mainchanneloutput .= '</a>';
		if ($settings['scf'])
			$mainchanneloutput .= ' ' . $tss->channelFlags($channel['channel_flags'], tss::FLAG_CHANNEL_MODE_CHANNEL_STATES_IMAGE, $imglarge);
		$mainchanneloutput .= cs_html_br(1);
		$chanUsers = $tss->channelClients($channel['cid']); 
		$userOutput = '';
		if (!empty($chanUsers))
		{
			$fcount = 0;
			$ucount = is_array($chanUsers) ? count($chanUsers) : 0;
			foreach ($chanUsers as $user)
			{ 
				$fcount++;
				$count = isset($subchannels[$channel['cid']]) ? count($subchannels[$channel['cid']]) : 0;
				$userOutput .= $indent;
				if ($settings['spf'])
					$player = '&nbsp;'.$tss->clientFlagsStatus($user['client_flags_status'], tss::FLAG_PLAYER_MODE_STATUS_IMAGE, $imglarge);
				else
					$player = '&nbsp;'.$tss->clientFlagsStatus(0, tss::FLAG_PLAYER_MODE_STATUS_IMAGE, $imglarge);
				$player .= '&nbsp;' . cs_secure(cs_encode($user['client_nickname'], $settings['charset']));
				if ($settings['spf'])
				{
					$player .= '&nbsp;'.$tss->clientFlagsGlobal($user['client_flags_global'], $user['client_flags_channel'], tss::FLAG_PLAYER_MODE_GLOBAL_IMAGE, $imglarge);
				}
				$userOutput .= $player;
				$userOutput .= cs_html_br(1);
			}
		}
		$subChannelsOutput = '';
		if (isset($subchannels[$channel['cid']]))
		{
			$numSub = count($subchannels[$channel['cid']]);
			$count = 1;
			foreach ($subchannels[$channel['cid']] as $subchannel)
			{
				$last = false;
				if ($count == $numSub)
					$last = true;
				$subChannelsOutput .= teamspeak_show_channel($tss, $subchannels, $subchannel, $depth + 1, $last, $settings);
			}
		}	
		$output .= $mainchanneloutput;
		if (!empty($userOutput))
			$output .= $userOutput;
		if (!empty($subChannelsOutput))
			$output .= $subChannelsOutput;
		return $output;
	} // function teamspeak_show_channel
}

if (!function_exists('teamspeak_count_users'))
{
	/**
	 * Count all users in this channel and all its subchannels
	 */
	function teamspeak_count_users($tss, $subchannels, $channel)
	{
		$count = 0;
		/* get users from this channel */
		$chanUsers = $tss->channelClients($channel['cid']);
		if ($chanUsers !== false)
			$count += count($chanUsers);
		if (isset($subchannels[$channel['cid']]))
		{
			foreach ($subchannels[$channel['cid']] as $subchannel)
				$count += teamspeak_count_users($tss, $subchannels, $subchannel);
		}
		return $count;
	} // function teamspeak_count_users
}
?>
