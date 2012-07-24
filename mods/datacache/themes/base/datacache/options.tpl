<br><br>
<table class="forum" style="width:{page:width}" cellpadding="0" cellspacing="1">
	<tr>
		<td class="headb">{lang:mod} - {lang:options}</td>
	</tr>
</table>
<br />
{if:view_1}
<table class="forum" style="width:{page:width}" cellpadding="0" cellspacing="1">
	<form method="post" name="options" action="?mod=datacache&amp;action=options">
	<tr>
		<td class="leftb" style="width: 200px;">{icon:groupevent} {lang:timeout}</td>
		<td class="leftc"><input type="text" name="timeout" value="{options:timeout}"></td>
	</tr>
	<tr>
		<td class="leftb">{icon:ksysguard} {lang:options}</td>
		<td class="leftc">
			<input type="submit" name="submit" value="{lang:create}" class="form" />
			<input type="reset" name="reset" value="{lang:reset}" class="form "/>
		</td>
  </tr>
	</form>
</table>
{stop:view_1}
{if:view_2}
<table class="forum" style="width:{page:width}" cellpadding="0" cellspacing="1">
	<tr>
		<td class="centerb">{lang:navok}</td>
	</tr>
	<tr>
		<td class="centerb"><a href="?mod=datacache&amp;action=manage">{lang:continue}</a></td>
	</tr>
</table>
{stop:view_2}
