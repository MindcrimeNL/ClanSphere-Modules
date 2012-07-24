<?php
// Geh aB Clan 2009 - www.gab-clan.org
// $Id$

$twitterUserAgent = 'ClanSphere Twitter Module';

function cs_detweet($string)
{
	global $cs_main;

	/* first convert from UTF-8 to local encoding */
	$string = cs_encode($string, 'UTF-8');
	$destring = html_entity_decode($string, ENT_QUOTES, $cs_main['charset']);
	// str_replace(array('&quot;', '&gt;', '&lt;'), array('"', '>', '<'), $string);
	return $destring;
} // function cs_detweet

function cs_tweet($string)
{
	global $cs_main;

	$string = cs_encode($string, $cs_main['charset'], 'UTF-8');
	return str_replace(array('&amp;'), array('&'), $string);
}

?>
