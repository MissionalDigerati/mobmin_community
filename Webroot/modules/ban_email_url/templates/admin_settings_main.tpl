<legend>Ban Email URL's</legend>
<p>Simply add the domains you would not like to have access to the website.</p>
<br />

<form name="ban-email-settings" method="post">
	<table class="table table-bordered table-striped">
			<tr>
				<td>Domain Names</td>
				<td><textarea name="domain-names" rows="8" cols="100">{$banned_domains}</textarea><br><p>Please comma seperate the domain names.</p></td>
			</tr>
		</tbody>
	</table>

  <div style="text-align:right">
    <input type="submit" name="submit" value="submit" class="btn btn-default" />
  </div>

</form>
