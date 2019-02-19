<h1 class="display-1">Übersicht</h1>

<p class="text-right">
	<a class="btn btn-success" href="?action=add">Spiel eintragen</a>
</p>

<div class="table-responsive">
	<table class="table table-sm table-striped table-hover">
		<thead>
			<tr>
				<th>Date</th>
				<th>Time</th>
				<th>Team A</th>
				<th>Team B</th>
				<th>Points</th>
				<th class="text-center">Optionen</th>
			</tr>
		</thead>
		<tbody>
	    {foreach item=game from=$games}
			<tr>
				<td>{$game.datetime|date_format:"d.m.Y"}</td>
				<td>{$game.datetime|date_format:"H:i"}</td>
				<td>{$game.playerA1} {if $game.playerA2}// {$game.playerA2}{/if}</td>
				<td>{$game.playerB1} {if $game.playerB2}// {$game.playerB2}{/if}</td>
				<td>{$game.setA1}:{$game.setB1} {$game.setA2}:{$game.setB2} {if $game.setA3}{$game.setA3}:{$game.setB3}{/if}</td>
				<td class="text-center"><a class="btn btn-info" href="?action=edit&id={$game.matchId}">Editieren</a> <a class="btn btn-danger" href="?action=delete&id={$game.matchId}">Löschen</a></td>
			</tr>
	    {foreachelse}
	      <tr>
	        <td colspan="9">Failed to get all Games from data base. Reason: {$error} </td>
	      </tr>
	    {/foreach}
		</tbody>
	</table>
</div>
