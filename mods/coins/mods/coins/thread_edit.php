<?php
global $cs_main;

/* rollback or receive coins for this edit thread */
$coins_options = array();
if (cs_coins_mod('board'))
{
	/* this module is supported */
	$coins_options = cs_sql_option(__FILE__, 'board');
	if (isset($coins_options['coins_receive_thread']) && $coins_options['coins_receive_thread'] > 0.0)
	{
		/* if we have a min length setting, we must do a test */
		if (isset($coins_options['coins_min_length']))
		{
			/* add coins if old text is smaller and new one is larger than limit */
			if (iconv_strlen(trim($thread_edit['threads_text']), $cs_main['charset']) <  $coins_options['coins_min_length']
					&& iconv_strlen(trim($board['threads_text']), $cs_main['charset']) >= $coins_options['coins_min_length'])
				cs_coins_receive($thread_edit['users_id'], 'board', $coins_options['coins_receive_thread']);
			
			/* rollback coins if old text is larger and new one is smaller than limit */
			if (iconv_strlen(trim($thread_edit['threads_text']), $cs_main['charset']) >=  $coins_options['coins_min_length']
					&& iconv_strlen(trim($board['threads_text']), $cs_main['charset']) < $coins_options['coins_min_length'])
				cs_coins_rollback($thread_edit['users_id'], 'board', 0.0, $coins_options['coins_receive_thread']);
			/* do nothing for any other case: old and new too small or too large */
		}
	}
}
?>
