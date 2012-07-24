<?php

class cs_bet
{
	const ROLLBACK_ALL = 0; // pay back bet amount, retrieve winnings
	const ROLLBACK_BET = 1; // pay back bet amount
	const ROLLBACK_NONE = 2;// do nothing
	
	const STATUS_OPEN = 0; // you can still bet
	const	STATUS_CLOSED = 1; // betting not possible anymore
	const STATUS_FINISHED = 2; // result entered and winnings have been paid

  const QUOTE_TYPE_SHARED = 0; // old "off"
  const QUOTE_TYPE_PERCENTAGE = 1; // old "on"
  const QUOTE_TYPE_FIXED = 2; // every quote set a fixed return value
	
	// bets_id, categories_id, bets_status , bets_title, bets_starts_at, bets_closed_at, bets_description
	public $bet_data = array();
	public $contestant_data = array();
	public $bet_users = array();
	public $bet_total;
	public $error = '';
	private $cs_lang;
	private $id = 0;
    
	public function __construct($id = 0)
	{
    if (!empty($id)) {$this->loadBet($id);}
		$this->cs_lang = cs_translate('bets');
		$this->options = cs_sql_option(__FILE__, 'bets');
  } // function __construct
	
	// -------------------------------------------------------------- //
	
  public function loadBet($id)
  {
    $this->id = (int)$id;

		// Initialize SQL Select vars for Bets
		$from = 'bets bet LEFT JOIN {pre}_categories cat ON cat.categories_id = bet.categories_id';
		$where = 'bet.bets_id = '. $this->id;
		$select = 'bet.*, cat.categories_name AS categories_name, cat.categories_picture AS categories_picture';
		$select .= ', cat.categories_id AS categories_id';

		$this->bet_data = cs_sql_select(__FILE__,$from,$select,$where);
		
		// Initialize SQL Select vars for Contestants
		$from = 'bets_contestants cbet LEFT JOIN {pre}_clans clan ON clan.clans_id = cbet.clans_id ';
		$from .= 'LEFT JOIN {pre}_bets_users busr ON busr.contestants_id = cbet.contestants_id ';
		$from .= 'WHERE cbet.bets_id = '. $this->id.' GROUP BY cbet.contestants_id';
		$where = 'cbet.bets_id = '. $this->id;
    $select = 'cbet.*, clan.clans_name AS clans_name, clan.clans_country AS clans_country, clan.clans_picture AS clans_picture, ROUND(SUM(busr.bets_amount), 2) AS placed';
		
		$this->contestant_data = cs_sql_select(__FILE__,$from,$select,0,'contestants_id',0,0);
		
		// Initialize SQL Select vars for Bets_users
		$from = 'bets_users busr '
			.'LEFT JOIN {pre}_users usr ON usr.users_id = busr.users_id '
			.'LEFT JOIN {pre}_coins cns ON cns.users_id = busr.users_id ';
		$where = 'busr.bets_id = '. $this->id;
		$select = 'busr.*, usr.users_nick AS name, cns.coins_total AS coins_total';
		
		$this->bet_users = cs_sql_select(__FILE__,$from,$select,$where,'busr.bets_users_time',0,0);
		
		$total= cs_sql_select(__FILE__,'bets_users busr WHERE busr.bets_id = '. $this->id.' GROUP BY busr.bets_id', 'SUM(busr.bets_amount) AS total');
		$this->bet_total = $total['total'];
	} // function loadBet
    
	// -------------------------------------------------------------- //
	
	public function saveBet($id = 0, $contestants = array(), $cs_bets)
	{
		$id = (int) $id;
		
		if (empty($cs_bets['categories_id']))
    		$this->error .= $this->cs_lang['no_category'] . cs_html_br(1);

		if (empty($contestants[0]) && empty($contestants[1]))
    		$this->error .= $this->cs_lang['no_contestant'] . cs_html_br(1);
		
		if ($cs_bets['bets_closed_at'] <cs_time() && $id == 0)
    		$this->error .= $this->cs_lang['no_closed_at'].  cs_html_br(1);
			
		if (empty($cs_bets['bets_title']) && empty($cs_bets['bets_auto_title']))
    		$this->error .= $this->cs_lang['no_title'].  cs_html_br(1);
		
		if (empty($this->error))
		{
			if (empty($id)) {$this->createBet($contestants, $cs_bets);}	
			else {$this->editBet($id, $contestants, $cs_bets);}
		}
		
	} // function saveBet
    
