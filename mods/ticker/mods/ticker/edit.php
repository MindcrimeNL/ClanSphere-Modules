<?php
// Clansphere 2009
// ticker - edit.php - flow
// 2007-08-13
// based on the Tickermodule from Mr.AndersoN

$cs_lang = cs_translate('ticker');
require_once('mods/ticker/func.php');

$ticker_id = $_REQUEST['id'];
settype($ticker_id, 'integer');

$data = array();

if(isset($_POST['submit'])) {

	$cs_ticker['ticker_direction']	= $_POST['ticker_direction'];
	$cs_ticker['ticker_amount']		= intval($_POST['ticker_amount']) == 0 ? 'null' : intval($_POST['ticker_amount']);
	$cs_ticker['ticker_delay']		= intval($_POST['ticker_delay']) == 0 ? 'null' : intval($_POST['ticker_delay']);
	$cs_ticker['ticker_content']	= $_POST['ticker_content'];

	$error = 0;
	$errormsg = '';

	if(empty($cs_ticker['ticker_direction'])) {
		$error++;
		$errormsg  = $cs_lang['no_direction'] . cs_html_br(1);
	}
	if(empty($cs_ticker['ticker_amount'])) {
		$error++;
		$errormsg .= $cs_lang['no_amount'] . cs_html_br(1);
	}
	if(empty($cs_ticker['ticker_delay'])) {
		$error++;
		$errormsg .= $cs_lang['no_delay'] . cs_html_br(1);
	}
	if(empty($cs_ticker['ticker_content'])) {
		$error++;
		$errormsg .= $cs_lang['no_content'] . cs_html_br(1);
	}
}
else
{
	$cs_ticker = cs_sql_select(__FILE__,'ticker','*','ticker_id = ' . $ticker_id);
}



if(!isset($_POST['submit'])) {
   $data['head']['body'] = $cs_lang['edit_text'];
}
elseif(!empty($error)) {
   $data['head']['body'] = $errormsg;
}

if(!empty($error) OR !isset($_POST['submit'])) {

	$cs_ticker['ticker_amount'] = $cs_ticker['ticker_amount'] == 'null' ? '0' : $cs_ticker['ticker_amount'];
	$cs_ticker['ticker_delay'] = $cs_ticker['ticker_delay'] == 'null' ? '0' : $cs_ticker['ticker_delay'];

	$direction[0]['ticker_direction'] = 'left';
	$direction[0]['name'] = $cs_lang['left'];
	$direction[1]['ticker_direction'] = 'right';
	$direction[1]['name'] = $cs_lang['right'];
	$data['ticker']['ticker_direction'] = cs_dropdown('ticker_direction','name',$direction,$cs_ticker['ticker_direction']); 
	$data['ticker']['ticker_amount'] = $cs_ticker['ticker_amount']; 
	$data['ticker']['ticker_delay'] = $cs_ticker['ticker_delay'];
	$data['ticker']['ticker_features'] = cs_ticker_features('ticker_content');
	$data['ticker']['ticker_content'] = $cs_ticker['ticker_content'];

  $data['ticker']['id'] = $ticker_id;

  echo cs_subtemplate(__FILE__,$data,'ticker','edit');
}
else {

	settype($cs_ticker['ticker_amount'], 'integer');
	settype($cs_ticker['ticker_delay'], 'integer');

	$ticker_cells = array_keys($cs_ticker);
	$ticker_save  = array_values($cs_ticker);
	cs_sql_update(__FILE__,'ticker',$ticker_cells,$ticker_save,$ticker_id);
	
  // clear datacache
	if (function_exists('cs_datacache_load'))
		cs_datacache_clear(null, 'ticker');

	cs_redirect($cs_lang['edit_success'], 'ticker') ;
}

?>
