<?php
global $cs_main;

/* rollback or receive coins for this edit comment */
$coins_options = array();
if (cs_coins_mod($mod))
{
	/* this module is supported */
	$coins_options = cs_sql_option(__FILE__, $mod);
	if (isset($coins_options['coins_receive']) && $coins_options['coins_receive'] > 0.0)
	{
		/* if we have a min length setting, we must do a test */
		if (isset($coins_options['coins_min_length']))
		{
			/* add coins if old text is smaller and new one is larger than limit */
			if (iconv_strlen(trim($orgtext), $cs_main['charset']) <  $coins_options['coins_min_length']
					&& iconv_strlen(trim($cs_comments['comments_text']), $cs_main['charset']) >= $coins_options['coins_min_length'])
				cs_coins_receive($cs_comments['users_id'], $mod, $coins_options['coins_receive']);
			
			/* rollback coins if old text is larger and new one is smaller than limit */
			if (iconv_strlen(trim($orgtext), $cs_main['charset']) >=  $coins_options['coins_min_length']
					&& iconv_strlen(trim($cs_comments['comments_text']), $cs_main['charset']) < $coins_options['coins_min_length'])
				cs_coins_rollback($cs_comments['users_id'], $mod, 0.0, $coins_options['coins_receive']);
			/* do nothing for any other case: old and new too small or too large */
		}
	}
}
else if (cs_coins_mod('comments'))
{
	/* use standard comments module */
	$coins_options = cs_sql_option(__FILE__, 'comments');
	if (isset($coins_options['coins_receive']) && $coins_options['coins_receive'] > 0.0)
	{
		/* if we have a min length setting, we must do a test */
		if (isset($coins_options['coins_min_length']))
		{
			/* add coins if old text is smaller and new one is larger than limit */
			if (iconv_strlen(trim($orgtext), $cs_main['charset']) <  $coins_options['coins_min_length']
					&& iconv_strlen(trim($cs_comments['comments_text']), $cs_main['charset']) >= $coins_options['coins_min_length'])
				cs_coins_receive($cs_comments['users_id'], 'comments', $coins_options['coins_receive']);
			
			/* rollback coins if old text is larger and new one is smaller than limit */
			if (iconv_strlen(trim($orgtext), $cs_main['charset']) >=  $coins_options['coins_min_length']
					&& iconv_strlen(trim($cs_comments['comments_text']), $cs_main['charset']) < $coins_options['coins_min_length'])
				cs_coins_rollback($cs_comments['users_id'], 'comments', 0.0, $coins_options['coins_receive']);
			/* do nothing for any other case: old and new too small or too large */
		}
	}
}
?>
