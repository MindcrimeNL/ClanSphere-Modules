<br><br>
<table class="forum" style="width:{page:width}" cellpadding="0" cellspacing="1">
	<tr>
		<td class="headb">{lang:mod_name} - {lang:options}</td>
	</tr>
</table>
<br />
{if:view_1}
<table class="forum" style="width:{page:width}" cellpadding="0" cellspacing="1">
	<form method="post" name="options" action="?mod=twitter&amp;action=options">
	<tr>
		<td class="leftc" style="width: 200px;">{icon:groupevent} {lang:users_enable}</td>
		<td class="leftb">{lang:yes} <input type="radio" name="users_enable" value="1" {options:users_enable_yes}> /
			<input type="radio" name="users_enable" value="0" {options:users_enable_no}> {lang:no}</td>
	</tr>
	<tr>
		<td class="leftc" colspan="2"><a href="http://dev.twitter.com/apps" target="twitter">{lang:your_apps}</a></td>
	</tr>
	<tr>
		<td class="leftc">{icon:personal} {lang:website_consumer_key}</td>
		<td class="leftb"><input type="text" name="website_consumer_key" value="{options:website_consumer_key}" maxlength="80" size="40" /></td>
	</tr>
	<tr>
		<td class="leftc">{icon:personal} {lang:website_consumer_secret}</td>
		<td class="leftb"><input type="password" name="website_consumer_secret" value="{options:website_consumer_secret}" maxlength="80" size="40" /></td>
	</tr>
	<tr>
		<td class="leftc" style="width: 200px;">{icon:home} {lang:website_enable}</td>
		<td class="leftb">{lang:yes} <input type="radio" name="website_enable" value="1" {options:website_enable_yes}> /
			<input type="radio" name="website_enable" value="0" {options:website_enable_no}> {lang:no}</td>
	</tr>
	<tr>
		<td class="leftc" colspan="2"><a href="http://twitter.com/settings/connections" target="twitter">{lang:your_connections}</a></td>
	</tr>
	<tr>
		<td class="leftc">{icon:personal} {lang:website_access_token}</td>
		<td class="leftb"><input type="text" name="website_access_token" value="{options:website_access_token}" maxlength="80" size="40" /></td>
	</tr>
	<tr>
		<td class="leftc">{icon:personal} {lang:website_access_secret}</td>
		<td class="leftb"><input type="password" name="website_access_secret" value="{options:website_access_secret}" maxlength="80" size="40" /></td>
	</tr>
	<tr>
		<td class="leftc">{icon:xclock} {lang:timeout}</td>
		<td class="leftb"><input type="text" name="timeout" value="{options:timeout}" maxlength="3" size="2" /></td>
	</tr>
	<tr>
		<td class="leftc">{icon:playlist} {lang:max_navlist}</td>
		<td class="leftb"><input type="text" name="max_navlist" value="{options:max_navlist}" maxlength="2" size="2" /></td>
	</tr>
	<tr>
		<td class="leftc">{icon:playlist} {lang:max_headline}</td>
		<td class="leftb"><input type="text" name="max_headline" value="{options:max_headline}" maxlength="2" size="2" /></td>
	</tr>
	<tr>
		<td class="leftb">{icon:ksysguard} {lang:options}</td>
		<td class="leftc">
			<input type="submit" name="submit" value="{lang:create}" class="form" />
			<input type="submit" name="request" value="{lang:keygen}" class="form" />
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
		<td class="centerb"><a href="?mod=twitter&amp;action=manage">{lang:continue}</a></td>
	</tr>
</table>
{stop:view_2}
