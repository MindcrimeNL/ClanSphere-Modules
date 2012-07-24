<table class="forum" style="width:{page:width}" cellpadding="0" cellspacing="{page:cellspacing}">
	<tr>
		<td class="headb">{lang:mod} - {lang:opt_bets}</td>
	</tr>
	<tr>
		<td class="leftb">{lang:body_bets}</td>
	</tr>
</table>
<br />

<form method="post" id="bets_options" action="{url:bets_options}">
<table class="forum" style="width:{page:width}" cellpadding="0" cellspacing="{page:cellspacing}">
	<tr>
		<td class="leftc">{icon:bets} {lang:pointsname}</td>
		<td class="leftb"><input type="text" name="pointsname" value="{com:pointsname}" maxlength="20" size="20" /></td>
	</tr>
	<tr>
	  <td class="leftc">{icon:playlist} {lang:auto_title_default}</td>
		<td class="leftb">{lang:yes} <input type="radio" name="auto_title" value="1"{com:auto_title_enable}> /
											<input type="radio" name="auto_title" value="0"{com:auto_title_disable}> {lang:no}</td>
	</tr>
	<tr>
	  <td class="leftc">{icon:playlist} {lang:auto_title_separator}</td>
		<td class="leftb"><input type="text" name="auto_title_separator" value="{com:auto_title_separator}" maxlength="80" size="10" /></td>
	</tr>
	<tr>
		<td class="leftc">{icon:bets} {lang:base_fee}</td>
		<td class="leftb"><input type="text" name="base_fee" value="{com:base_fee}" maxlength="20" size="4" /> {com:pointsname}</td>
	</tr>
    <tr>
      <td class="leftc">{icon:bets} {lang:default_quote_type}</td>
      <td class="leftb"><select name="quote_type">{com:quote_type_options}</select></td>
    </tr>
    <tr>
      <td class="leftc" colspan="2">{lang:quote_type_explain}</td>
    </tr>
    <tr>
	  <td class="leftc">{icon:stop} {lang:remove_quote}</td>
	  <td class="leftb"><input type="text" name="remove_quote" value="{com:remove_quote}" maxlength="20" size="4" /> %</td>
	</tr>
	<tr>
		<td class="leftc">{icon:playlist} {lang:max_navlist}</td>
		<td class="leftb"><input type="text" name="max_navlist" value="{com:max_navlist}" maxlength="20" size="4" /></td>
	</tr>
	<tr>
		<td class="leftc">{icon:playlist} {lang:max_navlist_title}</td>
		<td class="leftb"><input type="text" name="max_navlist_title" value="{com:max_navlist_title}" maxlength="20" size="4" /></td>
	</tr>
 <tr>
  <td class="leftc">{icon:cal} {lang:date_format}</td>
  <td class="leftb"><input type="text" name="date_format" value="{com:date_format}" maxlength="80" size="10" /> {com:date_format_example}</td>
 </tr>
	<tr>
		<td class="leftc">{icon:up} {lang:max_quote}</td>
		<td class="leftb"><input type="text" name="max_quote" value="{com:max_quote}" maxlength="20" size="4" /></td>
	</tr>
	<tr>
		<td class="leftc">{icon:download} {lang:min_quote}</td>
		<td class="leftb"><input type="text" name="min_quote" value="{com:min_quote}" maxlength="20" size="4" /></td>
	</tr>
	<tr>
		<td class="leftc">{icon:reload} {lang:super_quote}</td>
		<td class="leftb"><input type="text" name="super_quote" value="{com:win_quote}" maxlength="20" size="4" /></td>
	</tr>
 	<tr>
   <td class="leftc">{icon:bets} {com:coins_receive_text}</td>
   <td class="leftb"><input type="text" name="coins_receive" value="{com:coins_receive}" maxlength="20" size="4" /></td>
 	</tr>
 	<tr>
   <td class="leftc">{icon:bets} {com:coins_min_length_text}</td>
   <td class="leftb"><input type="text" name="coins_min_length" value="{com:coins_min_length}" maxlength="20" size="4" /></td>
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
