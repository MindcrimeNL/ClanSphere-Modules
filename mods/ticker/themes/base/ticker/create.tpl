<table class="forum" style="width:{page:width}" cellpadding="0" cellspacing="{page:cellspacing}">
	<tr>
		<td class="headb">{lang:mod_name} - {lang:create}</td>
	</tr>
	<tr>
		<td class="leftc">{head:body}</td>
	</tr>
</table>
<br />

<form method="post" id="ticker_create" action="{url:ticker_create}" enctype="multipart/form-data">
<table class="forum" style="width:{page:width}" cellpadding="0" cellspacing="{page:cellspacing}">
	<tr>
		<td class="leftc">{icon:cell_layout} {lang:direction} *</td>
		<td class="leftb">{ticker:ticker_direction}</td>
	</tr>
	<tr>
		<td class="leftc">{icon:db_comit} {lang:amount} *</td>
		<td class="leftb"><input type="text" name="ticker_amount" value="{ticker:ticker_amount}" maxlength="2" size="5" /></td>
	</tr>
	<tr>
		<td class="leftc">{icon:clock} {lang:delay} *</td>
		<td class="leftb"><input type="text" name="ticker_delay" value="{ticker:ticker_delay}" maxlength="2" size="5" /></td>
	</tr>
	<tr>	
		<td class="leftc">{icon:contents} {lang:content} *</td>
		<td class="leftb">{ticker:ticker_features}<br />
		<textarea class="rte_abcode" name="ticker_content" cols="75" rows="10" id="ticker_content">{ticker:ticker_content}</textarea>
		</td>
	</tr>
	<tr>
		<td class="leftc">{icon:ksysguard} {lang:options}</td>
		<td class="leftb"><input type="submit" name="submit" value="{lang:create}" /></td>
	</tr>
</table>
</form>