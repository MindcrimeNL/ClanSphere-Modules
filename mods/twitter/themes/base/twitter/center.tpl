<table class="forum" cellpadding="0" cellspacing="{page:cellspacing}" style="width:{page:width};">
  <tr>
    <td class="headb"><a href="{url:twitter_view}">{lang:twitter_view}</a> <a href="{url:twitter_viewown}">{lang:twitter_viewown}</a> <a href="{url:twitter_create}">{lang:twitter_create}</a></td>
  </tr>
</table>
<br />
<form method="post" id="twitter_center" action="{url:twitter_center}">
<table class="forum" cellpadding="0" cellspacing="{page:cellspacing}" style="width:{page:width};">
  <tr>
  	<td class="leftc" colspan="2"><a href="http://twitter.com/settings/connections" target="twitter">{lang:your_connections}</a></td>
  </tr>
	<tr>
		<td class="leftc">{icon:personal} {lang:twitter_access_token}</td>
		<td class="leftb">{twitter:twitter_access_token}</td>
	</tr>
  <tr>
    <td class="leftc">{icon:mail} {lang:twitter_account}</td>
    <td class="leftb"><input type="checkbox" name="twitter_delete" value="1" /> {lang:delete}</td>
  </tr>
  <tr>
    <td class="leftc"> {icon:ksysguard} {lang:options}</td>
    <td class="leftb"><input type="submit" name="submit" value="{lang:edit}" /> <input type="submit" name="request" value="{lang:keygen}" /></td>
  </tr>
</table>
</form>
