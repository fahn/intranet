<div id="formReportAllGames">
		<h3>Overview of All Played Games</h3>
		<p>Select a game for update or delete.</p>
		<hr/>
		<form>
			<table class="table table-sm table-striped table-hover">
				<caption>Table of All Played Games.</caption>
				<thead>
					<tr>
						<th>Id</th>
						<th>Date</th>
						<th>Time</th>
						<th>Team A</th>
						<th>Team B</th>
						<th>Points</th>
					</tr>
				</thead>
				<tbody>
          {foreach item=game from=$games}
					<tr>
						<td>
							<input type="radio" id="radioId" name	= "$variableNameAdminMatchId" value="{$game.matchId}"> {$game.matchId}
						</td>
						<td>{$game.datetime|date_format:"d.m.Y"}</td>
						<td>{$game.datetime|date_format:"H:i"}</td>
						<td>{$game.playerA1} {if $game.playerA2}// {$game.playerA2}{/if}</td>
						<td>{$game.playerB1} {if $game.playerB2}// {$game.playerB2}{/if}</td>

						<td>{$game.setA1}:{$game.setB1} {$game.setA2}:{$game.setB2} {if $game.setA3}{$game.setA3}:{$game.setB3}{/if}</td>
					</tr>

          {foreachelse}
            <tr>
              <td colspan="9">Failed to get all Games from data base. Reason: {$error} </td>
            </tr>
          {/foreach}
				</tbody>
			</table>
			<p>

        {if $isReporter}
				<input
					type		= "submit"
					name		= "<?php echo $variableNameAction; ?>"
					value		= "<?php echo $variableNameActionDeleteGame; ?>"
					formaction	= "<?php echo BrdbHtmlPage::PAGE_REPORT_ALL_GAME;?>"
					formmethod	= "post"
				/>
				<input
					type		= "submit"
					name		= "<?php echo $variableNameAction; ?>"
					value		= "<?php echo $variableNameActionUpdateGame; ?>"
					formaction	= "<?php echo BrdbHtmlPage::PAGE_REPORT_INSERT_GAME;?>"
					formmethod	= "post"
				/>
{/if}
			</p>
		</form>
	</div>