	private function createBet($contestants, $bet_data)
	{
		// Füge Wettdetails hinzu
		$bets_cells = array_keys($bet_data);
		$bets_save = array_values($bet_data);
		cs_sql_insert(__FILE__,'bets',$bets_cells,$bets_save);
		$created_bets_id = cs_sql_insertid(__FILE__);
		
		// Füge Wettkandidaten hinzu
		foreach($contestants as $key => $value)
		{
			if ($value['type'] == 'clan')
			{
				cs_sql_insert(__FILE__,'bets_contestants', array('bets_id', 'clans_id', 'bets_quote', 'bets_draw'), 
														   array( $created_bets_id , $value['value'], $value['quote'], $value['draw']) );
			}
			else if (!empty($value['value']))
			{
				cs_sql_insert(__FILE__,'bets_contestants', array('bets_id', 'bets_name', 'bets_quote', 'bets_draw'), 
														   array( $created_bets_id, $value['value'], $value['quote'], $value['draw']) );
			}
		}
		
		
 	} // function createBet
    
	private function editBet($bets_id, $contestants, $bet_data)
	{
		$bets_id = (int) $bets_id;

		// Wette wieder öffnen ?!?!
		if($bet_data['bets_closed_at'] > cs_time())
		{
			$bet_data['bets_status'] = self::STATUS_OPEN;	
		}

		// Füge Wettdetails hinzu
		$bets_cells = array_keys($bet_data);
		$bets_save = array_values($bet_data);
		cs_sql_update(__FILE__,'bets',$bets_cells,$bets_save,$bets_id);
		
			
		// Lösche entfernte Wettkandidaten aus Datenbank
		foreach ($this->contestant_data as $key => $value)
		{
			$found = false;
			foreach ($contestants as $key2 => $value2)
			{
				if ( array_search($value['contestants_id'], $value2 ))
				{
					$found = true;
				}
			}
			if ($found == false)
			{
				cs_sql_delete(__FILE__, 'bets_contestants', $value['contestants_id'], 'contestants_id');
				
				if (is_array($this->bet_users) && count($this->bet_users))
				{
					foreach($this->bet_users as $user_val)
					{
						if ($user_val['contestants_id'] == $value['contestants_id'] ) 
						{
							cs_coins_rollback($user_val['users_id'], 'bets', ($user_val['bets_amount'] + $this->options['base_fee']), 0.0);
							cs_sql_delete(__FILE__, 'bets_users', $user_val['bets_users_id']);
						}
					}
				}
			}
		}
		// Füge Wettkandidaten hinzu
		foreach($contestants as $key => $value)
		{
			
			if($value['id'] > 0)
			{
				if ($value['type'] == 'clan')
				{
					cs_sql_update(__FILE__,'bets_contestants', array('bets_id', 'clans_id', 'bets_quote', 'bets_draw'), 
															   array( $bets_id, $value['value'], $value['quote'], $value['draw']), 0, 'contestants_id ='.$value['id']);
				}
				else if (!empty($value['value']))
				{
					cs_sql_update(__FILE__,'bets_contestants', array('bets_id', 'bets_name', 'bets_quote', 'bets_draw'), 
															   array( $bets_id, $value['value'], $value['quote'], $value['draw']), 0, 'contestants_id ='.$value['id'] );
				}
			}
			else
			{
				if ($value['type'] == 'clan')
				{
					cs_sql_insert(__FILE__,'bets_contestants', array('bets_id', 'clans_id', 'bets_quote', 'bets_draw'), 
															   array( $bets_id, $value['value'], $value['quote'], $value['draw']));
				}
				else if (!empty($value['value']))
				{
					cs_sql_insert(__FILE__,'bets_contestants', array('bets_id', 'bets_name', 'bets_quote', 'bets_draw'), 
															   array( $bets_id, $value['value'], $value['quote'], $value['draw']));
				}
			}
		}
		
		/* reload new data */
		$this->loadBet($bets_id);
		$this->calcQuote();
		$this->loadBet($bets_id);
  } // function editBet

