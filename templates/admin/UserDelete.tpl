<form action="" method="post">
  <input type="hidden" name="userRegisterFormAction" id="userRegisterFormAction" value="{$hidden}">
  <input type="hidden" name="userRegisterAccountAdminUserId" id="userRegisterAccountAdminUserId" value="{$user.userId}">
  <h4>Möchtest du den folgenden Benutzer löschen ?</h4>
  <p>
    Vorname: {$user.firstName} <br>
    Nachname: {$user.lastName}
  </p>
  <div class="form-check mb-5 mt-5 text-center">
    <label class="form-check-label">
      <input type="checkbox" class="form-check-input">
      Ja, bitte löschen
    </label>
  </div>

  <button type="submit" class="btn btn-danger">Benutzer löschen</button>

  <a class="btn btn-info  float-right" href="">Zurück</a>
</form>
