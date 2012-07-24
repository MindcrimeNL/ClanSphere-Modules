<?php
global $cs_main;

/* add coins for creating a thread */
$coins_options = array();
if (cs_coins_mod('board'))
{
	/* this module is supported */
	$coins_options = cs_sql_option(__FILE__, 'board');
	if (isset($coins_options['coins_receive_thread']) && $coins_options['coins_receive_thread'] > 0.0)
	{
		if (!isset($coins_options['coins_min_length'])
			 || (isset($coins_options['coins_min_length'])
			 	&& iconv_strlen(trim($board['threads_text']), $cs_main['charset']) >= $coins_options['coins_min_length']))
			cs_coins_receive($board['users_id'], 'board', $coins_options['coins_receive_thread']);
	}
}
?>
