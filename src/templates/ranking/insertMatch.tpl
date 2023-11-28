<h2 class="display-1 mb-5">Spiel hinzufügen</h2>
<form action="" method="post">
    <input type="hidden" name="rankingFormAction" id="rankingFormAction" value="insertMatch">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="rankingPlayer">Spieler</label>
                <select class="form-control js-data-ajax" type="text" id="rankingPlayer" data-item="player" name="rankingPlayer"></select>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label for="rankingOpponent">Gegner</label>
                <select class="form-control js-data-ajax" type="text" id="rankingOpponent" data-item="player" name="rankingOpponent"></select>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label for="tournamentName">Satz 1</label>
                <div class="row">
                    <div class="col-md-5">
                        <input class="form-control" type="number" min="0" max="30" step="1" name="rankingSet1[]" placeholder="21" value="{$vars['set1A']}" required1>
                    </div>

                    <div class="col-md-2">:</div>

                    <div class="col-md-5">
                        <input class="form-control" type="number" min="0" max="30" step="1" name="rankingSet1[]" placeholder="21" value="{$vars['set1B']}" required1>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label for="tournamentName">Satz 2</label>
                <div class="row">
                    <div class="col-md-5">
                        <input class="form-control" type="number" min="0" max="30" step="1" name="rankingSet2[]" placeholder="21" value="{$vars['set2A']}" required1>
                    </div>

                    <div class="col-md-2">:</div>

                    <div class="col-md-5">
                        <input class="form-control" type="number" min="0" max="30" step="1" name="rankingSet2[]" placeholder="21" value="{$vars['set2B']}" required1>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label for="tournamentName">Satz 3</label>
                <div class="row">
                    <div class="col-md-5">
                        <input class="form-control" type="number" min="0" max="30" step="1" name="rankingSet3[]" placeholder="21" value="{$vars['set3A']}">
                    </div>

                    <div class="col-md-2">:</div>

                    <div class="col-md-5">
                        <input class="form-control" type="number" min="0" max="30" step="1" name="rankingSet3[]" placeholder="21" value="{$vars['set3B']}">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <label for="rankingGameTime">Datum:</label>
            <div class="input-group">
                <input class="form-control datetimepicker" type="text" id="rankingGameTime" name="rankingGameTime" placeholder="dd.mm.YYYY H:m" value="{$vars['enddate']|date_format:" d.m.Y"}" data-input required>
                <div class="input-group-addon">
                    <span class="glyphicon glyphicon-th"></span>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-5">
        <div class="col-md-6">
            <input type="submit" name="submit" class="btn btn-success btn-wide" value="Spiel {if $task == " add"}hinzufügen{else}editieren{/if}"> </div> <div class="col-md-6 text-right">
            <a class="btn btn-danger" href="#">Zurück</a>
        </div>
    </div>
</form>
