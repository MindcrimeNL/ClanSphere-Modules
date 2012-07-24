<?php
// Clansphere 2009
// ticker - func.php - flow
// 2007-08-13
// based on the Tickermodule from Mr.AndersoN

function marquee($func,$amount = 0,$delay = 0,$direction = 0) {

	if(empty($func)) {
		return '</marquee>';
	} else {
		$opt = cs_sql_option(__FILE__,'ticker');

		$var = '<marquee ';
		$var.= empty($amount) ? 'scrollamount="0" ' : 'scrollamount="' . $amount . '" ';
		$var.= empty($delay) ? 'scrolldelay="0" ' : 'scrolldelay="' . $delay . '" ';
		if(!empty($direction)) {
		$var .= 'direction="' . $direction . '"';
		}
		if(!empty($opt['stop_mo'])) {
		$var .= 'onMouseOver="this.stop()" onMouseOut="this.start()"';
		}
		return $var . '>';
	}
}

function ticker_features($name) {

	$cs_lang = cs_translate('system/abcodes');

	$color = "javascript:abc_insert('[color=' + this.form.color_";
	$color .= $name . ".options[this.form.color_" . $name . ".selectedIndex].value + ']'";
	$color .= ",'[/color]','" . $name . "');this.selectedIndex=0";
	$var = cs_html_select(1,'color_' . $name,"onchange=\"" . $color . "\"");
	$var .= cs_html_option($cs_lang['font_color'],'');
	$var .= cs_html_option($cs_lang['aqua'],'aqua',0,'color:aqua');
	$var .= cs_html_option($cs_lang['black'],'black',0,'color:black');
	$var .= cs_html_option($cs_lang['blue'],'blue',0,'color:blue');
	$var .= cs_html_option($cs_lang['fuchsia'],'fuchsia',0,'color:fuchsia');
	$var .= cs_html_option($cs_lang['gray'],'gray',0,'color:gray');
	$var .= cs_html_option($cs_lang['green'],'green',0,'color:green');
	$var .= cs_html_option($cs_lang['lime'],'lime',0,'color:lime');
	$var .= cs_html_option($cs_lang['maroon'],'maroon',0,'color:maroon');
	$var .= cs_html_option($cs_lang['navy'],'navy',0,'color:navy');
	$var .= cs_html_option($cs_lang['olive'],'olive',0,'color:olive');
	$var .= cs_html_option($cs_lang['orange'],'orange',0,'color:orange');
	$var .= cs_html_option($cs_lang['purple'],'purple',0,'color:purple');
	$var .= cs_html_option($cs_lang['red'],'red',0,'color:red');
	$var .= cs_html_option($cs_lang['silver'],'silver',0,'color:silver');
	$var .= cs_html_option($cs_lang['teal'],'teal',0,'color:teal');
	$var .= cs_html_option($cs_lang['white'],'white',0,'color:white');
	$var .= cs_html_option($cs_lang['yellow'],'yellow',0,'color:yellow');
	$var .= cs_html_select(0);

	$on	  = "onclick=\"javascript:abc_";

	$var .= "\n <input type=\"button\" name=\"b\" value=\"b\" ". $on . "insert('[b]','[/b]','" . $name . "','')\"  class=\"form\">";
	$var .= "\n <input type=\"button\" name=\"i\" value=\"i\" ". $on . "insert('[i]','[/i]','" . $name . "','')\"  class=\"form\">";
	$var .= "\n <input type=\"button\" name=\"u\" value=\"u\" ". $on . "insert('[u]','[/u]','" . $name . "','')\"  class=\"form\">";
	$var .= "\n <input type=\"button\" name=\"url\" value=\"url\" ". $on . "insert('[url]','[/url]','" . $name . "','')\"  class=\"form\">";
	$var .= cs_html_br(1);

	$var .= "\n <input type=\"button\" name=\"lastnews\" value=\"Last News\" ". $on . "insert('{ticker->news}','','" . $name . "','')\"  class=\"form\">";
	$var .= "\n <input type=\"button\" name=\"lastuser\" value=\"Last User\" ". $on . "insert('{ticker->user}','','" . $name . "','')\"  class=\"form\">";
	$var .= "\n <input type=\"button\" name=\"lastdls\" value=\"Last Files\" ". $on . "insert('{ticker->files}','','" . $name . "','')\"  class=\"form\">";
	$var .= "\n <input type=\"button\" name=\"online\" value=\"Online User\" ". $on . "insert('{ticker->online}','','" . $name . "','')\"  class=\"form\">";
	$var .= "\n <input type=\"button\" name=\"lastthreads\" value=\"Last Threads\" ". $on . "insert('{ticker->board}','','" . $name . "','')\"  class=\"form\">";
	$var .= "\n <input type=\"button\" name=\"lastwars\" value=\"Last Wars\" ". $on . "insert('{ticker->wars}','','" . $name . "','')\"  class=\"form\">";
	$var .= "\n <input type=\"button\" name=\"nextwars\" value=\"Next Wars\" ". $on . "insert('{ticker->warsnext}','','" . $name . "','')\"  class=\"form\">";
	$var .= cs_html_br(2);

	return $var;
}


function ticker_parse($ticker_string)
{
	$allowed = array('news', 'user', 'files', 'online', 'board', 'wars', 'warsnext');
	/* we need to call cs_abcode_load before using any cs_abcode_xxx function */
	cs_abcode_load();
	$str_pattern 	= '`\[[\s]?(b|u|i)[\s]?\][\s]?(.*?)[\s]?\[[\s]?/[\s]?\\1[\s]?\]`is';
	$str_replace	= '<\\1>\\2</\\1>';
	$ticker_string	= preg_replace($str_pattern, $str_replace, $ticker_string);
	$ticker_string	= preg_replace_callback("=\[color\=(.*?)\](.*?)\[/color\]=si", "cs_abcode_color", $ticker_string);
	$ticker_string	= preg_replace_callback("=\[url\=(.*?)\](.*?)\[/url\]=si","cs_abcode_url",$ticker_string);
  $ticker_string	= preg_replace_callback("=\[url\](.*?)\[/url\]=si","cs_abcode_url",$ticker_string);

  preg_match_all('#{ticker->(.*?)}#is',$ticker_string,$ticker_subpattern);
	$loop = count($ticker_subpattern[0]);
	$run  = 0;
	$ticker_array = array();
	while (!empty($loop))
	{
		$path = 'mods/ticker/';
		if (in_array($ticker_subpattern[1][$run],$allowed))
			$ticker_array[$ticker_subpattern[0][$run]] = $path . $ticker_subpattern[1][$run] . 'ticker.php';
		$run++;
		$loop--;
	}

	foreach ($ticker_array AS $pattern => $file)
	{
		if(file_exists($file)) {
			ob_start();
			include($file);
			$ob_contents = ob_get_contents();
			ob_end_clean();
			$ticker_string = str_replace($pattern, $ob_contents, $ticker_string);
		}
		else {
			$ticker_string = str_replace($pattern, '', $ticker_string);
		}
	}

  return $ticker_string;
}

?>
