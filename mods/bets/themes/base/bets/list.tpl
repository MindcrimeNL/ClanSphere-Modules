<table class="forum" cellpadding="0" cellspacing="{page:cellspacing}" style="width:{page:width}">
	<tr>
		<td class="headb" colspan="3">{lang:mod} - {lang:overview}</td>	
	</tr>
 	<tr>
  		<td class="leftb">
        	<form method="post" id="bets_list" action="{url:bets_list}&status={value:status}">
    			{lang:category} 
    			{head:dropdown}
    			<input type="submit" name="submit" value="{lang:show}" />
   			</form>
        </td>
  		<td class="leftb">{icon:contents} {lang:total}: {count:all}</td>
  		<td class="rightb">{pages:list}</td>
 	</tr>
</table>
<br />

<table  class="forum" cellpadding="0" cellspacing="{page:cellspacing}" style="width:{page:width}">
 	<tr>
  		<td class="leftb" style="text-align:center">{if:show_open}<a href="{url:bets_list:status=0}">{stop:show_open}
  		{lang:open}
  		{if:show_open}</a>{stop:show_open}</td>
  		<td class="leftb" style="text-align:center">{if:show_calc}<a href="{url:bets_list:status=1}">{stop:show_calc}
  		{lang:closed}
  		{if:show_calc}</a>{stop:show_calc}</td>
  		<td class="rightb" style="text-align:center">{if:show_closed}<a href="{url:bets_list:status=2}">{stop:show_closed}
  		{lang:ready}
  		{if:show_closed}</a>{stop:show_closed}</td>
 	</tr>
</table>

<br />
{head:message}
<table class="forum" cellpadding="0" cellspacing="{page:cellspacing}" style="width:{page:width}">
	<tr>
  		<td class="headb">{sort:date} {lang:date}</td>
  		<td class="headb">{sort:title} {lang:title}</td>
  		<td class="headb">{sort:category} {lang:category}</td>
        <td class="headb">{lang:bidding}</td>
 	</tr>{loop:bets}{if:nicht_abgelaufen}
 	<tr>
  		<td class="leftc"><a href="{url:bets_view:id={bets:bets_id}}" title="{lang:date}">{bets:date}</a></td>
 		<td class="leftc"><a href="{url:bets_view:id={bets:bets_id}}">{bets:bets_title}</a></td>
  		<td class="leftc"><a href="{url:categories_view:id={bets:cat_id}}">{bets:categories_name}</a></td>
        <td class="leftc" style="text-align:center"><a href="{url:bets_view:id={bets:bets_id}}">{bets:anzahl} {lang:participants}</a></td>
 	</tr>{stop:nicht_abgelaufen}{stop:bets}
</table>