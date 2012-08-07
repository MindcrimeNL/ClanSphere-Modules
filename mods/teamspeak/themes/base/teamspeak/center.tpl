<table class="forum" style="width:{page:width}" cellpadding="0" cellspacing="1">
	<tr>
		<td class="headb">{lang:mod} - {lang:head_center}</td>
	</tr>
	<tr>
		<td class="leftc">{head:message}</td>
	</tr>
</table>
<br />
<table class="forum" style="width:{page:width}" cellpadding="0" cellspacing="1">
  <tr>
    <td class="headb">{lang:name}</td>
	<td class="headb">{lang:ip}</td>
	<td class="headb">{lang:options}</td>
  </tr>
  {loop:tss}
  <tr>
    {if:nodata}
    <td class="centerb" colspan="3">{tss:no_data}</td>
    {stop:nodata}
	{if:data}
	<td class="leftb">{tss:name}</td>
	<td class="leftb">{tss:ip}</td>
	<td class="centerb">{tss:view}</td>
	{stop:data}
  </tr>
  {stop:tss}
</table>  
