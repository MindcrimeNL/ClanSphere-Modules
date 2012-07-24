<table class="forum" style="width:{page:width}" cellpadding="0" cellspacing="{page:cellspacing}">
	<tr>
		<td class="headb" colspan="3">{lang:mod_name}</td>
	</tr>
	<tr>
		<td class="leftb">{lang:credits}: {user:points}</td>
		<td class="leftb"><a href="{url:bets_list}">{lang:go_open}</a></td>
		<td class="rightb">{pages:list}</td>
	</tr>
 	<tr>
  		<td class="leftb" style="text-align:center">{if:show_open}<a href="{url:bets_center:status=0}">{stop:show_open}
  		{lang:open_bets}
  		{if:show_open}</a>{stop:show_open}</td>
  		<td class="leftb" style="text-align:center">{if:show_calc}<a href="{url:bets_center:status=1}">{stop:show_calc}
  		{lang:wait_bets}
  		{if:show_calc}</a>{stop:show_calc}</td>
  		<td class="rightb" style="text-align:center">{if:show_closed}<a href="{url:bets_center:status=2}">{stop:show_closed}
  		{lang:closed_bets}
  		{if:show_closed}</a>{stop:show_closed}</td>
 	</tr>
</table>
<br />

{head:getmsg}

<table class="forum" cellpadding="0" cellspacing="{page:cellspacing}" style="width:{page:width}">
	<tr>
  		<td class="headb">{sort:date} {lang:date}</td>
  		<td class="headb">{sort:title} {lang:title}</td>
  		<td class="headb">{sort:category} {lang:category}</td>
  		<td class="headb">{lang:bet_amount}</td>
  		{if:show_earning}<td class="headb">{lang:earned}</td>{stop:show_earning}
	</tr>
	{loop:bets}
	<tr>
  		<td class="leftc">{bets:date}</td>
  		<td class="leftc">{bets:title}</td>
  		<td class="leftc">{bets:category}</td>
  		<td class="leftc">{if:user_win}{icon:submit}{stop:user_win}{if:user_loose}{icon:cancel}{stop:user_loose} {bets:amount}</td>
  		{if:show_earnings}<td class="leftc" style="color: {bets:earnedcolor};">{bets:earned}</td>{stop:show_earnings}
	</tr>
	{stop:bets}
</table>
