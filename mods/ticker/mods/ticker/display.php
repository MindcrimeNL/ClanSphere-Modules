<?php
// Clansphere 2009
// ticker - display.php - flow
// 2007-08-13
// based on the Tickermodule from Mr.AndersoN

require_once('mods/ticker/func.php');

$from		 = 'ticker tck JOIN {pre}_options opt ON opt.options_mod=\'ticker\'';
$where		 = 'tck.ticker_id=opt.options_value AND opt.options_name=\'active_id\'';
$cs_ticker	 = cs_sql_select(__FILE__,$from,'*',$where);

echo cs_ticker_marquee(1,$cs_ticker['ticker_amount'],$cs_ticker['ticker_delay'],$cs_ticker['ticker_direction']);
echo cs_ticker_parse($cs_ticker['ticker_content']);
echo cs_ticker_marquee(0);

?>
