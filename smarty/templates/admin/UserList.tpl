<h3>User Accounts for Badminton Ranking</h3>
<p>Select an account for update or delete.</p>
<div class="alert alert-danger">
	<p>Klappt noch nicht!</p>
</div>

<form>
	<div class="table-responsive">
	<table class="table table-sm table-striped table-hover">
		<thead>
			<tr>
				<th colspan="2">Select</th>
				<th>EMail</th>
				<th>First Name</th>
				<th>Last Name</th>
				<th>Gender</th>
				<th class="text-center">Player</th>
				<th class="text-center">Reporter</th>
				<th class="text-center">Admin</th>
			</tr>
		</thead>
		<tbody>
{foreach item=user from=$users}
			<tr>
				<td><input type="radio" id="<?php echo $radioId; ?>" name="<?php echo $variableNameAdminUserId; ?>" value="<?php echo $loopUser->userId;?>"></td>
				<td><label class = "radio" for = "<?php echo $radioId; ?>">{$user.userId}</label></td>
				<td>{$user.email}</td>
				<td>{$user.firstName}</td>
				<td>{$user.lastName}</td>
				<td>{$user.gender}</td>
				<td class="text-center">{if $user.isPlayer}<i class="text-success glyphicon glyphicon-ok-circle"></i> {else} <i class="text-danger glyphicon glyphicon-ban-circle"></i>{/if}</td>
				<td class="text-center">{if $user.isReporter}<i class="text-success glyphicon glyphicon-ok-circle"></i> {else} <i class="text-danger glyphicon glyphicon-ban-circle"></i>{/if}</td>
				<td class="text-center">{if $user.isAdmin}<i class="text-success glyphicon glyphicon-ok-circle"</i> {else} <i class="text-danger glyphicon glyphicon-ban-circle"></i>{/if}</td>
			</tr>
{foreachelse}
	<tr>
    <td colspan="8">Failed to get all User from data base. Reason: {if $error} {$error} {/if}</td>
  </tr>
{/foreach}
		</tbody>
	</table>
	<p>
		<input  class="btn btn-success"
			type		= "submit"
			name		= "<?php echo $variableNameAction; ?>"
			value		= "<?php echo $variableNameActionDeleteAccount; ?>"
			formaction	= "<?php echo BrdbHtmlPage::PAGE_ADMIN_ALL_USER;?>"
			formmethod	= "post"
		/>
		<input class="btn btn-success"
			type		= "submit"
			name		= "<?php echo $variableNameAction; ?>"
			value		= "<?php echo $variableNameActionSelectAccount; ?>"
			formaction	= "<?php echo BrdbHtmlPage::PAGE_ADMIN_USER;?>"
			formmethod	= "post"
		/>
	</p>
	</div>
</form>
