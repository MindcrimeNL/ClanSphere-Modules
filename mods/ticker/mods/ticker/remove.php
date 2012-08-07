<?php
// Clansphere 2009
// ticker - remove.php - flow
// 2007-08-13
// based on the Tickermodule from Mr.AndersoN

$cs_lang = cs_translate('ticker');

$data = array();

$ticker_id = $_REQUEST['id'];
settype($ticker_id, 'integer');

if (isset($_GET['agree'])) {
	cs_sql_delete(__FILE__,'ticker',$ticker_id);
  cs_redirect($cs_lang['remove_success'], 'ticker');
}
elseif(isset($_GET['cancel'])) 
  cs_redirect($cs_lang['remove_false'], 'ticker');  
else {
  $data['head']['topline'] = sprintf($cs_lang['remove_query'],$ticker_id);
  $data['ticker']['content'] = cs_link($cs_lang['confirm'],'ticker','remove','id=' . $ticker_id . '&amp;agree');
  $data['ticker']['content'] .= ' - ';
  $data['ticker']['content'] .= cs_link($cs_lang['cancel'],'ticker','remove','id=' . $ticker_id . '&amp;cancel');
}

echo cs_subtemplate(__FILE__,$data,'ticker','remove');
?>