	/**
	 * Remove a bet, default return only the coins betted, but not the winnings
	 */    
  public function deleteBet($returnBets = 1)
  {
		if (empty($this->id))
		{
			$this->error .= $this->cs_lang['no_delete'];
			return;
		}
		switch ($this->bet_data['bets_status'])
		{
		case self::STATUS_OPEN:
		case self::STATUS_CLOSED:
			/* if status is STATUS_OPEN or STATUS_CLOSED, return all user bets if we may */
			if ($returnBets != self::ROLLBACK_NONE)
			{
				if (count($this->bet_users) > 0)
				foreach($this->bet_users as $user_val)
				{
					cs_coins_rollback($user_val['users_id'], 'bets', ($user_val['bets_amount'] + $this->options['base_fee']), 0.0);
					cs_sql_delete(__FILE__, 'bets_users', $user_val['bets_users_id']);
				}
			}
			break;
		case self::STATUS_FINISHED:
			if ($returnBets != self::ROLLBACK_NONE)
			{
				if (count($this->bet_users) > 0)
				foreach($this->bet_users as $user_val)
				{
					$amount_used = $user_val['bets_amount'];
					$amount_received = $user_val['bets_pay_amount'];
					/* check if we only pay back the ones who lost and not retrieve the winnings
					 * (or people with negative winnings (betted but lost))
					 */
					if ($returnBets == self::ROLLBACK_BET || $amount_received < 0.0)
						$amount_received = 0.0;
					cs_coins_rollback($user_val['users_id'], 'bets', ($amount_used + $this->options['base_fee']), $amount_received);
					cs_sql_delete(__FILE__, 'bets_users', $user_val['bets_users_id']);
				}
			}
			break;
		}
		/* remove all user bets */
		cs_sql_delete(__FILE__, 'bets_users', $this->id, 'bets_id');
		/* remove all contestants */
		cs_sql_delete(__FILE__, 'bets_contestants', $this->id, 'bets_id');
		/* remove bet */
		cs_sql_delete(__FILE__, 'bets',$this->id);

	} // function deleteBet
   
  
  /**
   * Place a bet
   * 
   * @param	int	$userID
   * @param	float	$amount
   */
  public function placeBet($userID, $amount, $contestantID)
  {
		// Get users account balance and possible bet
		$cs_bets = cs_sql_select(__FILE__,'bets_users','bets_id','bets_id = '.$this->bet_data['bets_id'].' AND users_id = '.(int)$userID);
		$cs_coins = cs_coins_exists($userID);
		if ($cs_coins === false)
		{
			/* no, try to create one */
			$cs_coins = cs_coins_create($userID);
			if ($cs_coins === false)
				$cs_coins['coins_total'] = 0.0;
		}

		// Error handling
		// Not enough money
		if (($amount + $this->options['base_fee']) > $cs_coins['coins_total'])
		{
			$this->error .= sprintf($this->cs_lang['not_enough_points'], $this->options['pointsname']).  cs_html_br(1);	
		}
		// Already placed bet 
		if (!empty($cs_bets['bets_id']))
		{
			$this->error .= ' - ' . $this->cs_lang['already_bet'].  cs_html_br(1);
		}
		// No amount placed
		if ($amount <= 0)
		{
			$this->error .= $this->cs_lang['no_amount'].  cs_html_br(1);
		}
		// No contestant selected
		if (empty($contestantID))
		{
			$this->error .= $this->cs_lang['no_contestant'].  cs_html_br(1);
		}
		// Already closed
		if ($this->bet_data['bets_closed_at'] < cs_time())
		{
			$this->error .= $this->cs_lang['not_open']. cs_html_br(1);
		}
		
		// Place bet in database
		if (empty($this->error))
		{
			$theTime = cs_time();
			if (cs_coins_use($userID, 'bets', ($amount + $this->options['base_fee'])))
			{
				cs_sql_insert(__FILE__,'bets_users', array('bets_id', 'contestants_id', 'users_id', 'bets_users_time', 'bets_amount'),
													 array($this->bet_data['bets_id'], $contestantID, $userID, $theTime, $amount));
//				$description = $this->cs_lang['placed_bet'] . ': <a href="#mod=bets&action=view&id='.$this->bet_data['bets_id'].'">'.$this->cs_lang['bet'].' #'.$this->bet_data['bets_id'].'</a>';
			}
			else
			{
				cs_error(__FILE__, 'Coins use failed for bets module, user #'.intval($userID).', amount '.($amount+$this->options['base_fee']).' coins.');
				$this->error .= $this->cs_lang['no_amount'].  cs_html_br(1);
			}
		}
	
  } // function placeBet
	
