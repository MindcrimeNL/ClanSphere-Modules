<table class="forum" cellpadding="0" cellspacing="{page:cellspacing}" style="width:{page:width}">
	<tr>
		<td class="headb" colspan="3">{lang:mod} - {lang:manage}</td>	
	</tr>
 	<tr>
  		<td class="leftb">{icon:editpaste} <a href="{url:bets_create}">{lang:new_bet}</a></td>
  		<td class="leftb">{icon:contents} {lang:total}: {count:all}</td>
  		<td class="rightb">{pages:list}</td>
 	</tr>
 	<tr>
  		<td class="leftb" colspan="2">{lang:select_status}</td>
  		<td class="rightb">
   			<form method="post" id="bets_manage" action="{url:bets_manage:status={value:status}}">
    			{lang:category} 
    			{head:dropdown}
    			<input type="submit" name="submit" value="{lang:show}" />
   			</form>
  		</td>
 	</tr>
 	<tr>
  		<td class="leftb" style="text-align:center">{if:show_open}<a href="{url:bets_manage:status=0}">{stop:show_open}
  			{lang:open_bets}
  			{if:show_open}</a>{stop:show_open}</td>
  		<td class="leftb" style="text-align:center">{if:show_calc}<a href="{url:bets_manage:status=1}">{stop:show_calc}
  		{lang:wait_bets}
  		{if:show_calc}</a>{stop:show_calc}</td>
  		<td class="rightb" style="text-align:center">{if:show_closed}<a href="{url:bets_manage:status=2}">{stop:show_closed}
  		{lang:closed_bets}
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
        <td class="headb">{lang:active}</td>
  		<td class="headb" colspan="2">{lang:options}</td>
 	</tr>{loop:bets}{if:nicht_abgelaufen}
 	<tr>
  		<td class="leftc"><a href="{url:bets_view:id={bets:bets_id}}" title="{lang:date}">{bets:date}</a></td>
 		<td class="leftc"><a href="{url:bets_view:id={bets:bets_id}}">{bets:bets_title}</a></td>
  		<td class="leftc"><a href="{url:categories_view:id={bets:cat_id}}">{bets:categories_name}</a></td>
        <td class="leftc" style="text-align:center">{if:public}{icon:submit}{stop:public}</td>
  		<td class="leftc" style="text-align:center">
        	{if:result}
            	<a href="{url:bets_result:bets_id={bets:bets_id}}" title="{lang:head_result}">{icon:smallcal}</a>
            	<a href="{url:bets_edit:bets_id={bets:bets_id}}" title="{lang:edit}">{icon:edit}</a>
            {stop:result}
        	{if:open}
            	<a href="{url:bets_edit:bets_id={bets:bets_id}}" title="{lang:edit}">{icon:edit}</a>
            {stop:open}
        </td>
  		<td class="leftc" style="text-align:center"><a href="{url:bets_remove:bets_id={bets:bets_id}}" title="{lang:remove}">{icon:editdelete}</a></td>
 	</tr>{stop:nicht_abgelaufen}{stop:bets}
</table>