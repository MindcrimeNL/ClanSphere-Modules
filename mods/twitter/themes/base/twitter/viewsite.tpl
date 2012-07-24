<table class="forum" style="width:{page:width}">
 <tr>
  <td class="headb"></td>
  <td class="headb">{lang:name}</td>
  <td class="headb">{lang:date}</td>
 </tr>{loop:tweets}
 <tr>
  <td class="leftb" rowspan="2">{tweets:image}</td>
  <td class="leftb">{icon:tweet}<b>{tweets:name}</b></td>
  <td class="leftb">{tweets:date}</td>
 </tr>
 <tr>
  <td class="leftb" colspan="2">{tweets:message}</td>
 </tr>{stop:tweets}
 <tr>
  <td></td>
  <td align="right">{if:prev}<a href="{url:twitter_viewsite:start=}{tweet:previous}">{icon:back}</a>{stop:prev}</td>
  <td align="left"><a href="{url:twitter_viewsite:start=}{tweet:next}">{icon:forward}</a></td>
 </tr>
</table>
