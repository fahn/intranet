<div id="formUserRegister">
  <form action="" method="post">
    <input type="hidden" name="userRegisterFormAction" id="userRegisterFormAction" value="Update My Account ">

    <h4 class="form-section"><i class="ft-eye"></i> About User</h4>

    <div class="row">
      <div class="col-md-6">
        <div class="form-group">
          <label for = "userRegisterAccountFirstName">Vorname:</label>
          <input class="form-control"  type="text" id="userRegisterAccountFirstName" name="userRegisterAccountFirstName" placeholder="Dein Vorname" value="{$vars['FNameValue']}">
        </div>
      </div>

      <div class="col-md-6">
        <div class="form-group">
          <label for = "userRegisterAccountLastName">Nachname:</label>
          <input class="form-control"  type="text" id="userRegisterAccountLastName" name="userRegisterAccountLastName" placeholder="Dein Nachname" value="{$vars['LNameValue']}">
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-md-6">
        <div class="form-group">
          <label for = "userRegisterAccountEmail">E-Mail-Adresse:</label>
          <input class="form-control" type="text" id="userRegisterAccountEmail" name="userRegisterAccountEmail" placeholder="your.name@email.com" value="{$vars['EmailValue']}">
        </div>
      </div>

      <div class="col-md-6">
        <div class="form-group">
          <label for="userRegisterAccountPhone">Telefon:</label>
          <input class="form-control"  type="text" id="userRegisterAccountPhone" name="userRegisterAccountPhone" placeholder="0162 ..." value="{$vars['phoneValue']}">
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-md-6">
        <label for="userRegisterAccountBday">Geburtstag:</label>
        <div class="input-group">
          <input type="text" class="form-control datepicker" id="userRegisterAccountBday" name="userRegisterAccountBday" placeholder="" value="{$vars['bdayValue']|date_format:"d.m.Y"}" data-input>
          <div class="input-group-addon">
              <span class="glyphicon glyphicon-th"></span>
          </div>
        </div>
      </div>

      <div class="col-md-6">
        <div class="form-group">
          <label for="userRegisterAccountGender">Geschlecht:</label>
          <select class="custom-select" name="userRegisterAccountGender">
            <option value="Male" {if $vars['genderValue'] == 'Male'}selected{/if}> Mann</option>
            <option value="Female" {if $vars['genderValue'] == 'Female'}selected{/if}> Frau</option>
          </select>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-md-6">
        <div class="form-group" data-toggle="tooltip" title="Kann nur ein Admin ändern">
          <label for = "userRegisterAccountPlayerId">Spielernummer:</label>
          <input class="form-control"  type="text" id="userRegisterAccountPlayerId" name="userRegisterAccountPlayerId" placeholder="" value="{$vars.playerIdValue|default:""}" disabled="disabled" >
        </div>
      </div>

      <div class="col-md-6">
        <div class="form-group" data-toggle="tooltip" title="Kann nur ein Admin ändern">
          <label for="userRegisterAccountClub">Verein:</label>
          <input class="form-control"  type="text" id="userRegisterAccountClub" name="userRegisterAccountClub" placeholder="" value="{$vars.clubNameValue|default:""}" disabled="disabled">
        </div>
      </div>
    </div>
    <input class="btn btn-success mb-5" type="submit" name="submit" value="Daten ändern">
  </form>
</div>
