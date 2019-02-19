<h2 class="display-1 mb-5">Spiel hinzufügen</h2>


<form action="" method="post">
  <input type="hidden" name="eloRankingFormAction" id="eloRankingFormAction" value="insertEloMatch">
  <div class="row">
    <div class="col-md-6">
      <div class="form-group">
        <label for="tournamentName">Spieler</label>
        <input class="form-control"  type="text" id="eloRankingPlayer" name="eloRankingPlayer" placeholder="B-/C-Rangliste" value="{$vars['player']}" required1>
      </div>
    </div>

    <div class="col-md-6">
      <div class="form-group">
        <label for="tournamentName">Gegner</label>
        <input class="form-control"  type="text" id="eloRankingOpponent" name="eloRankingOpponent" placeholder="B-/C-Rangliste" value="{$vars['opponent']}" required1>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-4">
      <div class="form-group">
        <label for="tournamentName">Satz 1</label>
        <div class="row">
          <div class="col-md-5">
            <input class="form-control" type="number" min="0" max="30" step="1" name="eloRankingSet1A" placeholder="21" value="{$vars['set1A']}" required1>
          </div>

          <div class="col-md-2">:</div>

          <div class="col-md-5">
            <input class="form-control" type="number" min="0" max="30" step="1" name="eloRankingSet1B" placeholder="21" value="{$vars['set1B']}" required1>
          </div>
      </div>
    </div>
  </div>

    <div class="col-md-4">
      <div class="form-group">
        <label for="tournamentName">Satz 2</label>
        <div class="row">
          <div class="col-md-5">
            <input class="form-control" type="number" min="0" max="30" step="1" name="eloRankingSet2A" placeholder="21" value="{$vars['set2A']}" required1>
          </div>

          <div class="col-md-2">:</div>

          <div class="col-md-5">
            <input class="form-control" type="number" min="0" max="30" step="1" name="eloRankingSet2B" placeholder="21" value="{$vars['set2B']}" required1>
          </div>
      </div>
    </div>
  </div>

    <div class="col-md-4">
      <div class="form-group">
        <label for="tournamentName">Satz 3</label>
        <div class="row">
          <div class="col-md-5">
            <input class="form-control" type="number" min="0" max="30" step="1" name="eloRankingSet3A" placeholder="21" value="{$vars['set3A']}">
          </div>

          <div class="col-md-2">:</div>

          <div class="col-md-5">
            <input class="form-control" type="number" min="0" max="30" step="1" name="eloRankingSet3B" placeholder="21" value="{$vars['set3B']}">
          </div>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-4">
    <div class="form-group">
      <label for="tournamentName">Datum</label>
      <input class="form-control"  type="text" id="tournamentName" name="tournamentName" placeholder="B-/C-Rangliste" value="{$vars['name']}" required1>
    </div>
  </div>
</div>


<div class="row mt-5">
    <div class="col-md-6">
        <input type="submit" name="submit" class="btn btn-success btn-wide" value="Turnier {if $task == "add"}hinzufügen{else}editieren{/if}">
    </div>
    <div class="col-md-6 text-right">
        <a class="btn btn-danger" href="/pages/rankingTournament.php">Zurück</a>
    </div>
</div>

</form>
