<h1 class="display-1 mb-5">Spieler {if $task == "edit"}bearbeiten{else}Hinzufügen{/if}</h1>

<form action="" method="post">
    <input type="hidden" name="userRegisterFormAction" id="userRegisterFormAction" value="{$hidden}">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="userRegisterAccountFirstName">Vorname</label>
                <input class="form-control"  type="text" id="userRegisterAccountFirstName" name="userRegisterAccountFirstName" placeholder="" value="{$info.firstName}" required>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label for="userRegisterAccountLastname">Nachname</label>
                <input class="form-control"  type="text" id="userRegisterAccountLastname" name="userRegisterAccountLastName" placeholder="" value="{$info.lastName}" required>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="userRegisterAccountEmail">E-Mail</label>
                <input class="form-control"  type="text" id="userRegisterAccountEmail" name="userRegisterAccountEmail" placeholder="" value="{$info.email}">
            </div>
        </div>

        <div class="col-md-6">
          <label class="d-block">Geschlecht</label>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="userRegisterAccountGender" id="inlineRadio1" value="Male" {if $info.gender == "Male"}checked{/if}>
            <label class="form-check-label" for="inlineRadio1">Mann</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="userRegisterAccountGender" id="inlineRadio2" value="Female" {if $info.gender == "Female"}checked{/if}>
            <label class="form-check-label" for="inlineRadio2">Frau</label>
          </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <label for="userRegisterAccountBday">Geburtsdatum</label>
            <div class="input-group date" data-provide="datepicker">
                <input class="form-control" type="text" id="userRegisterAccountBday" name="userRegisterAccountBday" placeholder="dd.mm.YYYY" value="{$info.bday|date_format:'d.m.Y'}">
                <div class="input-group-addon">
                    <span class="glyphicon glyphicon-th"></span>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label for="userRegisterAccountPhone">Telefon</label>
                <input class="form-control"  type="text" id="userRegisterAccountPhone" name="userRegisterAccountPhone" placeholder="" value="{$info.phone}">

            </div>
        </div>
    </div>

    <h3 class="display-3 mt-5 mb-5">Verein</h3>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="userRegisterAccountPlayerId">Spielernummer</label>
                <input class="form-control"  type="text" id="userRegisterAccountPlayerId" name="userRegisterAccountPlayerId" placeholder="" value="{$info.playerId}">
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label for="userRegisterAccountClubId">Verein</label>
                <select class="form-control" name="userRegisterAccountClubId">
                    {foreach item=club from=$clubs}
                        <option value="{$club.clubId}" {if $club.clubId == $info.clubId}selected{/if}>{$club.name}</option>
                    {/foreach}
                </select>
            </div>
        </div>
    </div>

    <h3 class="display-3 mt-5 mb-5">Generelles</h3>
    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label class="checkbox" for="userRegisterAccountIsPlayer"><input type="checkbox" value="1" id="userRegisterAccountIsPlayer" name="userRegisterAccountIsPlayer" data-toggle="checkbox" class="custom-checkbox" {if $info.activePlayer == 1}checked{/if}>
                <span class="icons"><span class="icon-unchecked"></span><span class="icon-checked"></span></span> Aktiver Spieler
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label class="checkbox" for="userRegisterAccountIsReporter"><input type="checkbox" value="1" id="userRegisterAccountIsReporter" name="userRegisterAccountIsReporter" data-toggle="checkbox" class="custom-checkbox" {if $info.reporter == 1}checked{/if}>
                <span class="icons">
                  <span class="icon-unchecked"></span>
                  <span class="icon-checked"></span>
                </span> Reporter
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label class="checkbox" for="userRegisterAccountIsAdmin"><input type="checkbox" value="1" id="userRegisterAccountIsAdmin" name="userRegisterAccountIsAdmin" data-toggle="checkbox" class="custom-checkbox" {if $info.admin == 1}checked{/if}>
                <span class="icons"><span class="icon-unchecked"></span><span class="icon-checked"></span></span> Admin
            </div>
        </div>
    </div>
    <p></p>

    <div class="row initline">
        <div class="col-md-6">
            <input type="submit" name="submit" class="btn btn-success btn-wide" value="{if $task == "edit"}Bearbeiten{else}Hinzufügen{/if}">
        </div>
    </div>
</form>
