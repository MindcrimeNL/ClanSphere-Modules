<table class="forum" cellpadding="0" cellspacing="{page:cellspacing}" style="width:{page:width};">
  <tr>
    <td class="headb"><a href="{url:twitter_view}">{lang:twitter_view}</a> <a href="{url:twitter_viewown}">{lang:twitter_viewown}</a></td>
  </tr>
  <tr>
    <td class="headb">{tweet:error}</td>
  </tr>
</table>
<br />
<form method="post" id="twitter_create" action="{url:twitter_create}">
<table class="forum" cellpadding="0" cellspacing="{page:cellspacing}" style="width:{page:width};">
  <tr>
    <td class="leftc" valign="top">{icon:tweet} {lang:twitter_message}</td>
    <td class="leftb"><textarea name="message" cols="40" rows="3">{tweet:message}</textarea></td>
  </tr>
  <tr>
    <td class="leftc" valign="top" colspan="2">{lang:message_size}<br />{tweet:currently}</td>
  </tr>
  <tr>
    <td class="leftc">{icon:ksysguard} {lang:options}</td>
    <td class="leftb"><input type="submit" name="submit" value="{lang:create}" /></td>
  </tr>  
</table>
</form>
