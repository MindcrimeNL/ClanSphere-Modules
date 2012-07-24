<form method="post" id="bets_remove" action="{bets:action}">
<table class="forum" cellpadding="0" cellspacing="{page:cellspacing}" style="width:{page:width}">
	<tr>
		<td class="headb" colspan="2">{lang:mod} - {lang:remove}</td>
	</tr>
	<tr>
		<td class="centerb" colspan="2">{bets:message}</td>
	</tr>
	<tr>
		<td class="leftb">{lang:rollback_option}</td>
		<td class="leftb"><select name="rollback_option">{bets:rollback_options}</select></td>
	</tr>
	<tr>
		<td class="centerb" colspan="2">
				<input type="hidden" name="bets_id" value="{bets:bets_id}"  />
				<input type="submit" name="agree" value="{lang:confirm}"  />
				<input type="submit" name="cancel" value="{lang:cancel}"  />
		</td>
	</tr>
</table>
</form>
