<?php
// bx clanportal 0.3.0.0
// $Id: view.php 825 2009-04-23 19:38:38Z nenTi $

//MANAGE 
$cs_lang['mod']	= 'Betting office';
$cs_lang['mod_text']	= 'Betting office';
$cs_lang['mod_name']	= 'Betting office';
$cs_lang['new_bet']	= 'Add new bet';

$cs_lang['select_status']	 = 'Select status of viewing';
$cs_lang['open_bets']	= 'Open bets';
$cs_lang['wait_bets']	= 'Closed bets';
$cs_lang['closed_bets']	= 'Finished bets';

$cs_lang['title']	= 'Title';
$cs_lang['active']	= 'Activ';

//Options 
$cs_lang['pointsname']	= 'Name of the currency';
$cs_lang['default_quote_type']	= 'Default quote system';
$cs_lang['auto_title']	= 'Create title automatically';
$cs_lang['auto_title_default']	= 'Checkbox automatic title checked';
$cs_lang['auto_title_separator']	= 'Automatic team separator';
$cs_lang['max_navlist']	 = 'Number of records in the navlist';
$cs_lang['max_navlist_title'] = 'Maximum title length in the navlist';
$cs_lang['body_bets']	 = 'Administration of the modules settings .';
$cs_lang['opt_bets']	 = 'Options';
$cs_lang['remove_quote']	 = 'Remove quote fee';
$cs_lang['max_quote'] = 'Maximum quote "Shared"';
$cs_lang['min_quote'] = 'Minimal quote "Shared"';
$cs_lang['super_quote'] = 'Super quote ( manipulates ALL quotes +/- )';
$cs_lang['base_fee'] = 'Base fee';
$cs_lang['date_format'] = 'Date format';

$cs_lang['quote_type_0'] = 'Shared';
$cs_lang['quote_type_1'] = 'Percentage';
$cs_lang['quote_type_2'] = 'Fixed';
$cs_lang['quote_type_explain'] = '"Shared": Winner gets share of the total bet amount.<br />"Percentage": For quote X, the winner gets (1 + SQ + X/100) for every coin.<br />"Fixed": For quote X, the winner gets X + SQ coins for every coin.<br />SQ = Super quote.';

//Create
$cs_lang['body_create']	 = 'Create a new bet here.';
$cs_lang['error_create'] = 'There was an error creating the bet:';

$cs_lang['start_date'] = 'Start date';
$cs_lang['end_date'] = 'Enddate (start of event)';
$cs_lang['quote_type']	= 'Quote system';
$cs_lang['contestant'] = 'Contestants';
$cs_lang['description'] = 'Description';
$cs_lang['or'] = 'or';
$cs_lang['bets_quote'] = 'Rate';
$cs_lang['min_contestants'] = 'Specify at least 2 Contestants:';
$cs_lang['add_contestant'] = 'Add';
$cs_lang['remove_contestant'] = 'Remove';
$cs_lang['quote_manual'] = 'Quote on/off';
$cs_lang['accept_draw'] = 'Allow users to bet on a drawn result';

$cs_lang['no_category']   = '- Category is required';
$cs_lang['no_contestant'] = '- Not enough contestants entered!';
$cs_lang['no_closed_at'] = '- No finish date expired!';
$cs_lang['no_title'] = '- No title entered!';

$cs_lang['more'] = 'more...';
$cs_lang['com_close'] = 'Close for comments';

//Remove
$cs_lang['really'] = 'Really want to remove bet "%s" ?';
$cs_lang['del_true'] = 'Bet deleted';
$cs_lang['del_false'] = 'Deletion canceled';
$cs_lang['no_selection'] = 'No bet selected';
$cs_lang['invalid_rollback_option'] = 'Invalid remove option!';
$cs_lang['rollback_option'] = 'Remove option';
$cs_lang['rollback_0'] = 'Return the bet amount to users &amp; retrieve the bet winnings from users';
$cs_lang['rollback_1'] = 'Return the bet amount to users &amp; let users keep any winnings';
$cs_lang['rollback_2'] = 'Just delete';

// List.php
$cs_lang['overview']= 'Overview';
$cs_lang['no_cat_text']= 'No information.';
$cs_lang['start']= 'Start on';
$cs_lang['ende'] = 'Ends on';
$cs_lang['stat'] = 'status';
$cs_lang['bets']= 'Bets';
$cs_lang['open'] = 'Open';
$cs_lang['go_open'] = 'Overview Open Bets';
$cs_lang['closed'] = 'Finished';
$cs_lang['on_calc'] = 'Closed';
$cs_lang['ready'] = 'Analyzed';
$cs_lang['participants'] = 'participants';

// edit.php
$cs_lang['head_edit']	= 'Edit';
$cs_lang['body_edit'] =  'Please fill in all fields marked with a *.';
$cs_lang['edit_done'] =  'Successfully changed';
$cs_lang['edit'] =  'edit';

// Result.php
$cs_lang['head_result']	= 'Enter result';
$cs_lang['body_result']	= 'Please enter the result.';
$cs_lang['winner']	= 'Winner';
$cs_lang['draw']	= 'Draw';
$cs_lang['result_booked'] = 'Result booked successfully';

//View.php
$cs_lang['details'] = 'Details';
$cs_lang['body_details'] = 'Detailed information about a bet.';
$cs_lang['bet'] = 'Bet';
$cs_lang['credits'] = 'Account balance';
$cs_lang['place_bet'] = 'Place bet';
$cs_lang['bidding'] = 'Biddings';
$cs_lang['result'] = 'Result';
$cs_lang['wins'] = 'wins';
$cs_lang['bet_amount'] = 'Bet amount'; 
$cs_lang['date'] = 'Date';
$cs_lang['team'] = 'Team / Opponent'; 
$cs_lang['earned'] = 'Earned'; 
$cs_lang['win_quote'] = 'Super quote';

//place_bet.php
$cs_lang['place_failed'] = 'Placing bet failed';
$cs_lang['placed_bet'] = 'Placed bet successfull!';
$cs_lang['not_enough_points'] = ' - Not enough %s';
$cs_lang['already_bet'] = 'You already placed your bet.';
$cs_lang['no_user'] = ' - You\'re not loged in.';
$cs_lang['no_desc'] = 'Place your bet now!';
$cs_lang['no_contestant'] = ' - No contestant choosen.';
$cs_lang['no_bets'] = 'No bets yet.';
$cs_lang['no_amount'] = 'Can\'t place empty bet.';
$cs_lang['no_delete'] = 'Can\'t delete bet!';
$cs_lang['remove_placed_bet'] = 'Retract bet';
$cs_lang['remove_placed_done'] = 'Placed bet retracted successfully.';

// toplist.php
$cs_lang['toplist'] = 'Toplist';
?>
