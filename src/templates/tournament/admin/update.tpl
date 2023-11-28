<h2 class="display-1 mb-5">Turnier {if $task == "add"}hinzufügen{else}editieren{/if}</h2>
{{$vars|print_r}}
{if $task == "edit" && $vars.visible == 0}
<div class="alert alert-danger">
    <strong>Achtung</strong> Das Turnier wurde gelöscht
</div>
{/if}

<form action="" method="post">
    <input type="hidden" name="tournamentFormAction" id="tournamentFormAction" value="{$hidden}">
    <div class="row">
        <div class="col-md-8">
            <div class="form-group">
                <label for="tournamentName">Name des Turniers:</label>
                <input class="form-control" type="text" id="tournamentName" name="tournamentName" placeholder="B-/C-Rangliste" value="{$vars['name']|default:""}" required>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-8">
            <div class="form-group">
                <label for="tournamentPlace">Ort:</label>
                <input class="form-control" type="text" id="tournamentPlace" name="tournamentPlace" placeholder="Braunschweig" value="{$vars['place']|default:""}" required>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <label for="tournamentStartdate">Start-Datum:</label>
            <div class="input-group">
                <input class="form-control datetimepicker" type="text" id="tournamentStartdate" name="tournamentStartdate" placeholder="dd.mm.YYYY" value="{$vars['startdate']|date_format:" d.m.Y H:m"|default:""}" data-input required>
                <div class="input-group-addon">
                    <span class="glyphicon glyphicon-th"></span>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <label for="tournamentEnddate">Enddatum:</label>
            <div class="input-group">
                <input class="form-control datepicker" type="text" id="tournamentEnddate" name="tournamentEnddate" placeholder="dd.mm.YYYY" value="{$vars['enddate']|date_format:" d.m.Y"|default:""}" data-input required>
                <div class="input-group-addon">
                    <span class="glyphicon glyphicon-th"></span>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <label for="tournamentDeadline">Meldeschluss:</label>
            <div class="input-group">
                <input class="form-control datepicker" type="text" id="tournamentDeadline" name="tournamentDeadline" placeholder="dd.mm.YYYY" value="{$vars['deadline']|date_format:" d.m.Y"|default:""}" data-input required>
                <div class="input-group-addon">
                    <span class="glyphicon glyphicon-th"></span>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="form-group">
                <label for="tournamentLink">Link zur Ausschreibung:</label>
                <input class="form-control" type="text" id="tournamentLink" name="tournamentLink" placeholder="http://" value="{$vars['link']|default:""}">
            </div>
        </div>
    </div>


    <div class="row">
        <div class="col-md-8">
            <div class="form-group">
                <label for="tournamentClassification">Altersklasse:</label>
                <select multiple="multiple" class="form-control js-example-basic-single" name="tournamentClassification[]" id="tournamentClassification" autocomplete="off">
                    {html_options options=$classificationArr selected=$vars.classification}
                </select>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="tournamentAdditionalClassification">Weitere:</label>
                <input class="form-control" type="text" id="tournamentAdditionalClassification" name="tournamentAdditionalClassification" placeholder="A,B,C" value="{$vars['additionalClassification']|default:""}">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label for="tournamentDiscipline">Diziplinen:</label>
                <select multiple="multiple" class="form-control js-example-basic-single" name="tournamentDiscipline[]" id="tournamentDiscipline" autocomplete="off">
                    {html_options options=$disciplineArr selected=$vars.discipline}
                </select>
            </div>
        </div>
    </div>

    <h3 class="mt-5 display-4">Benachrichtigung</h3>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="tournamentReporterId">Reporter:</label>
                <select  class="form-control js-example-basic-single" id="tournamentReporterId" name="tournamentReporterId" autocomplete="off">
                    {html_options options=$reporterArr selected=$vars.reporterId}
                </select>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="tournamentTournamentType">Type des Turniers:</label>
                <select class="form-control js-example-basic-single" name="tournamentTournamentType" autocomplete="off">
                    {html_options options=$tournamentType selected=$vars.tournamentType}
                </select>
            </div>
        </div>
    </div>
    <h3 class="mt-5">Beschreibung</h3>
    <div class="row">
        <div class="col-md-12">
            <textarea id="summernote" name="tournamentDescription">{$vars['description']|default:""}</textarea>
        </div>
    </div>


    {if $task == "edit"}
    <div class="row mt-5 mb-5">
        <div class="col-md-12">
            <div class="alert alert-warning">
                <strong>Warnung!</strong> Hiermit löscht du dieses Turnier
            </div>
            <input type="checkbox" name="tournamentDelete" id="tournamentDelete" value="1"> <label for="tournamentDelete">Turnier löschen</label>
        </div>
    </div>
    <p></p>
    {/if}

    <div class="row mt-5">
        <div class="col-md-6">
            <input type="submit" name="submit" class="btn btn-success btn-wide" value="Turnier {if $task == " add"}hinzufügen{else}editieren{/if}"> </div> <div class="col-md-6 text-right">
            <a class="btn btn-danger" href="/pages/rankingTournament.php">Zurück</a>
        </div>
    </div>
</form>
