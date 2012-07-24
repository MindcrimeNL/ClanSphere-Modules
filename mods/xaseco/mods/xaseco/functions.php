<?php
function cs_xaseco_time_played($secs, $day)
{
	$seconds = floor($secs % 60);
	$minutes = floor(($secs % 3600) / 60);
	$hours = floor($secs / 3600);
	$days = floor($hours / 24);
	
	if ($hours == 0)
		return sprintf('%02d:%02d', $minutes, $seconds);
	else if ($days == 0)
		return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
	else
		return sprintf('%d %s %02d:%02d:%02d', $days, $day, $hours, $minutes, $seconds);
} // function cs_xaseco_time_played
?>
