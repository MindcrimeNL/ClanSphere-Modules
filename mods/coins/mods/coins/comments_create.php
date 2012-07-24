<?php
global $cs_main;

/* add coins for creating a comment */
$coins_options = array();
if (cs_coins_mod($mod))
{
	/* this module is supported */
	$coins_options = cs_sql_option(__FILE__, $mod);
	if (isset($coins_options['coins_receive']) && $coins_options['coins_receive'] > 0.0)
	{
		if (!isset($coins_options['coins_min_length'])
			 || (isset($coins_options['coins_min_length'])
			 	&& iconv_strlen(trim($text), $cs_main['charset']) >= $coins_options['coins_min_length']))
			cs_coins_receive($account['users_id'], $mod, $coins_options['coins_receive']);
	}
}
else if (cs_coins_mod('comments'))
{
	/* use standard comments module */
	$coins_options = cs_sql_option(__FILE__, 'comments');
	if (isset($coins_options['coins_receive']) && $coins_options['coins_receive'] > 0.0)
	{
		if (!isset($coins_options['coins_min_length'])
			 || (isset($coins_options['coins_min_length'])
			 	&& iconv_strlen(trim($text), $cs_main['charset']) >= $coins_options['coins_min_length']))
			cs_coins_receive($account['users_id'], 'comments', $coins_options['coins_receive']);
	}
}
?>
