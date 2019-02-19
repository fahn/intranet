<h3>Vereine</h3>
<p class="text-right">
	<a class="btn btn-success" href="?action=add_club">Club hinzufügen</a>
</p>

{if $pagination}
	{include file="_pagination.tpl"}
{/if}

<div class="table-responsive">
	<table class="table table-sm table-striped table-hover">
		<thead>
			<tr>
				<th>Name</th>
				<th>Vereinsnummer</th>
				<th>Verband</th>
				<th class="text-center">Option</th>
			</tr>
		</thead>
		<tbody>
			{foreach item=club from=$clubs}
				<tr>
					<td>{$club.name}</td>
					<td>{$club.clubNumber}</td>
					<td>{$club.association}</td>
					<td class="text-center"><a class="btn btn-info" href="?action=edit&id={$club.clubId}">Editieren</a> <a class="btn btn-danger" href="?action=delete&id={$club.clubId}">Löschen</a></td>
				</tr>
			{foreachelse}
				<tr>
			    <td colspan="4" class="text-center">Failed to get all clubs.</td>
			  </tr>
			{/foreach}
		</tbody>
	</table>
</div>

{if $pagination}
	{include file="_pagination.tpl"}
{/if}
