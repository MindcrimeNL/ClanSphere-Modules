<?php
global $cs_main;

/* rollback received coins for this removed thread */
$coins_options = array();
if (cs_coins_mod('board') && isset($_POST['coins_rollback']) && !empty($_POST['coins_rollback']))
{
	/* this module is supported */
	$coins_options = cs_sql_option(__FILE__, 'board');
	if (isset($coins_options['coins_receive_thread']) && $coins_options['coins_receive_thread'] > 0.0)
	{
		if (!isset($coins_options['coins_min_length'])
			 || (isset($coins_options['coins_min_length'])
			 	&& iconv_strlen(trim($cs_thread['threads_text']), $cs_main['charset']) >= $coins_options['coins_min_length']))
			cs_coins_rollback($cs_thread['users_id'], 'board', 0.0, $coins_options['coins_receive_thread']);
	}
	/* we need to do more, we need to rollback all comments in the thread */
	if (isset($coins_options['coins_receive']) && $coins_options['coins_receive'] > 0.0)
	{
		/* get all comments */
		$rollback_comments = cs_sql_select(__FILE__, 'comments', '*', 'comments_mod=\'board\' AND comments_fid='.$thread_id, 0,0,0);
		if (is_array($rollback_comments) && count($rollback_comments))
		{
			foreach ($rollback_comments as $cs_comments)
			{
				/* for each comment, check the length if needed and rollback */
        if (!isset($coins_options['coins_min_length'])
            || (isset($coins_options['coins_min_length'])
               && iconv_strlen(trim($cs_comments['comments_text']), $cs_main['charset']) >= $coins_options['coins_min_length']))
          cs_coins_rollback($cs_comments['users_id'], 'board', 0.0, $coins_options['coins_receive']);

			}
		}
	}
}
?>
