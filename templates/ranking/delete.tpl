<form action="" method="post">
    <H1>Spiel löschen</H1>
    <input type="hidden" id="eloRankingFormAction" name="eloRankingFormAction" value="deleteEloMatch">
    <input type="hidden" name="eloRankingGameId" value="{$game.gameId}">
    <div class="row">
        <div class="col-md-12">
            <div class="alert alert-warning" role="alert">
              Möchten Sie das Spiel <br>
              <center>{$game.playerName} vs {$game.opponentName}<br>
              Sätze: {$game.sets}</center><br>
              wirklich löschen ? <br>
              Wenn <b>ja</b>, dann wird automatisch die Rangliste neu berechnet.
            </div>
            <div class="form-check mb-3">
                <input type="checkbox" value="1" class="form-check-input" id="exampleCheck1">
                <label class="form-check-label" for="exampleCheck1">Ja löschen und neu berechnen</label>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <input type="submit" class="btn btn-primary" value="Löschen">
        </div>
        <div class="col-md-6 text-right">
            <a class="btn btn-danger" href="{$linkBack}">Zurück</a>
        </div>
    </div>

</form>
