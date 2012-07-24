<table class="forum" style="width:{page:width}" cellpadding="0" cellspacing="{page:cellspacing}">
	<tr>
		<td class="headb">{lang:mod} - {lang:opt_coins}</td>
	</tr>
	<tr>
		<td class="leftb">{lang:body_coins}</td>
	</tr>
</table>
<br />

<form method="post" id="coins_options" action="{url:coins_options}">
<table class="forum" style="width:{page:width}" cellpadding="0" cellspacing="{page:cellspacing}">
	<tr>
		<td class="leftc">{icon:kexi} {lang:startcoins}</td>
		<td class="leftb"><input type="text" name="startcoins" value="{com:startcoins}" maxlength="20" size="20" /></td>
	</tr>
	<tr>
		<td class="leftc">{icon:kexi} {lang:coin_decimals}</td>
		<td class="leftb"><input type="text" name="coin_decimals" value="{com:coin_decimals}" maxlength="2" size="2" /></td>
	</tr>
	<tr>
		<td class="leftc">{icon:kcmdf} {lang:coin_mods}</td>
		<td class="leftb"><input type="text" name="coin_mods" value="{com:coin_mods}" maxlength="80" size="20" /></td>
	</tr>
	<tr>
		<td class="leftc">{icon:ksysguard} {lang:options}</td>
		<td class="leftb">
			<input type="submit" name="submit" value="{lang:edit}" />
			<input type="reset" name="reset" value="{lang:reset}" />
		</td>
	</tr>
</table>
</form>