	public function removePlacedBet($userID)
	{
		// Get users account balance
		$cs_bets = cs_sql_select(__FILE__,'bets_users','bets_users_id, bets_amount','bets_id = '.$this->bet_data['bets_id'].' AND users_id = '.(int)$userID);
		$cs_coins = cs_coins_exists($userID);
		if ($cs_coins === false)
		{
			/* no, try to create one */
			$cs_coins = cs_coins_create($userID);
			if ($cs_coins === false)
				$cs_coins['coins_total'] = 0;
		}
		
		// Error handling
		// Not betted
		if (empty($cs_bets['bets_amount']))
		{
			$this->error .= $this->cs_lang['no_bets'].  cs_html_br(1);	
		}
		if ($this->bet_data['bets_status'] > self::STATUS_OPEN)
		{
			$this->error .= $this->cs_lang['not_open']. cs_html_br(1);
		}

		// Remove placed bet (Wetteinsatz)
		if (empty($this->error)) 
		{
			$rollback_amount = ($cs_bets['bets_amount']-($cs_bets['bets_amount']/100)*$this->options['remove_quote']);
			if (cs_coins_rollback($userID, 'bets', $rollback_amount, 0.0))
			{
				cs_sql_delete(__FILE__, 'bets_users', $cs_bets['bets_users_id']);
//				$description = $this->cs_lang['remove_placed_done'] . ': <a href="#mod=bets&action=view&id='.$this->bet_data['bets_id'].'">'.$this->cs_lang['bet'].' #'.$this->bet_data['bets_id'].'</a>';
			}
			else
			{
				$this->error .= $this->cs_lang['no_amount'].  cs_html_br(1);
				cs_error(__FILE__, 'Coins rollback failed for bets module, user #'.intval($userID).', amount '.$rollback_amount.' coins.');
			}
		}
	} // function removePlacedBet
	
	public function enterResult($contestantID)
	{
		
		$contestantID = (int) $contestantID;
		$quote = cs_sql_select(__FILE__,'bets_contestants','bets_quote','contestants_id = '. $contestantID,0,0,1);
		$time = cs_time();
		if (count($this->bet_users))
		foreach ($this->bet_users as $key => $value)
		{
			if ($contestantID == $value['contestants_id'])
			{
//				$description = $this->cs_lang['result_booked'] . ': <a href="#mod=bets&action=view&id='.$this->bet_data['bets_id'].'">'.$this->cs_lang['bet'].' #'.$this->bet_data['bets_id'].'</a>';
				switch ($this->bet_data['bets_quote_type'])
				{
				case self::QUOTE_TYPE_SHARED:
					/* the quote is a calculated multiplyer */
					$amount = ($value['bets_amount'] * ($quote['bets_quote'])); // super quote has been integrated already
					break;
				case self::QUOTE_TYPE_PERCENTAGE:
					/* the quote is a percentage */
					$amount = ($value['bets_amount'] * (1.0 + $this->options['win_quote'] + $quote['bets_quote'] / 100.0));
					break;
				case self::QUOTE_TYPE_FIXED:
					/* the quote is a fixed multiplyer */
					$amount = ($value['bets_amount'] * (1.0 + $this->options['win_quote']) * $quote['bets_quote']);
					break;
				}
				if (cs_coins_receive($value['users_id'], 'bets', $amount))
				{
					cs_sql_update(__FILE__,'bets_users', array('bets_pay_time', 'bets_pay_amount'), array($time, $amount),$value['bets_users_id']);
				}
				else
					cs_error(__FILE__, 'Coins received failed for bets module, user #'.intval($value['users_id']).', amount '.$amount.' coins.');
			}
			else
			{
				cs_sql_update(__FILE__,'bets_users', array('bets_pay_time', 'bets_pay_amount'), array($time, -$value['bets_amount']),$value['bets_users_id']);
			}
		}
		cs_sql_update(__FILE__,'bets_contestants',array('bets_winner'),array(1), 0, 'contestants_id = '. $contestantID);
		cs_sql_update(__FILE__,'bets',array('bets_status'),array(self::STATUS_FINISHED), (int)$this->bet_data['bets_id']);
		
	} // function enterResult
	
	public function calcQuote()
	{
		
		switch ($this->bet_data['bets_quote_type'])
		{
		case self::QUOTE_TYPE_SHARED:
			/* calculate total amount of placed bets */ 
			$summ = 0;
			foreach ($this->contestant_data as $value)
			{
				$summ += $value['placed'];	
			}

			foreach ($this->contestant_data as $value)
			{
				if ( $value['placed'] > 0 )
				{
					$quote = (($summ - $value['placed']) / $value['placed']) + 1 + $this->options['win_quote'];

					if  ( $quote < $this->options['min_quote'] )
					{
						$quote = $this->options['min_quote'];
					}
					else if ( $quote > $this->options['max_quote'] )
					{
						$quote = $this->options['max_quote'];
					}
				}
				else {
					$quote = $this->options['max_quote'];	
				}
				if ( $quote != $value['bets_quote'] )
				{
					cs_sql_update(__FILE__,'bets_contestants', array('bets_quote'), 
															   array(round($quote, 2)), 
															   0, 
															   'contestants_id = '. $value['contestants_id']);	
				}
			}
			break;
		case self::QUOTE_TYPE_PERCENTAGE:
			break;
		case self::QUOTE_TYPE_FIXED:
			break;
		}
	} // function calcQuote
} // class cs_bet
?> 
