<h1 class="display-1 mb-5">Staff {if $laction == 'edit'}editieren{else}anlegen{/if}</h1>
<form action="" method="post">
    <input type="hidden" name="staffFormAction" id="staffFormAction" value="Insert Staff">
    <div class="row initline mb-5">
        <div class="col-md-4 input-control" data-role="select">
            <label class="d-block font-weight-bold">User</label>
            <select class="form-control js-data-ajax-user" type="text" id="staffUserId" name="staffUserId"></select>
        </div>


        <div class="col-md-4">
            <label class="d-block font-weight-bold">Reihe</label>
            <select class="js-example-basic-single form-control " name="staffRow" placeholder="Bitte wählen">
                {html_options options=$rowOption sselected=$data.row}
            </select>
        </div>

        <div class="col-md-4">
            <label class="d-block font-weight-bold">Position</label>
            <select class="js-example-basic-single form-control " name="staffPosition" placeholder="Bitte wählen">
                {html_options options=$colOption selected=$data.position}
            </select>
        </div>
    </div>

    <div class="row initline mb-5">
        <div class="col-md-12">
            <label class="d-block font-weight-bold">Beschreibung</label>
            <textarea class="form-control" id="staffDescription" rows="3" name="staffDescription">{$data.description}</textarea>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <input type="submit" name="submit" class="btn btn-success btn-wide" value="Melden">
        </div>
        <div class="col-md-6 text-right">
            <a class="btn btn-danger" href="?action=details&id={$tournament.tournamentId}">Zurück</a>
        </div>
    </div>
</form>
