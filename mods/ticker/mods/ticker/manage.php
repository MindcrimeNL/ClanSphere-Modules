<?php
// Clansphere 2009
// ticker - manage.php - flow
// 2007-08-13
// based on the Tickermodule from Mr.AndersoN

$cs_lang = cs_translate('ticker');
require_once('mods/ticker/func.php');

$data = array();

$cs_ticker	 = cs_sql_select(__FILE__,'ticker','*',0,'ticker_id DESC',0,0);
$ticker_loop = count($cs_ticker);

settype($_GET['activate'], 'integer');
$activate = isset($_GET['activate']) ? $_GET['activate'] : 0;
$where	  = "options_mod='ticker' AND options_name='active_id'";

$ticker_count = cs_sql_count(__FILE__,'ticker');

$data['link']['ticker_new'] = cs_link($cs_lang['ticker_new'],'ticker','create');
$data['head']['ticker_count'] = $ticker_count;
$data['head']['message'] = cs_getmsg();
  
if (!empty($activate))
{
	$opt_cell = array('options_value');
	$opt_cont = array($activate);
	cs_sql_update(__FILE__,'options',$opt_cell,$opt_cont,0,$where);
}

$active_id = cs_sql_select(__FILE__,'options','options_value',$where);
$data['tickers'] = array();
for($run=0; $run<$ticker_loop; $run++) {

  $data['tickers'][$run]['direction'] = $cs_lang[$cs_ticker[$run]['ticker_direction']];

  $data['tickers'][$run]['amount'] = $cs_ticker[$run]['ticker_amount'];
  $data['tickers'][$run]['delay'] = $cs_ticker[$run]['ticker_delay'];
  
  $data['tickers'][$run]['preview'] = cs_ticker_marquee(1,$cs_ticker[$run]['ticker_amount'],$cs_ticker[$run]['ticker_delay'],$cs_ticker[$run]['ticker_direction'])
  			.cs_ticker_parse($cs_ticker[$run]['ticker_content'])
  			.cs_ticker_marquee(0);
	$status = ($active_id['options_value'] == $cs_ticker[$run]['ticker_id'] ? 1 : 0);
	switch ($status) {
		case 0:
			$status = cs_link(cs_icon('cancel'),'ticker','manage','activate=' . $cs_ticker[$run]['ticker_id']);
			break;
		case 1:
			$status = cs_icon('submit');
			break;
	}
  $data['tickers'][$run]['status'] = $status;
  
  $data['tickers'][$run]['url_edit'] = cs_url('ticker','edit','id=' . $cs_ticker[$run]['ticker_id']);
  $data['tickers'][$run]['url_remove'] = cs_url('ticker','remove','id=' . $cs_ticker[$run]['ticker_id']);

}

echo cs_subtemplate(__FILE__,$data,'ticker','manage');

?>
