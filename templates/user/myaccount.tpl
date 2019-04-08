<h2 class="display-1 mb-5">Profil aktualisieren</h2>

<ul class="nav justify-content-center">
  <li class="nav-item">
    <a class="nav-link active" href="#">Profil</a>
  </li>
  <li class="nav-item">
    <a class="nav-link disabled" href="#">Bild</a>
  </li>
  <li class="nav-item">
    <a class="nav-link disabled" href="#">Password</a>
  </li>
</ul>

<hr>

<div id="formUserRegister">

  <form action="" method="post">
    <input type="hidden" name="userRegisterFormAction" id="userRegisterFormAction" value="Update My Account ">


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
        <div class="input-group date" data-provide="datepicker">
          <input type="text" class="form-control" id="userRegisterAccountBday" name="userRegisterAccountBday" placeholder="" value="{$vars['bdayValue']|date_format:"d.m.Y"}">
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
          <input class="form-control"  type="text" id="userRegisterAccountPlayerId" name="userRegisterAccountPlayerId" placeholder="" value="{$vars['playerIdValue']}" disabled="disabled" >
        </div>
      </div>

      <div class="col-md-6">
        <div class="form-group" data-toggle="tooltip" title="Kann nur ein Admin ändern">
          <label for="userRegisterAccountClub">Verein:</label>
          <input class="form-control"  type="text" id="userRegisterAccountClub" name="userRegisterAccountClub" placeholder="" value="{$vars['clubNameValue']}" disabled="disabled">
        </div>
      </div>
    </div>



    <!-- Success / Default -->
    <h2 class="mt-5 mb-2">Passwort</h2>
    <div class="row">
      <div class="col-md-6">
        <div class="form-group">
          <label for = "userRegisterAccountPassword">Password:</label>
          <input class="form-control" type="password" id="userRegisterAccountPassword" name="userRegisterAccountPassword" placeholder="" value="">
        </div>
      </div>

      <div class="col-md-6">
        <div class="form-group">
          <label for = "userRegisterAccountPassword2">Repeat Password:</label>
          <input class="form-control" type="password" id="userRegisterAccountPassword2" name="userRegisterAccountPassword2" placeholder="" value="">
        </div>
      </div>
    </div>

    <input class="btn btn-success" type="submit" name="submit" value="Daten ändern">
  </form>
</div>
