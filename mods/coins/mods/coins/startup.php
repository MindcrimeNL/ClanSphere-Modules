<?php
/**
 * General cs_coins functions
 */

/**
 * Get the user's total or default value if none exists
 *
 * @param	int	$users_id
 * 
 * @return floatval
 */
function cs_coins_total($users_id)
{
	$cs_coins = cs_coins_exists($users_id);
	
	if ($cs_coins !== false)
		return floatval($cs_coins['coins_total']);

	$coin_options = cs_sql_option(__FILE__, 'coins');
	return floatval($coin_options['startcoins']);
} // function cs_coins_total

/**
 * Check if a user already has a coins record and return it
 *
 * @param	int	$users_id
 * 
 * @return array array of a coins record, false otherwise
 */
function cs_coins_exists($users_id)
{
	$cs_coins = cs_sql_select(__FILE__, 'coins', '*', 'users_id = '.intval($users_id), 0, 0, 1);

	if (empty($cs_coins['users_id']))
		return false;
	return $cs_coins;
} // function cs_coins_exists

/**
 * Create a coin record for this user
 *
 * @param	int	$users_id
 * 
 * @return array array of a coins record, false otherwise
 */
function cs_coins_create($users_id)
{
	if (intval($users_id) <= 0)
		return false;
	$coin_options = cs_sql_option(__FILE__, 'coins');

	$fields = array('users_id', 'coins_total');
	$values = array(intval($users_id), floatval($coin_options['startcoins']));
	cs_sql_insert(__FILE__, 'coins', $fields, $values);

	return cs_coins_exists($users_id);
} // function cs_coins_create

/**
 * Check if a mod can use coins (defined by coins option "coin_mods")
 *
 * @param	string $mod the name of the mod
 *
 * @return boolean	true on success, false on failure
 */
function cs_coins_mod($mod)
{
	$options = cs_sql_option(__FILE__, 'coins');
	$mods = array_map('trim', explode(',', strtolower($options['coin_mods'])));
	if (in_array(strtolower($mod), $mods))
		return true;
	return false;
} // function cs_coins_mod

/**
 * Use coins. It will automatically create a new user coins record if he has none.
 * If the user has not enough coins in coins_total, the user can't use his coins.
 *
 * @param	int	$users_id
 * @param	string	$mod the mod which uses the amount
 * @param	float $amount amount to use
 *
 * @return true on success, false otherwise
 */
function cs_coins_use($users_id, $mod, $amount)
{
	global $cs_main;

	/* check if we have this mod */
	if (!cs_coins_mod($mod))
		return false;

	/* does the user already have a coin record? */
	$cs_coins = cs_coins_exists($users_id);
	if ($cs_coins === false)
	{
		/* no, try to create one */
		$cs_coins = cs_coins_create($users_id);
		if ($cs_coins === false)
			return false;
	}

	/* check if the user has enough coins */
	if ($cs_coins['coins_total'] < $amount)
		return false;

	$coins_field = 'coins_'.$mod.'_used';
	$fields = array($coins_field, 'coins_total');
	$values = array($cs_coins[$coins_field] + $amount, $cs_coins['coins_total'] - $amount);

	cs_sql_update(__FILE__, 'coins', $fields, $values, $cs_coins['coins_id']);
	return true;
} // function cs_coins_use

/**
 * Receive coins. It will automatically create a new user coins record if he has none.
 *
 * @param	int	$users_id
 * @param	string	$mod the mod which receives the amount
 * @param	float $amount amount to receive
 *
 * @return true on success, false otherwise
 */
function cs_coins_receive($users_id, $mod, $amount)
{
	/* check if we have this mod */
	if (!cs_coins_mod($mod))
		return false;

	/* does the user already have a coin record? */
	$cs_coins = cs_coins_exists($users_id);
	if ($cs_coins === false)
	{
		/* no, try to create one */
		$cs_coins = cs_coins_create($users_id);
		if ($cs_coins === false)
			return false;
	}

	$coins_field = 'coins_'.$mod.'_received';
	$fields = array($coins_field, 'coins_total');
	$values = array($cs_coins[$coins_field] + $amount, $cs_coins['coins_total'] + $amount);

	cs_sql_update(__FILE__, 'coins', $fields, $values, $cs_coins['coins_id']);
	return true;
} // function cs_coins_receive

/**
 * Rollback coins. It will NOT automatically create a new user coins record if he has none. 
 *
 * @param	int	$users_id
 * @param	string	$mod the mod which needs to rollback
 * @param	float $amount amount to rollback from use
 * @param	float $amount amount to rollback from receive
 *
 * @return true on success, false otherwise
 */
function cs_coins_rollback($users_id, $mod, $amount_used = 0.0, $amount_received = 0.0)
{
	/* check if we have this mod */
	if (!cs_coins_mod($mod))
		return false;

	/* does the user already have a coin record? */
	$cs_coins = cs_coins_exists($users_id);
	if ($cs_coins === false)
		return false;

	$coins_field_used = 'coins_'.$mod.'_used';
	$coins_field_received = 'coins_'.$mod.'_received';
	$fields = array($coins_field_used, $coins_field_received, 'coins_total');
	$values = array($cs_coins[$coins_field_used] - $amount_used, $cs_coins[$coins_field_received] - $amount_received, $cs_coins['coins_total'] + $amount_used - $amount_received);

	cs_sql_update(__FILE__, 'coins', $fields, $values, $cs_coins['coins_id']);
	return true;
} // function cs_coins_rollback
?>
