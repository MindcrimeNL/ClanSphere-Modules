<?php
global $cs_main;

/* rollback received coins for this removed comment */
$coins_options = array();
if (cs_coins_mod($mod))
{
	/* this module is supported */
	$coins_options = cs_sql_option(__FILE__, $mod);
	if (isset($coins_options['coins_receive']) && $coins_options['coins_receive'] > 0.0)
	{
		if (!isset($coins_options['coins_min_length'])
			 || (isset($coins_options['coins_min_length'])
			 	&& iconv_strlen(trim($cs_comments['comments_text']), $cs_main['charset']) >= $coins_options['coins_min_length']))
			cs_coins_rollback($cs_comments['users_id'], $mod, 0.0, $coins_options['coins_receive']);
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
			 	&& iconv_strlen(trim($cs_comments['comments_text']), $cs_main['charset']) >= $coins_options['coins_min_length']))
			cs_coins_rollback($cs_comments['users_id'], 'comments', 0.0, $coins_options['coins_receive']);
	}
}
?>
