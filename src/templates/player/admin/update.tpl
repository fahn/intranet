<h1 class="display-3 mb-5">Spieler {if $task == "edit"}bearbeiten{else}Hinzufügen{/if}</h1>

<form action="" method="post">
    <input type="hidden" name="playerFormAction" id="playerFormAction" value="{$hidden}">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="playerFirstName">Vorname</label>
                <input class="form-control" type="text" id="playerFirstName" name="playerFirstName" placeholder="" value="{$info.firstName}" required>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label for="playerLastname">Nachname</label>
                <input class="form-control" type="text" id="playerLastname" name="playerLastName" placeholder="" value="{$info.lastName}" required>
            </div>
        </div>
    </div>

    <div class="row">
    <div class="col-md-6">
        <label for="playerBday">Geburtsdatum</label>
        <div class="input-group date" data-provide="datepicker">
            <input class="form-control datepicker" type="text" id="playerBday" name="playerBday" placeholder="dd.mm.YYYY" value="{$info.bday|date_format:'d.m.Y'}" data-input>
            <div class="input-group-addon">
                <span class="glyphicon glyphicon-th"></span>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="playerGender">Geschlecht</label>
            <select class="form-control" id="playerGender" name="playerGender">
                <option value="male">Männnlich</option>
                <option value="female">Weiblich</option>
            </select>
        </div>
    </div>


    </div>

    <h3 class="display-3 mt-5 mb-5">Verein</h3>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="playerPlayerId">Spielernummer</label>
                <input class="form-control" type="text" id="playerPlayerId" name="playerPlayerId" placeholder="" value="{$info.playerId}" required>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label for="playerClubId">Verein</label>
                <select class="form-control" name="playerClubId" required>
                    {foreach item=club from=$clubs}
                    <option value="{$club.clubId}" {if $club.clubId==$info.clubId}selected{/if}>{$club.name} </option> {/foreach} </select> </div> </div> </div> <div class="row initline mt-5">
                        <div class="col-md-6">
                            <input type="submit" name="submit" class="btn btn-success btn-wide" value="{if $task == " edit"}Bearbeiten{else}Hinzufügen{/if}"> </div> </div> </form>
