<div id="formUserRegister">
  <form action="" method="post">
    <input type="hidden" name="userRegisterFormAction" id="userRegisterFormAction" value="changePassword">

    <h2 class="mt-5 mb-2">Altes Passwort</h2>
    <div class="row">
      <div class="col-md-12">
        <div class="form-group">
          <label for = "userRegisterAccountPassword">Password:</label>
          <input class="form-control" type="password" id="userRegisterAccountPassword" name="userRegisterAccountPassword" placeholder="" value="">
        </div>
      </div>

    </div>

    <!-- Success / Default -->
    <h2 class="mt-5 mb-2">Neues Passwort</h2>
    <div class="row">
      <div class="col-md-6">
        <div class="form-group">
          <label for = "userRegisterAccountNewPassword">Password:</label>
          <input class="form-control" type="password" id="userRegisterAccountNewPassword" name="userRegisterAccountNewPassword" placeholder="" value="">
        </div>
      </div>

      <div class="col-md-6">
        <div class="form-group">
          <label for = "userRegisterAccountRepeatNewPassword">Repeat Password:</label>
          <input class="form-control" type="password" id="userRegisterAccountRepeatNewPassword" name="userRegisterAccountRepeatNewPassword" placeholder="" value="">
        </div>
      </div>
    </div>

    <input class="btn btn-success" type="submit" name="submit" value="Passwort ändern">
  </form>
</div>
