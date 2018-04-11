<div id="formUserRegister">
  <h3>Update Your Account for Badminton Ranking</h3>
  <p>Change your email, full name, gender and password.</p>
  <hr/>
  <form action="" method="post">
    <input type="hidden" name="userRegisterFormAction" id="userRegisterFormAction" value="Update My Account ">


    <div class="row">
      <div class="col-md-6">
        <div class="form-group">
          <label for = "userRegisterAccountFirstName">First Name:</label>
          <input class="form-control"  type="text" id="userRegisterAccountFirstName" name="userRegisterAccountFirstName" placeholder="Dein Vorname" value="{$vars['FNameValue']}">
        </div>
      </div>

      <div class="col-md-6">
        <div class="form-group">
          <label for = "userRegisterAccountLastName">Last Name:</label>
          <input class="form-control"  type="text" id="userRegisterAccountLastName" name="userRegisterAccountLastName" placeholder="Dein Nachname" value="{$vars['LNameValue']}">
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-md-6">
        <div class="form-group">
          <label for = "userRegisterAccountEmail">E-mail:</label>
          <input class="form-control" type="text" id="userRegisterAccountEmail" name="userRegisterAccountEmail" placeholder="your.name@bc-comet.de" value="{$vars['EmailValue']}">
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
          <label style="display: block"  for="userRegisterAccountLastName">Geschlecht:</label>
          <input type="checkbox" checked data-toggle="switch" name="info-square-switch" data-on-color="success" id="switch-gender" />
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-md-6">
        <div class="form-group">
          <label for = "userRegisterAccountPlayerId">Spielernr:</label>
          <input class="form-control"  type="text" id="userRegisterAccountPlayerId" name="userRegisterAccountPlayerId" placeholder="" value="{$vars['playerIdValue']}">
        </div>
      </div>

      <div class="col-md-6">
        <div class="form-group">
          <label for="userRegisterAccountClub">Verein:</label>
          <select class="form-control" id="userRegisterAccountClub" name="userRegisterAccountClub">
            {foreach item=club from=$clubs}
              <option value="{$club.clubId}">{$club.name}
            {/foreach}
          </select>
        </div>
      </div>
    </div>



    <!-- Success / Default -->
    <h4>Passwort</h4>
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

    <h4>Weiteres</h4>

    <input class="btn btn-success" type="submit" name="submit" value="Spieler anlegen">
  </form>
</div>
